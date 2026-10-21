<?php
namespace Modules\Session\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Session\Models\EventSession;
use Illuminate\Http\Request;

class EventSessionController extends Controller
{
    public function index(Request $request)
    {
        $items = EventSession::with(['event', 'speakers'])
            ->when($request->filled('event_id'), fn($q) => $q->where('event_id', $request->event_id))
            ->orderBy('start_datetime')->get();
        // jumlah peserta per sesi (kolom participants.session_id), untuk menampilkan keterisian
        $taken = \App\Models\Participant::whereNotNull('session_id')
            ->where(function ($q) { $q->whereNull('status')->orWhere('status', '!=', 'rejected'); })
            ->selectRaw('session_id, count(*) as c')->groupBy('session_id')->pluck('c', 'session_id');
        $events = \Modules\Event\Models\Event::orderBy('title')->get(['id', 'title']);
        $selectedEvent = $request->filled('event_id') ? \Modules\Event\Models\Event::find($request->event_id) : null;
        return view('admin.event_sessions.index', compact('items', 'selectedEvent', 'taken', 'events'));
    }

    public function create()
    {
        $events = \Modules\Event\Models\Event::all();

        return view('admin.event_sessions.create', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'title' => 'required',
  'description' => 'nullable',
  'room' => 'nullable',
  'start_datetime' => 'nullable',
  'end_datetime' => 'nullable',
  'max_participants' => 'nullable|numeric',
));

        EventSession::create($validated);

        return redirect()->route('admin.event-sessions.index')
            ->with('success', 'Sesi Event berhasil ditambahkan.');
    }

    public function show(EventSession $event_session)
    {
        return redirect()->route('admin.event-sessions.index');
    }

    public function edit(EventSession $event_session)
    {
        $events = \Modules\Event\Models\Event::all();
        $eventsession = $event_session;

        return view('admin.event_sessions.edit', compact('events', 'eventsession'));
    }

    public function update(Request $request, EventSession $event_session)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'title' => 'required',
  'description' => 'nullable',
  'room' => 'nullable',
  'start_datetime' => 'nullable',
  'end_datetime' => 'nullable',
  'max_participants' => 'nullable|numeric',
));

        $event_session->update($validated);

        return redirect()->route('admin.event-sessions.index')
            ->with('success', 'Sesi Event berhasil diperbarui.');
    }

    public function destroy(EventSession $event_session)
    {
        $event_session->delete();

        return redirect()->route('admin.event-sessions.index')
            ->with('success', 'Sesi Event berhasil dihapus.');
    }
}
