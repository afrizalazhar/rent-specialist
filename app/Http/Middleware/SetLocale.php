<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Public site is Indonesian-only in v1. The middleware is a single
        // point to switch to a session-stored / browser-preferred locale
        // when we add a second language.
        app()->setLocale(config('app.locale', 'id'));

        return $next($request);
    }
}
