<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use App\Models\InvitationTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvitationTemplateController extends Controller
{
    public function index()
    {
        $templates = InvitationTemplate::latest()->get();
        return view('admin.invitation_templates.index', compact('templates'));
    }

    public function create()
    {
        $categories = EventCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.invitation_templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $allowedCategories = EventCategory::where('is_active', true)->pluck('slug')->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => ['required', Rule::in($allowedCategories)],
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:0,1',
        ]);

        // Merge default config with any incoming config
        $config = array_merge(
            InvitationTemplate::getDefaultConfig(),
            $request->input('config', [])
        );

        $template = InvitationTemplate::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'config' => $config,
        ]);

        return redirect()->route('invitation-templates.index')
            ->with('success', 'Template berhasil dibuat.');
    }

    public function edit($id)
    {
        $template = InvitationTemplate::findOrFail($id);
        $categories = EventCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.invitation_templates.edit', compact('template', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $template = InvitationTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Config update from builder
        $config = $request->input('config', $template->config);

        $template->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'config' => $config,
        ]);

        return redirect()->route('invitation-templates.index')
            ->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $template = InvitationTemplate::findOrFail($id);

        // Check if template is used by any events
        if ($template->events()->count() > 0) {
            return redirect()->back()->with('error', 'Template tidak bisa dihapus karena sedang digunakan oleh event.');
        }

        $template->delete();
        return redirect()->route('invitation-templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }

    public function show($id)
    {
        return redirect()->route('invitation-templates.index');
    }

    public function builder($id)
    {
        $template = InvitationTemplate::findOrFail($id);
        return view('admin.invitation_templates.builder', compact('template'));
    }

    public function updateConfig(Request $request, $id)
    {
        $template = InvitationTemplate::findOrFail($id);

        $config = $request->validate([
            'layout' => 'array',
            'colors' => 'array',
            'typography' => 'array',
            'buttons' => 'array',
            'sections' => 'array',
            'advanced' => 'array',
        ]);

        $template->update(['config' => $config]);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi berhasil disimpan.'
        ]);
    }

    public function preview($id)
    {
        $template = InvitationTemplate::findOrFail($id);

        // Create dummy data for preview
        $participantData = [
            'name' => 'John Doe',
            'type' => 'Mahasiswa',
            'nim' => '2021010101',
            'seat_number' => 'A-12',
        ];

        if ($template->category == 'wisuda') {
            $participantData['program_studi'] = 'Teknik Informatika';
            $participantData['fakultas'] = 'Fakultas Teknik';
        } elseif ($template->category == 'rapat') {
            $participantData['jabatan'] = 'Ketua Panitia';
            $participantData['unit'] = 'UPT Teknologi Informasi';
            $participantData['type'] = 'Staff';
        } elseif ($template->category == 'seminar' || $template->category == 'workshop') {
            $participantData['type'] = 'Moderator';
        }

        $dummyInvitation = (object) [
            'unique_code' => 'PREVIEW12345',
            'participant' => (object) $participantData,
            'event' => (object) [
                'title' => 'Event Preview (Judul)',
                'slug' => 'preview-event',
                'event_type' => $template->category,
                'start_datetime' => now()->addDays(7),
                'end_datetime' => now()->addDays(7)->addHours(4),
                'location' => 'Aula Universitas',
                'description' => 'Ini adalah preview konten deskripsi event untuk template undangan.',
                'rules' => "1. Harap datang tepat waktu.\n2. Berpakaian rapi dan sopan.\n3. Menjaga ketertiban selama acara.",
                'organizer_unit' => 'Panitia Pelaksana',
                'contact_whatsapp' => '628123456789',
                'location_link' => 'https://goo.gl/maps/example',
                'rundowns' => collect([
                    (object) ['time_start' => '08:00', 'time_end' => '09:00', 'agenda' => 'Registrasi Ulang'],
                    (object) ['time_start' => '09:00', 'time_end' => '10:00', 'agenda' => 'Pembukaan Acara'],
                    (object) ['time_start' => '10:00', 'time_end' => '12:00', 'agenda' => 'Sesi Utama'],
                ]),
            ],
        ];

        $templateConfig = $template->config;

        return view('public.events.invitation', [
            'invitation' => $dummyInvitation,
            'templateConfig' => $templateConfig,
            'isPreview' => true,
        ]);
    }
}
