<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        if ($provider === 'microsoft') {
            return Socialite::driver('microsoft')
                ->scopes(['openid', 'profile', 'email', 'offline_access'])
                ->redirect();
        } elseif ($provider === 'google') {
            return Socialite::driver('google')
                ->scopes([
                    'openid',
                    'profile',
                    'email',
                ])
                ->with(['access_type' => 'online', 'prompt' => 'select_account'])
                ->redirect();
        }
        
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            if ($provider === 'microsoft') {
                $socialUser = Socialite::driver('microsoft')->user();
            } else {
                $socialUser = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            \Log::error('Social Auth Error (' . $provider . '): ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'error' => 'Failed to authenticate with ' . ucfirst($provider) . '. Please try again or use another login method.'
            ]);
        }

        try {
            $user = $this->findOrCreateUser($socialUser, $provider);
            
            if (!$user) {
                throw new \Exception('Failed to create or find user');
            }
            
            Auth::login($user, true);
            
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            \Log::error('User Authentication Error (' . $provider . '): ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'error' => 'Authentication successful, but we encountered an issue. Please try again.'
            ]);
        }
    }

    private function findOrCreateUser($socialUser, $provider)
    {
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            return $user;
        }

        return User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'email_verified_at' => now(),
            'password' => bcrypt(uniqid()),
        ]);
    }
}
