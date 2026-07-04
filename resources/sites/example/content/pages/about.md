---
title: About
description: What this example site is and how markdown pages work.
updated: 2026-07-04
---

# About this site

This page is a plain markdown file at `content/pages/about.md`. Its URL is
`/about` — the path mirrors the file path, and nested folders work too
(`content/pages/guides/setup.md` becomes `/guides/setup`).

The front matter at the top sets the meta title, description, and last-modified
date for the sitemap. Everything else is regular GitHub-flavored markdown:

- Lists, **bold**, *italic*, `inline code`
- Tables, task lists, strikethrough
- Fenced code blocks

| Feature | File |
| ------- | ---- |
| Blog posts | `content/blog/*.md` |
| Static pages | `content/pages/*.md` |
| Custom pages | `views/pages/*.blade.php` |
