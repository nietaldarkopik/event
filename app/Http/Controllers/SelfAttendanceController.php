<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Presensi mandiri peserta. Bukti "berada di lokasi" = memindai QR di layar panitia
 * (kode berganti tiap menit, lihat AttendanceService). Setelah QR valid, peserta punya
 * beberapa menit untuk memasukkan kode undangan / nomor WhatsApp-nya.
 */
class SelfAttendanceController extends Controller
{
    private function event(string $slug): Event
    {
        return Event::where('slug', $slug)->firstOrFail();
    }

    private function proofKey(Event $event): string
    {
        return 'attendance_proof_' . $event->id;
    }

    private function hasProof(Request $request, Event $event): bool
    {
        $until = $request->session()->get($this->proofKey($event));

        return $until && now()->timestamp <= $until;
    }

    public function show(Request $request, string $slug)
    {
        $event = $this->event($slug);

        if ($request->filled('t')) {
            if (AttendanceService::tokenValid($event, $request->query('t'))) {
                $request->session()->put($this->proofKey($event), now()->addMinutes(AttendanceService::PROOF_MINUTES)->timestamp);
                // Buang token dari URL supaya link yang dibagikan tidak ikut membawa bukti.
                return redirect()->route('attendance.self', ['slug' => $event->slug]);
            }

            return $this->render($event, $request, ['error' => 'Kode QR sudah kedaluwarsa. Scan ulang QR yang sedang tampil di layar panitia.']);
        }

        return $this->render($event, $request);
    }

    public function submit(Request $request, string $slug)
    {
        $event = $this->event($slug);
        $data = $request->validate([
            'code' => 'required|string|max:60',
            'type' => 'required|in:check_in,check_out',
        ]);

        if (!$this->hasProof($request, $event)) {
            return $this->render($event, $request, ['error' => 'Verifikasi lokasi habis. Scan ulang QR yang sedang tampil di layar panitia.']);
        }

        if ($reason = AttendanceService::selfClosedReason($event)) {
            return $this->render($event, $request, ['error' => $reason]);
        }

        [$invitation, $error] = AttendanceService::findInvitation($event, $data['code']);
        if (!$invitation) {
            return $this->render($event, $request, ['error' => $error, 'old_code' => $data['code']]);
        }

        // Event bersesi: jendela waktu mengikuti sesi peserta, bukan jam seluruh event.
        $participant = $invitation->participant;
        $usesSessions = $event->session_mode !== 'none' && $event->sessions()->exists();
        if ($usesSessions) {
            if (!$participant->session_id) {
                return $this->render($event, $request, ['error' => 'Sesi Anda belum ditetapkan oleh panitia. Silakan hubungi panitia di lokasi.', 'old_code' => $data['code']]);
            }
            $session = $participant->session;
            if ($reason = AttendanceService::selfClosedReason($event, $session)) {
                return $this->render($event, $request, ['error' => $reason . ' (Sesi Anda: ' . AttendanceService::sessionLabel($session) . ')', 'old_code' => $data['code']]);
            }
        }

        $type = $data['type'] === 'check_in' ? 'check_in' : AttendanceService::secondaryType($event);

        try {
            $log = AttendanceService::record($event, $invitation->participant, $invitation, $type, 'self', null, $request->ip(), $request->userAgent());
        } catch (\RuntimeException $e) {
            return $this->render($event, $request, ['error' => $e->getMessage(), 'old_code' => $data['code']]);
        }

        // Terlambat dihitung dari jam mulai sesi peserta (bila ada), kalau tidak dari jam mulai event.
        $startRef = ($usesSessions && $participant->session && $participant->session->start_datetime) ? $participant->session->start_datetime : $event->start_date;
        $late = null;
        if ($type === 'check_in' && $startRef && $log->scanned_at->gt(Carbon::parse($startRef)->addMinutes(15))) {
            $late = Carbon::parse($startRef)->diffInMinutes($log->scanned_at);
        }

        return $this->render($event, $request, ['success' => [
            'name' => $invitation->participant->name,
            'type' => $type === 'check_in' ? 'Presensi Datang' : 'Presensi Pulang',
            'time' => $log->scanned_at->translatedFormat('d M Y, H:i:s'),
            'late' => $late,
            'session' => $usesSessions ? AttendanceService::sessionLabel($participant->session) : null,
        ]]);
    }

    private function render(Event $event, Request $request, array $extra = [])
    {
        $window = AttendanceService::selfWindow($event);

        return view('public.events.self_attendance', array_merge([
            'event' => $event,
            'hasProof' => $this->hasProof($request, $event),
            'closedReason' => AttendanceService::selfClosedReason($event),
            'window' => $window,
            'hasPulang' => true,
        ], $extra));
    }
}
