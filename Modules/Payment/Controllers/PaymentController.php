<?php
namespace Modules\Payment\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Payment\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $items = Payment::with('registration')
            ->when($request->filled('event_id'), fn($q) => $q->whereHas('registration', fn($r) => $r->where('event_id', $request->event_id)))
            ->when($request->filled('participant_id'), fn($q) => $q->whereHas('registration', fn($r) => $r->where('participant_id', $request->participant_id)))
            ->latest()->get();
        $selectedEvent = $request->filled('event_id') ? \Modules\Event\Models\Event::find($request->event_id) : null;
        return view('admin.payments.index', compact('items', 'selectedEvent'));
    }

    public function create()
    {
        $registrations = \Modules\Registration\Models\Registration::all();

        return view('admin.payments.create', compact('registrations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'registration_id' => 'required',
  'invoice_number' => 'required',
  'payment_method' => 'nullable',
  'payment_gateway' => 'nullable',
  'transaction_id' => 'nullable',
  'amount' => 'required|numeric',
  'status' => 'required',
  'paid_at' => 'nullable',
));

        Payment::create($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function show(Payment $payment)
    {
        return view('admin.payments.show');
    }

    public function edit(Payment $payment)
    {
        $registrations = \Modules\Registration\Models\Registration::all();

        return view('admin.payments.edit', compact('registrations'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate(array (
  'registration_id' => 'required',
  'invoice_number' => 'required',
  'payment_method' => 'nullable',
  'payment_gateway' => 'nullable',
  'transaction_id' => 'nullable',
  'amount' => 'required|numeric',
  'status' => 'required',
  'paid_at' => 'nullable',
));

        $payment->update($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
}
