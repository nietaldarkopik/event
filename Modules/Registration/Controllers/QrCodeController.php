<?php
namespace Modules\Registration\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Registration\Models\QrCode;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function index()
    {
        $items = QrCode::latest()->get();
        return view('admin.qr_codes.index', compact('items'));
    }

    public function create()
    {
        $registrations = \Modules\Registration\Models\Registration::all();

        return view('admin.qr_codes.create', compact('registrations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'registration_id' => 'required',
  'code' => 'required',
  'expired_at' => 'nullable',
  'is_used' => 'nullable',
));

        QrCode::create($validated);

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR Code Tiket berhasil ditambahkan.');
    }

    public function show(QrCode $qr_code)
    {
        return redirect()->route('admin.qr-codes.index');
    }

    public function edit(QrCode $qr_code)
    {
        $registrations = \Modules\Registration\Models\Registration::all();
        $qrcode = $qr_code;

        return view('admin.qr_codes.edit', compact('registrations', 'qrcode'));
    }

    public function update(Request $request, QrCode $qr_code)
    {
        $validated = $request->validate(array (
  'registration_id' => 'required',
  'code' => 'required',
  'expired_at' => 'nullable',
  'is_used' => 'nullable',
));

        $qr_code->update($validated);

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR Code Tiket berhasil diperbarui.');
    }

    public function destroy(QrCode $qr_code)
    {
        $qr_code->delete();

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR Code Tiket berhasil dihapus.');
    }
}
