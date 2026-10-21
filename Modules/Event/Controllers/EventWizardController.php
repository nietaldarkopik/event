<?php
namespace Modules\Event\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Authentication\Models\User;
use Modules\Category\Models\EventCategory;
use Modules\Event\Models\Event;
use Modules\Event\Models\EventCommittee;
use Modules\Organization\Models\Organization;
use Modules\Session\Models\EventSession;
use Modules\Speaker\Models\Speaker;
use Modules\Sponsor\Models\Sponsor;
use Modules\Ticket\Models\TicketType;

/**
 * Wizard input event dengan auto-save.
 *
 * Tiap step menyimpan datanya sendiri-sendiri lewat AJAX, tanpa menunggu data lengkap:
 *  - field event (step 1, 2, 4)     -> PATCH save(), parsial, hanya field yang dikirim
 *  - baris relasi (sesi, tiket, dst) -> POST saveItem(), per baris, upsert
 * Event dibuat sebagai DRAFT begitu judul terisi, sehingga bisa dilanjutkan kapan saja.
 */
class EventWizardController extends Controller
{
    /** Field event yang boleh disimpan wizard => rule validasi (semua parsial/opsional). */
    private function eventRules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|nullable|exists:event_categories,id',
            'organization_id' => 'sometimes|nullable|exists:organizations,id',
            'short_description' => 'sometimes|nullable|string|max:1000',
            'description' => 'sometimes|nullable|string',
            'rules' => 'sometimes|nullable|string',
            'organizer_unit' => 'sometimes|nullable|string|max:191',
            'contact_whatsapp' => 'sometimes|nullable|string|max:191',
            'venue_name' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string',
            'city' => 'sometimes|nullable|string|max:100',
            'province' => 'sometimes|nullable|string|max:100',
            'location_link' => 'sometimes|nullable|url',
            'meeting_link' => 'sometimes|nullable|url|max:191',
            'registration_start' => 'sometimes|nullable|date',
            'registration_end' => 'sometimes|nullable|date',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date',
            'max_participants' => 'sometimes|nullable|integer|min:1',
            'certificate_enabled' => 'sometimes|boolean',
            'attendance_required' => 'sometimes|boolean',
            'visibility' => 'sometimes|in:PUBLIC,PRIVATE',
            'session_mode' => 'sometimes|in:none,admin,choose',
            'invitation_template_id' => 'sometimes|nullable|exists:invitation_templates,id',
        ];
    }

    public function create()
    {
        return $this->render(new Event());
    }

    public function edit(Event $event)
    {
        return $this->render($event);
    }

    private function render(Event $event)
    {
        $exists = $event->exists;

        return view('admin.events.wizard', [
            'event' => $event,
            'categories' => EventCategory::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
            'speakers' => Speaker::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'templates' => \App\Models\InvitationTemplate::where('is_active', true)->orderBy('name')->get(['id', 'name', 'category']),
            'sessions' => $exists ? $event->sessions()->with('speakers:id')->orderBy('start_datetime')->get() : collect(),
            'tickets' => $exists ? $event->ticketTypes()->orderBy('id')->get() : collect(),
            'sponsors' => $exists ? $event->sponsors()->orderBy('id')->get() : collect(),
            'committees' => $exists ? $event->committees()->with('user')->orderBy('sort_order')->orderBy('id')->get() : collect(),
        ]);
    }

    /** Buat draft event begitu judul ada. Field lain yang sudah terisi ikut disimpan. */
    public function start(Request $request)
    {
        $rules = $this->eventRules();
        $rules['title'] = 'required|string|max:255';
        $data = $request->validate($rules);
        $this->assertDateOrder($data);

        $event = new Event();
        $event->forceFill($this->normalize($data));
        $event->uuid = (string) Str::uuid();
        $event->slug = $this->uniqueSlug($data['title']);
        $event->status = 'DRAFT';
        $event->visibility = $data['visibility'] ?? 'PUBLIC';
        $event->created_by = auth()->id();
        $event->save();

        return response()->json([
            'id' => $event->id,
            'saved_at' => now()->format('H:i:s'),
        ]);
    }

    /** Simpan parsial field event. */
    public function save(Request $request, Event $event)
    {
        $data = $request->validate($this->eventRules());
        $this->assertDateOrder($data, $event);

        $event->forceFill($this->normalize($data))->save();

        return response()->json(['saved_at' => now()->format('H:i:s')]);
    }

    public function banner(Request $request, Event $event)
    {
        $request->validate([
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($event->banner_image) {
            Storage::disk('public')->delete($event->banner_image);
        }
        $event->banner_image = $request->file('banner_image')->store('event_banners', 'public');
        $event->save();

        return response()->json([
            'url' => asset('storage/' . $event->banner_image),
            'saved_at' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Tambah data master baru langsung dari wizard (pembicara, kategori, organisasi) tanpa pindah menu.
     * Bila nama yang sama sudah ada, data yang ada dikembalikan (tidak dibuat dobel).
     */
    public function storeRelation(Request $request, string $kind)
    {
        switch ($kind) {
            case 'speaker':
                $data = $request->validate([
                    'name' => 'required|string|max:150',
                    'company' => 'nullable|string|max:150',
                    'title' => 'nullable|string|max:150',
                ]);
                $row = Speaker::whereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])])->first();
                if (! $row) {
                    $row = new Speaker($data);
                    $row->uuid = (string) Str::uuid();
                    $row->save();
                }
                break;

            case 'category':
                $data = $request->validate(['name' => 'required|string|max:100']);
                $row = EventCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])])->first();
                if (! $row) {
                    $slug = Str::slug($data['name'], '_') ?: 'kategori';
                    $base = $slug;
                    $i = 1;
                    while (EventCategory::where('slug', $slug)->exists()) {
                        $slug = $base . '_' . $i++;
                    }
                    $row = new EventCategory();
                    $row->forceFill(['name' => $data['name'], 'slug' => $slug, 'is_active' => 1, 'icon' => 'fa-calendar-alt', 'color' => '#15803d'])->save();
                }
                break;

            case 'organization':
                $data = $request->validate([
                    'name' => 'required|string|max:200',
                    'email' => 'nullable|email|max:150',
                    'phone' => 'nullable|string|max:50',
                ]);
                $row = Organization::whereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])])->first();
                if (! $row) {
                    $row = new Organization($data);
                    $row->uuid = (string) Str::uuid();
                    $row->save();
                }
                break;

            default:
                abort(404);
        }

        return response()->json(['id' => $row->id, 'name' => $row->name, 'existing' => ! $row->wasRecentlyCreated]);
    }

    /** Upload gambar dari editor teks kaya (tombol gambar, drag & drop, atau tempel). */
    public function uploadImage(Request $request)
    {
        $request->validate(['file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072']);

        $path = $request->file('file')->store('event_content/' . date('Y/m'), 'public');

        return response()->json(['location' => asset('storage/' . $path)]);
    }

    /** Upsert satu baris relasi (sesi / tiket / sponsor / panitia). */
    public function saveItem(Request $request, Event $event, string $type)
    {
        $cfg = $this->itemConfig($type);
        $data = $request->validate($cfg['rules']);
        unset($data['logo']); // file diproses terpisah di bawah

        $item = $request->filled('id')
            ? $cfg['model']::where('event_id', $event->id)->findOrFail($request->input('id'))
            : new $cfg['model'](['event_id' => $event->id]);

        if ($type === 'sessions' && array_key_exists('description', $data)) {
            $data['description'] = \App\Support\Html::clean($data['description']);
        }

        $item->fill($data);
        $item->event_id = $event->id;
        if ($type === 'committees' && ! $item->exists) {
            $item->sort_order = (int) EventCommittee::where('event_id', $event->id)->max('sort_order') + 1;
        }

        if ($type === 'sponsors' && $request->hasFile('logo')) {
            if ($item->logo) {
                Storage::disk('public')->delete($item->logo);
            }
            $item->logo = $request->file('logo')->store('sponsor_logos', 'public');
        }

        $item->save();

        if ($type === 'sessions') {
            $item->speakers()->sync($request->input('speaker_ids') ?: []);
        }

        return response()->json([
            'id' => $item->id,
            'logo_url' => ($type === 'sponsors' && $item->logo) ? asset('storage/' . $item->logo) : null,
            'saved_at' => now()->format('H:i:s'),
        ]);
    }

    public function deleteItem(Event $event, string $type, int $id)
    {
        $cfg = $this->itemConfig($type);
        $item = $cfg['model']::where('event_id', $event->id)->findOrFail($id);

        if ($type === 'sessions') {
            $item->speakers()->detach();
        }
        if ($type === 'sponsors' && $item->logo) {
            Storage::disk('public')->delete($item->logo);
        }
        $item->delete();

        return response()->json(['saved_at' => now()->format('H:i:s')]);
    }

    /** Publikasi hanya bila data minimal lengkap; selain itu tetap DRAFT. */
    public function publish(Event $event)
    {
        $missing = [];
        if (! $event->title) { $missing[] = 'Judul event'; }
        if (! $event->category_id) { $missing[] = 'Kategori'; }
        if (! $event->start_date) { $missing[] = 'Tanggal mulai'; }
        if (! $event->end_date) { $missing[] = 'Tanggal selesai'; }
        if (! $event->venue_name && ! $event->address) { $missing[] = 'Lokasi'; }

        if ($missing) {
            return response()->json([
                'message' => 'Data belum lengkap: ' . implode(', ', $missing),
                'missing' => $missing,
            ], 422);
        }

        $event->status = 'PUBLISHED';
        $event->save();

        return response()->json([
            'redirect' => route('admin.events.index'),
            'message' => 'Event berhasil dipublikasikan.',
        ]);
    }

    private function itemConfig(string $type): array
    {
        switch ($type) {
            case 'sessions':
                return [
                    'model' => EventSession::class,
                    'rules' => [
                        'id' => 'nullable|integer',
                        'title' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'room' => 'nullable|string|max:150',
                        'start_datetime' => 'nullable|date',
                        'end_datetime' => 'nullable|date',
                        'max_participants' => 'nullable|integer|min:1',
                        'speaker_ids' => 'nullable|array',
                        'speaker_ids.*' => 'integer|exists:speakers,id',
                    ],
                ];
            case 'tickets':
                return [
                    'model' => TicketType::class,
                    'rules' => [
                        'id' => 'nullable|integer',
                        'name' => 'required|string|max:100',
                        'price' => 'nullable|numeric|min:0',
                        'quota' => 'nullable|integer|min:1',
                        'sale_start' => 'nullable|date',
                        'sale_end' => 'nullable|date',
                        'description' => 'nullable|string',
                    ],
                ];
            case 'sponsors':
                return [
                    'model' => Sponsor::class,
                    'rules' => [
                        'id' => 'nullable|integer',
                        'name' => 'required|string|max:255',
                        'website' => 'nullable|url|max:255',
                        'level' => 'nullable|in:GOLD,SILVER,BRONZE,MEDIA',
                        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                    ],
                ];
            case 'committees':
                return [
                    'model' => EventCommittee::class,
                    'rules' => [
                        'id' => 'nullable|integer',
                        'name' => 'required|string|max:200',
                        'unit' => 'nullable|string|max:150',
                        'section' => 'nullable|string|max:120',
                        'position' => 'nullable|string|max:100',
                    ],
                ];
        }

        abort(404);
    }

    private function normalize(array $data): array
    {
        // events.event_type menentukan perilaku khusus (mis. wisuda); selalu mengikuti slug kategori.
        if (array_key_exists('category_id', $data)) {
            $cat = $data['category_id'] ? EventCategory::find($data['category_id']) : null;
            $data['event_type'] = $cat ? $cat->slug : null;
        }

        // Isi editor teks kaya dibersihkan di server (daftar putih tag/atribut), bukan hanya di browser.
        foreach (['description', 'rules'] as $rich) {
            if (array_key_exists($rich, $data)) {
                $data[$rich] = \App\Support\Html::clean($data[$rich]);
            }
        }

        foreach (['certificate_enabled', 'attendance_required'] as $flag) {
            if (array_key_exists($flag, $data)) {
                $data[$flag] = (bool) $data[$flag];
            }
        }
        // Sistem sertifikat membaca `allow_certificate`, halaman publik membaca keduanya: jaga agar selalu sama.
        if (array_key_exists('certificate_enabled', $data)) {
            $data['allow_certificate'] = $data['certificate_enabled'];
        }

        return $data;
    }

    /** Tanggal selesai tidak boleh sebelum tanggal mulai (dicek hanya bila keduanya terisi). */
    private function assertDateOrder(array $data, ?Event $event = null): void
    {
        $pairs = [['start_date', 'end_date'], ['registration_start', 'registration_end']];

        foreach ($pairs as [$from, $to]) {
            $a = array_key_exists($from, $data) ? $data[$from] : optional($event)->$from;
            $b = array_key_exists($to, $data) ? $data[$to] : optional($event)->$to;

            if ($a && $b && strtotime((string) $b) < strtotime((string) $a)) {
                throw ValidationException::withMessages([
                    $to => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
                ]);
            }
        }
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'event';
        $slug = $base;
        $i = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $base . '_' . $i++;
        }

        return $slug;
    }
}
