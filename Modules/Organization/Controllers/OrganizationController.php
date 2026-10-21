<?php
namespace Modules\Organization\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Organization\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $items = Organization::latest()->get();
        return view('admin.organizations.index', compact('items'));
    }

    public function create()
    {

        return view('admin.organizations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'name' => 'required',
  'logo' => 'nullable',
  'email' => 'nullable|email',
  'phone' => 'nullable',
  'website' => 'nullable',
  'address' => 'nullable',
));

        Organization::create($validated);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil ditambahkan.');
    }

    public function show(Organization $organization)
    {
        return view('admin.organizations.show');
    }

    public function edit(Organization $organization)
    {

        return view('admin.organizations.edit');
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate(array (
  'name' => 'required',
  'logo' => 'nullable',
  'email' => 'nullable|email',
  'phone' => 'nullable',
  'website' => 'nullable',
  'address' => 'nullable',
));

        $organization->update($validated);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil dihapus.');
    }
}
