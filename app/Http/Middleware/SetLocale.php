<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('locale') && in_array($request->get('locale'), ['en', 'id'])) {
            session(['locale' => $request->get('locale')]);
            app()->setLocale($request->get('locale'));
        }
        elseif (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }
        else {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
