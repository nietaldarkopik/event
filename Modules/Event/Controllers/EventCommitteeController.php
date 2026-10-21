<?php
namespace Modules\Event\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Event\Models\EventCommittee;
use Illuminate\Http\Request;

class EventCommitteeController extends Controller
{
    public function index()
    {
        $items = EventCommittee::with(['event', 'user'])->orderBy('event_id')->orderBy('sort_order')->orderBy('id')->get();
        return view('admin.event_committees.index', compact('items'));
    }

    public function create()
    {
        $events = \Modules\Event\Models\Event::all();
        $users = \Modules\Authentication\Models\User::all();

        return view('admin.event_committees.create', compact('events', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'user_id' => 'required',
  'position' => 'required',
));

        EventCommittee::create($validated);

        return redirect()->route('admin.event-committees.index')
            ->with('success', 'Panitia Event berhasil ditambahkan.');
    }

    public function show(EventCommittee $event_committee)
    {
        return redirect()->route('admin.event-committees.index');
    }

    public function edit(EventCommittee $event_committee)
    {
        $events = \Modules\Event\Models\Event::all();
        $users = \Modules\Authentication\Models\User::all();

        return view('admin.event_committees.edit', compact('event_committee', 'events', 'users'));
    }

    public function update(Request $request, EventCommittee $event_committee)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'user_id' => 'required',
  'position' => 'required',
));

        $event_committee->update($validated);

        return redirect()->route('admin.event-committees.index')
            ->with('success', 'Panitia Event berhasil diperbarui.');
    }

    public function destroy(EventCommittee $event_committee)
    {
        $event_committee->delete();

        return redirect()->route('admin.event-committees.index')
            ->with('success', 'Panitia Event berhasil dihapus.');
    }
}
