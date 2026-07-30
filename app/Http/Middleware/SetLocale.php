<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the request language.
 *
 * Order of preference: what the visitor last chose (session), what their
 * browser last carried (cookie), then the app default. The cookie is what
 * survives a logout or a new session, so a returning Arabic reader does not
 * land on an English page.
 */
class SetLocale
{
    public const COOKIE = 'sortifya_locale';

    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('sortifya.locales'));

        $locale = collect([
            $request->session()->get('locale'),
            $request->cookie(self::COOKIE),
            config('app.locale'),
        ])->first(fn ($candidate) => in_array($candidate, $supported, true));

        app()->setLocale($locale);

        // Dates in the ledger and "posted 3 hours ago" follow the same choice.
        Carbon::setLocale($locale);

        return $next($request);
    }
}
