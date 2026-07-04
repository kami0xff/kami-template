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
}
