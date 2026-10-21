<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\Event;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificatePublicController extends Controller
{
    /**
     * Public Verification Page (Target for QR Code)
     */
    public function verify($hash)
    {
        $certificate = Certificate::with(['participant', 'committee', 'event', 'category'])
            ->where('certificate_hash', $hash)
            ->first();

        if (!$certificate) {
            return view('public.certificates.verify', [
                'status' => 'INVALID',
                'message' => 'Sertifikat tidak ditemukan dalam database kami.'
            ]);
        }

        if ($certificate->is_revoked) {
            return view('public.certificates.verify', [
                'status' => 'REVOKED',
                'certificate' => $certificate,
                'message' => 'Sertifikat ini telah dicabut oleh penyelenggara.'
            ]);
        }

        // Audit log for verification
        \App\Models\CertificateAuditLog::create([
            'certificate_id' => $certificate->id,
            'event_id' => $certificate->event_id,
            'participant_id' => $certificate->participant_id,
            'action' => 'verified',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('public.certificates.verify', [
            'status' => 'VALID',
            'certificate' => $certificate,
            'message' => 'Sertifikat ini valid dan terdaftar secara resmi.'
        ]);
    }

    /**
     * Show Manual Retrieval Form
     */
    public function showRetrieveForm()
    {
        return view('public.certificates.retrieve');
    }

    /**
     * Process Manual Retrieval
     */
    public function retrieve(Request $request)
    {
        $request->validate([
            'unique_code' => 'required|string',
        ]);

        $certificate = Certificate::where('unique_code', $request->unique_code)->first();

        if (!$certificate) {
            return back()->with('error', 'Kode pengambilan tidak valid.');
        }

        return redirect()->route('certificates.public_download', $certificate->unique_code);
    }

    /**
     * Public Secure Download
     */
    public function download($unique_code)
    {
        $certificate = Certificate::with(['participant', 'committee', 'event', 'category', 'template'])
            ->where('unique_code', $unique_code)
            ->firstOrFail();

        // Sertifikat yang dicabut tidak boleh lagi diunduh lewat tautan publik.
        if ($certificate->is_revoked) {
            abort(410, 'Sertifikat ini telah dicabut oleh penyelenggara.');
        }

        $certificateService = app(CertificateService::class);

        // Replace variables in layout
        $layoutJson = $certificateService->replaceVariables($certificate->template->layout_json, $certificate);

        // Generate QR Code for PDF - Targeting Verification URL
        $verifyUrl = route('certificates.verify', $certificate->certificate_hash);
        $qrcode = base64_encode(QrCode::format('svg')->size(100)->generate($verifyUrl));

        $data = [
            'certificate' => $certificate,
            'layoutJson' => $layoutJson,
            'qrcode' => $qrcode,
            'background' => $certificate->template->background_file
                ? public_path('storage/' . $certificate->template->background_file)
                : null,
            'orientation' => $certificate->template->orientation,
        ];

        // Audit log for download
        \App\Models\CertificateAuditLog::create([
            'certificate_id' => $certificate->id,
            'event_id' => $certificate->event_id,
            'participant_id' => $certificate->participant_id,
            'action' => 'downloaded',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $pdf = Pdf::loadView('admin.certificates.pdf_canvas', $data);
        $pdf->setPaper('a4', $certificate->template->orientation);

        return $pdf->stream('certificate-' . $certificate->certificate_number . '.pdf');
    }
}
