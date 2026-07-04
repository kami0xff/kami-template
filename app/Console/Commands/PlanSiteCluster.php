<?php

namespace App\Console\Commands;

use App\Services\Anthropic;
use App\Services\Sites\ClusterRepository;
use App\Services\Sites\ContentRepository;
use App\Services\Sites\Site;
use App\Services\Sites\SiteRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Topic cluster planner: one PILLAR page per head keyword (broad,
 * competitive) surrounded by SPOKE articles (narrow long-tail queries), all
 * interlinked so the spokes push authority up to the pillar.
 *
 *   php artisan site:cluster example                       # list clusters + progress
 *   php artisan site:cluster example "pergola"             # plan a new cluster (AI)
 *   php artisan site:cluster example pergola --write=3     # draft next 3 planned articles
 *
 * The plan is a reviewable YAML file (resources/sites/{key}/clusters/) —
 * prune spokes or tweak titles/keywords before drafting. Drafting delegates
 * to site:write with --cluster, which injects hub-and-spoke link
 * instructions into the prompt. Spokes are drafted first; the pillar is
 * drafted last so it can link to every spoke that actually exists.
 */
class PlanSiteCluster extends Command
{
    protected $signature = 'site:cluster
                            {site : Site key under resources/sites/}
                            {topic? : Head topic to plan (e.g. "pergola"), or an existing cluster name}
                            {--spokes=8 : Number of spoke articles to plan}
                            {--write=0 : Draft up to N planned articles (spokes first, pillar last)}
                            {--publish : Pass --publish to site:write (skip the draft stage)}';

    protected $description = 'Plan a topic cluster (pillar + spoke articles) and optionally draft it in batches';

    public function handle(
        SiteRegistry $registry,
        ContentRepository $content,
        ClusterRepository $clusters,
        Anthropic $anthropic,
    ): int {
        $site = $registry->get($this->argument('site'));

        if ($site === null) {
            $this->components->error("Site [{$this->argument('site')}] not found under resources/sites/.");

            return self::FAILURE;
        }

        $topic = $this->argument('topic');

        if ($topic === null) {
            return $this->listClusters($site, $clusters);
        }

        $name = Str::slug($topic);
        $plan = $clusters->get($site, $name);

        if ($plan === null) {
            try {
                $plan = $this->plan($site, $content, $anthropic, $topic);
            } catch (\Exception $e) {
                $this->components->error($e->getMessage());

                return self::FAILURE;
            }

            $clusters->save($site, $name, $plan);
            $this->components->info("Cluster [{$name}] planned — review/edit resources/sites/{$site->key}/clusters/{$name}.yaml");
        }

        $this->renderPlan($name, $plan);

        if (($budget = (int) $this->option('write')) > 0) {
            return $this->write($site, $clusters, $name, $plan, $budget);
        }

        $this->line('');
        $this->components->bulletList([
            'Edit the YAML to prune spokes or adjust titles/keywords',
            "Draft the next articles: php artisan site:cluster {$site->key} {$name} --write=3",
        ]);

        return self::SUCCESS;
    }

    protected function listClusters(Site $site, ClusterRepository $clusters): int
    {
        $all = $clusters->all($site);

        if ($all === []) {
            $this->components->info("No clusters yet. Plan one: php artisan site:cluster {$site->key} \"your topic\"");

            return self::SUCCESS;
        }

        foreach ($all as $name => $plan) {
            $this->renderPlan($name, $plan);
        }

        return self::SUCCESS;
    }

