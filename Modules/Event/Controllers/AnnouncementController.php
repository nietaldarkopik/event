<?php
namespace Modules\Event\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Event\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $items = Announcement::when($request->filled('event_id'), fn($q) => $q->where('event_id', $request->event_id))
            ->latest()->get();
        $selectedEvent = $request->filled('event_id') ? \Modules\Event\Models\Event::find($request->event_id) : null;
        return view('admin.announcements.index', compact('items', 'selectedEvent'));
    }

    public function create()
    {
        $events = \Modules\Event\Models\Event::all();

        return view('admin.announcements.create', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'title' => 'required',
  'content' => 'required',
  'published_at' => 'nullable',
));

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show');
    }

    public function edit(Announcement $announcement)
    {
        $events = \Modules\Event\Models\Event::all();

        return view('admin.announcements.edit', compact('events'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'title' => 'required',
  'content' => 'required',
  'published_at' => 'nullable',
));

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
