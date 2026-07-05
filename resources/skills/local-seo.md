# Local SEO Skill (programmatic city × service pages)

Skill for local landing pages: one service in one city/village, for a business
with a physical service area. Use it with
`site:write {site} "Rénovation à Venelles" --skill=local-seo`.

The first half of this file is strategy for the operator (which pages to
create and when); the second half is the editorial contract for writing one
page. Both are injected into the writing prompt so the AI understands where
each page sits in the structure.

## Terminology (fixed — never bend this)

- **The cluster is always the service/trade** ("rénovation", "peinture") —
  that's what you plan with `site:cluster`. A city is never a cluster.
- **The pillar** is the service page — the hub every spoke links up to.
- **Spokes** are the city landing pages and the articles. A big city with lots
  of work is just a heavyweight spoke with satellite articles around it — it
  never becomes its own cluster or pillar.
- **One landing page per town, maximum.** A village never gets "a set of
  articles about it"; depth for an important city comes from city-qualified
  trade articles (below), not from more pages about the place.

## Strategy: which pages earn the right to exist

- **Gate page creation on unique assets, not keyword ideas.** A city page is
  created only when at least 2–3 city-specific slots can be filled: a photo of
  a real job there, a testimonial naming the town, a concrete logistics fact
  ("intervention sous 48h, 15 min de l'atelier"). Cities that can't fill the
  slots get a mention on the regional page instead — not their own URL.
- **Tier the structure; never build the full service × city matrix.**
  1. One strong **pillar page per service** (the hub of the cluster).
  2. **Service × city pages** only for the 5–10 towns with proven demand.
  3. One **"zone d'intervention" page** listing every other town served.
  Promote a town from tier 3 to tier 2 only when Search Console shows
  impressions for it. Publish slowly: a handful of pages per month,
  each genuinely finished, beats hundreds of templated clones.
- **Prune as deliberately as you publish.** After 4–6 months, a page with no
  impressions is merged back into the regional page (with a 301), not left to
  rot. Site-wide quality is evaluated as a whole; dead pages tax the rest.
- **The local pack is won elsewhere.** For "métier + petite ville" queries the
  Google Business Profile often outranks any page. City pages exist to support
  it (landing pages, consistent name/address/phone); reviews that mention the
  town by name are the strongest signal of all — ask clients for them.

## Articles: the buyer test

Every article idea passes one test before it's written: **would the person
searching this ever hire the artisan?** Someone googling how a village is
developing, its history or its urbanism is a resident or a journalist, not a
buyer — never write city journalism. Topical authority is built on the trade,
not on geography; off-trade content makes the site look *less* focused.

Two article types, both pure trade content:

- **Generic trade articles** ("Par où commencer une rénovation ?") — shared
  infrastructure; every city landing page can link to them.
- **City-qualified trade articles** ("Rénover dans le centre ancien d'Aix :
  ce que les ABF autorisent", "Travaux en copropriété à Marseille :
  autorisations, syndic, délais", "Prix d'une rénovation de mas autour
  d'Aix en 2026") — written only for cities with real volume, each linking to
  that city's landing page. These are the most local and least fakeable pages
  on the site: pick topics where the city genuinely changes the answer.

## Expanding into a new city (the sequence)

1. **Mention first**: add the town to the zone d'intervention page and drop a
   natural sideways mention on an existing page — the future URL gets internal
   context before it exists.
2. **Landing page**, gated on substance. With no jobs there yet, local
   expertise carries it: housing stock, copropriété/permit reality, which
   districts are served, travel time. Research-grade specifics pass the gate;
   a name-swap does not.
3. **City-qualified articles one at a time**, on the normal publishing rhythm,
   each linking to the landing page and the pillar. A burst of ten in a week
   looks manufactured — and outruns the ability to make each one good.
4. **Read Search Console and deepen where it responds.** Long-tail article
   queries move before the head term — that's normal, especially in a big
   competitive city. If a big city works, the next split is by district/
   arrondissement ("rénovation appartement Marseille 8ème") — same gated
   landing-page logic, one level down.
5. **Upgrade with proof**: the first real job there puts photos and a
   testimonial on the landing page — that's when rankings usually firm up.

## Writing one city page

The page must read as if written by the artisan for that town — never as a
template with the city name swapped in. If a paragraph would survive replacing
the city with a neighboring one, it is not local enough: rewrite or cut it.

### Local angles (pick what genuinely applies)

- **Housing stock**: what people actually live in there (mas, bastide,
  copropriété années 70, lotissement récent) and what that implies for the
  work — materials, surfaces, common problems.
- **Regulatory reality**: historic-center constraints (ABF / Bâtiments de
  France), façade color palettes, déclaration préalable habits of that
  commune — artisans win trust by knowing this cold.
- **Climate and terrain**: exposure (mistral, humidity, coastal salt), soil,
  slope — whatever changes how the job is done there.
- **Logistics**: drive time from the workshop, response time, travel fees if
  any, access constraints (historic center parking, narrow streets).
- **Proof from that town**: the project photo with a caption naming the place,
  the testimonial, the recognizable landmark near a past job.

### Structure

1. **H1** — service + city, promising a concrete outcome
   ("Rénovation de mas à Venelles : l'artisan à 15 minutes").
2. **Direct answer** — 40–60 words: what the artisan does in this town, for
   whom, and the one number that matters (delay, distance, years active there).
3. **Body H2s** — organized around the local angles above, phrased as the
   questions locals search. Answer first sentence, then elaborate.
4. **Proof section** — the job(s) done in or near the town, with images
   (`/images/{slug}/name.png` + `<!-- TODO photo: what to shoot -->`).
5. **FAQ** — 3–5 questions that are *different in this town* (travel fees,
   access, delay, that commune's permit habits). Never reuse another city's
   FAQ verbatim.
6. **CTA** — one clear action (quote request / call), repeated at most twice.

### Linking (this is what makes it a cluster, not a doorway page)

- Link **up** to the service pillar page.
- Link **sideways** to 1–2 neighboring city pages, in a natural sentence
  ("nous intervenons aussi à Éguilles"), not a link farm footer.
- Link to 2–3 **blog articles** that fit the page's topic (the prompt lists
  what exists). Descriptive anchors, never "cliquez ici".

### Schema and metadata

- Front matter description mentions service + city + one differentiator.
- The page should carry LocalBusiness/Service context: the business's real
  address and the town in `areaServed` terms — surface the facts in the text
  (address, phone, hours) so the schema and the visible page agree.

### Style

- Same rules as the base skill: humans first, concrete over generic, no
  filler, short sentences. First person from the artisan is welcome and
  builds E-E-A-T ("je travaille sur Venelles depuis 2012").
- The city name appears in the H1, the direct answer, one H2 and naturally in
  the body — not in every paragraph.
