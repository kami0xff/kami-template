<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\Sites\ContentRepository;
use App\Services\Sites\MarkdownDocument;
use App\Services\Sites\Site;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(protected ContentRepository $content)
    {
    }

    public function home(Site $site): View
    {
        return $this->render($site, 'home');
    }

    public function show(Site $site, string $path): View
    {
        return $this->render($site, $path);
    }

    /**
     * A page is either a Blade view (site::pages.{path}, for bespoke layouts)
     * or a markdown file (content/pages/{path}.md, for plain content).
     */
    protected function render(Site $site, string $path): View
    {
        $viewName = 'site::pages.' . str_replace('/', '.', $path);

        if (view()->exists($viewName)) {
            return view($viewName, [
                'posts' => $this->content->posts($site, withDrafts: app()->environment('local')),
            ]);
        }

        $doc = $this->content->page($site, $path, withDrafts: app()->environment('local'));

        abort_if($doc === null, 404);

        $this->applySeo($site, $doc, $path === 'home' ? '/' : '/' . $path);

        return view('site::page', ['doc' => $doc]);
    }

    protected function applySeo(Site $site, MarkdownDocument $doc, string $urlPath): void
    {
        seo()->title($doc->title())
            ->description($doc->description())
            ->canonical($site->url($urlPath));

        if ($doc->image()) {
            seo()->image($doc->image());
        }
    }
}
