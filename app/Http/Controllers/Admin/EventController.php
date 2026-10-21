<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = Event::query()->withCount('participants');

        if ($request->has('type')) {
            $query->where('event_type', $request->type);
        }

        $events = $query->latest()->get();

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $categories = EventCategory::where('is_active', true)->get();
        return view('admin.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'event_category_id' => 'required|exists:event_categories,id',
            'participant_quota' => 'nullable|integer|min:1',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required',
            'location_link' => 'nullable|url',
            'description' => 'required',
            'price' => 'numeric|min:0',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rundown.*.time_start' => 'required',
            'rundown.*.time_end' => 'required',
            'rundown.*.agenda' => 'required',
        ]);

        $imagePath = null;
        if ($request->hasFile('banner_image')) {
            $imagePath = $request->file('banner_image')->store('event_banners', 'public');
        }

        $category = EventCategory::find($request->event_category_id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $imagePath, $category) {
            $event = Event::create([
                'slug' => \Illuminate\Support\Str::slug($request->title) . '-' . time(),
                'title' => $request->title,
                'category_id' => $category->id,
                'event_type' => $category->slug,
                'participant_quota' => $request->participant_quota,
                'description' => $request->description,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
                'venue_name' => $request->location,
                'location_link' => $request->location_link,
                'meeting_link' => $request->meeting_link,
                'organizer_unit' => $request->organizer_unit,
                'contact_whatsapp' => $request->contact_whatsapp,
                'wa_pertanian' => $request->wa_pertanian,
                'wa_kehutanan' => $request->wa_kehutanan,
                'wa_teknik' => $request->wa_teknik,
                'wa_ekonomi' => $request->wa_ekonomi,
                'banner_image' => $imagePath,
                'is_internal' => $request->has('is_internal'),
                'is_paid' => $request->has('is_paid'),
                'price' => $request->has('is_paid') ? ($request->price ?? 0) : 0,
                'allow_certificate' => $request->has('allow_certificate'),
                'has_seat_number' => $request->has('has_seat_number'),
                'rules' => $request->rules,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            if ($request->has('rundown')) {
                foreach ($request->rundown as $index => $item) {
                    $event->rundowns()->create([
                        'time_start' => $item['time_start'],
                        'time_end' => $item['time_end'],
                        'agenda' => $item['agenda'],
                        'order_number' => $index + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Event $event)
    {
        $event->load('rundowns');
        $categories = EventCategory::where('is_active', true)->get();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {

        $validated = $request->validate([
            'title' => 'required',
            'event_category_id' => 'required|exists:event_categories,id',
            'participant_quota' => 'nullable|integer|min:1',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required',
            'location_link' => 'nullable|url',
            'description' => 'required',
            'price' => 'numeric|min:0',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rundown.*.time_start' => 'required',
            'rundown.*.time_end' => 'required',
            'rundown.*.agenda' => 'required',
            'status' => 'required|in:draft,published,closed',
        ]);

        $imagePath = $event->banner_image;
        if ($request->hasFile('banner_image')) {
            // Delete old image if exists
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('banner_image')->store('event_banners', 'public');
        }

        $category = EventCategory::find($request->event_category_id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $event, $imagePath, $category) {
            $event->update([
                'title' => $request->title,
                'category_id' => $category->id,
                'event_type' => $category->slug,
                'participant_quota' => $request->participant_quota,
                'description' => $request->description,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
                'venue_name' => $request->location,
                'location_link' => $request->location_link,
                'meeting_link' => $request->meeting_link,
                'organizer_unit' => $request->organizer_unit,
                'contact_whatsapp' => $request->contact_whatsapp,
                'wa_pertanian' => $request->wa_pertanian,
                'wa_kehutanan' => $request->wa_kehutanan,
                'wa_teknik' => $request->wa_teknik,
                'wa_ekonomi' => $request->wa_ekonomi,
                'banner_image' => $imagePath,
                'is_internal' => $request->has('is_internal'),
                'is_paid' => $request->has('is_paid'),
                'price' => $request->has('is_paid') ? ($request->price ?? 0) : 0,
                'allow_certificate' => $request->has('allow_certificate'),
                'has_seat_number' => $request->has('has_seat_number'),
                'rules' => $request->rules,
                'status' => $request->status,
            ]);

            // Sync Rundowns (Delete All & Recreate)
            $event->rundowns()->delete();

            if ($request->has('rundown')) {
                foreach ($request->rundown as $index => $item) {
                    $event->rundowns()->create([
                        'time_start' => $item['time_start'],
                        'time_end' => $item['time_end'],
                        'agenda' => $item['agenda'],
                        'order_number' => $index + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully');
    }

    public function show(Event $event)
    {
        $event->load(['rundowns', 'participants']);
        return view('admin.events.show', compact('event'));
    }

    public function previewDetail($slug)
    {
        if (!request()->hasValidSignature()) {
            abort(403, 'Link preview tidak valid atau sudah kedaluwarsa.');
        }

        $event = Event::where('slug', $slug)->with(['rundowns', 'participants'])->firstOrFail();
        return view('admin.events.preview', compact('event'));
    }
}
