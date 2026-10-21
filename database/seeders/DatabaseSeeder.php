<?php

namespace Database\Seeders;

use Modules\Authentication\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'admin@unwim.ac.id'],
            [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'status' => 'ACTIVE',
            ]
        );

        if (! $user->hasRole($superAdminRole->name)) {
            $user->assignRole($superAdminRole);
        }

        // Seed default categories matching FE types
        $categories = [
            ['name' => 'Rapat Internal', 'icon' => 'fa-users', 'color' => '#6c757d'],
            ['name' => 'Seminar', 'icon' => 'fa-graduation-cap', 'color' => '#007bff'],
            ['name' => 'Workshop', 'icon' => 'fa-laptop-code', 'color' => '#28a745'],
            ['name' => 'Wisuda', 'icon' => 'fa-user-graduate', 'color' => '#fd7e14'],
            ['name' => 'Kuliah Umum', 'icon' => 'fa-chalkboard-teacher', 'color' => '#17a2b8'],
            ['name' => 'Lomba / Kompetisi', 'icon' => 'fa-trophy', 'color' => '#ffc107'],
            ['name' => 'Pelatihan', 'icon' => 'fa-chalkboard', 'color' => '#e83e8c'],
            ['name' => 'Pengabdian Masyarakat', 'icon' => 'fa-handshake', 'color' => '#6f42c1'],
        ];

        foreach ($categories as $cat) {
            \Modules\Category\Models\EventCategory::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'icon' => $cat['icon'],
                    'color' => $cat['color']
                ]
            );
        }

        $this->call(SampleDataSeeder::class);

        Artisan::call('permissions:sync-routes');
    }
}
