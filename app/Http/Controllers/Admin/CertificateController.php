<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateAuditLog;
use App\Models\Event;
use Illuminate\Http\Request;

/** Daftar sertifikat yang sudah terbit: lihat, unduh, cabut/pulihkan, hapus. Penerbitan ada di Generator per event. */
class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $certificates = Certificate::with(['participant', 'committee', 'event', 'category'])
            ->when($request->filled('event_id'), fn ($q) => $q->where('event_id', $request->event_id))
            ->latest('issued_at')->get();
        $events = Event::orderBy('title')->get(['id', 'title']);
        $selectedEvent = $request->filled('event_id') ? $events->firstWhere('id', (int) $request->event_id) : null;

        return view('admin.certificates.issued', compact('certificates', 'events', 'selectedEvent'));
    }

    /** Ekspor CSV: nomor, penerima, kategori, dan tautan unduh publik (untuk dibagikan ke penerima, mis. panitia tanpa WhatsApp di sistem). */
    public function export(Request $request)
    {
        $rows = Certificate::with(['participant', 'committee', 'event', 'category'])
            ->when($request->filled('event_id'), fn ($q) => $q->where('event_id', $request->event_id))
            ->orderBy('event_id')->orderBy('certificate_number')->get();

        $filename = 'sertifikat-terbit-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['No. Sertifikat', 'Penerima', 'Jenis', 'Unit/Asal', 'Jabatan/Seksi', 'Kategori', 'Event', 'Status', 'Tautan Unduh']);
            foreach ($rows as $c) {
                fputcsv($out, [
                    $c->certificate_number,
                    $c->display_name,
                    $c->isCommittee() ? 'Panitia' : 'Peserta',
                    $c->display_unit,
                    $c->isCommittee() ? trim(optional($c->committee)->position . ' / ' . optional($c->committee)->section, ' /') : '',
                    optional($c->category)->name,
                    optional($c->event)->title,
                    $c->is_revoked ? 'Dicabut' : 'Aktif',
                    route('certificates.public_download', $c->unique_code),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Cabut (tidak valid lagi di halaman verifikasi) atau pulihkan sertifikat. */
    public function toggleRevoke(Request $request, $id)
    {
        $cert = Certificate::findOrFail($id);
        $request->validate(['reason' => 'nullable|string|max:255']);

        if ($cert->is_revoked) {
            $cert->update(['is_revoked' => false, 'revoked_at' => null, 'revoked_by' => null, 'revoke_reason' => null]);
            $action = 'restored';
            $msg = 'Sertifikat dipulihkan dan valid kembali.';
        } else {
            $cert->update(['is_revoked' => true, 'revoked_at' => now(), 'revoked_by' => auth()->id(), 'revoke_reason' => $request->reason]);
            $action = 'revoked';
            $msg = 'Sertifikat dicabut. Halaman verifikasi akan menandainya tidak berlaku.';
        }

        CertificateAuditLog::create([
            'certificate_id' => $cert->id, 'event_id' => $cert->event_id, 'participant_id' => $cert->participant_id,
            'action' => $action, 'performed_by' => auth()->id(), 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', $msg);
    }

    public function destroy($id)
    {
        Certificate::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Sertifikat berhasil dihapus.');
    }
}
