<?php
namespace Modules\Ticket\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Ticket\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function index()
    {
        $items = TicketType::with('event')->latest()->get();
        return view('admin.ticket_types.index', compact('items'));
    }

    public function create()
    {
        $events = \Modules\Event\Models\Event::all();

        return view('admin.ticket_types.create', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'name' => 'required',
  'price' => 'required|numeric',
  'quota' => 'required|numeric',
  'sale_start' => 'nullable',
  'sale_end' => 'nullable',
  'description' => 'nullable',
));

        TicketType::create($validated);

        return redirect()->route('admin.ticket-types.index')
            ->with('success', 'Jenis Tiket berhasil ditambahkan.');
    }

    public function show(TicketType $ticket_type)
    {
        return redirect()->route('admin.ticket-types.index');
    }

    public function edit(TicketType $ticket_type)
    {
        $events = \Modules\Event\Models\Event::all();
        $tickettype = $ticket_type;

        return view('admin.ticket_types.edit', compact('events', 'tickettype'));
    }

    public function update(Request $request, TicketType $ticket_type)
    {
        $validated = $request->validate(array (
  'event_id' => 'required',
  'name' => 'required',
  'price' => 'required|numeric',
  'quota' => 'required|numeric',
  'sale_start' => 'nullable',
  'sale_end' => 'nullable',
  'description' => 'nullable',
));

        $ticket_type->update($validated);

        return redirect()->route('admin.ticket-types.index')
            ->with('success', 'Jenis Tiket berhasil diperbarui.');
    }

    public function destroy(TicketType $ticket_type)
    {
        $ticket_type->delete();

        return redirect()->route('admin.ticket-types.index')
            ->with('success', 'Jenis Tiket berhasil dihapus.');
    }
}
