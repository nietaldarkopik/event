<?php $__env->startSection('title', $event->title); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .hero-detail {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)), url('<?php echo e($event->banner_url ?: "https://images.unsplash.com/photo-1540575861501-7cf05a4b125a?auto=format&fit=crop&q=80&w=2070"); ?>');
            background-size: cover;
            background-position: center;
            padding: 120px 0 80px;
            color: white;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
        }

        .timeline-modern {
            position: relative;
            padding-left: 3rem;
        }

        .timeline-modern::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2.5rem;
        }

        .timeline-dot {
            position: absolute;
            left: -3rem;
            width: 32px;
            height: 32px;
            background: white;
            border: 4px solid var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            margin-right: 8px;
            color: white !important;
        }

        .share-btn:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="event-hero">
        <div class="container" data-aos="fade-up">
            <?php if($event->banner_image): ?>
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-12">
                        <img src="<?php echo e(asset('storage/' . $event->banner_image)); ?>" alt="<?php echo e($event->title); ?>"
                            class="img-fluid rounded-20 shadow-2xl animate__animated animate__zoomIn"
                            style="width: 100%; max-height: 500px; object-fit: cover;">
                    </div>
                </div>
            <?php endif; ?>
            <div class="row align-items-center">
                <div class="col-lg-12 animate__animated animate__fadeInLeft">
                    <span
                        class="badge badge-warning px-4 py-2 rounded-pill font-weight-bold mb-3 shadow-sm text-uppercase letter-spacing-1">
                        <?php echo e(str_replace('_', ' ', $event->event_type)); ?>

                    </span>
                    <h1 class="display-4 font-weight-bold mb-5 line-height-tight text-center">
                        <?php echo e($event->title); ?>

                    </h1>
                    <div class="d-flex flex-wrap align-items-center opacity-90 font-weight-light fs-5">
                        <div class="mr-4 mb-3"><i class="far fa-calendar-check mr-2 text-warning"></i>
                            <?php echo e(\Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y')); ?></div>
                        <div class="mr-4 mb-3"><i class="far fa-clock mr-2 text-info"></i>
                            <?php echo e(\Carbon\Carbon::parse($event->start_datetime)->format('H:i')); ?> WIB</div>
                        <div class="mb-3"><i class="fas fa-map-marker-alt mr-2 text-danger"></i> <?php echo e($event->location); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="container mb-5 mt-4">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8 mb-4">
                    <div class="card glass-card shadow-2xl border-0 overflow-hidden" data-aos="fade-up">
                        <div class="card-body p-5">
                            <div class="mb-5">
                                <h4 class="font-weight-bold mb-4 text-dark d-flex align-items-center">
                                    <i class="fas fa-info-circle text-primary mr-3"></i> Deskripsi Acara
                                </h4>
                                <div class="text-muted fs-5 line-height-relaxed rich-content"><?php echo \App\Support\Html::render($event->description); ?></div>
                            </div>

                            <?php $agendaItems = $event->agendaItems(); $agendaMultiDay = $agendaItems->map(fn($a) => $a->date ? \Carbon\Carbon::parse($a->date)->toDateString() : null)->filter()->unique()->count() > 1; ?>
                            <?php if($agendaItems->count() > 0): ?>
                                <div class="mt-5">
                                    <h4 class="font-weight-bold mb-5 text-dark d-flex align-items-center">
                                        <i class="fas fa-stream text-primary mr-3"></i> Jadwal & Rundown
                                    </h4>
                                    <div class="timeline-modern">
                                        <?php $__currentLoopData = $agendaItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rundown): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="timeline-item">
                                                <div class="timeline-dot shadow-sm">
                                                    <i class="fas fa-circle text-primary small"></i>
                                                </div>
                                                <div class="bg-light p-4 rounded-xl border-left-primary shadow-sm">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge badge-primary-light text-primary font-weight-bold px-3">
                                                            <?php if($agendaMultiDay && !empty($rundown->date)): ?><?php echo e(\Carbon\Carbon::parse($rundown->date)->translatedFormat('d M')); ?>, <?php endif; ?><?php echo e(\Carbon\Carbon::parse($rundown->time_start)->format('H:i')); ?><?php if($rundown->time_end): ?> - <?php echo e(\Carbon\Carbon::parse($rundown->time_end)->format('H:i')); ?><?php endif; ?>
                                                        </span>
                                                    </div>
                                                    <h5 class="font-weight-bold text-dark mb-0"><?php echo e($rundown->agenda); ?></h5>
                                                    <?php if(!empty($rundown->detail)): ?><div class="small text-muted mt-2"><?php echo e($rundown->detail); ?></div><?php endif; ?>
                                                    <?php if(!empty($rundown->body)): ?><div class="small text-muted mt-2 rich-content"><?php echo \App\Support\Html::render($rundown->body); ?></div><?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 120px; z-index: 10;">
                        <div class="card glass-card border-0 shadow-xl" data-aos="fade-left" data-aos-delay="200">
                            <div class="card-body p-4">
                                <h5 class="font-weight-bold text-dark mb-4 px-2">Tiket & Pendaftaran</h5>

                                <?php if($event->event_type != 'rapat' && $event->event_type != 'wisuda'): ?>
                                    <div class="bg-primary-light rounded-xl p-4 mb-4 text-center">
                                        <small class="text-muted d-block text-uppercase font-weight-bold mb-1">Harga
                                            Tiket</small>
                                        <h2 class="font-weight-bold text-primary mb-0">
                                            <?php if($event->is_paid): ?>
                                                Rp <?php echo e(number_format($event->price, 0, ',', '.')); ?>

                                            <?php else: ?>
                                                GRATIS
                                            <?php endif; ?>
                                        </h2>
                                    </div>
                                <?php endif; ?>

                                <?php
                                    $regStart = $event->registration_start ? \Carbon\Carbon::parse($event->registration_start) : null;
                                    $regEnd = $event->registration_end ? \Carbon\Carbon::parse($event->registration_end) : null;
                                    $regState = $event->registrationState();
                                    $regLabel = $regState['label'];
                                    $regClass = ['open' => 'badge-success', 'not_open' => 'badge-secondary', 'closed' => 'badge-danger', 'full' => 'badge-danger'][$regState['state']];
                                ?>
                                <ul class="list-unstyled mb-4 px-2">
                                    <li class="d-flex justify-content-between mb-3">
                                        <span class="text-muted"><i class="fas fa-users mr-2 text-primary"></i>
                                            Status:</span>
                                        <span class="badge <?php echo e($regClass); ?> px-3 rounded-pill"><?php echo e($regLabel); ?></span>
                                    </li>
                                    <?php if($regStart || $regEnd): ?>
                                        <li class="mb-3">
                                            <div class="text-muted mb-1"><i class="fas fa-calendar-check mr-2 text-primary"></i> Registrasi:</div>
                                            <div class="d-flex justify-content-between small pl-4">
                                                <span class="text-muted">Dibuka</span>
                                                <span class="font-weight-bold text-dark"><?php echo e($regStart ? $regStart->translatedFormat('d M Y, H:i') . ' WIB' : '-'); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between small pl-4 mt-1">
                                                <span class="text-muted">Ditutup</span>
                                                <span class="font-weight-bold text-dark"><?php echo e($regEnd ? $regEnd->translatedFormat('d M Y, H:i') . ' WIB' : '-'); ?></span>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                    <?php
                                        $hasCertificate = $event->certificate_enabled || $event->allow_certificate;
                                        $hasVenue = filled($event->venue_name) || filled($event->address);
                                        $hasMeeting = filled($event->meeting_link);
                                        $mediaLabel = $hasVenue && $hasMeeting ? 'Hybrid (Luring & Daring)'
                                            : ($hasMeeting ? 'Daring (Online)' : ($hasVenue ? 'Luring (Offline)' : null));
                                    ?>
                                    <?php if($event->event_type == 'wisuda' || ($event->event_type != 'rapat' && $hasCertificate)): ?>
                                        <li class="d-flex justify-content-between mb-3">
                                            <span class="text-muted"><i class="fas fa-certificate mr-2 text-primary"></i>
                                                Fasilitas:</span>
                                            <span class="font-weight-bold text-dark small">
                                                <?php if($event->event_type == 'wisuda'): ?>
                                                    Ijazah
                                                <?php else: ?>
                                                    E-Sertifikat
                                                <?php endif; ?>
                                            </span>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($mediaLabel): ?>
                                        <li class="d-flex justify-content-between <?php echo e($hasMeeting ? 'mb-3' : ''); ?>">
                                            <span class="text-muted"><i class="fas fa-globe mr-2 text-primary"></i>
                                                Media:</span>
                                            <span class="font-weight-bold text-dark small"><?php echo e($mediaLabel); ?></span>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($hasMeeting): ?>
                                        <li class="d-flex justify-content-between">
                                            <span class="text-muted"><i class="fas fa-link mr-2 text-primary"></i> Link
                                                Meeting:</span>
                                            <a href="<?php echo e($event->meeting_link); ?>" target="_blank"
                                                class="font-weight-bold text-primary small text-truncate ml-2"
                                                style="max-width: 150px;">
                                                Klik Disini <i class="fas fa-external-link-alt ml-1"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>

                                <?php if(strtolower($event->status) == 'published'): ?>
                                    <?php if($event->event_type != 'rapat'): ?>
                                        <?php if($regState['state'] !== 'open'): ?>
                                            <button type="button" class="btn btn-secondary btn-block btn-lg py-3 rounded-xl font-weight-bold mb-2" disabled>
                                                <i class="fas fa-lock mr-2"></i>
                                                <?php echo e(['not_open' => 'Pendaftaran Belum Dibuka', 'closed' => 'Pendaftaran Ditutup', 'full' => 'Kuota Penuh'][$regState['state']]); ?>

                                            </button>
                                            <p class="small text-muted text-center mb-3"><?php echo e($regState['message']); ?></p>
                                        <?php elseif($event->event_type == 'wisuda'): ?>
                                            <button type="button" data-toggle="modal" data-target="#wisudaRegisterModal"
                                                class="btn btn-primary-modern btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-lg shadow-primary-20 hover-lift mb-3">
                                                Daftar Sekarang <i class="fas fa-university ml-2"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" data-toggle="modal" data-target="#generalRegisterModal"
                                                class="btn btn-primary-modern btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-lg shadow-primary-20 hover-lift mb-3">
                                                Daftar Sekarang <i class="fas fa-edit ml-2"></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php if(($event->certificate_enabled || $event->allow_certificate) && !in_array($event->event_type, ['rapat', 'wisuda'])): ?>
                                            <button type="button" data-toggle="modal" data-target="#claimCertificateModal"
                                                class="btn btn-success btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-sm hover-lift mb-3 animate__animated animate__pulse animate__infinite">
                                                <i class="fas fa-certificate mr-2"></i> Ambil Sertifikat
                                            </button>
                                        <?php endif; ?>

                                        <?php if($regState['state'] === 'open'): ?>
                                        <div class="text-center my-3">
                                            <span class="text-muted small">
                                                <?php if($event->event_type == 'wisuda'): ?>
                                                    Isi formulir pendaftaran wisuda dengan data yang benar
                                                <?php else: ?>
                                                    Isi formulir pendaftaran dengan data yang benar
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if(!$event->is_internal && $event->meeting_link): ?>
                                        <a href="<?php echo e($event->meeting_link); ?>" target="_blank"
                                            class="btn btn-success btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-sm hover-lift mb-3">
                                            <i class="fas fa-video mr-2"></i> Gabung Meeting (Online)
                                        </a>
                                    <?php endif; ?>

                                    <button type="button"
                                        class="btn <?php echo e(($event->event_type == 'rapat' || $event->event_type == 'wisuda') ? 'btn-primary' : 'btn-outline-primary'); ?> btn-block btn-lg py-3 rounded-xl font-weight-bold hover-lift mb-3"
                                        data-toggle="modal" data-target="#invitationModal">
                                        <i class="fas fa-ticket-alt mr-2"></i> Buka Undangan
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-block btn-lg py-3 rounded-xl font-weight-bold"
                                        disabled>
                                        Pendaftaran Ditutup
                                    </button>
                                <?php endif; ?>

                                <?php if(session('success')): ?>
                                    <div class="alert alert-success mt-3 mb-0 rounded-xl">
                                        <i class="fas fa-check-circle mr-2"></i> <?php echo e(session('success')); ?>

                                    </div>
                                <?php endif; ?>
                                <?php if(session('error')): ?>
                                    <div class="alert alert-danger mt-3 mb-0 rounded-xl">
                                        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo e(session('error')); ?>

                                    </div>
                                <?php endif; ?>

                                <?php echo $__env->make('components.recaptcha-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                                <div class="text-center mt-4 border-top pt-4">
                                    <p class="small text-muted font-weight-bold text-uppercase mb-3 letter-spacing-1">
                                        Bagikan
                                        Event</p>
                                    <div class="d-flex justify-content-center">
                                        <a href="https://wa.me/?text=<?php echo e(urlencode($event->title . ' - ' . url()->current())); ?>"
                                            target="_blank" class="share-btn bg-success shadow-sm">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <a href="https://twitter.com/intent/tweet?text=<?php echo e(urlencode($event->title)); ?>&url=<?php echo e(urlencode(url()->current())); ?>"
                                            target="_blank" class="share-btn bg-info shadow-sm">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode(url()->current())); ?>"
                                            target="_blank" class="share-btn bg-primary shadow-sm">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if($event->event_type != 'rapat'): ?>
                            <?php $attWindow = \App\Services\AttendanceService::selfWindow($event); ?>
                            <div class="card glass-card border-0 shadow-sm mt-3" data-aos="fade-up" data-aos-delay="300">
                                <div class="card-body p-4">
                                    <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-clipboard-check text-primary mr-2"></i> Cara Presensi di Lokasi</h6>
                                    <ol class="small text-muted pl-3 mb-2">
                                        <li class="mb-1"><b class="text-dark">Lewat petugas:</b> tunjukkan QR Code undangan (dikirim via WhatsApp/email setelah pendaftaran disetujui) ke petugas.</li>
                                        <li><b class="text-dark">Mandiri:</b> di lokasi, scan QR yang ditampilkan panitia di layar, lalu masukkan <b>kode undangan</b> Anda dan tekan Presensi.</li>
                                    </ol>
                                    <?php if($attWindow): ?>
                                        <div class="small text-muted"><i class="far fa-clock mr-1"></i> Presensi mandiri dibuka <?php echo e($attWindow[0]->translatedFormat('H:i')); ?> WIB sampai acara selesai. Terlambat tetap bisa presensi selama acara berlangsung.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="400">
                            <?php
                                $questionWaNumber = $event->contact_whatsapp ?? '6282216668383';
                                $questionWaMessage = urlencode("Halo Admin " . ($event->organizer_unit ?? 'EVENTUNWIM') . ", saya ingin bertanya tentang acara: " . $event->title);
                                $questionWaUrl = "https://wa.me/{$questionWaNumber}?text={$questionWaMessage}";
                            ?>
                            <p class="text-muted small px-4">Ada pertanyaan tentang event ini? <br>
                                <a href="#" onclick="onClickWhatsApp(event, '<?php echo e($questionWaUrl); ?>', '<?php echo e($event->slug); ?>')"
                                    class="text-primary font-weight-bold">Hubungi Panitia</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invitation Modal -->
        <div class="modal fade" id="invitationModal" tabindex="-1" role="dialog" aria-labelledby="invitationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content glass-card border-0">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="bg-primary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width: 80px; height: 80px;">
                            <i class="fas fa-key fa-2x text-primary"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">Buka Undangan Digital</h4>
                        <p class="text-muted mb-4 px-4">Masukkan kode unik yang Anda terima melalui WhatsApp dari sistem
                            kami.
                        </p>

                        <form action="<?php echo e(route('events.invitation.check')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="event_id" value="<?php echo e($event->id); ?>">
                            <div class="form-group mb-4">
                                <input type="text" name="unique_code"
                                    class="form-control form-control-lg text-center font-weight-bold letter-spacing-2 rounded-xl border-2"
                                    placeholder="KODE-UNIQUE" required style="height: 60px; font-size: 1.25rem;">
                            </div>
                            <button type="submit"
                                class="btn btn-primary btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-lg">
                                Lihat Undangan Saya <i class="fas fa-chevron-right ml-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Wisuda Registration Modal -->
        <?php if($event->event_type == 'wisuda'): ?>
            <div class="modal fade" id="wisudaRegisterModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
                        <div class="modal-header border-0 bg-primary-dark text-white p-4">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas fa-user-graduate mr-2"></i> Form Pendaftaran Wisuda
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?php echo e(route('events.register', $event->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="modal-body p-4">
                                <?php echo $__env->make('public.events._session_picker', ['event' => $event], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <p class="text-muted mb-4">
                                    Lengkapi data di bawah ini dengan benar. Setelah pendaftaran Anda diverifikasi dan memenuhi syarat,
                                    kode undangan dan informasi sertifikat akan dikirim melalui email dan WhatsApp.
                                </p>
                                <div class="form-group">
                                    <label>NIM</label>
                                    <input type="text" name="nim"
                                        class="form-control <?php $__errorArgs = ['nim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('nim')); ?>" required>
                                    <?php $__errorArgs = ['nim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name"
                                        class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('name')); ?>" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label>Fakultas</label>
                                    <select name="fakultas_id" id="wisuda-fakultas-select"
                                        class="form-control <?php $__errorArgs = ['fakultas_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="">-- Pilih Fakultas --</option>
                                        <?php $__currentLoopData = ($faculties ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faculty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($faculty->id); ?>" <?php echo e(old('fakultas_id') == $faculty->id ? 'selected' : ''); ?>>
                                                <?php echo e($faculty->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['fakultas_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label>Program Studi</label>
                                    <select name="program_studi_id" id="wisuda-prodi-select"
                                        class="form-control <?php $__errorArgs = ['program_studi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="">-- Pilih Program Studi --</option>
                                        <?php $__currentLoopData = ($studyPrograms ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($prodi->id); ?>" data-parent="<?php echo e($prodi->parent_id); ?>"
                                                <?php echo e(old('program_studi_id') == $prodi->id ? 'selected' : ''); ?>>
                                                <?php echo e($prodi->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['program_studi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email"
                                        class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('email')); ?>" required>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label>WhatsApp Aktif</label>
                                    <input type="text" name="whatsapp"
                                        class="form-control <?php $__errorArgs = ['whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('whatsapp')); ?>" required>
                                    <?php $__errorArgs = ['whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light p-3">
                                <button type="button" class="btn btn-light rounded-pill px-4"
                                    data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    Kirim Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var fakultasSelect = document.getElementById('wisuda-fakultas-select');
                    var prodiSelect = document.getElementById('wisuda-prodi-select');
                    if (!fakultasSelect || !prodiSelect) return;

                    var options = Array.prototype.slice.call(prodiSelect.options);

                    function filterProdi() {
                        var facultyId = fakultasSelect.value;
                        prodiSelect.value = '';
                        options.forEach(function (opt) {
                            if (!opt.value) {
                                opt.hidden = false;
                                opt.disabled = false;
                                return;
                            }
                            var parentId = opt.getAttribute('data-parent');
                            var visible = !facultyId || parentId === facultyId;
                            opt.hidden = !visible;
                            opt.disabled = !visible;
                        });
                    }

                    fakultasSelect.addEventListener('change', filterProdi);
                    filterProdi();
                });
            </script>
        <?php endif; ?>

        <!-- General Registration Modal (non-wisuda, non-rapat events) -->
        <?php if($event->event_type != 'wisuda' && $event->event_type != 'rapat'): ?>
            <div class="modal fade" id="generalRegisterModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
                        <div class="modal-header border-0 bg-primary-dark text-white p-4">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas fa-edit mr-2"></i> Form Pendaftaran
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?php echo e(route('events.register', $event->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="modal-body p-4">
                                <?php echo $__env->make('public.events._session_picker', ['event' => $event], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <p class="text-muted mb-4">
                                    Lengkapi data di bawah ini untuk mendaftar acara <strong><?php echo e($event->title); ?></strong>.
                                </p>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name"
                                        class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('name')); ?>" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label>WhatsApp Aktif</label>
                                    <input type="text" name="whatsapp"
                                        class="form-control <?php $__errorArgs = ['whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('whatsapp')); ?>" required>
                                    <?php $__errorArgs = ['whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email"
                                        class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('email')); ?>" required>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label>Instansi / Unit Kerja</label>
                                    <input type="text" name="unit"
                                        class="form-control <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('unit')); ?>" required>
                                    <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light p-3">
                                <button type="button" class="btn btn-light rounded-pill px-4"
                                    data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    Kirim Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Faculty Choice Modal for Wisuda -->
        <?php if($event->event_type == 'wisuda'): ?>
            <div class="modal fade" id="facultyModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
                        <div class="modal-header border-0 bg-primary-dark text-white p-4">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-university mr-2"></i> Pilih Fakultas Anda
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            <p class="text-muted mb-4 px-2">Silakan pilih fakultas untuk menghubungi PIC pendaftaran yang
                                sesuai:
                            </p>
                            <div class="row no-gutters">
                                <?php
                                    $faculties = [
                                        ['id' => 'wa_pertanian', 'name' => 'Fakultas Pertanian', 'icon' => 'fa-seedling', 'color' => 'success'],
                                        ['id' => 'wa_kehutanan', 'name' => 'Fakultas Kehutanan', 'icon' => 'fa-tree', 'color' => 'teal'],
                                        ['id' => 'wa_teknik', 'name' => 'Fakultas Teknik', 'icon' => 'fa-cog', 'color' => 'primary'],
                                        ['id' => 'wa_ekonomi', 'name' => 'Fakultas Ekonomi', 'icon' => 'fa-chart-line', 'color' => 'warning'],
                                    ];
                                ?>

                                <?php $__currentLoopData = $faculties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-12 mb-3">
                                        <?php
                                            $waNum = $event->{$fac['id']} ?? ($event->contact_whatsapp ?? '6282216668383');
                                            $waMsg = urlencode("Halo Admin Wisuda " . $fac['name'] . ", saya ingin mendaftar untuk: " . $event->title);
                                            $waLink = "https://wa.me/{$waNum}?text={$waMsg}";
                                        ?>
                                        <a href="#" onclick="onClickWhatsApp(event, '<?php echo e($waLink); ?>', '<?php echo e($event->slug); ?>')" class="text-decoration-none">
                                            <div
                                                class="faculty-card p-3 rounded-xl bg-white border shadow-sm transition-all hover-scale d-flex align-items-center">
                                                <div class="rounded-circle bg-<?php echo e($fac['color']); ?>-light d-flex align-items-center justify-content-center mr-3"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="fas <?php echo e($fac['icon']); ?> text-<?php echo e($fac['color']); ?> fa-lg"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="font-weight-bold text-dark mb-0"><?php echo e($fac['name']); ?></h6>
                                                    <small class="text-muted">Klik untuk Chat WhatsApp PIC</small>
                                                </div>
                                                <i class="fas fa-chevron-right text-muted small"></i>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light p-3">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .faculty-card:hover {
                    border-color: var(--primary);
                    background: #f8fbff !important;
                    transform: translateY(-2px);
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                }

                .bg-teal-light {
                    background-color: rgba(20, 184, 166, 0.1);
                }

                .text-teal {
                    color: #14b8a6;
                }

                .bg-success-light {
                    background-color: rgba(34, 197, 94, 0.1);
                }

                .bg-primary-light {
                    background-color: rgba(79, 70, 229, 0.1);
                }

                .bg-warning-light {
                    background-color: rgba(245, 158, 11, 0.1);
                }

                .rounded-xl {
                    border-radius: 1rem !important;
                }
            </style>
        <?php endif; ?>

        <!-- Claim Certificate Modal -->
        <?php if(($event->event_type == 'workshop' || $event->event_type == 'seminar') && $event->allow_certificate): ?>
            <div class="modal fade" id="claimCertificateModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
                        <div class="modal-header border-0 bg-success text-white p-4">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-certificate mr-2"></i> Ambil Sertifikat
                                Digital</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <div class="bg-success-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-file-download fa-2x text-success"></i>
                            </div>
                            <h4 class="font-weight-bold text-dark mb-2">Masukkan Kode Unik</h4>
                            <p class="text-muted mb-4 px-4">Sertifikat Anda siap diunduh. Masukkan kode unik untuk memverifikasi
                                data Anda.</p>

                            <form action="<?php echo e(route('events.certificate.claim')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="event_id" value="<?php echo e($event->id); ?>">
                                <div class="form-group mb-4">
                                    <input type="text" name="unique_code"
                                        class="form-control form-control-lg text-center font-weight-bold letter-spacing-2 rounded-xl border-2"
                                        placeholder="KODE-ANDA" required style="height: 60px; font-size: 1.25rem;">
                                </div>
                                <button type="submit"
                                    class="btn btn-success btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-lg">
                                    Unduh Sertifikat Sekarang <i class="fas fa-download ml-2"></i>
                                </button>
                            </form>
                        </div>
                        <div class="modal-footer border-0 bg-light p-3">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/public/events/show.blade.php ENDPATH**/ ?>