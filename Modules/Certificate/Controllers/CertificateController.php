<?php
namespace Modules\Certificate\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Certificate\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $items = Certificate::when($request->filled('event_id'), fn($q) => $q->where('event_id', $request->event_id))
            ->when($request->filled('participant_id'), fn($q) => $q->where('participant_id', $request->participant_id))
            ->latest()->get();
        $selectedEvent = $request->filled('event_id') ? \Modules\Event\Models\Event::find($request->event_id) : null;
        return view('admin.certificates.index', compact('items', 'selectedEvent'));
    }

    public function create()
    {
        $registrations = \Modules\Registration\Models\Registration::all();

        return view('admin.certificates.create', compact('registrations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'registration_id' => 'required',
  'certificate_number' => 'required',
  'certificate_file' => 'nullable',
  'issued_at' => 'nullable',
));

        Certificate::create($validated);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    public function show(Certificate $certificate)
    {
        return view('admin.certificates.show');
    }

    public function edit(Certificate $certificate)
    {
        $registrations = \Modules\Registration\Models\Registration::all();

        return view('admin.certificates.edit', compact('registrations'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate(array (
  'registration_id' => 'required',
  'certificate_number' => 'required',
  'certificate_file' => 'nullable',
  'issued_at' => 'nullable',
));

        $certificate->update($validated);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil dihapus.');
    }
}
