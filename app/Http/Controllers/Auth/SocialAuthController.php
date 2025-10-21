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
        }
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            if ($provider === 'microsoft') {
                $socialUser = Socialite::driver('microsoft')->user();
            } else {
                $socialUser = Socialite::driver($provider)->user();
            }
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Failed to authenticate with ' . ucfirst($provider) . ': ' . $e->getMessage()]);
        }

        $user = $this->findOrCreateUser($socialUser, $provider);
        
        Auth::login($user, true);
        
        return redirect()->route('dashboard');
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
