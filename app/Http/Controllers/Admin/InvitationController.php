<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $query = Invitation::with(['participant', 'event']);

        if ($request->has('event_id') && $request->event_id != '') {
            $query->where('event_id', $request->event_id);
        }

        $invitations = $query->latest()->paginate(15);
        $events = \App\Models\Event::all();

        return view('admin.invitations.index', compact('invitations', 'events'));
    }
}
