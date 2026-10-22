<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Event;
use App\Models\Invitation;
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
            'code' => 'required_without:invitation_id|nullable|string|max:60',
            'invitation_id' => 'required_without:code|nullable|integer',
            'type' => 'required|in:check_in,check_out',
        ]);

        if (!$this->hasProof($request, $event)) {
            return $this->render($event, $request, ['error' => 'Verifikasi lokasi habis. Scan ulang QR yang sedang tampil di layar panitia.']);
        }

        if ($reason = AttendanceService::selfClosedReason($event)) {
            return $this->render($event, $request, ['error' => $reason]);
        }

        if (!empty($data['invitation_id'])) {
            // Dipilih dari hasil pencarian nama; harus undangan aktif milik event ini.
            $invitation = Invitation::with('participant')->where('event_id', $event->id)->where('is_active', true)->find($data['invitation_id']);
            $error = 'Peserta tidak ditemukan untuk event ini. Silakan cari ulang.';
            $data['code'] = $invitation->unique_code ?? '';
        } else {
            [$invitation, $error] = AttendanceService::findInvitation($event, $data['code']);
        }
        if (!$invitation || !$invitation->participant) {
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

    /**
     * Pencarian peserta untuk autocomplete (JSON). Butuh bukti scan QR lokasi, sama seperti submit.
     * Kode registrasi lengkap = cocok persis; selain itu dianggap nama dengan wildcard (spasi / * / % memisahkan
     * potongan kata, mis. "eul sop" cocok "EULIS RIANTI SOPANIA"). Kode undangan tidak pernah ikut dikirim.
     */
    public function search(Request $request, string $slug)
    {
        $event = $this->event($slug);
        if (!$this->hasProof($request, $event)) {
            return response()->json(['error' => 'Verifikasi lokasi habis. Scan ulang QR yang sedang tampil di layar panitia.'], 403);
        }

        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 3) {
            return response()->json(['results' => []]);
        }

        $query = Invitation::with('participant.session')->where('event_id', $event->id)->where('is_active', true)
            ->whereHas('participant', fn ($p) => $p->whereNull('deleted_at'));

        $byCode = (clone $query)->whereRaw('UPPER(unique_code) = ?', [strtoupper($q)])->first();
        if ($byCode) {
            $found = collect([$byCode]);
        } else {
            $words = array_values(array_filter(preg_split('/[\s*%]+/u', $q)));
            $like = '%' . implode('%', array_map(fn ($w) => addcslashes($w, '%_\\'), $words)) . '%';
            $found = $query->whereHas('participant', fn ($p) => $p->where('name', 'like', $like))
                ->get()->sortBy(fn ($i) => $i->participant->name)->take(10)->values();
        }

        $done = AttendanceLog::where('event_id', $event->id)->where('type', 'check_in')
            ->whereIn('participant_id', $found->pluck('participant_id'))->pluck('participant_id')->all();

        return response()->json(['results' => $found->map(function ($inv) use ($done) {
            $p = $inv->participant;

            return [
                'id' => $inv->id,
                'name' => $p->name,
                'nim' => $p->nim,
                'prodi' => $p->program_studi,
                'fakultas' => $p->fakultas,
                'jabatan' => $p->jabatan,
                'unit' => $p->unit,
                'session' => AttendanceService::sessionLabel($p->session),
                'whatsapp' => $p->whatsapp ? preg_replace('/(\d{4})\d+(\d{3})$/', '$1****$2', $p->whatsapp) : null,
                'checked_in' => in_array($p->id, $done, true),
            ];
        })->all()]);
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
