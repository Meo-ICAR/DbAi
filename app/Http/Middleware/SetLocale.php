<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user has a locale set in the session
        if (session()->has('locale')) {
            App::setLocale(session('locale'));
        } else {
            // Get the browser's preferred language
            $locale = $this->getBrowserLocale($request);
            App::setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }

    /**
     * Get the browser's preferred locale
     */
    protected function getBrowserLocale($request): string
    {
        $acceptedLanguages = $request->getLanguages();
        
        // Check if any of the accepted languages is Italian
        foreach ($acceptedLanguages as $language) {
            if (str_starts_with($language, 'it')) {
                return 'it';
            }
        }
        
        // Default to English
        return 'en';
    }
}
