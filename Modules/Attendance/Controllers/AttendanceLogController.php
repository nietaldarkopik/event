<?php
namespace Modules\Attendance\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Attendance\Models\AttendanceLog;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $items = AttendanceLog::with('registration')
            ->when($request->filled('event_id'), fn($q) => $q->whereHas('registration', fn($r) => $r->where('event_id', $request->event_id)))
            ->when($request->filled('participant_id'), fn($q) => $q->whereHas('registration', fn($r) => $r->where('participant_id', $request->participant_id)))
            ->latest()->get();
        $selectedEvent = $request->filled('event_id') ? \Modules\Event\Models\Event::find($request->event_id) : null;
        return view('admin.attendance_logs.index', compact('items', 'selectedEvent'));
    }

    public function create()
    {
        $registrations = \Modules\Registration\Models\Registration::all();
        $sessions = \Modules\Session\Models\EventSession::all();
        $scanners = \Modules\Authentication\Models\User::all();

        return view('admin.attendance_logs.create', compact('registrations', 'sessions', 'scanners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'registration_id' => 'required',
  'session_id' => 'nullable',
  'scanner_user_id' => 'nullable',
  'device' => 'nullable',
  'scan_time' => 'nullable',
  'status' => 'required',
));

        AttendanceLog::create($validated);

        return redirect()->route('admin.attendance-logs.index')
            ->with('success', 'Log Kehadiran berhasil ditambahkan.');
    }

    public function show(AttendanceLog $attendance_log)
    {
        return redirect()->route('admin.attendance-logs.index');
    }

    public function edit(AttendanceLog $attendance_log)
    {
        $registrations = \Modules\Registration\Models\Registration::all();
        $sessions = \Modules\Session\Models\EventSession::all();
        $scanners = \Modules\Authentication\Models\User::all();
        $attendancelog = $attendance_log;

        return view('admin.attendance_logs.edit', compact('registrations', 'sessions', 'scanners', 'attendancelog'));
    }

    public function update(Request $request, AttendanceLog $attendance_log)
    {
        $validated = $request->validate(array (
  'registration_id' => 'required',
  'session_id' => 'nullable',
  'scanner_user_id' => 'nullable',
  'device' => 'nullable',
  'scan_time' => 'nullable',
  'status' => 'required',
));

        $attendance_log->update($validated);

        return redirect()->route('admin.attendance-logs.index')
            ->with('success', 'Log Kehadiran berhasil diperbarui.');
    }

    public function destroy(AttendanceLog $attendance_log)
    {
        $attendance_log->delete();

        return redirect()->route('admin.attendance-logs.index')
            ->with('success', 'Log Kehadiran berhasil dihapus.');
    }
}
