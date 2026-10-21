<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'EVENTUNWIM', 'type' => 'text'],
            ['key' => 'site_description', 'value' => 'Portal Event Resmi Universitas Winaya Mukti', 'type' => 'text'],
            ['key' => 'site_logo', 'value' => 'logo_default.png', 'type' => 'image'],
            ['key' => 'contact_email', 'value' => 'info@unwim.ac.id', 'type' => 'text'],
            ['key' => 'contact_phone', 'value' => '(022) 12345678', 'type' => 'text'],
            ['key' => 'contact_address', 'value' => 'Jl. Pahlawan No. 123, Bandung', 'type' => 'text'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
