<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\Sites\Site;
use App\Services\Sites\SitemapGenerator;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(Site $site, SitemapGenerator $sitemap): Response
    {
        return response()
            ->view('site::sitemap', ['urls' => $sitemap->urls($site)])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Per-site robots.txt with the sitemap location. Without this route,
     * every static site would fall through to the main app's public/robots.txt
     * (wrong Disallows, no Sitemap line).
     */
    public function robots(Site $site): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /search',
            '',
            'Sitemap: ' . $site->url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines) . "\n")
            ->header('Content-Type', 'text/plain');
    }
}
