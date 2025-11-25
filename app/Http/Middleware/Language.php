<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Attempt to get the language from AJAX request or header
        $lang = request()->header('Accept-Language', 'th');
        // Validate and set the language
        if (in_array($lang, ['en', 'th'])) {
            App::setLocale($lang);
            Config::set('app.locale', $lang);
            Config::set('app.fallback_locale', $lang);
            Config::set('app.faker_locale', $lang.'_'.strtoupper($lang));
        }

        return $next($request);
    }
}
