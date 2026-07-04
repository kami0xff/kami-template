---
title: Hola Mundo
description: El primer artículo del sitio de ejemplo, traducido al español.
date: 2026-06-01
updated: 2026-06-15
section: Anuncios
tags: [noticias, ejemplo]
tldr:
  - Este archivo es content/es/blog/hello-world.md — el mismo slug que la versión inglesa, así que ambos quedan enlazados como traducciones.
  - El front matter alimenta todas las etiquetas SEO igual que en el idioma por defecto.
faq:
  - question: ¿Dónde viven los artículos traducidos?
    answer: En content/{locale}/blog dentro de la carpeta del sitio. El nombre del archivo es el slug, y un slug igual en dos idiomas marca los documentos como traducciones.
---

# Hola Mundo

Esta es la traducción al español de `hello-world.md`. Se sirve en
`/es/blog/hello-world`, mientras que la versión inglesa vive en
`/blog/hello-world`.

## Qué genera el sistema automáticamente

- Etiquetas `hreflang` entre ambas versiones (más `x-default`)
- Una entrada por idioma en el sitemap
- Un feed RSS por idioma (`/es/feed.xml`)
- La interfaz del sitio traducida vía `lang/es.json`

## Conclusión

Escribe markdown en la carpeta del idioma, haz commit y despliega — el
resto es automático.
