<?php
namespace Modules\Category\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Category\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function index()
    {
        $items = EventCategory::latest()->get();
        return view('admin.event_categories.index', compact('items'));
    }

    public function create()
    {

        return view('admin.event_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'name' => 'required',
  'icon' => 'nullable',
  'color' => 'nullable',
));

        EventCategory::create($validated);

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Kategori Event berhasil ditambahkan.');
    }

    public function show(EventCategory $event_category)
    {
        return redirect()->route('admin.event-categories.index');
    }

    public function edit(EventCategory $event_category)
    {
        return view('admin.event_categories.edit', ['eventcategory' => $event_category]);
    }

    public function update(Request $request, EventCategory $event_category)
    {
        $validated = $request->validate(array (
  'name' => 'required',
  'icon' => 'nullable',
  'color' => 'nullable',
));

        $event_category->update($validated);

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Kategori Event berhasil diperbarui.');
    }

    public function destroy(EventCategory $event_category)
    {
        $event_category->delete();

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Kategori Event berhasil dihapus.');
    }
}