    /**
     * Ask the AI to design the cluster: which long-tail queries deserve
     * their own article, and what the pillar must cover to own the head term.
     */
    protected function plan(Site $site, ContentRepository $content, Anthropic $anthropic, string $topic): array
    {
        $this->components->info("Planning a cluster around \"{$topic}\"...");

        $spokes = max(3, (int) $this->option('spokes'));

        $existing = $content->posts($site)
            ->take(50)
            ->map(fn($post) => "- /blog/{$post->slug} — \"{$post->title()}\"")
            ->implode("\n") ?: '(none)';

        $description = $site->seo['description'] ?? config('seo.description', '');

        $prompt = <<<PROMPT
        You are an elite SEO strategist designing a topic cluster (pillar + spokes).

        <site_context>
        Site: "{$site->name}" — {$site->url('/')}
        Site description: {$description}
        Existing articles (do NOT plan duplicates of these):
        {$existing}
        </site_context>

        <assignment>
        Head topic: {$topic}

        Design one PILLAR page targeting the broad head keyword, plus {$spokes} SPOKE
        articles, each targeting ONE specific long-tail query a real person searches.
        Rules:
        - Every spoke must be winnable by a new site: specific question or comparison,
          not another broad overview. Favor queries with clear search intent
          (cost, "vs", how-to, sizing, mistakes, best-X-for-Y).
        - Slugs: lowercase, hyphenated, 3-6 words, include the keyword.
        - The pillar slug should read like a definitive guide URL.
        - "angle" is a one-line brief a writer could work from.
        </assignment>

        Respond with ONLY this JSON (no text outside it):
        {
            "topic": "Human-readable cluster theme",
            "keyword": "head keyword",
            "pillar": {
                "slug": "head-keyword-guide",
                "title": "SEO title under 60 chars for the definitive guide",
                "keyword": "head keyword",
                "angle": "What the pillar must cover to be the best page on the internet for the head keyword"
            },
            "spokes": [
                {
                    "slug": "long-tail-slug",
                    "title": "SEO title under 60 chars",
                    "keyword": "the long-tail query",
                    "intent": "informational|commercial|transactional",
                    "angle": "One-line brief"
                }
            ]
        }
        PROMPT;

        $plan = $anthropic->completeJson($prompt, 4096);

        if (empty($plan['pillar']['slug']) || empty($plan['spokes'])) {
            throw new \RuntimeException('AI response did not contain a usable cluster plan.');
        }

        // Everything starts as planned; site:cluster --write flips to drafted.
        $plan['pillar']['status'] = 'planned';
        $plan['spokes'] = array_map(
            fn(array $spoke) => $spoke + ['status' => 'planned'],
            array_values($plan['spokes'])
        );

        return $plan;
    }

    /**
     * Draft up to $budget planned articles via site:write. Spokes first —
     * the pillar goes last (and only once every spoke is drafted) so its
     * body can link to spoke pages that actually exist.
     */
    protected function write(Site $site, ClusterRepository $clusters, string $name, array $plan, int $budget): int
    {
        $drafted = 0;

        foreach ($plan['spokes'] as $i => $spoke) {
            if ($budget <= 0) {
                break;
            }

            if (($spoke['status'] ?? 'planned') !== 'planned') {
                continue;
            }

            if ($this->draft($site, $name, $spoke) !== self::SUCCESS) {
                return self::FAILURE;
            }

            $plan['spokes'][$i]['status'] = 'drafted';
            $clusters->save($site, $name, $plan);
            $budget--;
            $drafted++;
        }

        $spokesPending = collect($plan['spokes'])->where('status', 'planned')->count();

        if ($budget > 0 && $spokesPending === 0 && ($plan['pillar']['status'] ?? 'planned') === 'planned') {
            // The pillar is the long, comprehensive hub page.
            if ($this->draft($site, $name, $plan['pillar'], words: 2500) !== self::SUCCESS) {
                return self::FAILURE;
            }

            $plan['pillar']['status'] = 'drafted';
            $clusters->save($site, $name, $plan);
            $drafted++;
        }

        $this->line('');
        $this->components->info("Drafted {$drafted} article(s) from cluster [{$name}].");

        if ($spokesPending > 0 || ($plan['pillar']['status'] ?? '') !== 'drafted') {
            $this->components->bulletList([
                "Continue: php artisan site:cluster {$site->key} {$name} --write=3",
            ]);
        }

        return self::SUCCESS;
    }

    protected function draft(Site $site, string $name, array $entry, int $words = 1200): int
    {
        $this->line('');
        $this->components->twoColumnDetail("Drafting /blog/{$entry['slug']}", $entry['keyword'] ?? '');

        return $this->call('site:write', array_filter([
            'site' => $site->key,
            'topic' => ($entry['title'] ?? $entry['slug']) . (empty($entry['angle']) ? '' : " — {$entry['angle']}"),
            '--slug' => $entry['slug'],
            '--keywords' => $entry['keyword'] ?? null,
            '--words' => $words,
            '--cluster' => $name,
            '--publish' => $this->option('publish') ?: null,
        ]));
    }

    protected function renderPlan(string $name, array $plan): void
    {
        $this->line('');
        $this->components->twoColumnDetail(
            "<options=bold>Cluster [{$name}]</> {$plan['topic']}",
            "head keyword: {$plan['keyword']}"
        );

        $rows = [[
            'PILLAR',
            '/blog/' . ($plan['pillar']['slug'] ?? '?'),
            $plan['pillar']['keyword'] ?? '',
            $plan['pillar']['status'] ?? 'planned',
        ]];

        foreach ($plan['spokes'] ?? [] as $spoke) {
            $rows[] = [
                'spoke',
                '/blog/' . $spoke['slug'],
                $spoke['keyword'] ?? '',
                $spoke['status'] ?? 'planned',
            ];
        }

        $this->table(['Role', 'URL', 'Keyword', 'Status'], $rows);
    }
}
