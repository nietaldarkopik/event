<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = \App\Models\CertificateTemplate::latest()->paginate(10);
        return view('admin.certificates.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.certificates.templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'event_type' => 'required',
            'orientation' => 'required',
            'layout' => 'required',
            'background_file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('background_file')->store('certificates/backgrounds', 'public');

        \App\Models\CertificateTemplate::create([
            'name' => $request->name,
            'event_type' => $request->event_type,
            'orientation' => $request->orientation,
            'layout' => $request->layout,
            'background_file' => $path,
            'font_title' => $request->font_title,
            'font_body' => $request->font_body,
            'text_color' => $request->text_color ?? '#000000',
        ]);

        return redirect()->route('certificate-templates.index')->with('success', 'Template berhasil dibuat.');
    }

    public function edit($id)
    {
        $template = \App\Models\CertificateTemplate::findOrFail($id);
        return view('admin.certificates.templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = \App\Models\CertificateTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'event_type' => 'required',
            'orientation' => 'required',
            'layout' => 'required',
            'background_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only([
            'name',
            'event_type',
            'orientation',
            'layout',
            'font_title',
            'font_body',
            'text_color'
        ]);

        if ($request->hasFile('background_file')) {
            $data['background_file'] = $request->file('background_file')->store('certificates/backgrounds', 'public');
        }

        $template->update($data);

        return redirect()->route('certificate-templates.index')->with('success', 'Template berhasil diupdate.');
    }

    public function destroy($id)
    {
        $template = \App\Models\CertificateTemplate::findOrFail($id);

        // Check if template is used by any categories or certificates
        if ($template->categories()->count() > 0 || $template->certificates()->count() > 0) {
            return redirect()->back()->with('error', 'Template tidak bisa dihapus karena sedang digunakan.');
        }

        $template->delete();
        return redirect()->route('certificate-templates.index')->with('success', 'Template dihapus.');
    }

    public function show($id)
    {
        return redirect()->route('certificate-templates.index');
    }

    public function builder($id)
    {
        $template = \App\Models\CertificateTemplate::findOrFail($id);
        return view('admin.certificates.templates.builder', compact('template'));
    }

    public function updateLayout(Request $request, $id)
    {
        $template = \App\Models\CertificateTemplate::findOrFail($id);

        $request->validate([
            'layout_json' => 'required',
            'background_image' => 'nullable' // Can be base64 or handled separately
        ]);

        $updateData = [
            'layout_json' => $request->layout_json
        ];

        // If background is sent as a file
        if ($request->hasFile('background_file')) {
            $path = $request->file('background_file')->store('certificates/backgrounds', 'public');
            $updateData['background_file'] = $path;
        } elseif ($request->gallery_background_url) {
            // If background is from gallery, extract the relative path
            $storageUrl = asset('storage/');
            $path = str_replace($storageUrl . '/', '', $request->gallery_background_url);
            $updateData['background_file'] = $path;
        }

        $template->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Layout berhasil disimpan.',
            'background_url' => isset($path) ? asset('storage/' . $path) : ($template->background_file ? asset('storage/' . $template->background_file) : null)
        ]);
    }

    public function preview($id)
    {
        $template = \App\Models\CertificateTemplate::findOrFail($id);
        // This will be implemented to show a live preview or exported PDF
        // For now, just return a simple view or JSON
        return response()->json($template->layout_json);
    }
}
