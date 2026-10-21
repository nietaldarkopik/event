<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!class_exists('App\Models\User')) {
            class_alias(\Modules\Authentication\Models\User::class, 'App\Models\User');
        }
        if (!class_exists('App\Models\Event')) {
            class_alias(\Modules\Event\Models\Event::class, 'App\Models\Event');
        }
        if (!class_exists('App\Models\EventCategory')) {
            class_alias(\Modules\Category\Models\EventCategory::class, 'App\Models\EventCategory');
        }
        if (!class_exists('App\Models\Participant')) {
            class_alias(\Modules\Participant\Models\Participant::class, 'App\Models\Participant');
        }
        if (!class_exists('App\Models\Certificate')) {
            class_alias(\Modules\Certificate\Models\Certificate::class, 'App\Models\Certificate');
        }
        if (!class_exists('App\Models\AttendanceLog')) {
            class_alias(\Modules\Attendance\Models\AttendanceLog::class, 'App\Models\AttendanceLog');
        }
        if (!class_exists('App\Models\Payment')) {
            class_alias(\Modules\Payment\Models\Payment::class, 'App\Models\Payment');
        }
        if (!class_exists('App\Models\Registration')) {
            class_alias(\Modules\Registration\Models\Registration::class, 'App\Models\Registration');
        }
        if (!class_exists('App\Models\TicketType')) {
            class_alias(\Modules\Ticket\Models\TicketType::class, 'App\Models\TicketType');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Nama hari/bulan pada pesan WA, email, dan halaman publik berbahasa Indonesia.
        \Carbon\Carbon::setLocale('id');
        \Illuminate\Pagination\Paginator::useBootstrap();   // tautan halaman bergaya Bootstrap (sesuai AdminLTE)

        try {
            Schema::defaultStringLength(191);
            if (\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::all()->pluck('value', 'key');
                \View::share('settings', $settings);
            }
        } catch (\Exception $e) {
            // Fallback if DB connection fails
        }
    }
}
