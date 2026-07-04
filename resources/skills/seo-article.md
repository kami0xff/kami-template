# SEO Article Skill

Editorial skills live in `resources/skills/*.md` and are injected into the
`site:write` prompt. This is the default (`--skill=seo-article`); add more
skills (e.g. `product-review.md`, `comparison.md`) and pick one with
`site:write ... --skill=product-review`. A site can override any skill with
`resources/sites/{key}/skills/{skill}.md`.

## Search intent first

- Identify the intent behind the topic (informational, comparison, transactional)
  and match the format to it: how-to → steps; "best X" → listicle with criteria;
  "X vs Y" → comparison table first.
- The reader must get their answer without scrolling: after the H1 and a one-line
  hook, give a direct 40–60 word answer to the main question. This paragraph is
  written to win the featured snippet.

## Structure

1. **H1** — contains the primary keyword, promises a specific outcome.
2. **Direct answer** — 40–60 words, immediately usable, no throat-clearing.
3. **TL;DR** — 3–5 bullet takeaways (goes in front matter, rendered as a box).
4. **[TOC]** — include this placeholder after the intro; it renders a linked
   table of contents (jump links can surface in Google sitelinks).
5. **Body sections** — H2 per subtopic; phrase H2s as questions users actually
   search when natural. Under each question-H2, answer in the first sentence,
   then elaborate. Keep paragraphs under 4 lines. Use H3 sparingly.
6. **Data** — present comparisons and numbers as markdown tables; crawlers parse
   tables directly and they win snippet/AI-overview citations. Every statistic
   names its source inline as a link: "according to [Source](url), ...".
7. **Listicles** — when the format calls for it, number the H2s (e.g.
   "1. Standing desks improve posture") so the list structure is explicit.
8. **Images** — add placeholder images where a screenshot or diagram genuinely
   helps: `![descriptive alt text with keyword](/images/{slug}/short-name.png)`
   plus an HTML comment `<!-- TODO screenshot: what to capture -->`. Never
   fabricate image URLs to external sites. (The site serves images from its
   `images/` folder; the pipeline converts them to responsive WebP
   automatically, so plain `.png`/`.jpg` references are correct.)
9. **Conclusion** — H2 "Conclusion" (or a sharper variant): summarize the
   position, then one clear CTA (try the tool, read the linked guide, follow
   the author).
10. **FAQ** — 3–5 real long-tail questions (front matter; rendered + FAQPage
    schema). Answers are 40–80 words, self-contained, no "as mentioned above".

## Linking policy

- **Authority links out**: 2–4 links to genuinely authoritative sources
  (standards bodies, official docs, primary research, recognized publications).
  Link them where the claim is made. These build trust with readers and
  crawlers — never link to content farms or competitors for the same keyword.
- **Internal links**: link naturally to the site's other articles where they
  genuinely help (the prompt lists what exists, with their tags). 2–5 internal
  links in the body, descriptive anchor text — never "click here".
- All source links also go in the `sources` front matter list (rendered as a
  References section).

## E-E-A-T

- Write from hands-on experience: "when I tested...", "in my setup..." — the
  author is a named practitioner, not a content mill. First person is welcome.
- Be opinionated where the evidence supports it; hedge only where honest.
- Include at least one insight that would not appear in the top 10 results —
  a measurement, a gotcha, an unexpected trade-off.

## Style

- Write for humans first. No keyword stuffing; use the primary keyword in the
  H1, the direct answer, one H2, and naturally throughout. Cover the semantic
  field (related entities, synonyms) instead of repeating the exact phrase.
- Concrete over generic: numbers, names, versions, prices — not "many options
  exist".
- No filler phrases ("in today's fast-paced world", "it's important to note").
- Reading level: clear enough for a smart non-specialist. Short sentences win.

## Engagement

- One quiz question (front matter) that tests the article's core decision —
  it keeps readers on the page and helps them self-diagnose their answer.
- The TL;DR box, TOC, and tables give scanners multiple entry points; assume
  60% of readers never read a full paragraph.
