<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the application locale
     *
     * @param  string  $locale
     */
    public function switch($locale)
    {
        if (in_array($locale, ['en', 'id'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        return redirect()->back();
    }
}
