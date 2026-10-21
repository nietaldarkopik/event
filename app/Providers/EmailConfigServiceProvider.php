<?php

namespace App\Providers;

use App\Models\EmailSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class EmailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only configure email from database if the table exists and there's an active setting
        try {
            if (\Schema::hasTable('email_settings')) {
                $activeSetting = EmailSetting::where('is_active', true)->first();
                
                if ($activeSetting) {
                    $this->configureMailFromDatabase($activeSetting);
                }
            }
        } catch (\Exception $e) {
            // If there's any error (like table doesn't exist yet), fall back to .env config
            // This prevents errors during migration or when table is not ready
        }
    }

    /**
     * Configure mail settings from database
     */
    protected function configureMailFromDatabase(EmailSetting $setting): void
    {
        Config::set('mail.default', $setting->mailer);
        
        if ($setting->mailer === 'smtp') {
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $setting->host,
                'port' => $setting->port,
                'encryption' => $setting->encryption,
                'username' => $setting->username,
                'password' => $setting->password,
                'timeout' => null,
                'auth_mode' => null,
            ]);
        }
        
        // Set from address and name
        Config::set('mail.from', [
            'address' => $setting->from_address,
            'name' => $setting->from_name,
        ]);
    }
}