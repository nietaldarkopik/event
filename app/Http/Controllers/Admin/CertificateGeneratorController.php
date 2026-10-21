<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateGeneratorController extends Controller
{
    public function index()
    {
        $events = \App\Models\Event::where('status', 'PUBLISHED')->orWhere('status', 'FINISHED')->latest()->get();
        return view('admin.certificates.generator.index', compact('events'));
    }

    public function show($eventId)
    {
        $event = \App\Models\Event::with(['certificates.participant', 'certificates.committee', 'certificates.category', 'certificateCategories.template', 'certificateSignatures'])->findOrFail($eventId);

        // Peserta yang berhak: semua kecuali yang ditolak. "Hadir" = sudah presensi datang.
        $participants = \App\Models\Participant::where('event_id', $event->id)
            ->where(function ($q) { $q->whereNull('status')->orWhere('status', '!=', 'rejected'); })->get();
        $presentIds = \App\Models\AttendanceLog::where('event_id', $event->id)->where('type', 'check_in')->pluck('participant_id')->unique();
        $issuedIds = $event->certificates->pluck('participant_id');

        $stats = [
            'total' => $participants->count(),
            'with_category' => $participants->whereNotNull('certificate_category_id')->count(),
            'present' => $participants->whereIn('id', $presentIds)->count(),
            'issued' => $issuedIds->unique()->count(),
        ];
        $hasCertificateFlag = $event->certificate_enabled || $event->allow_certificate;

        // Panitia (dari SK/ST): jumlah per seksi, dan berapa yang sudah menerima sertifikat.
        $committee = \Modules\Event\Models\EventCommittee::where('event_id', $event->id)->orderBy('sort_order')->orderBy('id')->get();
        $committeeIssued = $event->certificates->whereNotNull('committee_id')->pluck('committee_id')->unique();
        $committeeSections = $committee->groupBy(fn ($m) => $m->section ?: 'Tanpa seksi')->map(fn ($g) => [
            'total' => $g->count(),
            'issued' => $g->whereIn('id', $committeeIssued)->count(),
            'unnamed' => $g->where('position', 'Pelindung')->count(),
        ]);
        $committeeStats = ['total' => $committee->count(), 'issued' => $committee->whereIn('id', $committeeIssued)->count()];

        return view('admin.certificates.generator.show', compact('event', 'stats', 'hasCertificateFlag', 'committeeSections', 'committeeStats'));
    }

    public function generateMass(Request $request, $eventId)
    {
        $request->validate([
            'default_category_id' => 'nullable|integer',
            'only_present' => 'nullable|boolean',
        ]);

        $event = \App\Models\Event::with(['certificateCategories'])->findOrFail($eventId);
        $certificateService = app(\App\Services\CertificateService::class);

        // Kategori bawaan untuk peserta yang belum punya kategori (harus kategori milik event ini).
        $default = $request->filled('default_category_id')
            ? $event->certificateCategories->firstWhere('id', (int) $request->default_category_id)
            : null;
        if ($request->filled('default_category_id') && ! $default) {
            return back()->with('error', 'Kategori yang dipilih bukan milik event ini.');
        }
        $onlyPresent = $request->boolean('only_present');
        $presentIds = \App\Models\AttendanceLog::where('event_id', $event->id)->where('type', 'check_in')->pluck('participant_id')->unique()->flip();

        $created = $already = $notPresent = $noCategory = $noTemplate = 0;

        $participants = \App\Models\Participant::with('certificateCategory')->where('event_id', $event->id)
            ->where(function ($q) { $q->whereNull('status')->orWhere('status', '!=', 'rejected'); })->get();

        foreach ($participants as $participant) {
            if ($onlyPresent && ! isset($presentIds[$participant->id])) {
                $notPresent++;
                continue;
            }

            $category = $participant->certificateCategory ?: $default;
            if (! $category) {
                $noCategory++;
                continue;
            }
            if (! $category->template_id) {
                $noTemplate++;
                continue;
            }
            if (! $participant->certificate_category_id) {
                $participant->update(['certificate_category_id' => $category->id]);
            }

            $certificate = $certificateService->generate($participant, $category);
            $certificate->wasRecentlyCreated ? $created++ : $already++;
        }

        $parts = ["$created sertifikat baru diterbitkan"];
        if ($already) { $parts[] = "$already sudah pernah terbit"; }
        if ($notPresent) { $parts[] = "$notPresent dilewati karena belum hadir"; }
        if ($noCategory) { $parts[] = "$noCategory dilewati karena belum punya kategori sertifikat"; }
        if ($noTemplate) { $parts[] = "$noTemplate dilewati karena kategorinya belum memiliki template"; }

        return redirect()->back()->with($created ? 'success' : 'error', implode(', ', $parts) . '.');
    }

    /** Terbitkan sertifikat untuk panitia event, dengan satu kategori (mis. "Panitia") dan filter seksi. */
    public function generateCommittee(Request $request, $eventId)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'sections' => 'nullable|array',
            'sections.*' => 'string|max:120',
            'include_patrons' => 'nullable|boolean',
        ]);

        $event = \App\Models\Event::with('certificateCategories')->findOrFail($eventId);
        $category = $event->certificateCategories->firstWhere('id', (int) $request->category_id);
        if (! $category) {
            return back()->with('error', 'Kategori yang dipilih bukan milik event ini.');
        }
        if (! $category->template_id) {
            return back()->with('error', 'Kategori "' . $category->name . '" belum memiliki template. Pilih template di pengaturan sertifikat event.');
        }

        $sections = collect($request->input('sections', []));
        if ($sections->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu seksi panitia.');
        }
        $includePatrons = $request->boolean('include_patrons');

        $service = app(\App\Services\CertificateService::class);
        $created = $already = $skipped = 0;

        $members = \Modules\Event\Models\EventCommittee::where('event_id', $event->id)->orderBy('sort_order')->orderBy('id')->get();
        foreach ($members as $member) {
            $section = $member->section ?: 'Tanpa seksi';
            if (! $sections->contains($section)) {
                continue;
            }
            // "Pelindung" hanya berupa jabatan (tanpa nama orang), tidak ikut kecuali dipilih.
            if ($member->position === 'Pelindung' && ! $includePatrons) {
                $skipped++;
                continue;
            }
            if (! $member->display_name) {
                $skipped++;
                continue;
            }
            $cert = $service->generateForCommittee($member, $category);
            $cert->wasRecentlyCreated ? $created++ : $already++;
        }

        $parts = ["$created sertifikat panitia baru diterbitkan"];
        if ($already) { $parts[] = "$already sudah pernah terbit"; }
        if ($skipped) { $parts[] = "$skipped dilewati (Pelindung tanpa nama / tanpa nama)"; }

        return redirect()->back()->with($created ? 'success' : 'error', implode(', ', $parts) . '.');
    }

    public function downloadPdf($id)
    {
        $certificate = \App\Models\Certificate::with(['participant', 'committee', 'event', 'category', 'template'])->findOrFail($id);
        $certificateService = app(\App\Services\CertificateService::class);

        // 1. Replace variables in layout
        $layoutJson = $certificateService->replaceVariables($certificate->template->layout_json, $certificate);

        // 2. Generate QR - Targeting PUBLIC Verification URL
        $verifyUrl = route('certificates.verify', $certificate->certificate_hash);
        $qrcode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($verifyUrl));

        // 3. Prepare data for view
        $data = [
            'certificate' => $certificate,
            'layoutJson' => $layoutJson,
            'qrcode' => $qrcode,
            'background' => $certificate->template->background_file
                ? public_path('storage/' . $certificate->template->background_file)
                : null,
            'orientation' => $certificate->template->orientation,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.certificates.pdf_canvas', $data);
        $pdf->setPaper('a4', $certificate->template->orientation);

        return $pdf->stream('certificate-' . $certificate->certificate_number . '.pdf');
    }
}
