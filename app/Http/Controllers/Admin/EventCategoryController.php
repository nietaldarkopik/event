<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventCategoryController extends Controller
{
    public function index()
    {
        $categories = EventCategory::latest()->get();
        return view('admin.event_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.event_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:event_categories,slug',
            'description' => 'nullable|string',
        ]);

        EventCategory::create([
            'name' => $request->name,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Kategori Event berhasil ditambahkan.');
    }

    public function edit(EventCategory $eventCategory)
    {
        return view('admin.event_categories.edit', compact('eventCategory'));
    }

    public function update(Request $request, EventCategory $eventCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:event_categories,slug,' . $eventCategory->id,
            'description' => 'nullable|string',
        ]);

        $eventCategory->update([
            'name' => $request->name,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Kategori Event berhasil diperbarui.');
    }

    public function destroy(EventCategory $eventCategory)
    {
        // Check if used in events (optional, but good practice)
        // For now, just delete
        $eventCategory->delete();

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Kategori Event berhasil dihapus.');
    }
}
