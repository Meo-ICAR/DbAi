<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class WelcomeController extends Controller
{
    /**
     * Show the welcome page based on the user's language preference
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $locale = App::getLocale();
        
        // If the current locale is Italian, show the Italian welcome page
        // Otherwise, fall back to the default welcome page
        return view($locale === 'it' ? 'welcome_it' : 'welcome');
    }
    
    /**
     * Set the application locale
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocale($locale)
    {
        // Only allow supported locales
        if (in_array($locale, ['en', 'it'])) {
            session(['locale' => $locale]);
            App::setLocale($locale);
        }
        
        return redirect()->back();
    }
}
