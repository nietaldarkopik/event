<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissionsFromRoutes extends Command
{
    protected $signature = 'permissions:sync-routes';

    protected $description = 'Sinkronisasi permission Spatie berdasarkan route admin (nama route).';

    public function handle()
    {
        $roles = [
            'super_admin' => Role::firstOrCreate([
                'name' => 'super_admin',
                'guard_name' => 'web',
            ]),
            'admin_event' => Role::firstOrCreate([
                'name' => 'admin_event',
                'guard_name' => 'web',
            ]),
            'admin_sertifikat' => Role::firstOrCreate([
                'name' => 'admin_sertifikat',
                'guard_name' => 'web',
            ]),
            'admin_keuangan' => Role::firstOrCreate([
                'name' => 'admin_keuangan',
                'guard_name' => 'web',
            ]),
        ];

        $routes = Route::getRoutes();

        $count = 0;

        foreach ($routes as $route) {
            $name = $route->getName();
            $uri = $route->uri();

            if (! $name) {
                continue;
            }

            if (strpos($uri, 'admin') !== 0 && strpos($uri, 'admin/') !== 0) {
                continue;
            }

            $permission = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);

            $this->assignPermissionToRoles($permission, $roles);

            $count++;
        }

        $this->info("Permissions synced from admin routes: {$count} permissions processed.");

        return 0;
    }

    protected function assignPermissionToRoles(Permission $permission, array $roles): void
    {
        foreach ($roles as $role) {
            $role->unsetRelation('permissions');
        }
        $name = $permission->name;

        if ($name === 'dashboard') {
            foreach ($roles as $role) {
                if (! $role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }

            if (! $roles['super_admin']->hasPermissionTo($permission)) {
                $roles['super_admin']->givePermissionTo($permission);
            }

            return;
        }

        $adminEventPatterns = [
            'admin.event-categories.',
            'admin.events.',
            'admin.event-sessions.',
            'admin.session-speakers.',
            'admin.speakers.',
            'admin.ticket-types.',
            'admin.registrations.',
            'admin.participants.',
            'admin.qr-codes.',
            'admin.attendance-logs.',
            'admin.sponsors.',
            'admin.galleries.',
            'admin.announcements.',
            'admin.whatsapp.',
            'participants.',
            'invitation-templates.',
            'attendance.',
            'invitations.',
        ];

        $adminSertifikatPatterns = [
            'admin.certificates.',
            'certificate-templates.',
            'certificate-generator.',
            'certificates.',
            'events.certificates.',
            'admin.certificate-media.',
            'certificate-media.',
        ];

        $adminKeuanganPatterns = [
            'admin.payments.',
            'finance.',
        ];

        $superAdminOnlyPatterns = [
            'admin.organizations.',
            'admin.event-committees.',
            'admin.users.',
            'whatsapp.',
            'reports.',
            'settings.',
        ];

        if ($this->startsWithAny($name, $adminEventPatterns)) {
            if (! $roles['admin_event']->hasPermissionTo($permission)) {
                $roles['admin_event']->givePermissionTo($permission);
            }
        }

        if ($this->startsWithAny($name, $adminSertifikatPatterns)) {
            if (! $roles['admin_sertifikat']->hasPermissionTo($permission)) {
                $roles['admin_sertifikat']->givePermissionTo($permission);
            }
        }

        if ($this->startsWithAny($name, $adminKeuanganPatterns)) {
            if (! $roles['admin_keuangan']->hasPermissionTo($permission)) {
                $roles['admin_keuangan']->givePermissionTo($permission);
            }
        }

        if ($this->startsWithAny($name, $superAdminOnlyPatterns)) {
            if (! $roles['super_admin']->hasPermissionTo($permission)) {
                $roles['super_admin']->givePermissionTo($permission);
            }
        }

        if (! $roles['super_admin']->hasPermissionTo($permission)) {
            $roles['super_admin']->givePermissionTo($permission);
        }
    }

    protected function startsWithAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}