<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Payment;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_events' => Event::count(),
            'total_participants' => Participant::count(),
            'total_income' => Payment::where('status', 'paid')->sum('amount'),
            'active_events' => Event::where('status', 'PUBLISHED')->count(),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function exportParticipants(Request $request)
    {
        // Placeholder for export logic (e.g., PDF/Excel)
        // For now, just a simple list view or raw data
        $query = Participant::with('event');

        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $participants = $query->get();

        return response()->streamDownload(function () use ($participants) {
            echo "Nama,Email,WhatsApp,Event,Status\n";
            foreach ($participants as $p) {
                $eventTitle = $p->event->title ?? '-';
                echo "{$p->name},{$p->email},{$p->whatsapp},{$eventTitle},{$p->payment_status}\n";
            }
        }, 'participants-report.csv');
    }
}
