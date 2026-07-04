---
title: Writing Content in Markdown
description: The editing workflow — drop a markdown file in the folder, rebuild, done.
date: 2026-06-20
author: Jane Doe
tags: [guide]
cluster: markdown-publishing
---

# Writing content in markdown

The whole editing workflow is file-based:

1. Create `content/blog/my-post.md` with front matter and markdown
2. Preview it locally at `/blog/my-post` (drafts render in local too)
3. Commit and push — the deploy runs `site:build` and the static HTML goes live

## Drafts

Add `draft: true` to the front matter and the post is hidden from the blog
index, the sitemap, and the static build — but still visible in your local
environment so you can preview it. Remove the flag to publish.

```yaml
---
title: Work in Progress
draft: true
---
```

## Only the essentials are required

A minimal post is just a title and a date; description, tags, image, and the
rest are optional and fall back to sensible defaults (the description falls
back to an excerpt of the content).
