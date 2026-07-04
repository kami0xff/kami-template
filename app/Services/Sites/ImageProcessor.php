<?php

namespace App\Services\Sites;

use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;

/**
 * Content image pipeline for static sites.
 *
 * Source images live in resources/sites/{key}/images/ and are referenced
 * from markdown as /images/foo.png. This service:
 *
 *  - generates resized WebP variants (foo-480.webp, foo-800.webp, ...) into
 *    public/static/{domain}/images/, where Caddy serves them statically
 *    (site:build pre-generates everything; ImageController fills misses
 *    on demand so local dev needs no build step);
 *  - rewrites rendered <img> tags: WebP src + srcset/sizes, intrinsic
 *    width/height (prevents layout shift), lazy loading for everything
 *    below the first image, fetchpriority=high for the first (the LCP).
 *
 * SVG and GIF pass through untouched (vector / animation), they are only
 * copied to the static output.
 */
class ImageProcessor
{
    /** Variant widths. The blog column is 720 CSS px, so 1600 covers 2x retina. */
    public const WIDTHS = [480, 800, 1600];

    /** How wide the image actually renders — lets the browser pick a variant. */
    public const SIZES = '(max-width: 768px) 100vw, 720px';

    protected const RASTER = ['png', 'jpg', 'jpeg', 'webp'];

    protected const WEBP_QUALITY = 82;

    public function sourceDir(Site $site): string
    {
        return $site->path . '/images';
    }

    /** Resolve a /images/... relative path to its source file, or null. */
    public function source(Site $site, string $relative): ?string
    {
        $relative = ltrim($relative, '/');

        if ($relative === '' || str_contains($relative, '..')) {
            return null;
        }

        $file = $this->sourceDir($site) . '/' . $relative;

        return is_file($file) ? $file : null;
    }

    /**
     * Generate one WebP variant (idempotent: skipped when the output is
     * newer than the source). Returns the output path.
     */
    public function generate(Site $site, string $relative, int $width): ?string
    {
        if (!in_array($width, self::WIDTHS, true)) {
            return null;
        }

        $source = $this->source($site, $relative);

        if ($source === null || !in_array(strtolower(pathinfo($source, PATHINFO_EXTENSION)), self::RASTER, true)) {
            return null;
        }

        $output = $this->outputPath($site, $this->variantName($relative, $width));

        if (is_file($output) && filemtime($output) >= filemtime($source)) {
            return $output;
        }

        File::ensureDirectoryExists(dirname($output));

        ImageManager::gd()
            ->read($source)
            ->scaleDown(width: $width)
            ->toWebp(self::WEBP_QUALITY)
            ->save($output);

        return $output;
    }

    /**
     * Pre-generate every variant of every source image and copy the
     * originals (fallbacks, SVGs, GIFs) into the static output.
     *
     * @return int Number of files written or refreshed.
     */
    public function buildAll(Site $site): int
    {
        $dir = $this->sourceDir($site);

        if (!File::isDirectory($dir)) {
            return 0;
        }

        $written = 0;

        foreach (File::allFiles($dir) as $file) {
            $relative = $file->getRelativePathname();

            // Original is always available at its own URL (og:image, SVG, GIF).
            $copy = $this->outputPath($site, $relative);

            if (!is_file($copy) || filemtime($copy) < $file->getMTime()) {
                File::ensureDirectoryExists(dirname($copy));
                File::copy($file->getPathname(), $copy);
                $written++;
            }

            foreach ($this->targetWidths($file->getPathname()) as $width) {
                $before = is_file($this->outputPath($site, $this->variantName($relative, $width)));

                if ($this->generate($site, $relative, $width) && !$before) {
                    $written++;
                }
            }
        }

        return $written;
    }

    /**
     * Rewrite /images/ <img> tags in rendered HTML: WebP srcset, intrinsic
     * dimensions, lazy loading (except the first image — the likely LCP —
     * which gets fetchpriority=high instead).
     */
    public function transformHtml(Site $site, string $html): string
    {
        $first = true;

        return preg_replace_callback(
            '/<img\s[^>]*src="\/images\/([^"]+)"[^>]*>/i',
            function (array $match) use ($site, &$first) {
                $tag = $this->buildTag($site, $match[1], $match[0], $first);
                $first = false;

                return $tag;
            },
            $html
        ) ?? $html;
    }

    protected function buildTag(Site $site, string $relative, string $original, bool $isFirst): string
    {
        $source = $this->source($site, $relative);

        if ($source === null) {
            return $original; // Missing file: leave the tag for the author to notice.
        }

        $alt = preg_match('/alt="([^"]*)"/', $original, $m) ? $m[1] : '';
        $title = preg_match('/title="([^"]*)"/', $original, $m) ? $m[1] : null;

        $loading = $isFirst
            ? 'fetchpriority="high" decoding="async"'
            : 'loading="lazy" decoding="async"';

        $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        $size = in_array($ext, self::RASTER, true) ? @getimagesize($source) : false;

        // SVG/GIF/unreadable: keep the original URL, still add lazy loading.
        if (!in_array($ext, self::RASTER, true) || $size === false) {
            return $this->tag("/images/{$relative}", $alt, $title, null, null, null, $loading);
        }

        [$width, $height] = $size;
        $targets = $this->targetWidths($source);

        // Smaller than the smallest variant: original file, intrinsic dims.
        if ($targets === []) {
            return $this->tag("/images/{$relative}", $alt, $title, $width, $height, null, $loading);
        }

        $fallback = in_array(800, $targets, true) ? 800 : max($targets);

        $srcset = collect($targets)
            ->map(fn(int $w) => '/images/' . $this->variantName($relative, $w) . " {$w}w")
            ->implode(', ');

        return $this->tag(
            '/images/' . $this->variantName($relative, $fallback),
            $alt,
            $title,
            $fallback,
            (int) round($fallback * $height / $width),
            $srcset,
            $loading,
        );
    }

    protected function tag(
        string $src,
        string $alt,
        ?string $title,
        ?int $width,
        ?int $height,
        ?string $srcset,
        string $loading,
    ): string {
        $attrs = ['src="' . $src . '"', 'alt="' . $alt . '"'];

        if ($srcset !== null) {
            $attrs[] = 'srcset="' . $srcset . '"';
            $attrs[] = 'sizes="' . self::SIZES . '"';
        }

        if ($width !== null && $height !== null) {
            $attrs[] = 'width="' . $width . '"';
            $attrs[] = 'height="' . $height . '"';
        }

        if ($title !== null) {
            $attrs[] = 'title="' . $title . '"';
        }

        return '<img ' . implode(' ', $attrs) . ' ' . $loading . '>';
    }

    /** Variant widths that make sense for a source (never upscale). */
    protected function targetWidths(string $source): array
    {
        if (!in_array(strtolower(pathinfo($source, PATHINFO_EXTENSION)), self::RASTER, true)) {
            return [];
        }

        $size = @getimagesize($source);

        if ($size === false) {
            return [];
        }

        return array_values(array_filter(self::WIDTHS, fn(int $w) => $w <= $size[0]));
    }

    public function variantName(string $relative, int $width): string
    {
        $dir = pathinfo($relative, PATHINFO_DIRNAME);
        $name = pathinfo($relative, PATHINFO_FILENAME);

        return ($dir === '.' ? '' : $dir . '/') . "{$name}-{$width}.webp";
    }

    protected function outputPath(Site $site, string $relative): string
    {
        return public_path('static/' . $site->canonicalDomain() . '/images/' . $relative);
    }
}
