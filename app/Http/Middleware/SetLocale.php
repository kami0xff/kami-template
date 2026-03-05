<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');
        $supportedLocales = array_keys(config('locales.supported', ['en' => []]));

        if ($locale && in_array($locale, $supportedLocales)) {
            App::setLocale($locale);

            $rtlLocales = config('locales.rtl', []);
            if (in_array($locale, $rtlLocales)) {
                $request->attributes->set('text_direction', 'rtl');
            }

            $request->route()->forgetParameter('locale');
        }

        $response = $next($request);

        if ($locale && in_array($locale, $supportedLocales)) {
            $response->cookie('locale_detected', $locale, 60 * 24 * 365);
        }

        return $response;
    }
}
