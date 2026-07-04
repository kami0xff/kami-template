<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\Sites\ImageProcessor;
use App\Services\Sites\Site;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves site content images dynamically — the fallback behind the static
 * build. In production Caddy serves the pre-generated files under
 * public/static/{domain}/images/ and this controller only fills cache
 * misses (writing the variant so the next hit is static). In local dev it
 * makes images and variants work with no build step at all.
 *
 * Variant widths are whitelisted (ImageProcessor::WIDTHS), so this cannot
 * be abused to generate arbitrary sizes.
 */
class ImageController extends Controller
{
    public function show(Site $site, string $path, ImageProcessor $images): BinaryFileResponse
    {
        $headers = ['Cache-Control' => 'public, max-age=604800'];

        // Exact source file (original raster, SVG, GIF).
        if ($source = $images->source($site, $path)) {
            return response()->file($source, $headers);
        }

        // Generated variant: {name}-{width}.webp from {name}.{raster ext}.
        if (preg_match('/^(.+)-(\d{3,4})\.webp$/', $path, $m)) {
            foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
                $variant = $images->generate($site, "{$m[1]}.{$ext}", (int) $m[2]);

                if ($variant !== null) {
                    return response()->file($variant, $headers);
                }
            }
        }

        abort(404);
    }
}
