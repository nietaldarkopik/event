<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class EmailSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emailSettings = EmailSetting::orderBy('is_active', 'desc')->orderBy('name')->get();
        return view('admin.email-settings.index', compact('emailSettings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.email-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mailer' => 'required|string|max:50',
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'encryption' => 'nullable|string|max:50',
            'from_address' => 'required|email',
            'from_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        // If this is set as active, deactivate others
        if ($request->has('is_active')) {
            EmailSetting::where('is_active', true)->update(['is_active' => false]);
        }

        EmailSetting::create($validated);

        return redirect()->route('admin.email-settings.index')
            ->with('success', 'Email setting berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $setting = EmailSetting::findOrFail($id);
        return view('admin.email-settings.show', compact('setting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = EmailSetting::findOrFail($id);
        return view('admin.email-settings.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $setting = EmailSetting::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mailer' => 'required|string|max:50',
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'sometimes|string',
            'encryption' => 'nullable|string|max:50',
            'from_address' => 'required|email',
            'from_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        // If password is not provided, keep the existing one
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // If this is set as active, deactivate others
        if ($request->has('is_active')) {
            EmailSetting::where('is_active', true)->where('id', '!=', $id)->update(['is_active' => false]);
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        $setting->update($validated);

        return redirect()->route('admin.email-settings.index')
            ->with('success', 'Email setting berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $setting = EmailSetting::findOrFail($id);
        
        // Prevent deleting active setting
        if ($setting->is_active) {
            return redirect()->route('admin.email-settings.index')
                ->with('error', 'Tidak dapat menghapus email setting yang aktif.');
        }
        
        $setting->delete();
        
        return redirect()->route('admin.email-settings.index')
            ->with('success', 'Email setting berhasil dihapus.');
    }

    /**
     * Toggle active status of email setting
     */
    public function toggleActive($id)
    {
        $setting = EmailSetting::findOrFail($id);
        
        // Deactivate all other settings
        EmailSetting::where('is_active', true)->update(['is_active' => false]);
        
        // Activate this setting
        $setting->update(['is_active' => true]);
        
        return redirect()->route('admin.email-settings.index')
            ->with('success', 'Email setting berhasil diaktifkan.');
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request, $id)
    {
        $request->validate([
            'target_email' => 'required|email'
        ]);
        
        $setting = EmailSetting::findOrFail($id);
        $targetEmail = $request->target_email;
        
        try {
            // Temporarily use this setting for test
            Config::set('mail.default', $setting->mailer);
            Config::set('mail.mailers.smtp.host', $setting->host);
            Config::set('mail.mailers.smtp.port', $setting->port);
            Config::set('mail.mailers.smtp.encryption', $setting->encryption);
            Config::set('mail.mailers.smtp.username', $setting->username);
            Config::set('mail.mailers.smtp.password', $setting->password);
            Config::set('mail.from.address', $setting->from_address);
            Config::set('mail.from.name', $setting->from_name);
            
            // Send test email
            Mail::raw('Test Email dari EVENT UNWIM - ' . $setting->name, function($message) use ($setting, $targetEmail) {
                $message->to($targetEmail) // Send to target email
                        ->subject('Test Email Configuration - ' . $setting->name);
            });
            
            return redirect()->route('admin.email-settings.index')
                ->with('success', 'Test email berhasil dikirim ke ' . $targetEmail);
                
        } catch (\Exception $e) {
            return redirect()->route('admin.email-settings.index')
                ->with('error', 'Gagal mengirim test email: ' . $e->getMessage());
        }
    }
}
