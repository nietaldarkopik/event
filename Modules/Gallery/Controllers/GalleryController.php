<?php
namespace Modules\Gallery\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Gallery\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $items = Gallery::when($request->filled('event_id'), fn($q) => $q->where('event_id', $request->event_id))
            ->latest()->get();
        $selectedEvent = $request->filled('event_id') ? \Modules\Event\Models\Event::find($request->event_id) : null;
        return view('admin.galleries.index', compact('items', 'selectedEvent'));
    }

    public function create()
    {
        $events = \Modules\Event\Models\Event::all();

        return view('admin.galleries.create', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'title' => 'required',
  'image' => 'required',
  'sort_order' => 'nullable|numeric',
));

        Gallery::create($validated);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri Foto berhasil ditambahkan.');
    }

    public function show(Gallery $gallery)
    {
        return redirect()->route('admin.galleries.index');
    }

    public function edit(Gallery $gallery)
    {
        $events = \Modules\Event\Models\Event::all();

        return view('admin.galleries.edit', compact('events'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'title' => 'required',
  'image' => 'required',
  'sort_order' => 'nullable|numeric',
));

        $gallery->update($validated);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri Foto berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        $gallery->delete();

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri Foto berhasil dihapus.');
    }
}
