<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $media = CertificateMedia::orderBy('created_at', 'desc')->paginate(12);
        return view('admin.certificates.media.index', compact('media'));
    }

    public function apiList(Request $request)
    {
        $type = $request->type;
        $query = CertificateMedia::query();

        if ($type) {
            $query->where('type', $type);
        }

        $media = $query->orderBy('created_at', 'desc')->get();

        return response()->json($media->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'url' => asset('storage/' . $m->file_path),
                'type' => $m->type
            ];
        }));
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:logo,signature,stamp,background',
            'file' => 'required|image|max:2048'
        ]);

        $path = $request->file('file')->store('certificates/media', 'public');

        $media = CertificateMedia::create([
            'name' => $request->name,
            'type' => $request->type,
            'file_path' => $path,
            'is_global' => true
        ]);

        return response()->json([
            'success' => true,
            'media' => [
                'id' => $media->id,
                'name' => $media->name,
                'url' => asset('storage/' . $media->file_path),
                'type' => $media->type
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:logo,signature,stamp,background',
            'file' => 'required|image|max:2048'
        ]);

        $path = $request->file('file')->store('certificates/media', 'public');

        CertificateMedia::create([
            'name' => $request->name,
            'type' => $request->type,
            'file_path' => $path,
            'is_global' => true
        ]);

        return back()->with('success', 'Media berhasil diunggah.');
    }

    public function destroy($id)
    {
        $media = CertificateMedia::findOrFail($id);
        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return back()->with('success', 'Media berhasil dihapus.');
    }
}
