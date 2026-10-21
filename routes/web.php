<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\AuthController;
Route::get('/cek-sertifikat', [\App\Http\Controllers\PublicEventController::class, 'certificateCheck'])->name('certificates.check');
Route::get('/cek-sertifikat/cari', [\App\Http\Controllers\PublicEventController::class, 'certificateSearch'])->name('certificates.search');

// New Public Certificate Module Routes
Route::get('/verify/{hash}', [\App\Http\Controllers\CertificatePublicController::class, 'verify'])->name('certificates.verify');
Route::get('/certificates/retrieve', [\App\Http\Controllers\CertificatePublicController::class, 'showRetrieveForm'])->name('certificates.retrieve.form');
Route::post('/certificates/retrieve', [\App\Http\Controllers\CertificatePublicController::class, 'retrieve'])->name('certificates.retrieve');
Route::get('/certificates/public-download/{unique_code}', [\App\Http\Controllers\CertificatePublicController::class, 'download'])->name('certificates.public_download');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// SSO Routes
Route::get('auth/{provider}/redirect', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirect'])->name('auth.redirect');
Route::get('auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'callback'])->name('auth.callback');

// Public Event Routes
Route::get('/', [\App\Http\Controllers\PublicEventController::class, 'home'])->name('home');
Route::get('/events', [\App\Http\Controllers\PublicEventController::class, 'index'])->name('events.index');
Route::get('/calendar', [\App\Http\Controllers\PublicEventController::class, 'calendar'])->name('events.calendar');
// Undangan digital via link (dikirim di WhatsApp)
Route::get('/undangan/{code}', [\App\Http\Controllers\PublicEventController::class, 'invitation'])->name('invitation.show');
// Presensi mandiri peserta (dibuka lewat QR layar di lokasi acara)
Route::get('/presensi/{slug}', [\App\Http\Controllers\SelfAttendanceController::class, 'show'])->name('attendance.self');
Route::post('/presensi/{slug}', [\App\Http\Controllers\SelfAttendanceController::class, 'submit'])->middleware('throttle:12,1')->name('attendance.self.submit');
Route::get('/events/{slug}', [\App\Http\Controllers\PublicEventController::class, 'show'])->name('events.show');
Route::post('/events/certificate/claim', [\App\Http\Controllers\PublicEventController::class, 'claimCertificate'])->name('events.certificate.claim');
Route::post('/events/invitation/check', [\App\Http\Controllers\PublicEventController::class, 'checkInvitation'])->name('events.invitation.check');
Route::get('/events/invitation/check', [\App\Http\Controllers\PublicEventController::class, 'checkInvitation']);
Route::post('/whatsapp/redirect', [\App\Http\Controllers\PublicEventController::class, 'whatsappRedirect'])->name('whatsapp.redirect');
Route::post('/events/{event}/register', [\App\Http\Controllers\PublicEventController::class, 'register'])->name('events.register');

// Admin Preview Route (Publicly accessible but signed)
Route::get('admin/events/preview-detail/{slug}', [\App\Http\Controllers\Admin\EventController::class, 'previewDetail'])->name('admin.events.preview_detail');

// Redirect /admin ke dashboard admin
Route::get('/admin', function () {
    return redirect()->route('dashboard');
});

Route::prefix('admin')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::middleware(['auth', 'permission.route', 'keep.list'])->group(function () {
        Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

        // Admin Event (Super Admin & Admin Event)
        Route::middleware('can:admin_event')->group(function () {
            // Wizard input event (auto-save per step). Harus didaftarkan SEBELUM resource 'events'
            // agar 'events/wizard' tidak tertangkap oleh events/{event}.
            Route::prefix('events/wizard')->name('admin.events.wizard.')->group(function () {
                $ctl = \Modules\Event\Controllers\EventWizardController::class;
                Route::get('/', [$ctl, 'create'])->name('create');
                Route::post('/', [$ctl, 'start'])->name('start');
                Route::post('image', [$ctl, 'uploadImage'])->name('image');
                Route::post('relation/{kind}', [$ctl, 'storeRelation'])->where('kind', 'speaker|category|organization')->name('relation');
                Route::get('{event}', [$ctl, 'edit'])->whereNumber('event')->name('edit');
                Route::patch('{event}', [$ctl, 'save'])->whereNumber('event')->name('save');
                Route::post('{event}/banner', [$ctl, 'banner'])->whereNumber('event')->name('banner');
                Route::post('{event}/items/{type}', [$ctl, 'saveItem'])->whereNumber('event')->name('item.save');
                Route::delete('{event}/items/{type}/{id}', [$ctl, 'deleteItem'])->whereNumber(['event', 'id'])->name('item.delete');
                Route::post('{event}/publish', [$ctl, 'publish'])->whereNumber('event')->name('publish');
            });

            Route::resource('event-categories', \App\Http\Controllers\Admin\EventCategoryController::class)->names('admin.event-categories');
            Route::resource('events', \App\Http\Controllers\Admin\EventController::class)->names('admin.events');
            Route::post('participants/import', [\App\Http\Controllers\Admin\ParticipantController::class, 'import'])->name('participants.import');
            Route::get('participants/import-template', [\App\Http\Controllers\Admin\ParticipantController::class, 'downloadImportTemplate'])->name('participants.import-template');
            Route::post('participants/{id}/seat-number', [\App\Http\Controllers\Admin\ParticipantController::class, 'updateSeatNumber'])->name('participants.updateSeatNumber');
            Route::post('participants/{id}/certificate-category', [\App\Http\Controllers\Admin\ParticipantController::class, 'updateCertificateCategory'])->name('participants.updateCertificateCategory');
            Route::post('participants/{id}/approve', [\App\Http\Controllers\Admin\ParticipantController::class, 'approve'])->name('participants.approve');
            Route::post('participants/{id}/resend-email', [\App\Http\Controllers\Admin\ParticipantController::class, 'resendEmail'])->name('participants.resend-email');
            Route::post('participants/{id}/resend-whatsapp', [\App\Http\Controllers\Admin\ParticipantController::class, 'resendWhatsApp'])->name('participants.resend-whatsapp');
            Route::post('participants/bulk-session', [\App\Http\Controllers\Admin\ParticipantController::class, 'bulkSession'])->name('participants.bulkSession');
            Route::post('participants/{id}/session', [\App\Http\Controllers\Admin\ParticipantController::class, 'updateSession'])->name('participants.updateSession');
            Route::resource('participants', \App\Http\Controllers\Admin\ParticipantController::class);
            Route::resource('invitation-templates', \App\Http\Controllers\Admin\InvitationTemplateController::class);
            Route::get('invitation-templates/{id}/builder', [\App\Http\Controllers\Admin\InvitationTemplateController::class, 'builder'])->name('invitation-templates.builder');
            Route::post('invitation-templates/{id}/config', [\App\Http\Controllers\Admin\InvitationTemplateController::class, 'updateConfig'])->name('invitation-templates.updateConfig');
            Route::get('invitation-templates/{id}/preview', [\App\Http\Controllers\Admin\InvitationTemplateController::class, 'preview'])->name('invitation-templates.preview');
            Route::get('attendance/scanner', [\App\Http\Controllers\Admin\AttendanceController::class, 'scanner'])->name('attendance.scanner');
            Route::post('attendance/scan', [\App\Http\Controllers\Admin\AttendanceController::class, 'processScan'])->name('attendance.process');
            Route::post('attendance/manual', [\App\Http\Controllers\Admin\AttendanceController::class, 'manual'])->name('attendance.manual');
            Route::get('attendance/event/{id}/display', [\App\Http\Controllers\Admin\AttendanceController::class, 'display'])->name('attendance.display');
            Route::get('attendance/event/{id}/token', [\App\Http\Controllers\Admin\AttendanceController::class, 'token'])->name('attendance.token');
            Route::get('attendance/event/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'showEvent'])->name('attendance.event');
            Route::get('attendance/event/{id}/pdf', [\App\Http\Controllers\Admin\AttendanceController::class, 'exportPdf'])->name('attendance.export_pdf');
            Route::get('attendance/event/{id}/csv', [\App\Http\Controllers\Admin\AttendanceController::class, 'exportCsv'])->name('attendance.export_csv');
            Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class)->only(['index']);
            Route::resource('invitations', \App\Http\Controllers\Admin\InvitationController::class)->only(['index']);
        });

        // Admin Sertifikat (Super Admin & Admin Sertifikat)
        Route::middleware('can:admin_sertifikat')->group(function () {
            Route::resource('certificate-templates', \App\Http\Controllers\Admin\CertificateTemplateController::class);
            Route::get('certificate-templates/{id}/builder', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'builder'])->name('certificate-templates.builder');
            Route::post('certificate-templates/{id}/update-layout', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'updateLayout'])->name('certificate-templates.updateLayout');
            Route::get('certificate-templates/{id}/preview', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'preview'])->name('certificate-templates.preview');

            Route::get('certificate-generator', [\App\Http\Controllers\Admin\CertificateGeneratorController::class, 'index'])->name('certificate-generator.index');
            Route::get('certificate-generator/{event}', [\App\Http\Controllers\Admin\CertificateGeneratorController::class, 'show'])->name('certificate-generator.show');
            Route::post('certificate-generator/{event}/generate-committee', [\App\Http\Controllers\Admin\CertificateGeneratorController::class, 'generateCommittee'])->name('certificate-generator.generateCommittee');
            Route::post('certificate-generator/{event}/generate', [\App\Http\Controllers\Admin\CertificateGeneratorController::class, 'generateMass'])->name('certificate-generator.generate');
            Route::get('certificates/{id}/download', [\App\Http\Controllers\Admin\CertificateGeneratorController::class, 'downloadPdf'])->name('certificates.download');
            Route::get('certificates/export', [\App\Http\Controllers\Admin\CertificateController::class, 'export'])->name('certificates.export');
            Route::post('certificates/{id}/revoke', [\App\Http\Controllers\Admin\CertificateController::class, 'toggleRevoke'])->name('certificates.revoke');
            Route::resource('certificates', \App\Http\Controllers\Admin\CertificateController::class)->only(['index', 'destroy']);

            // Event Certificate Settings
            Route::get('events/{event}/certificates/settings', [\App\Http\Controllers\Admin\EventCertificateController::class, 'settings'])->name('events.certificates.settings');
            Route::post('events/{event}/certificates/categories', [\App\Http\Controllers\Admin\EventCertificateController::class, 'storeCategory'])->name('events.certificates.storeCategory');
            Route::put('events/certificates/categories/{category}', [\App\Http\Controllers\Admin\EventCertificateController::class, 'updateCategory'])->name('events.certificates.updateCategory');
            Route::delete('events/certificates/categories/{category}', [\App\Http\Controllers\Admin\EventCertificateController::class, 'destroyCategory'])->name('events.certificates.destroyCategory');

            Route::post('events/{event}/certificates/signatures', [\App\Http\Controllers\Admin\EventCertificateController::class, 'storeSignature'])->name('events.certificates.storeSignature');
            Route::delete('events/certificates/signatures/{signature}', [\App\Http\Controllers\Admin\EventCertificateController::class, 'destroySignature'])->name('events.certificates.destroySignature');

            Route::post('events/{event}/certificates/logos', [\App\Http\Controllers\Admin\EventCertificateController::class, 'storeLogo'])->name('events.certificates.storeLogo');
            Route::delete('events/certificates/logos/{logo}', [\App\Http\Controllers\Admin\EventCertificateController::class, 'destroyLogo'])->name('events.certificates.destroyLogo');

            // Media Gallery
            Route::get('certificate-media/api', [\App\Http\Controllers\Admin\MediaController::class, 'apiList'])->name('certificate-media.api');
            Route::post('certificate-media/api', [\App\Http\Controllers\Admin\MediaController::class, 'apiStore'])->name('certificate-media.api.store');
            Route::resource('certificate-media', \App\Http\Controllers\Admin\MediaController::class)->only(['index', 'store', 'destroy'])->names('admin.certificate-media');
        });

        // Admin Keuangan (Super Admin & Admin Keuangan)
        Route::middleware('can:admin_keuangan')->group(function () {
            Route::get('finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('finance.index');
            Route::post('finance/{id}/status', [\App\Http\Controllers\Admin\FinanceController::class, 'updateStatus'])->name('finance.updateStatus');
        });

        // Super Admin Only (Reports, Settings)
        Route::middleware('can:super_admin')->group(function () {
            Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/export/participants', [\App\Http\Controllers\Admin\ReportController::class, 'exportParticipants'])->name('reports.export.participants');

            Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
            Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
            Route::resource('settings/users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'settings'])->only(['index', 'store', 'update', 'destroy']);
            Route::resource('settings/roles', \App\Http\Controllers\Admin\RoleController::class, ['as' => 'settings']);

            Route::post('units/reorder', [\App\Http\Controllers\Admin\UnitController::class, 'reorder'])->name('units.reorder');
            Route::resource('units', \App\Http\Controllers\Admin\UnitController::class)->names('units');
            
            // Email Settings
Route::resource('email-settings', \App\Http\Controllers\Admin\EmailSettingController::class)->names('admin.email-settings');
Route::post('email-settings/{id}/toggle-active', [\App\Http\Controllers\Admin\EmailSettingController::class, 'toggleActive'])->name('admin.email-settings.toggle-active');
Route::post('email-settings/{id}/test-email', [\App\Http\Controllers\Admin\EmailSettingController::class, 'testEmail'])->name('admin.email-settings.test-email');
        });

        // ==========================================
        // NEW MODULAR ROUTING SYSTEM
        // ==========================================
        Route::middleware('can:super_admin')->group(function () {
            Route::resource('organizations', \Modules\Organization\Controllers\OrganizationController::class)->names('admin.organizations');
            Route::resource('event-committees', \Modules\Event\Controllers\EventCommitteeController::class)->names('admin.event-committees');
        });

        Route::middleware('can:admin_event')->group(function () {
            Route::resource('event-categories', \Modules\Category\Controllers\EventCategoryController::class)->names('admin.event-categories');
            Route::resource('events', \Modules\Event\Controllers\EventController::class)->names('admin.events');
            Route::resource('event-sessions', \Modules\Session\Controllers\EventSessionController::class)->names('admin.event-sessions');
            Route::resource('speakers', \Modules\Speaker\Controllers\SpeakerController::class)->names('admin.speakers');
            Route::resource('ticket-types', \Modules\Ticket\Controllers\TicketTypeController::class)->names('admin.ticket-types');
            // NOTE: Modules\Participant\Controllers\ParticipantController is a mismatched
            // scaffold (wrong fields, invalid blade syntax) — routed to the working
            // App\Http\Controllers\Admin\ParticipantController instead. See line 67.
            Route::resource('participants', \App\Http\Controllers\Admin\ParticipantController::class)->names('admin.participants');
            // Modul lama berbasis tabel `registrations` (registrasi, QR tiket, log kehadiran) dinonaktifkan: alur pendaftaran
            // sekarang memakai `participants` + `invitations` + `attendance_logs`. Alamat lama dialihkan ke halaman penggantinya
            // agar tautan/bookmark tidak 404 dan tidak ada dua tempat berbeda untuk data yang sama.
            Route::redirect('registrations', '/admin/participants');
            Route::redirect('registrations/{any}', '/admin/participants')->where('any', '.*');
            Route::redirect('qr-codes', '/admin/participants');
            Route::redirect('qr-codes/{any}', '/admin/participants')->where('any', '.*');
            Route::redirect('attendance-logs', '/admin/attendance');
            Route::redirect('attendance-logs/{any}', '/admin/attendance')->where('any', '.*');
            Route::resource('sponsors', \Modules\Sponsor\Controllers\SponsorController::class)->names('admin.sponsors');
            Route::resource('galleries', \Modules\Gallery\Controllers\GalleryController::class)->names('admin.galleries');
            Route::resource('announcements', \Modules\Event\Controllers\AnnouncementController::class)->names('admin.announcements');

            // WhatsApp Logs (Admin Event perlu lihat status WA approve QR dikirim atau tidak!)
            Route::get('logs/whatsapp', [\App\Http\Controllers\Admin\WhatsAppController::class, 'index'])->name('admin.whatsapp.logs');
            Route::post('logs/whatsapp/{id}/resend', [\App\Http\Controllers\Admin\WhatsAppController::class, 'resend'])->name('admin.whatsapp.resend');
        });

        // Pembayaran lama (berbasis registrations) dialihkan ke halaman Keuangan yang dipakai alur peserta.
        Route::middleware('can:admin_keuangan')->group(function () {
            Route::redirect('payments', '/admin/finance');
            Route::redirect('payments/{any}', '/admin/finance')->where('any', '.*');
        });
    });
});
