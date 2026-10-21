<?php
namespace Modules\Registration\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Registration\Models\Registration;
use \Modules\Registration\Models\QrCode;
use \App\Services\QrCodeImageService;
use \App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $items = Registration::with(['event', 'participant'])
            ->when($request->filled('event_id'), fn($q) => $q->where('event_id', $request->event_id))
            ->when($request->filled('participant_id'), fn($q) => $q->where('participant_id', $request->participant_id))
            ->latest()->get();
        $selectedEvent = $request->filled('event_id') ? \Modules\Event\Models\Event::find($request->event_id) : null;
        return view('admin.registrations.index', compact('items', 'selectedEvent'));
    }

    public function create()
    {
        $events = \Modules\Event\Models\Event::all();
        $participants = \Modules\Participant\Models\Participant::all();
        $ticketTypes = \Modules\Ticket\Models\TicketType::all();

        return view('admin.registrations.create', compact('events', 'participants', 'ticketTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'participant_id' => 'required',
  'ticket_type_id' => 'nullable',
  'registration_number' => 'required',
  'status' => 'required',
  'registered_at' => 'nullable',
  'checked_in_at' => 'nullable',
  'notes' => 'nullable',
));

        Registration::create($validated);

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Registrasi Event berhasil ditambahkan.');
    }

    public function approve(Registration $registration)
    {
        if ($registration->status === 'APPROVED') {
            return redirect()->route('admin.registrations.index')
                ->with('info', 'Registrasi ini sudah di-approve sebelumnya.');
        }

        $registration->load(['event', 'participant']);
        $registration->update([
            'status' => 'APPROVED',
        ]);

        $participant = $registration->participant;
        $event = $registration->event;
        $qrCode = $registration->qrCode;

        if (!$qrCode) {
            $uniqueCode = $registration->registration_number
                ? $registration->registration_number
                : 'REG-' . strtoupper(Str::random(12));

            $attendanceUrl = url('/attendance/verify?registration=' . $registration->uuid . '&code=' . $uniqueCode);

            $qrImage = QrCodeImageService::generateAndSave($uniqueCode, $attendanceUrl);

            $qrCode = QrCode::create([
                'registration_id' => $registration->id,
                'code'            => $uniqueCode,
                'expired_at'      => $event?->end_date,
                'is_used'         => false,
            ]);
        } else {
            $qrImage = QrCodeImageService::generateAndSave(
                $qrCode->code,
                url('/attendance/verify?registration=' . $registration->uuid . '&code=' . $qrCode->code)
            );
        }

        $eventTitle = $event?->title ?? 'Event';
        $eventDate = $event && $event->start_date
            ? \Carbon\Carbon::parse($event->start_date)->translatedFormat('l, d F Y H:i')
            : '-';
        $venue = $event?->venue_name ?? '-';
        $participantName = $participant?->name ?? 'Peserta';
        $regNumber = $registration->registration_number ?? '-';

        $message = "*🎉 Pendaftaran Event Diterima!*\n\n";
        $message .= "Halo *{$participantName}*,\n\n";
        $message .= "Pendaftaran Anda untuk event berikut telah *DISETUJUI*:\n\n";
        $message .= "📅 *Event*: {$eventTitle}\n";
        $message .= "🕒 *Waktu*: {$eventDate}\n";
        $message .= "📍 *Tempat*: {$venue}\n";
        $message .= "🎟️ *No. Registrasi*: {$regNumber}\n\n";
        $message .= "📲 *PENTING*: Simpan QR Code di bawah ini. Anda harus *MENSCAN QR Code* ini saat datang untuk melakukan absensi kehadiran.\n\n";
        $message .= "Terima kasih. Sampai jumpa di acara!\n\n";
        $message .= "— Panitia EVENT UNWIM";

        $caption = "QR Absensi - {$eventTitle} - {$participantName}";

        if ($participant && !empty($participant->whatsapp)) {
            WhatsAppService::sendWithImage(
                participant: $participant,
                type: 'registration_approved_qr',
                message: $message,
                imagePath: $qrImage['path'] ?? null,
                imageUrl: $qrImage['url'] ?? null,
                caption: $caption
            );
        }

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Registrasi berhasil di-approve. Notifikasi WA dengan QR Code absensi sedang dikirim.');
    }

    public function reject(Request $request, Registration $registration)
    {
        if (in_array($registration->status, ['REJECTED', 'CANCELLED'])) {
            return redirect()->route('admin.registrations.index')
                ->with('info', 'Status registrasi ini sudah ' . $registration->status . '.');
        }

        $note = $request->get('reason') ?? 'Ditolak oleh admin.';
        $registration->update([
            'status' => 'REJECTED',
            'notes'  => $note,
        ]);

        $registration->load(['event', 'participant']);
        $participant = $registration->participant;
        $event = $registration->event;

        if ($participant && !empty($participant->whatsapp)) {
            $eventTitle = $event?->title ?? 'Event';
            $participantName = $participant?->name ?? 'Peserta';
            $message = "*ℹ️ Pendaftaran Event Ditolak*\n\n";
            $message .= "Halo *{$participantName}*,\n\n";
            $message .= "Mohon maaf, pendaftaran Anda untuk event *{$eventTitle}* saat ini belum dapat kami terima.\n\n";
            $message .= "Alasan: {$note}\n\n";
            $message .= "Terima kasih atas perhatiannya.\n\n";
            $message .= "— Panitia EVENT UNWIM";

            WhatsAppService::send($participant, 'registration_rejected', $message);
        }

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Registrasi berhasil ditolak.');
    }

    public function show(Registration $registration)
    {
        $registration->load(['event', 'participant', 'ticketType', 'payment', 'qrCode', 'attendanceLogs']);
        return view('admin.registrations.show', compact('registration'));
    }

    public function edit(Registration $registration)
    {
        $events = \Modules\Event\Models\Event::all();
        $participants = \Modules\Participant\Models\Participant::all();
        $ticketTypes = \Modules\Ticket\Models\TicketType::all();

        return view('admin.registrations.edit', compact('registration', 'events', 'participants', 'ticketTypes'));
    }

    public function update(Request $request, Registration $registration)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'participant_id' => 'required',
  'ticket_type_id' => 'nullable',
  'registration_number' => 'required',
  'status' => 'required',
  'registered_at' => 'nullable',
  'checked_in_at' => 'nullable',
  'notes' => 'nullable',
));

        $registration->update($validated);

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Registrasi Event berhasil diperbarui.');
    }

    public function destroy(Registration $registration)
    {
        $registration->delete();

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Registrasi Event berhasil dihapus.');
    }
}
