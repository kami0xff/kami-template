<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\Sites\ContentRepository;
use App\Services\Sites\Site;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function index(Site $site, ContentRepository $content): Response
    {
        return response()
            ->view('site::feed', ['posts' => $content->posts($site)])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
