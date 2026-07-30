<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    /**
     * Switches language and returns the visitor to the page they were on.
     *
     * Stored in the session for this visit and in a year-long cookie so the
     * choice survives logging out and coming back.
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('sortifya.locales')), 404);

        $request->session()->put('locale', $locale);

        return back()->withCookie(
            Cookie::make(SetLocale::COOKIE, $locale, 60 * 24 * 365)
        );
    }
}
