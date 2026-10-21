<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\CertificateCategory;
use App\Models\CertificateSignature;
use App\Models\CertificateLogo;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventCertificateController extends Controller
{
    public function settings($eventId)
    {
        $event = Event::with([
            'certificateCategories.template',
            'certificateSignatures',
            'certificateLogos'
        ])->findOrFail($eventId);

        $templates = CertificateTemplate::orderBy('name')->get();

        return view('admin.events.certificates.settings', compact('event', 'templates'));
    }

    public function storeCategory(Request $request, $eventId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'nullable|exists:certificate_templates,id',
            'prefix' => 'nullable|string|max:20',
        ]);

        CertificateCategory::create([
            'event_id' => $eventId,
            'template_id' => $request->template_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'certificate_prefix' => $request->prefix ?? '',
            'auto_map_to_role' => false,
            'mapped_role' => null,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Kategori sertifikat berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $categoryId)
    {
        $category = CertificateCategory::findOrFail($categoryId);

        $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'nullable|exists:certificate_templates,id',
            'prefix' => 'nullable|string|max:20',
        ]);

        $category->update([
            'name' => $request->name,
            'template_id' => $request->template_id,
            'certificate_prefix' => $request->prefix ?? $category->certificate_prefix,
        ]);

        return redirect()->back()->with('success', 'Kategori sertifikat berhasil diperbarui.');
    }

    public function destroyCategory($categoryId)
    {
        $category = CertificateCategory::findOrFail($categoryId);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori sertifikat berhasil dihapus.');
    }

    public function storeSignature(Request $request, $eventId)
    {
        $request->validate([
            'signer_name' => 'required|string|max:255',
            'signer_position' => 'required|string|max:255',
            'signature_file' => 'required_without:signature_gallery_path|image|mimes:png,jpg,jpeg|max:2048',
            'signature_gallery_path' => 'nullable|string',
            'stamp_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'stamp_gallery_path' => 'nullable|string',
        ]);

        if ($request->hasFile('signature_file')) {
            $signaturePath = $request->file('signature_file')->store('certificates/signatures', 'public');
        } else {
            // If from gallery, the path is already relative to storage? 
            // Our gallery stores absolute-looking but relative paths.
            // Let's assume the input is the relative path from storage/
            $signaturePath = $request->signature_gallery_path;
        }

        if ($request->hasFile('stamp_image')) {
            $stampPath = $request->file('stamp_image')->store('certificates/stamps', 'public');
        } else {
            $stampPath = $request->stamp_gallery_path;
        }

        CertificateSignature::create([
            'event_id' => $eventId,
            'signer_name' => $request->signer_name,
            'signer_position' => $request->signer_position,
            'signature_file' => $signaturePath,
            'stamp_image_path' => $stampPath,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Tanda tangan berhasil ditambahkan.');
    }

    public function destroySignature($signatureId)
    {
        $signature = CertificateSignature::findOrFail($signatureId);
        $signature->delete();

        return redirect()->back()->with('success', 'Tanda tangan berhasil dihapus.');
    }

    public function storeLogo(Request $request, $eventId)
    {
        $request->validate([
            'logo' => 'required_without:logo_gallery_path|image|mimes:png,jpg,jpeg|max:2048',
            'logo_gallery_path' => 'nullable|string',
            'logo_type' => 'required|in:event,organizer,sponsor',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('certificates/logos', 'public');
        } else {
            $path = $request->logo_gallery_path;
        }

        CertificateLogo::create([
            'event_id' => $eventId,
            'image_path' => $path,
            'logo_type' => $request->logo_type,
            'is_primary' => $request->logo_type === 'event',
        ]);

        return redirect()->back()->with('success', 'Logo berhasil ditambahkan.');
    }

    public function destroyLogo($logoId)
    {
        $logo = CertificateLogo::findOrFail($logoId);
        $logo->delete();

        return redirect()->back()->with('success', 'Logo berhasil dihapus.');
    }
}
