---
title: Hello World
description: The first post on the example site, showing the full front matter reference.
date: 2026-06-01
updated: 2026-06-15
author: Jane Doe
section: Announcements
tags: [news, example]
image: https://example.test/img/hello-world.png
---

# Hello World

This post is `content/blog/hello-world.md` — the filename is the URL slug, so
it's served at `/blog/hello-world`.

Every front matter field above feeds the SEO layer automatically:

- `title` and `description` become the meta title / description
- `date` and `updated` become `article:published_time` / `article:modified_time`
  and `datePublished` / `dateModified` in the BlogPosting JSON-LD schema
- `author`, `section`, and `tags` fill the article meta tags and schema keywords
- `image` becomes `og:image`, `twitter:image`, and the schema image

The post also gets a BreadcrumbList schema, a canonical URL, and an entry in
`/sitemap.xml` with its `lastmod` — nothing to wire up per post.
