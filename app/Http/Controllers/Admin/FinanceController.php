<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\QrCodeImageService;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['participant.event']);

        if ($request->has('event_id') && $request->event_id != '') {
            $query->whereHas('participant', function ($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        $payments = $query->latest()->paginate(15);
        $events = \App\Models\Event::where('is_paid', true)->get();

        return view('admin.finance.index', compact('payments', 'events'));
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = \App\Models\Payment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,failed,paid',
            'payment_method' => 'nullable|in:transfer,cash',
            'evidence_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048', // 2MB Max
        ]);

        $data = [
            'status' => $request->status,
        ];

        if ($request->status == 'approved') {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();

            if ($request->has('payment_method')) {
                $data['method'] = $request->payment_method;
            }

            if ($request->hasFile('evidence_file')) {
                $path = $request->file('evidence_file')->store('payments', 'public');
                $data['proof_file'] = $path;
            }
        }

        $payment->update($data);

        if ($request->status == 'approved') {
            $participant = $payment->participant;
            $participant->update(['status' => 'approved']);

            // Generate Invitation if not exists
            if (!$participant->invitation) {
                $uniqueCode = 'EVT' . $participant->event_id . strtoupper(\Illuminate\Support\Str::random(6));

                \App\Models\Invitation::create([
                    'event_id'       => $participant->event_id,
                    'participant_id' => $participant->id,
                    'unique_code'    => $uniqueCode,
                    'is_active'      => true,
                    'sent_via'       => 'finance_approval',
                    'sent_at'        => now(),
                ]);
            } else {
                $uniqueCode = $participant->invitation->unique_code;
            }

            // Generate QR Code PNG image
            $qr    = QrCodeImageService::generateAndSave($uniqueCode);
            $event = $participant->event;

            // Compose WhatsApp message
            $message  = "*KONFIRMASI PEMBAYARAN & PENDAFTARAN EVENT*\n\n";
            $message .= "Halo *{$participant->name}*,\n\n";
            $message .= "✅ Pembayaran Anda telah *DIKONFIRMASI* oleh panitia.\n\n";
            $message .= "*Detail Event:*\n";
            $message .= "📌 Event : {$event->title}\n";
            if ($event->event_date ?? $event->date ?? null) {
                $eventDate = $event->event_date ?? $event->date;
                $message .= "📅 Tanggal: " . \Carbon\Carbon::parse($eventDate)->translatedFormat('l, d F Y') . "\n";
            }
            if ($event->location ?? $event->venue ?? null) {
                $message .= "📍 Lokasi : " . ($event->location ?? $event->venue) . "\n";
            }
            $message .= "\n*Kode Undangan Anda:*\n";
            $message .= "🎫 *{$uniqueCode}*\n\n";
            $message .= "📷 *QR Code terlampir adalah kartu akses Anda.*\n\n";
            $message .= \App\Services\AttendanceService::selfHintText($event, $uniqueCode) . "\n\n";
            $message .= "Sampai jumpa di acara! 🎉";

            // Send WhatsApp with QR image
            \App\Services\WhatsAppService::sendWithImage(
                $participant,
                'payment_approved_qr',
                $message,
                null,
                $qr['url'],
                "QR Code Akses — {$event->title} | Kode: {$uniqueCode}"
            );
        }

        return redirect()->back()->with('success', 'Status pembayaran diperbarui.');
    }
}
