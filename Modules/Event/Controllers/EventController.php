<?php
namespace Modules\Event\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Event\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $items = Event::with(['organization', 'category', 'creator'])->withCount(['participantRecords as registrations_count' => function ($q) {
                $q->where(function ($w) { $w->whereNull('status')->orWhere('status', '!=', 'rejected'); });
            }])->latest()->get();
        return view('admin.events.index', compact('items'));
    }

    public function create()
    {
        $organizations = \Modules\Organization\Models\Organization::all();
        $categorys = \Modules\Category\Models\EventCategory::all();
        $creators = \Modules\Authentication\Models\User::all();

        return view('admin.events.create', compact('organizations', 'categorys', 'creators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'organization_id' => 'nullable',
  'category_id' => 'nullable',
  'created_by' => 'nullable',
  'title' => 'required',
  'slug' => 'nullable',
  'short_description' => 'nullable',
  'description' => 'nullable',
  'contact_whatsapp' => 'nullable',
  'banner' => 'nullable',
  'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
  'venue_name' => 'nullable',
  'address' => 'nullable',
  'city' => 'nullable',
  'province' => 'nullable',
  'registration_start' => 'nullable',
  'registration_end' => 'nullable',
  'start_date' => 'nullable',
  'end_date' => 'nullable',
  'max_participants' => 'nullable|numeric',
  'certificate_enabled' => 'nullable',
  'attendance_required' => 'nullable',
  'visibility' => 'required',
  'status' => 'required',
));

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('event_banners', 'public');
        } else {
            unset($validated['banner_image']);
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $organizations = \Modules\Organization\Models\Organization::all();
        $categorys = \Modules\Category\Models\EventCategory::all();
        $creators = \Modules\Authentication\Models\User::all();

        return view('admin.events.edit', compact('event', 'organizations', 'categorys', 'creators'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate(array (
  'organization_id' => 'nullable',
  'category_id' => 'nullable',
  'created_by' => 'nullable',
  'title' => 'required',
  'slug' => 'nullable',
  'short_description' => 'nullable',
  'description' => 'nullable',
  'contact_whatsapp' => 'nullable',
  'banner' => 'nullable',
  'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
  'venue_name' => 'nullable',
  'address' => 'nullable',
  'city' => 'nullable',
  'province' => 'nullable',
  'registration_start' => 'nullable',
  'registration_end' => 'nullable',
  'start_date' => 'nullable',
  'end_date' => 'nullable',
  'max_participants' => 'nullable|numeric',
  'certificate_enabled' => 'nullable',
  'attendance_required' => 'nullable',
  'visibility' => 'required',
  'status' => 'required',
));

        if ($request->hasFile('banner_image')) {
            if ($event->banner_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($event->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('event_banners', 'public');
        } else {
            unset($validated['banner_image']);
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}
