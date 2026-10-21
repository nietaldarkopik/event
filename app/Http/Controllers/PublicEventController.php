<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PublicEventController extends Controller
{
    public function home()
    {
        $featuredEvents = \App\Models\Event::where('status', 'PUBLISHED')
            ->latest()
            ->take(6)
            ->get();
        return view('public.home', compact('featuredEvents'));
    }

    public function index(Request $request)
    {
        $query = \App\Models\Event::where('status', 'PUBLISHED');

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        } elseif ($request->has('type') && $request->type != '') {
            $query->where('category_id', $request->type);
        }

        $events = $query->latest()->paginate(9);
        return view('public.events.index', compact('events'));
    }

    public function show($slug)
    {
        $event = \App\Models\Event::where('slug', $slug)->where('status', 'PUBLISHED')->with('rundowns')->firstOrFail();

        $faculties = \App\Models\Unit::where('type', 'fakultas')->where('is_active', true)->orderBy('name')->get();
        $studyPrograms = \App\Models\Unit::where('type', 'prodi')->where('is_active', true)->orderBy('name')->get();

        return view('public.events.show', compact('event', 'faculties', 'studyPrograms'));
    }

    public function preview($slug)
    {
        if (!request()->hasValidSignature()) {
            abort(403, 'Link preview tidak valid atau sudah kedaluwarsa.');
        }

        $event = \App\Models\Event::where('slug', $slug)->with('rundowns')->firstOrFail();
        return view('public.events.show', compact('event'));
    }

    public function calendar()
    {
        $events = \App\Models\Event::where('status', 'PUBLISHED')->get();
        $calendarEvents = [];

        foreach ($events as $event) {
            $calendarEvents[] = [
                'title' => $event->title,
                'start' => $event->start_datetime->toIso8601String(),
                'end' => $event->end_datetime->toIso8601String(),
                'url' => route('events.show', $event->slug),
                'className' => 'event-' . $event->event_type,
                'extendedProps' => [
                    'type' => $event->event_type
                ]
            ];
        }

        return view('public.events.calendar', compact('calendarEvents'));
    }

    public function register(Request $request, \App\Models\Event $event)
    {
        if ($event->event_type === 'rapat') {
            abort(404);
        }

        // Tombol di halaman bisa disembunyikan, tetapi server tetap harus menolak pendaftaran di luar jadwal.
        $reg = $event->registrationState();
        if ($reg['state'] !== 'open') {
            return redirect()->route('events.show', $event->slug)->withInput()->with('error', $reg['message']);
        }

        $isWisuda = $event->event_type === 'wisuda';

        if ($isWisuda) {
            $request->validate([
                'nim' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'fakultas_id' => 'required|exists:units,id',
                'program_studi_id' => 'required|exists:units,id',
                'email' => 'required|email|max:255',
                'whatsapp' => 'required|string|max:20',
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'whatsapp' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'unit' => 'required|string|max:255',
            ]);
        }

        if ($event->participant_quota && $event->participants()->count() >= $event->participant_quota) {
            return redirect()->route('events.show', $event->slug)
                ->withInput()
                ->with('error', 'Kuota pendaftaran untuk event ini sudah penuh.');
        }

        $sessionId = null;
        if (\App\Services\SessionService::usesChoice($event)) {
            $request->validate(['session_id' => 'required|integer'], ['session_id.required' => 'Silakan pilih sesi yang diikuti.']);
            $sessionId = (int) $request->session_id;
        }

        $exists = \App\Models\Participant::where('event_id', $event->id)
            ->where(function ($q) use ($request, $isWisuda) {
                $q->where('email', $request->email);
                if ($isWisuda) {
                    $q->orWhere('nim', $request->nim);
                }
            })
            ->first();

        if ($exists) {
            return redirect()->route('events.show', $event->slug)
                ->withInput()
                ->with('error', 'Anda sudah terdaftar untuk event ini.');
        }

        if ($isWisuda) {
            $fakultasUnit = \App\Models\Unit::where('id', $request->fakultas_id)->where('type', 'fakultas')->first();
            $prodiUnit = \App\Models\Unit::where('id', $request->program_studi_id)->where('type', 'prodi')->first();

            try {
                \App\Services\SessionService::createParticipant($event, $sessionId, [
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'email' => $request->email,
                'type' => 'mahasiswa',
                'nim' => $request->nim,
                'program_studi' => $prodiUnit ? $prodiUnit->name : null,
                'fakultas' => $fakultasUnit ? $fakultasUnit->name : null,
                'unit_id' => $prodiUnit ? $prodiUnit->id : null,
                'status' => 'pending',
                ]);
            } catch (\RuntimeException $e) {
                return redirect()->route('events.show', $event->slug)->withInput()->with('error', $e->getMessage());
            }

            return redirect()->route('events.show', $event->slug)
                ->with('success', 'Pendaftaran berhasil dikirim. Menunggu verifikasi admin.');
        }

        try {
            $participant = \App\Services\SessionService::createParticipant($event, $sessionId, [
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'email' => $request->email,
                'type' => 'umum',
                'unit' => $request->unit,
                'status' => $event->is_paid ? 'pending_payment' : 'approved',
            ]);
        } catch (\RuntimeException $e) {
            return redirect()->route('events.show', $event->slug)->withInput()->with('error', $e->getMessage());
        }

        if (!$event->is_paid) {
            $uniqueCode = 'EVT' . $event->id . strtoupper(\Illuminate\Support\Str::random(6));

            $participant->invitation()->create([
                'event_id' => $event->id,
                'unique_code' => $uniqueCode,
                'is_active' => true,
                'sent_via' => 'manual',
            ]);

            $participant->setRelation('event', $event);
            $invitation = $participant->invitation;
            $invitation->update(['sent_via' => 'system', 'sent_at' => now()]);

            // Pesan yang sama dengan pendaftaran lewat admin: detail event, kode, QR, panduan presensi, link.
            try {
                \App\Services\InvitationNotifier::sendWhatsApp($participant, $invitation, 'invitation_qr');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal kirim WA undangan pendaftaran publik: ' . $e->getMessage(), ['participant_id' => $participant->id]);
            }
        }

        // Event gratis: langsung tampilkan undangan beserta QR Code-nya, tidak perlu menunggu WhatsApp.
        if (!$event->is_paid && isset($uniqueCode)) {
            return redirect()->route('invitation.show', ['code' => $uniqueCode])
                ->with('registered', true);
        }

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Pendaftaran berhasil dikirim. Silakan lanjutkan pembayaran sesuai instruksi panitia.');
    }

    public function checkInvitation(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'unique_code' => 'required',
        ]);

        $invitation = \App\Models\Invitation::where('event_id', $request->event_id)
            ->where('unique_code', $request->unique_code)
            ->with(['participant.session', 'event.rundowns'])
            ->first();

        if (!$invitation) {
            return back()->with('error', 'Kode undangan tidak valid untuk event ini.');
        }

        return $this->invitationView($invitation);
    }

    /** Undangan digital lewat link langsung (tanpa form), dipakai di pesan WhatsApp. */
    public function invitation(string $code)
    {
        $invitation = \App\Models\Invitation::whereRaw('UPPER(unique_code) = ?', [strtoupper($code)])
            ->where('is_active', true)
            ->with(['participant.session', 'event.rundowns'])
            ->firstOrFail();

        return $this->invitationView($invitation);
    }

    /** Render undangan memakai template yang dipilih di event (bila ada & aktif). */
    private function invitationView($invitation)
    {
        $template = $invitation->event ? $invitation->event->invitationTemplate : null;
        $templateConfig = ($template && $template->is_active) ? $template->config : null;

        return view('public.events.invitation', compact('invitation', 'templateConfig'));
    }

    public function certificateCheck()
    {
        return view('public.certificates.check');
    }

    public function certificateSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|min:3',
        ]);

        $search = $request->input('query');

        $certificates = \App\Models\Certificate::where('certificate_number', 'like', "%{$search}%")
            ->orWhereHas('participant', function ($q) use ($search) {
                $q->where('whatsapp', 'like', "%{$search}%");
            })
            ->with(['event', 'participant'])
            ->get();

        return view('public.certificates.check', compact('certificates', 'search'));
    }
    public function claimCertificate(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'unique_code' => 'required',
        ]);

        $invitation = \App\Models\Invitation::where('event_id', $request->event_id)
            ->where('unique_code', $request->unique_code)
            ->first();

        if (!$invitation) {
            return back()->with('error', 'Kode unik tidak valid untuk event ini.');
        }

        $certificate = \App\Models\Certificate::where('event_id', $request->event_id)
            ->where('participant_id', $invitation->participant_id)
            ->first();

        if (!$certificate) {
            return back()->with('error', 'Sertifikat Anda belum tersedia atau sedang diproses. Silakan hubungi admin jika sudah mendapatkan konfirmasi.');
        }

        return redirect()->route('certificates.public_download', $certificate->unique_code);
    }

    public function whatsappRedirect(Request $request)
    {
        // 1. Check Honeypot
        if ($request->website_hp) {
            return response()->json(['status' => 'error', 'message' => 'Spam terdeteksi.'], 403);
        }

        // 2. Verify ReCaptcha
        $token = $request->g_recaptcha_response;
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $token
        ]);

        if (!$response->successful() || !$response->json('success') || $response->json('score') < 0.5) {
            return response()->json(['status' => 'error', 'message' => 'Verifikasi captcha gagal. Silakan coba lagi.'], 403);
        }

        // 3. Return redirect URL
        return response()->json([
            'status' => 'success',
            'redirect_url' => $request->target_url
        ]);
    }
}
