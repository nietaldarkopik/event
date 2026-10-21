<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Ensure PDF facade is imported, or aliased in config/app.php
use Illuminate\Support\Str;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $events = \App\Models\Event::where('status', 'PUBLISHED')->get();

        $query = \App\Models\AttendanceLog::with(['event', 'participant']);

        if ($request->has('event_id') && $request->event_id != '') {
            $query->where('event_id', $request->event_id);
        }

        $logs = $query->latest()->paginate(20);

        return view('admin.attendance.index', compact('logs', 'events'));
    }

    public function showEvent($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $participants = \App\Models\Participant::with(['session',
            'attendanceLogs' => function ($q) use ($eventId) {
                $q->where('event_id', $eventId);
            },
            'invitation' => function ($q) use ($eventId) {
                $q->where('event_id', $eventId);
            }
        ])->where('event_id', $eventId)->get();

        return view('admin.attendance.event_detail', compact('event', 'participants'));
    }

    public function scanner(Request $request)
    {
        $event = null;
        if ($request->has('event_id')) {
            $event = \App\Models\Event::findOrFail($request->event_id);
        }
        return view('admin.attendance.scanner', compact('event'));
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'qrcode' => 'required',
            'scan_type' => 'required|in:check_in,check_out,consumption',
        ]);

        $code = $request->qrcode;
        $scanType = $request->scan_type;

        // Backward-compat: older QR codes encoded a full URL
        // ("/check-invitation?code=XXX") instead of the bare code.
        if (preg_match('/[?&]code=([^&]+)/', $code, $matches)) {
            $code = urldecode($matches[1]);
        }

        // Find Invitation by Unique Code
        $invitation = \App\Models\Invitation::with('participant', 'event')->where('unique_code', $code)->first();

        if (!$invitation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode QR tidak valid!',
            ], 404);
        }

        $event = $invitation->event;

        if ($request->has('event_id') && $request->event_id != '') {
            if ($invitation->event_id != $request->event_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code ini untuk event ' . $event->title . ', bukan event yang sedang discan!',
                ], 400);
            }
        }

        if (!$invitation->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Undangan tidak aktif!',
            ], 400);
        }

        try {
            AttendanceService::record(
                $event,
                $invitation->participant,
                $invitation,
                $scanType,
                'admin_scan',
                auth()->id(),
                $request->ip(),
                $request->header('User-Agent')
            );
        } catch (\RuntimeException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        $message = [
            'check_in' => 'Berhasil Absensi Datang!',
            'check_out' => 'Berhasil Absensi Pulang!',
            'consumption' => 'Berhasil Absensi Pulang!'
        ][$scanType];

        // Event bersesi: tampilkan sesi peserta dan beri peringatan (tidak memblokir) bila di luar jam sesinya.
        $sessionLabel = null;
        $warning = null;
        if ($event->session_mode !== 'none' && $event->sessions()->exists()) {
            $session = $invitation->participant->session;
            if (!$session) {
                $warning = 'Sesi peserta ini belum ditetapkan.';
            } else {
                $sessionLabel = AttendanceService::sessionLabel($session);
                $w = AttendanceService::selfWindow($event, $session);
                if ($w && (now()->lt($w[0]) || now()->gt($w[1]))) {
                    $warning = 'Peserta terdaftar di ' . $sessionLabel . ' (saat ini di luar jam sesinya).';
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'participant' => $invitation->participant->name,
            'event' => $event->title,
            'time' => now()->format('H:i:s'),
            'session' => $sessionLabel,
            'warning' => $warning,
        ]);
    }

    /** Presensi manual: admin mencari peserta lalu menandai / membatalkan kehadiran. */
    public function manual(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'participant_id' => 'required|exists:participants,id',
            'type' => 'required|in:check_in,check_out,consumption',
            'action' => 'required|in:set,unset',
        ]);

        $event = \App\Models\Event::findOrFail($data['event_id']);
        $participant = \App\Models\Participant::where('event_id', $event->id)->findOrFail($data['participant_id']);

        if ($data['action'] === 'unset') {
            $logs = \App\Models\AttendanceLog::where('event_id', $event->id)->where('participant_id', $participant->id);
            // Membatalkan kehadiran datang juga membatalkan pulang agar urutan tetap konsisten.
            if ($data['type'] === 'check_in') {
                $logs->delete();
            } else {
                $logs->where('type', $data['type'])->delete();
            }

            return response()->json(['status' => 'success', 'message' => 'Presensi ' . $participant->name . ' dibatalkan.']);
        }

        try {
            $log = AttendanceService::record($event, $participant, $participant->invitation, $data['type'], 'manual', auth()->id(), $request->ip(), $request->header('User-Agent'));
        } catch (\RuntimeException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $participant->name . ' ditandai hadir.',
            'time' => $log->scanned_at->format('H:i:s'),
            'method' => AttendanceService::METHOD_LABELS['manual'],
        ]);
    }

    /** Layar presensi mandiri: QR yang diproyeksikan di lokasi acara. */
    public function display($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);

        return view('admin.attendance.display', [
            'event' => $event,
            'window' => AttendanceService::selfWindow($event),
            'closedReason' => AttendanceService::selfClosedReason($event),
        ]);
    }

    /** QR terbaru untuk layar presensi (dipanggil berkala oleh halaman layar). */
    public function token($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $url = AttendanceService::selfUrl($event, AttendanceService::currentToken($event));

        return response()->json([
            'url' => $url,
            'svg' => (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(360)->margin(1)->generate($url),
            'closed' => AttendanceService::selfClosedReason($event),
            'expires_in' => AttendanceService::TOKEN_SLOT_SECONDS - (time() % AttendanceService::TOKEN_SLOT_SECONDS),
        ]);
    }

    public function exportPdf($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $participants = \App\Models\Participant::with('attendanceLogs')
            ->where('event_id', $eventId)
            ->get();

        $pdf = Pdf::loadView('admin.exports.attendance_pdf', compact('event', 'participants'));
        return $pdf->download('absensi-' . Str::slug($event->title) . '.pdf');
    }

    public function exportCsv($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $participants = \App\Models\Participant::with(['attendanceLogs', 'session'])
            ->where('event_id', $eventId)
            ->get();

        $filename = "absensi-" . Str::slug($event->title) . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $secondaryCol = 'Absensi Pulang';
        $columns = ['No', 'Nama Peserta', 'Email', 'WhatsApp', 'Sesi', 'Absensi Datang', 'Waktu Datang', $secondaryCol, 'Waktu Pulang'];

        $callback = function () use ($event, $participants, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($participants as $index => $p) {
                $checkIn = $p->attendanceLogs->where('type', 'check_in')->first();
                $secondaryLog = null;
                if ($event->event_type == 'wisuda') {
                    $secondaryLog = $p->attendanceLogs->where('type', 'consumption')->first();
                } else {
                    $secondaryLog = $p->attendanceLogs->where('type', 'check_out')->first();
                }

                $statusCheckIn = $checkIn ? 'HADIR' : 'TIDAK HADIR';
                $timeCheckIn = $checkIn ? $checkIn->scanned_at : '-';

                $statusSecondary = $secondaryLog ? 'OK' : '-';
                $timeSecondary = $secondaryLog ? $secondaryLog->scanned_at : '-';

                fputcsv($file, [
                    $index + 1,
                    $p->name,
                    $p->email,
                    $p->whatsapp,
                    $p->session ? $p->session->title : '-',
                    $statusCheckIn,
                    $timeCheckIn,
                    $statusSecondary,
                    $timeSecondary
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
