<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('super_admin', function ($user) {
            return $user->hasRole('super_admin');
        });

        Gate::define('admin_event', function ($user) {
            return $user->hasAnyRole(['super_admin', 'admin_event']);
        });

        Gate::define('admin_keuangan', function ($user) {
            return $user->hasAnyRole(['super_admin', 'admin_keuangan']);
        });

        Gate::define('admin_sertifikat', function ($user) {
            return $user->hasAnyRole(['super_admin', 'admin_sertifikat']);
        });

        Gate::define('manage_settings', function ($user) {
            return $user->hasRole('super_admin');
        });
    }
}
