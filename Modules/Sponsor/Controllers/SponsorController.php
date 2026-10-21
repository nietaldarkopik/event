<?php
namespace Modules\Sponsor\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Sponsor\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index(Request $request)
    {
        $items = Sponsor::when($request->filled('event_id'), fn($q) => $q->where('event_id', $request->event_id))
            ->latest()->get();
        $selectedEvent = $request->filled('event_id') ? \Modules\Event\Models\Event::find($request->event_id) : null;
        return view('admin.sponsors.index', compact('items', 'selectedEvent'));
    }

    public function create()
    {
        $events = \Modules\Event\Models\Event::all();

        return view('admin.sponsors.create', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'name' => 'required',
  'logo' => 'nullable',
  'website' => 'nullable',
  'level' => 'required',
));

        Sponsor::create($validated);

        return redirect()->route('admin.sponsors.index')
            ->with('success', 'Sponsor berhasil ditambahkan.');
    }

    public function show(Sponsor $sponsor)
    {
        return redirect()->route('admin.sponsors.index');
    }

    public function edit(Sponsor $sponsor)
    {
        $events = \Modules\Event\Models\Event::all();

        return view('admin.sponsors.edit', compact('events'));
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'name' => 'required',
  'logo' => 'nullable',
  'website' => 'nullable',
  'level' => 'required',
));

        $sponsor->update($validated);

        return redirect()->route('admin.sponsors.index')
            ->with('success', 'Sponsor berhasil diperbarui.');
    }

    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();

        return redirect()->route('admin.sponsors.index')
            ->with('success', 'Sponsor berhasil dihapus.');
    }
}
