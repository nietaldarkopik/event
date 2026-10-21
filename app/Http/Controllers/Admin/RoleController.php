<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions'])->get();
        return view('admin.settings.roles.index', compact('roles'));
    }

    public function create()
    {
        return redirect()->route('settings.roles.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
        ]);

        $roleName = Str::slug(strtolower($request->name), '_');

        Role::create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        return redirect()->back()->with('success', "Role '{$roleName}' berhasil dibuat.");
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        // Group permissions logically for visual matrix display
        $groupedPermissions = [];
        foreach ($permissions as $p) {
            $group = $this->determineGroup($p->name);
            $groupedPermissions[$group][] = $p;
        }

        ksort($groupedPermissions);

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.settings.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $selectedPermissions = $request->input('permissions', []);
        $role->syncPermissions($selectedPermissions);

        return redirect()->route('settings.roles.index')
            ->with('success', "Hak akses / Permission untuk Role '{$role->name}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['super_admin'])) {
            return redirect()->back()->with('error', 'Role super_admin adalah role sistem bawaan dan tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()->back()->with('success', "Role '{$role->name}' berhasil dihapus.");
    }

    private function determineGroup(string $name): string
    {
        if (Str::startsWith($name, ['admin.events.', 'admin.event-sessions.', 'admin.speakers.', 'admin.session-speakers.', 'events.'])) {
            return '1. Manajemen Acara / Event';
        }
        if (Str::startsWith($name, ['admin.participants.', 'admin.registrations.', 'admin.ticket-types.', 'admin.attendance-logs.', 'admin.qr-codes.', 'participants.', 'registrations.'])) {
            return '2. Registrasi, Tiket & Absensi';
        }
        if (Str::startsWith($name, ['admin.certificates.', 'admin.certificate-media.', 'certificate-templates.', 'certificate-generator.', 'certificates.'])) {
            return '3. Sertifikat Digital';
        }
        if (Str::startsWith($name, ['admin.payments.', 'finance.', 'payments.'])) {
            return '4. Pembayaran & Keuangan';
        }
        if (Str::startsWith($name, ['settings.', 'admin.users.', 'admin.email-settings.', 'admin.organizations.', 'units.'])) {
            return '5. Pengaturan Sistem & User';
        }
        if (Str::startsWith($name, ['admin.whatsapp.', 'admin.announcements.', 'admin.galleries.', 'admin.sponsors.', 'reports.'])) {
            return '6. Promosi, Laporan & Log WA';
        }

        return '7. Modul Lainnya';
    }
}
