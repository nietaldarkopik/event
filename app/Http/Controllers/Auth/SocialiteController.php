<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Modules\Authentication\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Get Socialite driver instance, mapping custom providers like 'unwim' to 'google'.
     */
    protected function getSocialiteDriver($provider)
    {
        $driverName = ($provider === 'unwim') ? 'google' : $provider;
        $driver = Socialite::driver($driverName);

        $unwimRedirect = config('services.unwim.redirect');
        if ($provider === 'unwim' && $unwimRedirect) {
            $driver->redirectUrl($unwimRedirect);
        }

        return $driver;
    }

    public function redirect($provider)
    {
        try {
            return $this->getSocialiteDriver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal mengarahkan ke SSO ' . ucfirst($provider) . ': ' . $e->getMessage()
            ]);
        }
    }

    public function callback($provider)
    {
        try {
            $socialUser = $this->getSocialiteDriver($provider)->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Update existing user with SSO ID
                if ($provider === 'google') {
                    $user->update(['google_id' => $socialUser->getId()]);
                } elseif ($provider === 'unwim') {
                    $user->update(['unwim_id' => $socialUser->getId()]);
                }

                Auth::login($user);
                return $this->redirectBasedOnRole($user);
            } else {
                // Auto-register new user with default role 'peserta_event'
                $defaultRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'peserta_event', 'guard_name' => 'web']);
                $newUser = User::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Google User',
                    'email' => $socialUser->getEmail(),
                    'role_id' => $defaultRole ? $defaultRole->id : null,
                    'google_id' => $provider === 'google' ? $socialUser->getId() : null,
                    'unwim_id' => $provider === 'unwim' ? $socialUser->getId() : null,
                    'status' => 'ACTIVE',
                    'avatar' => $socialUser->getAvatar(),
                ]);

                if ($defaultRole) {
                    $newUser->assignRole($defaultRole);
                }

                Auth::login($newUser);
                return $this->redirectBasedOnRole($newUser);
            }

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan ' . ucfirst($provider) . '. ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Redirect user based on their assigned role.
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->hasRole('peserta_event') || !$user->hasAnyRole(['super_admin', 'admin_event', 'admin_sertifikat', 'admin_keuangan'])) {
            return redirect()->route('home')->with('success', 'Berhasil login sebagai Peserta.');
        }

        return redirect()->intended(route('dashboard'));
    }
}

