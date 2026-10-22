<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventInvitationMail;
use App\Services\QrCodeImageService;

class ParticipantController extends Controller
{
    /**
     * Default WhatsApp invitation text shown as a starting point in the
     * import modal; admins can edit it there before blasting. Placeholders
     * are substituted per participant in buildInvitationMessage().
     */
    public const DEFAULT_WA_TEMPLATE = "*KODE REGISTRASI PESERTA*\n\nHalo *{nama}*,\n\nAnda terdaftar sebagai peserta pada event:\n📌 *{event}*\n📅 {tanggal}\n📍 {lokasi}\n\n*Kode Registrasi:* {kode}\n\n{presensi}\n\n🔗 Info lengkap event: {link}";

    public function index(Request $request)
    {
        $events = \App\Models\Event::latest()->get();
        $selectedEvent = $request->filled('event_id') ? $events->find($request->event_id) : null;

        // Dasar kueri (hanya filter event): dipakai untuk angka ringkasan agar tidak ikut berubah saat mencari.
        $base = \App\Models\Participant::query()->when($selectedEvent, fn ($q) => $q->where('event_id', $selectedEvent->id));

        $stats = [
            'total' => (clone $base)->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'waiting' => (clone $base)->whereIn('status', ['pending', 'pending_payment'])->count(),
            'present' => (clone $base)->whereHas('attendanceLogs', fn ($q) => $q->where('type', 'check_in'))->count(),
        ];

        $q = trim((string) $request->input('q', ''));
        $perPage = in_array((int) $request->input('per_page'), [20, 50, 100], true) ? (int) $request->input('per_page') : 20;

        $participants = (clone $base)
            ->with(['event', 'unitRelation', 'invitation', 'attendanceLogs', 'session'])
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)->orWhere('whatsapp', 'like', $like)->orWhere('email', 'like', $like)
                        ->orWhere('nim', 'like', $like)->orWhere('program_studi', 'like', $like)->orWhere('fakultas', 'like', $like)
                        ->orWhereHas('invitation', fn ($i) => $i->where('unique_code', 'like', $like));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('session_id'), fn ($query) => $request->session_id === 'none'
                ? $query->whereNull('session_id') : $query->where('session_id', $request->session_id))
            ->when($request->input('presence') === 'present', fn ($query) => $query->whereHas('attendanceLogs', fn ($a) => $a->where('type', 'check_in')))
            ->when($request->input('presence') === 'absent', fn ($query) => $query->whereDoesntHave('attendanceLogs', fn ($a) => $a->where('type', 'check_in')))
            ->latest()->paginate($perPage)->withQueryString();

        // Halaman sudah tidak ada (mis. baris terakhir di halaman terakhir baru dihapus): ke halaman terakhir yang masih ada.
        if ($participants->isEmpty() && $participants->currentPage() > 1) {
            return redirect()->to($participants->url($participants->lastPage()));
        }

        $certificateCategories = $selectedEvent ? $selectedEvent->certificateCategories : collect();

        // Sesi event (mis. wisuda sesi 1/2) + pilihan kriteria untuk penetapan sesi massal.
        $sessions = $selectedEvent ? \App\Services\SessionService::overview($selectedEvent) : collect();
        $criteria = [];
        if ($selectedEvent && $sessions->count()) {
            foreach (['fakultas', 'program_studi', 'type'] as $col) {
                $criteria[$col] = \App\Models\Participant::where('event_id', $selectedEvent->id)->whereNotNull($col)->where($col, '!=', '')
                    ->distinct()->orderBy($col)->pluck($col)->values();
            }
        }
        $units = \App\Models\Unit::orderBy('parent_id')->orderBy('name')->get();
        $defaultWaTemplate = self::DEFAULT_WA_TEMPLATE;
        $importSessionsByEvent = \Modules\Session\Models\EventSession::orderBy('start_datetime')->orderBy('id')->get(['id', 'event_id', 'title'])->groupBy('event_id');

        return view('admin.participants.index', compact('importSessionsByEvent', 'participants', 'events', 'selectedEvent', 'certificateCategories', 'units', 'defaultWaTemplate', 'sessions', 'criteria', 'stats', 'q', 'perPage'));
    }

    /** Tetapkan / ubah sesi satu peserta (panitia boleh melampaui kuota; sisa kuota dikembalikan untuk info). */
    public function updateSession(Request $request, $id)
    {
        $request->validate(['session_id' => 'nullable|integer']);

        $participant = Participant::findOrFail($id);
        $sessionId = $request->session_id ?: null;
        if ($sessionId) {
            \Modules\Session\Models\EventSession::where('event_id', $participant->event_id)->findOrFail($sessionId);
        }
        $participant->update(['session_id' => $sessionId]);

        $info = $sessionId ? \App\Services\SessionService::overview($participant->event)->firstWhere('id', $sessionId) : null;

        return response()->json([
            'success' => true,
            'message' => 'Sesi tersimpan.',
            'over_quota' => $info && $info->max_participants && $info->taken > $info->max_participants,
        ]);
    }

    /** Tetapkan sesi massal: semua peserta event, atau yang cocok dengan fakultas/prodi/tipe tertentu. */
    public function bulkSession(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'session_id' => 'required|integer',
            'field' => 'required|in:all,fakultas,program_studi,type',
            'value' => 'nullable|string|max:191',
            'only_unassigned' => 'nullable|boolean',
        ]);

        \Modules\Session\Models\EventSession::where('event_id', $data['event_id'])->findOrFail($data['session_id']);

        $q = Participant::where('event_id', $data['event_id']);
        if ($data['field'] !== 'all') {
            if (!isset($data['value']) || $data['value'] === '') {
                return response()->json(['success' => false, 'message' => 'Pilih nilai kriteria terlebih dahulu.'], 422);
            }
            $q->where($data['field'], $data['value']);
        }
        if (!empty($data['only_unassigned'])) {
            $q->whereNull('session_id');
        }

        $count = $q->update(['session_id' => $data['session_id']]);

        return response()->json(['success' => true, 'message' => $count . ' peserta ditetapkan ke sesi tersebut.', 'count' => $count]);
    }

    public function updateCertificateCategory(Request $request, $id)
    {
        $request->validate([
            'certificate_category_id' => 'nullable|exists:certificate_categories,id',
        ]);

        $participant = Participant::findOrFail($id);
        $participant->update(['certificate_category_id' => $request->certificate_category_id]);

        return response()->json(['success' => true, 'message' => 'Kategori sertifikat tersimpan.']);
    }

    public function create()
    {
        $events = \App\Models\Event::where('status', 'PUBLISHED')->orWhere('status', 'DRAFT')->get();
        $units = \App\Models\Unit::orderBy('name')->get();
        $sessionsByEvent = \Modules\Session\Models\EventSession::orderBy('start_datetime')->get(['id', 'event_id', 'title'])->groupBy('event_id');
        return view('admin.participants.create', compact('events', 'units', 'sessionsByEvent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required',
            'whatsapp' => 'required|numeric',
            'type' => 'required',
            'unit_id' => 'nullable|exists:units,id',
            'session_id' => 'nullable|integer',
        ]);

        $event = \App\Models\Event::findOrFail($request->event_id);

        if ($request->session_id && !\Modules\Session\Models\EventSession::where('event_id', $event->id)->where('id', $request->session_id)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Sesi yang dipilih bukan milik event ini.');
        }

        if ($event->participant_quota && $event->participants()->count() >= $event->participant_quota) {
            return redirect()->back()->withInput()->with('error', 'Kuota peserta untuk event ini sudah penuh.');
        }

        // Seat Uniqueness Check
        if ($request->seat_number) {
            $exists = Participant::where('event_id', $request->event_id)
                ->where('seat_number', $request->seat_number)
                ->exists();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Nomor kursi sudah digunakan oleh peserta lain di event ini.');
            }
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $event) {
            $status = $event->is_paid ? 'pending_payment' : 'approved';

            $participant = \App\Models\Participant::create([
                'event_id' => $request->event_id,
                'session_id' => $request->session_id ?: null,
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'email' => $request->email,
                'type' => $request->type,
                'nim' => $request->nim,
                'program_studi' => $request->program_studi,
                'fakultas' => $request->fakultas,
                'jabatan' => $request->jabatan,
                'unit' => $request->unit,
                'unit_id' => $request->unit_id,
                'seat_number' => $request->seat_number,
                'status' => $status,
            ]);

            if ($event->is_paid) {
                // Create Payment Record (Pending)
                \App\Models\Payment::create([
                    'event_id' => $event->id,
                    'participant_id' => $participant->id,
                    'amount' => $event->price,
                    'status' => 'pending',
                ]);

                // Optional: Send WA for Payment Instruction?
            } else {
                // Free Event: Auto Approve & Generate Invitation

                // Generate Unique Code (Format: EVT-ID-RANDOM)
                $uniqueCode = 'EVT' . $request->event_id . strtoupper(\Illuminate\Support\Str::random(6));

                // Create Invitation
                $invitation = \App\Models\Invitation::create([
                    'event_id' => $request->event_id,
                    'participant_id' => $participant->id,
                    'unique_code' => $uniqueCode,
                    'is_active' => true,
                    'sent_via' => 'manual',
                ]);

                // Send WhatsApp Notification with QR barcode
                $this->sendInvitationWhatsApp($participant, $invitation, 'invitation');
            }
        });

        if ($event->is_paid) {
            return redirect()->route('admin.participants.index')->with('success', 'Participant added. Status is Pending Payment. Please check Finance.');
        } else {
            return redirect()->route('admin.participants.index')->with('success', 'Participant added and Invitation code generated & Sent to WA.');
        }
    }

    public function edit($id)
    {
        $participant = Participant::with('unitRelation')->findOrFail($id);
        $events = \App\Models\Event::all();
        $units = \App\Models\Unit::orderBy('name')->get();
        $eventSessions = \Modules\Session\Models\EventSession::where('event_id', $participant->event_id)->orderBy('start_datetime')->get(['id', 'title']);
        return view('admin.participants.edit', compact('participant', 'events', 'units', 'eventSessions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $participant = Participant::findOrFail($id);

        // Seat Uniqueness Check
        if ($request->seat_number) {
            $exists = Participant::where('event_id', $participant->event_id)
                ->where('seat_number', $request->seat_number)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Nomor kursi sudah digunakan oleh peserta lain di event ini.');
            }
        }

        $participant->update($request->all());

        return redirect()->route('admin.participants.index')->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function show($id)
    {
        $participant = Participant::with(['event', 'invitation', 'payments', 'certificates'])->findOrFail($id);

        // Generate QR Code
        $qrcode = '';
        if ($participant->invitation) {
            $qrcode = QrCode::size(200)->generate($participant->invitation->unique_code);
        }

        return view('admin.participants.show', compact('participant', 'qrcode'));
    }

    public function approve($id)
    {
        $participant = Participant::with(['event', 'invitation'])->findOrFail($id);

        if ($participant->status === 'approved') {
            return redirect()->back()->with('success', 'Peserta sudah dalam status disetujui.');
        }

        DB::transaction(function () use ($participant) {
            $participant->update(['status' => 'approved']);

            $invitation = $participant->invitation;

            if (!$invitation) {
                $uniqueCode = 'EVT' . $participant->event_id . strtoupper(\Illuminate\Support\Str::random(6));

                $invitation = \App\Models\Invitation::create([
                    'event_id'       => $participant->event_id,
                    'participant_id' => $participant->id,
                    'unique_code'    => $uniqueCode,
                    'is_active'      => true,
                    'sent_via'       => 'system',
                    'sent_at'        => now(),
                ]);
            } else {
                $uniqueCode = $invitation->unique_code;
                $invitation->update([
                    'sent_via' => 'system',
                    'sent_at'  => now(),
                ]);
            }

            // Generate QR Code PNG image
            $qr = QrCodeImageService::generateAndSave($uniqueCode);

            // Compose WhatsApp message
            $event   = $participant->event;
            $message = "*KONFIRMASI PENDAFTARAN EVENT*\n\n";
            $message .= "Halo *{$participant->name}*,\n\n";
            $message .= "Selamat! Pendaftaran Anda telah *DISETUJUI* oleh panitia.\n\n";
            $message .= "*Detail Event:*\n";
            $message .= "📌 Event : {$event->title}\n";
            if ($event->event_date ?? $event->date ?? null) {
                $eventDate = $event->event_date ?? $event->date;
                $message .= "📅 Tanggal: " . \Carbon\Carbon::parse($eventDate)->translatedFormat('l, d F Y') . "\n";
            }
            if ($event->location ?? $event->venue ?? null) {
                $message .= "📍 Lokasi : " . ($event->location ?? $event->venue) . "\n";
            }
            $message .= "\n*Kode Undangan Anda:*\n";
            $message .= "🎫 *{$uniqueCode}*\n\n";
            $message .= "📷 *QR Code terlampir adalah kartu akses Anda.*\n\n";
            $message .= \App\Services\AttendanceService::selfHintText($event, $uniqueCode) . "\n\n";
            $message .= "🔗 Info lengkap event: " . ($event->slug ? route('events.show', $event->slug) : url('/')) . "\n\n";
            $message .= "Sampai jumpa di acara! 🎉";

            // Send WhatsApp with QR image. Use the public URL, not the local
            // path: the WA gateway runs under a different host user and only
            // allows reading local files under its own domain's directory.
            \App\Services\WhatsAppService::sendWithImage(
                $participant,
                'invitation_qr',
                $message,
                null,
                $qr['url'],
                "QR Code Akses — {$event->title} | Kode: {$uniqueCode}"
            );

            if ($participant->email) {
                try {
                    Mail::to($participant->email)->send(new EventInvitationMail($invitation));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Gagal mengirim email undangan saat approve peserta: ' . $e->getMessage(), [
                        'participant_id' => $participant->id,
                        'email' => $participant->email,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Peserta berhasil di-approve dan undangan dikirim.');
    }

    public function updateSeatNumber(Request $request, $id)
    {
        $request->validate([
            'seat_number' => 'nullable|string|max:20',
        ]);

        $participant = Participant::findOrFail($id);

        // Seat Uniqueness Check
        if ($request->seat_number) {
            $exists = Participant::where('event_id', $participant->event_id)
                ->where('seat_number', $request->seat_number)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor kursi sudah digunakan oleh peserta lain.'
                ], 422);
            }
        }

        $participant->update(['seat_number' => $request->seat_number]);

        return response()->json(['success' => true, 'message' => 'Nomor kursi tersimpan.']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'file' => 'required|mimes:csv,txt',
            'status_option' => 'nullable|in:auto,approved,pending',
            'default_session_id' => 'nullable|integer',
        ]);

        $event = \App\Models\Event::findOrFail($request->event_id);

        // Sesi event: dipakai untuk mencocokkan kolom "sesi" di CSV (nomor urut atau judul) dan sesi bawaan.
        $eventSessions = \Modules\Session\Models\EventSession::where('event_id', $event->id)->orderBy('start_datetime')->orderBy('id')->get();
        $defaultSessionId = $request->input('default_session_id') ?: null;
        if ($defaultSessionId && !$eventSessions->contains('id', (int) $defaultSessionId)) {
            return redirect()->back()->with('error', 'Sesi bawaan yang dipilih bukan milik event ini.');
        }
        $sessionByNumber = $eventSessions->values()->mapWithKeys(fn ($s, $i) => [(string) ($i + 1) => $s->id]);
        $sessionByTitle = $eventSessions->mapWithKeys(fn ($s) => [strtolower(trim($s->title)) => $s->id]);
        $file = $request->file('file');
        $statusOption = $request->input('status_option', 'auto');
        // Kotak centang yang tidak dicentang TIDAK ikut dikirim browser, jadi "tidak ada" = jangan kirim.
        // (Nilai bawaan true di sini dulu membuat WhatsApp tetap terkirim walau kotaknya dikosongkan.)
        $sendWa = $request->boolean('send_wa');
        $waMessageTemplate = trim((string) $request->input('wa_message', '')) ?: null;

        // Map recognized header aliases (lowercase) to the participant field they fill.
        $columnAliases = [
            'nama' => 'name', 'name' => 'name',
            'email' => 'email',
            'whatsapp' => 'whatsapp', 'no_whatsapp' => 'whatsapp', 'wa' => 'whatsapp',
            'tipe' => 'type', 'type' => 'type',
            'nim' => 'nim',
            'program_studi' => 'program_studi', 'prodi' => 'program_studi',
            'fakultas' => 'fakultas',
            'jabatan' => 'jabatan',
            'unit' => 'unit',
            'no_kursi' => 'seat_number', 'nomor_kursi' => 'seat_number', 'seat_number' => 'seat_number', 'kursi' => 'seat_number',
            'sesi' => 'session', 'session' => 'session',
            'kode_registrasi' => 'registration_code', 'kode' => 'registration_code', 'registration_code' => 'registration_code',
        ];

        $handle = fopen($file->getRealPath(), "r");
        $rawHeader = fgetcsv($handle);
        $header = array_map(fn ($h) => $columnAliases[strtolower(trim((string) $h))] ?? null, $rawHeader);

        // Preload units so "unit" values in the file (matched by name) resolve to unit_id.
        $units = \App\Models\Unit::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id]);

        $imported = 0;
        $errors = 0;
        $currentCount = $event->participants()->count();

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== FALSE) {
                $data = [];
                foreach ($header as $index => $field) {
                    if ($field !== null) {
                        $data[$field] = trim((string) ($row[$index] ?? ''));
                    }
                }

                $name = $data['name'] ?? '';
                $email = $data['email'] ?? '';
                $whatsapp = $data['whatsapp'] ?? '';

                if ($name === '' || $whatsapp === '') {
                    continue; // Skip invalid rows
                }

                $type = ($data['type'] ?? '') ?: 'umum';
                $unitId = isset($data['unit']) && $data['unit'] !== ''
                    ? ($units[strtolower($data['unit'])] ?? null)
                    : null;
                $seatNumber = $data['seat_number'] ?? '';

                // Sesi: kolom "sesi" (nomor urut 1,2,.. atau judul sesi) menimpa sesi bawaan; kosong = sesi bawaan.
                $sessionId = $defaultSessionId;
                $sessionRaw = strtolower($data['session'] ?? '');
                if ($sessionRaw !== '') {
                    $sessionId = $sessionByNumber[$sessionRaw] ?? $sessionByTitle[$sessionRaw] ?? null;
                    if (!$sessionId) {
                        $errors++; // sesi tidak dikenali: lewati agar tidak salah masuk sesi
                        continue;
                    }
                }
                $registrationCode = $data['registration_code'] ?? '';

                // Check duplicate email in this event
                $exists = $email !== '' && \App\Models\Participant::where('event_id', $event->id)
                    ->where('email', $email)
                    ->exists();

                // Seat number and registration code must each be unique;
                // skip the row rather than silently overwrite another
                // participant's seat or fail the whole import on a DB
                // unique-constraint violation.
                $seatTaken = $seatNumber !== '' && \App\Models\Participant::where('event_id', $event->id)
                    ->where('seat_number', $seatNumber)
                    ->exists();
                $codeTaken = $registrationCode !== '' && \App\Models\Invitation::where('unique_code', $registrationCode)->exists();

                if (!$exists && !$seatTaken && !$codeTaken) {
                    if ($event->participant_quota && $currentCount >= $event->participant_quota) {
                        $errors++;
                        continue;
                    }

                    if ($statusOption === 'approved') {
                        $status = 'approved';
                    } elseif ($statusOption === 'pending') {
                        $status = $event->is_paid ? 'pending_payment' : 'pending';
                    } else { // auto: follow the event's payment setting
                        $status = $event->is_paid ? 'pending_payment' : 'approved';
                    }

                    $participant = \App\Models\Participant::create([
                        'event_id' => $event->id,
                        'session_id' => $sessionId,
                        'name' => $name,
                        'email' => $email ?: null,
                        'whatsapp' => $whatsapp,
                        'type' => $type, // umum, mahasiswa, dosen, tendik
                        'nim' => ($data['nim'] ?? '') ?: null,
                        'program_studi' => ($data['program_studi'] ?? '') ?: null,
                        'fakultas' => ($data['fakultas'] ?? '') ?: null,
                        'jabatan' => ($data['jabatan'] ?? '') ?: null,
                        'unit_id' => $unitId,
                        'seat_number' => $seatNumber ?: null,
                        'status' => $status,
                    ]);

                    if ($status === 'pending_payment') {
                        // Create Payment Record (Pending) — awaiting payment confirmation
                        \App\Models\Payment::create([
                            'event_id' => $event->id,
                            'participant_id' => $participant->id,
                            'amount' => $event->price,
                            'status' => 'pending',
                        ]);
                    } elseif ($status === 'approved') {
                        // Use the registration code supplied in the CSV, if any; otherwise generate one.
                        $uniqueCode = $registrationCode !== '' ? $registrationCode : ('EVT' . $event->id . strtoupper(\Illuminate\Support\Str::random(6)));
                        $invitation = $participant->invitation()->create([
                            'event_id' => $event->id,
                            'unique_code' => $uniqueCode,
                            'is_active' => true,
                            'sent_via' => 'manual',
                        ]);

                        if ($sendWa) {
                            // Lewat queue: ratusan peserta tidak muat dalam batas waktu request PHP.
                            // Dikirim setelah transaksi impor commit, supaya data peserta sudah ada.
                            \App\Jobs\SendInvitationWhatsAppJob::dispatch($participant->id, $invitation->id, 'invitation', $waMessageTemplate)->afterCommit();
                        }
                    }
                    // status === 'pending': no invitation/WA yet — admin approves
                    // manually later (button "Setujui"), which generates the
                    // invitation and sends the WA at that point.

                    $imported++;
                    $currentCount++;
                } else {
                    $errors++;
                }
            }
            \Illuminate\Support\Facades\DB::commit();
            fclose($handle);

            return redirect()->route('admin.participants.index')->with('success', "Import selesai! $imported data berhasil diimport. $errors data duplikat/dilewati."
                . ($sendWa ? ' WhatsApp dikirim bertahap di latar belakang; pantau di Log WhatsApp.' : ' WhatsApp tidak dikirim; kirim nanti lewat tombol WhatsApp pada tiap peserta.'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
    /**
     * Download a blank CSV template matching the columns expected by import().
     */
    public function downloadImportTemplate()
    {
        $columns = ['nama', 'email', 'whatsapp', 'tipe', 'nim', 'program_studi', 'fakultas', 'jabatan', 'unit', 'no_kursi', 'sesi', 'kode_registrasi'];
        $examples = [
            ['Budi Santoso', 'budi.santoso@example.com', '081234567890', 'mahasiswa', '2110631010001', 'Teknik Sipil', 'Fakultas Teknik', '', 'Prodi Teknik Sipil', 'A1', '1', ''],
            ['Dr. Siti Aminah', 'siti.aminah@example.com', '081298765432', 'dosen', '', '', 'Fakultas Ekonomi & Bisnis', 'Dosen Manajemen', 'Prodi Manajemen', 'A2', '2', 'EVT4VIP001'],
            ['Andi Wijaya', 'andi.wijaya@example.com', '081211223344', 'umum', '', '', '', '', '', '', '', ''],
        ];

        $callback = function () use ($columns, $examples) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($examples as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, 'template_import_peserta.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function destroy($id)
    {
        \App\Models\Participant::findOrFail($id)->delete();
        return redirect()->route('admin.participants.index')->with('success', 'Participant deleted.');
    }

    /**
     * Resend email invitation to participant
     */
    public function resendEmail($id)
    {
        $participant = Participant::with(['event', 'invitation'])->findOrFail($id);

        if (!$participant->email) {
            return redirect()->back()->with('error', 'Peserta tidak memiliki alamat email.');
        }

        if (!$participant->invitation) {
            return redirect()->back()->with('error', 'Peserta tidak memiliki undangan yang dapat dikirim ulang.');
        }

        try {
            Mail::to($participant->email)->send(new EventInvitationMail($participant->invitation));
            return redirect()->back()->with('success', 'Email undangan berhasil dikirim ulang ke ' . $participant->email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    /**
     * Resend WhatsApp invitation to participant
     */
    public function resendWhatsApp($id)
    {
        $participant = Participant::with(['event', 'invitation'])->findOrFail($id);

        if (empty($participant->whatsapp)) {
            return redirect()->back()->with('error', 'Peserta tidak memiliki nomor WhatsApp.');
        }

        try {
            $invitation = $participant->invitation;

            // Peserta lama (mis. dari pendaftaran publik sebelum perbaikan) bisa
            // berstatus approved tapi belum punya undangan/kode QR sama sekali.
            if (!$invitation) {
                $uniqueCode = 'EVT' . $participant->event_id . strtoupper(\Illuminate\Support\Str::random(6));
                $invitation = \App\Models\Invitation::create([
                    'event_id'       => $participant->event_id,
                    'participant_id' => $participant->id,
                    'unique_code'    => $uniqueCode,
                    'is_active'      => true,
                    'sent_via'       => 'system',
                    'sent_at'        => now(),
                ]);
            }

            $event = $participant->event;

            $message = "*KONFIRMASI PENDAFTARAN EVENT*\n\n";
            $message .= "Halo *{$participant->name}*,\n\n";
            $message .= "Terima kasih telah mendaftar pada event:\n";
            $message .= "*{$event->title}*\n\n";
            $message .= "Berikut adalah Kode Undangan / Kartu Akses Anda:\n";
            $message .= "Kode: *{$invitation->unique_code}*\n\n";
            $message .= \App\Services\AttendanceService::selfHintText($event, $invitation->unique_code) . "\n\n";
            $message .= "🔗 Info lengkap event: " . ($event->slug ? route('events.show', $event->slug) : url('/')) . "\n\n";
            $message .= "Sampai jumpa di lokasi acara!";

            $qr = QrCodeImageService::generateAndSave($invitation->unique_code);

            \App\Services\WhatsAppService::sendWithImage(
                $participant,
                'invitation_resend',
                $message,
                null,
                $qr['url'],
                "QR Code Akses — {$event->title} | Kode: {$invitation->unique_code}"
            );

            return redirect()->back()->with('success', 'WhatsApp berhasil dikirim ulang ke ' . $participant->whatsapp);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Send the invitation WhatsApp message: QR barcode image, registration
     * code, event info, and a link to the public event page. Shared by
     * store(), import(), approve() and resendWhatsApp() so every path that
     * issues an invitation notifies the participant the same way.
     */
    private function sendInvitationWhatsApp(Participant $participant, \App\Models\Invitation $invitation, string $waType, ?string $template = null): void
    {
        \App\Services\InvitationNotifier::sendWhatsApp($participant, $invitation, $waType, $template);
    }

    private function buildInvitationMessage(Participant $participant, \App\Models\Invitation $invitation, ?string $template = null): string
    {
        return \App\Services\InvitationNotifier::buildMessage($participant, $invitation, $template);
    }
}
