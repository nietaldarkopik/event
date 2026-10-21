<?php
namespace Modules\Speaker\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Speaker\Models\Speaker;
use Illuminate\Http\Request;

class SpeakerController extends Controller
{
    public function index()
    {
        $items = Speaker::withCount('sessions')->latest()->get();
        return view('admin.speakers.index', compact('items'));
    }

    public function create()
    {

        return view('admin.speakers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'name' => 'required',
  'company' => 'nullable',
  'title' => 'nullable',
  'photo' => 'nullable',
  'bio' => 'nullable',
  'linkedin' => 'nullable',
  'instagram' => 'nullable',
  'email' => 'nullable|email',
  'phone' => 'nullable',
));

        Speaker::create($validated);

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Pembicara berhasil ditambahkan.');
    }

    public function show(Speaker $speaker)
    {
        return redirect()->route('admin.speakers.index');
    }

    public function edit(Speaker $speaker)
    {

        return view('admin.speakers.edit');
    }

    public function update(Request $request, Speaker $speaker)
    {
        $validated = $request->validate(array (
  'name' => 'required',
  'company' => 'nullable',
  'title' => 'nullable',
  'photo' => 'nullable',
  'bio' => 'nullable',
  'linkedin' => 'nullable',
  'instagram' => 'nullable',
  'email' => 'nullable|email',
  'phone' => 'nullable',
));

        $speaker->update($validated);

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Pembicara berhasil diperbarui.');
    }

    public function destroy(Speaker $speaker)
    {
        $speaker->delete();

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Pembicara berhasil dihapus.');
    }
}
