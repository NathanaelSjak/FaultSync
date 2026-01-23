<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is in query string (e.g., ?locale=id)
        if ($request->has('locale') && in_array($request->get('locale'), ['en', 'id'])) {
            session(['locale' => $request->get('locale')]);
            app()->setLocale($request->get('locale'));
        }
        // Check if locale is stored in session
        elseif (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }
        // Use APP_LOCALE from .env
        else {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
