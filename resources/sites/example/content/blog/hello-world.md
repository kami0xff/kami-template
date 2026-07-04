---
title: Hello World
description: The first post on the example site, showing the full front matter reference.
date: 2026-06-01
updated: 2026-06-15
section: Announcements
tags: [news, example]
image: https://example.test/img/hello-world.png
tldr:
  - The filename is the URL slug, so this file is served at /blog/hello-world.
  - Front matter drives every SEO tag, schema, and sitemap entry automatically.
  - Rich fields (tldr, faq, quiz, related, sources) render as on-page sections.
faq:
  - question: Where do blog posts live?
    answer: In content/blog inside the site folder. Each markdown file becomes one post, and the filename becomes the URL slug. Drafts are hidden everywhere except local dev.
  - question: What does the front matter control?
    answer: Everything SEO-related — meta title and description, publish and update dates, article schema, FAQ schema, the TL;DR box, the quiz, related articles, and the references list.
quiz:
  question: What determines a blog post's URL?
  options:
    - A route defined in routes/web.php
    - The markdown filename
    - A database column
  answer: 1
  explanation: The filename is the slug — hello-world.md is served at /blog/hello-world.
related:
  - writing-content
sources:
  - title: "Google Search Central: Structured data"
    url: https://developers.google.com/search/docs/appearance/structured-data
---

# Hello World

This post is `content/blog/hello-world.md` — the filename is the URL slug, so
it's served at `/blog/hello-world`.

[TOC]

## How the front matter feeds SEO

Every field above feeds the SEO layer automatically:

- `title` and `description` become the meta title / description
- `date` and `updated` become `article:published_time` / `article:modified_time`
  and `datePublished` / `dateModified` in the BlogPosting JSON-LD schema
- `section` and `tags` fill the article meta tags and schema keywords
- `image` becomes `og:image`, `twitter:image`, and the schema image

## What the rich fields render

| Field | On-page section | Schema |
| ----- | --------------- | ------ |
| `tldr` | TL;DR box under the byline | — |
| `faq` | FAQ accordion | FAQPage |
| `quiz` | Interactive quick-check | — |
| `related` | Related articles | — |
| `sources` | References list | — |

The post also gets a BreadcrumbList schema, a canonical URL, a Person author
schema from `site.php`, and an entry in `/sitemap.xml` with its `lastmod` —
nothing to wire up per post.

## Conclusion

Write markdown, commit, deploy — the template handles the rest. Next, read
[how the writing workflow works](/blog/writing-content).
