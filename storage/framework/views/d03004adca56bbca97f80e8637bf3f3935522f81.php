<?php $__env->startSection('title', 'Daftar Peserta'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center">
    <h1>Daftar Peserta</h1>
    <div>
        <a href="<?php echo e(route('attendance.scanner')); ?><?php echo e(optional($selectedEvent)->id ? '?event_id=' . $selectedEvent->id : ''); ?>" class="btn btn-outline-success">
            <i class="fas fa-camera"></i> Scan Presensi
        </a>
        <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#importModal">
            <i class="fas fa-file-import"></i> Impor CSV
        </button>
        <a href="<?php echo e(route('admin.participants.create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Peserta</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<?php
    $statusMap = ['approved' => ['Disetujui', 'success'], 'pending' => ['Menunggu', 'warning'], 'pending_payment' => ['Menunggu Bayar', 'warning'], 'rejected' => ['Ditolak', 'danger']];
    $hasSessions = $selectedEvent && $sessions->count();
    $hasCerts = $selectedEvent && $certificateCategories->count();
    $filtered = request()->filled('q') || request()->filled('status') || request()->filled('session_id') || request()->filled('presence');
?>

<div class="row mb-3">
    <?php $__currentLoopData = [['Total peserta', $stats['total'], 'users', 'primary'], ['Disetujui', $stats['approved'], 'check-circle', 'success'], ['Menunggu', $stats['waiting'], 'hourglass-half', 'warning'], ['Sudah hadir', $stats['present'], 'clipboard-check', 'info']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$lbl, $num, $ico, $clr]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-md-3 mb-2">
            <div class="p-stat">
                <span class="p-stat-ic text-<?php echo e($clr); ?>"><i class="fas fa-<?php echo e($ico); ?>"></i></span>
                <div><div class="p-stat-n"><?php echo e(number_format($num)); ?></div><div class="p-stat-l"><?php echo e($lbl); ?></div></div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="card">
    <div class="card-body pb-2">
        <form method="GET" id="filterForm">
            <div class="form-row align-items-center">
                <div class="col-lg-3 mb-2">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                        <input type="search" name="q" id="q" value="<?php echo e($q); ?>" class="form-control" placeholder="Cari nama, WhatsApp, email, NIM, atau kode..." autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <select name="event_id" class="form-control auto-submit">
                        <option value="">Semua event</option>
                        <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($event->id); ?>" <?php echo e(optional($selectedEvent)->id == $event->id ? 'selected' : ''); ?>><?php echo e(\Illuminate\Support\Str::limit($event->title, 55)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-6 col-lg-2 mb-2">
                    <select name="status" class="form-control auto-submit">
                        <option value="">Semua status</option>
                        <?php $__currentLoopData = $statusMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => [$lbl]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php echo e(request('status') === $k ? 'selected' : ''); ?>><?php echo e($lbl); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-6 col-lg-2 mb-2">
                    <select name="presence" class="form-control auto-submit">
                        <option value="">Semua kehadiran</option>
                        <option value="present" <?php echo e(request('presence') === 'present' ? 'selected' : ''); ?>>Sudah hadir</option>
                        <option value="absent" <?php echo e(request('presence') === 'absent' ? 'selected' : ''); ?>>Belum hadir</option>
                    </select>
                </div>
                <?php if($hasSessions): ?>
                    <div class="col-6 col-lg-2 mb-2">
                        <select name="session_id" class="form-control auto-submit">
                            <option value="">Semua sesi</option>
                            <option value="none" <?php echo e(request('session_id') === 'none' ? 'selected' : ''); ?>>Belum bersesi</option>
                            <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($sess->id); ?>" <?php echo e((string) request('session_id') === (string) $sess->id ? 'selected' : ''); ?>><?php echo e($sess->title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="col-auto mb-2">
                    <?php if($filtered): ?><a href="<?php echo e(route('admin.participants.index', array_filter(['event_id' => optional($selectedEvent)->id]))); ?>" class="btn btn-link text-muted" title="Hapus semua filter"><i class="fas fa-times"></i> Reset</a><?php endif; ?>
                </div>
            </div>
        </form>
    </div>
    <?php if($selectedEvent && $sessions->count()): ?>
        <div class="card-body border-bottom bg-light">
            <div class="d-flex flex-wrap align-items-center mb-2">
                <b class="mr-3"><i class="fas fa-layer-group text-primary"></i> Sesi:</b>
                <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge badge-light border mr-2 p-2">
                        <?php echo e($sess->title); ?>

                        <?php if($sess->start_datetime): ?>(<?php echo e(\Carbon\Carbon::parse($sess->start_datetime)->format('H.i')); ?><?php if($sess->end_datetime): ?>&ndash;<?php echo e(\Carbon\Carbon::parse($sess->end_datetime)->format('H.i')); ?><?php endif; ?>)<?php endif; ?>
                        &middot; <b><?php echo e($sess->taken); ?></b><?php if($sess->max_participants): ?> / <?php echo e($sess->max_participants); ?><?php endif; ?> peserta
                        <?php if($sess->is_full): ?><span class="text-danger">penuh</span><?php endif; ?>
                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <span class="badge badge-secondary ml-1">Mode: <?php echo e(['none' => 'Tanpa sesi', 'admin' => 'Ditetapkan panitia', 'choose' => 'Dipilih peserta'][$selectedEvent->session_mode ?? 'none'] ?? '-'); ?></span>
            </div>
            <form id="bulkSessionForm" class="form-inline">
                <span class="mr-2 small text-muted">Tetapkan massal:</span>
                <select id="bulkField" class="form-control form-control-sm mr-2">
                    <option value="all">Semua peserta</option>
                    <?php if(!empty($criteria['fakultas']) && count($criteria['fakultas'])): ?><option value="fakultas">Fakultas</option><?php endif; ?>
                    <?php if(!empty($criteria['program_studi']) && count($criteria['program_studi'])): ?><option value="program_studi">Program Studi</option><?php endif; ?>
                    <?php if(!empty($criteria['type']) && count($criteria['type'])): ?><option value="type">Tipe</option><?php endif; ?>
                </select>
                <select id="bulkValue" class="form-control form-control-sm mr-2" style="display:none; max-width:240px"></select>
                <span class="mr-2 small text-muted">&rarr; ke</span>
                <select id="bulkSession" class="form-control form-control-sm mr-2">
                    <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($sess->id); ?>"><?php echo e($sess->title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div class="form-check mr-2 small">
                    <input type="checkbox" class="form-check-input" id="bulkOnlyNew" checked>
                    <label class="form-check-label" for="bulkOnlyNew">hanya yang belum bersesi</label>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
            </form>
        </div>
    <?php endif; ?>
    <div class="card-body pt-2 pb-0">
        <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
            <span>Menampilkan <b><?php echo e($participants->firstItem() ?: 0); ?>&ndash;<?php echo e($participants->lastItem() ?: 0); ?></b> dari <b><?php echo e(number_format($participants->total())); ?></b> peserta<?php echo e($filtered ? ' (hasil filter)' : ''); ?></span>
            <form method="GET" class="form-inline">
                <?php $__currentLoopData = request()->except(['per_page', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <label class="mr-2 mb-0">Baris</label>
                <select name="per_page" class="form-control form-control-sm auto-submit">
                    <?php $__currentLoopData = [20, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($n); ?>" <?php echo e($perPage == $n ? 'selected' : ''); ?>><?php echo e($n); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 no-tidy p-table">
            <thead>
                <tr>
                    <th>Peserta</th>
                    <?php if (! ($selectedEvent)): ?><th>Event</th><?php endif; ?>
                    <th>Kode &middot; Kursi</th>
                    <?php if($hasSessions): ?><th style="min-width:150px">Sesi</th><?php endif; ?>
                    <th>Kehadiran</th>
                    <?php if($hasCerts): ?><th style="min-width:150px">Kategori Sertifikat</th><?php endif; ?>
                    <th>Status</th>
                    <th class="text-right p-sticky">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $checkIn = $item->attendanceLogs->firstWhere('type', 'check_in');
                        $checkOut = $item->attendanceLogs->first(fn ($log) => in_array($log->type, ['check_out', 'consumption']));
                        [$sLabel, $sColor] = $statusMap[$item->status] ?? [$item->status ?: '-', 'secondary'];
                        $sub = collect([$item->nim, $item->program_studi ?: optional($item->unitRelation)->name, $item->fakultas, $item->jabatan, $item->unit])->filter()->unique()->take(2)->implode(' · ');
                    ?>
                    <tr>
                        <td>
                            <div class="p-name"><?php echo e($item->name); ?> <?php if($item->type): ?><span class="list-chip"><?php echo e(ucfirst($item->type)); ?></span><?php endif; ?></div>
                            <?php if($sub): ?><div class="list-sub"><?php echo e(\Illuminate\Support\Str::limit($sub, 60)); ?></div><?php endif; ?>
                            <div class="list-sub">
                                <?php if($item->whatsapp): ?><i class="fab fa-whatsapp"></i> <?php echo e($item->whatsapp); ?><?php endif; ?>
                                <?php if($item->email): ?><span class="ml-2"><i class="far fa-envelope"></i> <?php echo e(\Illuminate\Support\Str::limit($item->email, 28)); ?></span><?php endif; ?>
                            </div>
                        </td>
                        <?php if (! ($selectedEvent)): ?><td class="small"><?php echo e(\Illuminate\Support\Str::limit($item->event->title ?? '-', 34)); ?></td><?php endif; ?>
                        <td class="text-nowrap">
                            <code class="p-code"><?php echo e(optional($item->invitation)->unique_code ?: '—'); ?></code>
                            <form class="seat-form mt-1" data-id="<?php echo e($item->id); ?>">
                                <div class="input-group input-group-sm" style="width:118px">
                                    <input type="text" name="seat_number" class="form-control" placeholder="Kursi" value="<?php echo e($item->seat_number); ?>">
                                    <div class="input-group-append"><button type="submit" class="btn btn-outline-secondary" title="Simpan nomor kursi"><i class="fas fa-check"></i></button></div>
                                </div>
                            </form>
                        </td>
                        <?php if($hasSessions): ?>
                            <td>
                                <select class="form-control form-control-sm session-select" data-id="<?php echo e($item->id); ?>">
                                    <option value="">Belum ditetapkan</option>
                                    <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($sess->id); ?>" <?php echo e($item->session_id == $sess->id ? 'selected' : ''); ?>><?php echo e($sess->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        <?php endif; ?>
                        <td class="text-nowrap small">
                            <?php if($checkIn): ?>
                                <div><span class="text-success"><i class="fas fa-sign-in-alt"></i></span> <?php echo e($checkIn->scanned_at->format('d/m H.i')); ?></div>
                                <div class="<?php echo e($checkOut ? '' : 'text-muted'); ?>"><i class="fas fa-sign-out-alt"></i> <?php echo e($checkOut ? $checkOut->scanned_at->format('d/m H.i') : 'belum pulang'); ?></div>
                            <?php else: ?>
                                <span class="text-muted">Belum hadir</span>
                            <?php endif; ?>
                        </td>
                        <?php if($hasCerts): ?>
                            <td>
                                <select class="form-control form-control-sm cert-category-select" data-id="<?php echo e($item->id); ?>">
                                    <option value="">Belum diatur</option>
                                    <?php $__currentLoopData = $certificateCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cat->id); ?>" <?php echo e($item->certificate_category_id == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        <?php endif; ?>
                        <td><span class="badge badge-<?php echo e($sColor); ?> px-2 py-1"><?php echo e($sLabel); ?></span></td>
                        <td class="text-right text-nowrap p-sticky">
                            <?php if($item->status !== 'approved'): ?>
                                <form action="<?php echo e(route('participants.approve', $item->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Setujui peserta ini? Undangan + QR akan dikirim ke WhatsApp.');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui &amp; kirim undangan"><i class="fas fa-check"></i> Setujui</button>
                                </form>
                            <?php else: ?>
                                <form action="<?php echo e(route('participants.resend-whatsapp', $item->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Kirim ulang undangan + QR ke WhatsApp peserta ini?');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Kirim ulang undangan ke WhatsApp"><i class="fab fa-whatsapp"></i></button>
                                </form>
                            <?php endif; ?>
                            <a href="<?php echo e(route('admin.participants.show', $item->id)); ?>" class="btn btn-sm btn-outline-info" title="Detail &amp; QR"><i class="fas fa-eye"></i></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" title="Menu lainnya"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="<?php echo e(route('admin.participants.edit', $item->id)); ?>"><i class="fas fa-edit fa-fw mr-1"></i> Edit data</a>
                                    <?php if($item->event_id): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('attendance.event', $item->event_id)); ?>"><i class="fas fa-clipboard-check fa-fw mr-1"></i> Presensi event ini</a>
                                        <a class="dropdown-item" href="<?php echo e(route('certificate-generator.show', $item->event_id)); ?>"><i class="fas fa-award fa-fw mr-1"></i> Sertifikat event ini</a>
                                        <a class="dropdown-item" href="<?php echo e(route('admin.events.wizard.edit', $item->event_id)); ?>"><i class="fas fa-calendar fa-fw mr-1"></i> Buka event</a>
                                    <?php endif; ?>
                                    <div class="dropdown-divider"></div>
                                    <form action="<?php echo e(route('admin.participants.destroy', $item->id)); ?>" method="POST" onsubmit="return confirm('Hapus peserta ini beserta undangan dan datanya?');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash fa-fw mr-1"></i> Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                        <?php echo e($filtered ? 'Tidak ada peserta yang cocok dengan filter.' : 'Belum ada peserta.'); ?>

                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
        <small class="text-muted">Halaman <?php echo e($participants->currentPage()); ?> dari <?php echo e($participants->lastPage()); ?></small>
        <?php echo e($participants->links()); ?>

    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('participants.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Impor Peserta dari CSV</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Cara import peserta:</strong>
                        <ol class="mb-0 pl-3">
                            <li>Unduh template CSV di bawah ini.</li>
                            <li>Isi data peserta pada template tersebut, satu peserta per baris. Jangan hapus atau ubah baris judul (header) paling atas.</li>
                            <li>Kolom <code>unit</code> diisi sesuai nama pada daftar <em>Master Data Unit</em> di bawah (fakultas atau program studi), boleh dikosongkan jika tidak relevan (mis. peserta umum).</li>
                            <li>Pilih event tujuan, lalu unggah berkas CSV yang sudah diisi.</li>
                            <li>Peserta dengan email yang sudah terdaftar di event yang sama akan dilewati (tidak diimpor ulang).</li>
                        </ol>
                    </div>
                    <a href="<?php echo e(route('participants.import-template')); ?>" class="btn btn-sm btn-outline-success mb-3">
                        <i class="fas fa-download"></i> Unduh Template CSV
                    </a>
                    <div class="form-group">
                        <label for="import_event_id">Event</label>
                        <select class="form-control" id="import_event_id" name="event_id" required>
                            <option value="">-- Pilih Event --</option>
                            <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($event->id); ?>"><?php echo e($event->title); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group" id="import_session_group" style="display:none">
                        <label for="import_default_session_id">Sesi Bawaan</label>
                        <select class="form-control" id="import_default_session_id" name="default_session_id">
                            <option value="">-- Tanpa sesi / ikuti kolom "sesi" di CSV --</option>
                        </select>
                        <small class="text-muted d-block mt-1">Dipakai untuk baris CSV yang kolom <b>sesi</b>-nya kosong. Kolom <b>sesi</b> di CSV (nomor urut sesi: 1, 2, ... atau judul sesi persis) akan menimpa pilihan ini.</small>
                    </div>
                    <div class="form-group">
                        <label for="import_status_option">Status Peserta Setelah Diimpor</label>
                        <select class="form-control" id="import_status_option" name="status_option">
                            <option value="auto">Otomatis (ikuti pengaturan event: gratis = langsung disetujui, berbayar = menunggu pembayaran)</option>
                            <option value="approved">Langsung Disetujui</option>
                            <option value="pending">Menunggu Persetujuan (Pending)</option>
                        </select>
                        <small class="text-muted d-block mt-1">Peserta berstatus <b>Menunggu</b> tidak langsung dapat kode QR/undangan — panitia bisa menyetujuinya nanti lewat tombol "Setujui" di daftar peserta.</small>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="import_send_wa" name="send_wa" value="1" checked>
                        <label class="form-check-label" for="import_send_wa">Kirim WhatsApp (kode registrasi + QR) otomatis ke peserta yang langsung disetujui</label>
                    </div>
                    <div class="form-group" id="import_wa_message_group">
                        <label for="import_wa_message">Isi Pesan WhatsApp</label>
                        <textarea class="form-control" id="import_wa_message" name="wa_message" rows="8"><?php echo e($defaultWaTemplate); ?></textarea>
                        <small class="text-muted d-block mt-1">
                            Placeholder yang bisa dipakai: <code>{nama}</code>, <code>{event}</code>, <code>{tanggal}</code>, <code>{lokasi}</code>, <code>{kode}</code>, <code>{link}</code> — otomatis diganti data masing-masing peserta. Gambar QR tetap dilampirkan terpisah.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="import_file">Berkas CSV</label>
                        <input type="file" class="form-control-file" id="import_file" name="file" accept=".csv,.txt" required>
                        <small class="text-muted d-block mt-1">
                            Kolom yang didukung (baris pertama harus header, urutan bebas):
                            <strong>nama, email, whatsapp, tipe, nim, program_studi, fakultas, jabatan, unit, no_kursi, sesi, kode_registrasi</strong>.<br>
                            &bull; <strong>nama</strong>, <strong>whatsapp</strong>, <strong>tipe</strong> wajib diisi.<br>
                            &bull; <strong>tipe</strong>: mahasiswa, dosen, tendik, atau umum.<br>
                            &bull; <strong>nim</strong>: khusus mahasiswa. <strong>jabatan</strong>: khusus dosen/tendik.<br>
                            &bull; <strong>unit</strong>: nama fakultas/prodi sesuai Master Data Unit di bawah (opsional).<br>
                            &bull; <strong>no_kursi</strong>: nomor kursi (opsional), harus unik per event — baris dengan kursi yang sudah dipakai akan dilewati.<br>
                            &bull; <strong>sesi</strong>: sesi peserta (opsional) — isi nomor urut sesi (1, 2, ...) sesuai urutan waktu, atau judul sesi persis. Baris dengan sesi yang tidak dikenali akan dilewati.<br>
                            &bull; <strong>kode_registrasi</strong>: kode undangan/QR yang sudah ditentukan sendiri (opsional, kosongkan untuk digenerate otomatis). Harus unik di seluruh sistem, dan hanya berlaku jika status peserta langsung <strong>Disetujui</strong> — untuk status Pending, kode baru tetap dibuat otomatis saat panitia menyetujui nanti.
                        </small>
                    </div>
                    <div class="form-group mb-0">
                        <a class="d-inline-block mb-1" data-toggle="collapse" href="#unitMasterData" role="button" aria-expanded="false" aria-controls="unitMasterData">
                            <i class="fas fa-list"></i> Lihat Master Data Unit (Fakultas &amp; Program Studi)
                        </a>
                        <div class="collapse" id="unitMasterData">
                            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nama Unit</th>
                                            <th>Jenis</th>
                                            <th>Induk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($unit->name); ?></td>
                                                <td><?php echo e($unit->type); ?></td>
                                                <td><?php echo e($unit->parent->name ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr><td colspan="3" class="text-muted">Belum ada master data unit.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .p-stat { background: #fff; border: 1px solid #e3e8e5; border-radius: 10px; padding: .7rem 1rem; display: flex; align-items: center; box-shadow: 0 1px 2px rgba(16,24,20,.05); }
    .p-stat-ic { font-size: 1.4rem; width: 42px; height: 42px; border-radius: 10px; background: #f3f6f4; display: flex; align-items: center; justify-content: center; margin-right: .8rem; }
    .p-stat-n { font-size: 1.35rem; font-weight: 700; line-height: 1.1; color: #14532d; }
    .p-stat-l { font-size: .75rem; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; }
    .p-table thead th { background: #f0f4f1; font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; color: #374151; border-top: 0; white-space: nowrap; }
    .p-table td { vertical-align: middle; padding: .65rem .75rem; }
    .p-name { font-weight: 600; color: #14532d; line-height: 1.25; }
    .p-code { background: #f3f6f4; color: #166534; padding: .1rem .4rem; border-radius: 5px; font-size: .78rem; }
    .p-sticky { position: sticky; right: 0; background: #fff; box-shadow: -8px 0 10px -8px rgba(16,24,20,.18); z-index: 2; }
    .p-table thead .p-sticky { background: #f0f4f1; }
    .p-table tbody tr:hover .p-sticky { background: #f0fdf4; }
    .p-table .dropdown-menu { z-index: 1050; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        var importSessions = <?php echo json_encode($importSessionsByEvent, 15, 512) ?>;
        var importEventSelect = document.getElementById('import_event_id');
        if (importEventSelect) {
            importEventSelect.addEventListener('change', function () {
                var list = importSessions[importEventSelect.value] || [];
                var sel = document.getElementById('import_default_session_id');
                sel.innerHTML = '<option value="">-- Tanpa sesi / ikuti kolom "sesi" di CSV --</option>';
                list.forEach(function (s, i) {
                    var o = document.createElement('option');
                    o.value = s.id; o.textContent = 'Sesi ' + (i + 1) + ' — ' + s.title;
                    sel.appendChild(o);
                });
                document.getElementById('import_session_group').style.display = list.length ? '' : 'none';
            });
        }

        var sendWaCheckbox = document.getElementById('import_send_wa');
        var statusOptionSelect = document.getElementById('import_status_option');
        var waMessageGroup = document.getElementById('import_wa_message_group');
        function toggleWaMessageGroup() {
            var willSendWa = sendWaCheckbox.checked && statusOptionSelect.value !== 'pending';
            waMessageGroup.style.display = willSendWa ? '' : 'none';
        }
        if (sendWaCheckbox && statusOptionSelect && waMessageGroup) {
            toggleWaMessageGroup();
            sendWaCheckbox.addEventListener('change', toggleWaMessageGroup);
            statusOptionSelect.addEventListener('change', toggleWaMessageGroup);
        }

        document.querySelectorAll('.seat-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var id = form.dataset.id;
                var seat = form.querySelector('[name="seat_number"]').value;
                fetch('<?php echo e(url("admin/participants")); ?>/' + id + '/seat-number', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ seat_number: seat })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (!data.success) { alert(data.message || 'Gagal menyimpan nomor kursi.'); return; }
                    var inp = form.querySelector('input'); inp.style.boxShadow = '0 0 0 3px rgba(22,163,74,.35)'; setTimeout(function () { inp.style.boxShadow = ''; }, 900);
                }).catch(function () { alert('Gagal menghubungi server.'); });
            });
        });


        // ---- Sesi peserta ----
        document.querySelectorAll('.session-select').forEach(function (select) {
            select.addEventListener('change', function () {
                fetch('<?php echo e(url("admin/participants")); ?>/' + select.dataset.id + '/session', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ session_id: select.value || null })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (!data.success) { alert(data.message || 'Gagal menyimpan sesi.'); return; }
                    if (data.over_quota) alert('Perhatian: sesi ini sekarang melebihi kuota yang ditetapkan.');
                    select.style.boxShadow = '0 0 0 3px rgba(22,163,74,.35)';
                    setTimeout(function () { select.style.boxShadow = ''; }, 900);
                }).catch(function () { alert('Gagal menghubungi server.'); });
            });
        });

        var bulkForm = document.getElementById('bulkSessionForm');
        if (bulkForm) {
            var criteria = <?php echo json_encode($criteria ?? [], 15, 512) ?>;
            var bField = document.getElementById('bulkField'), bValue = document.getElementById('bulkValue');
            bField.addEventListener('change', function () {
                var list = criteria[bField.value] || [];
                bValue.innerHTML = list.map(function (v) { return '<option>' + String(v).replace(/</g, '&lt;') + '</option>'; }).join('');
                bValue.style.display = bField.value === 'all' ? 'none' : '';
            });
            bulkForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var label = bField.value === 'all' ? 'SEMUA peserta' : ('peserta dengan ' + bField.options[bField.selectedIndex].text + ' "' + bValue.value + '"');
                if (!confirm('Tetapkan ' + label + (document.getElementById('bulkOnlyNew').checked ? ' yang belum bersesi' : '') + ' ke sesi terpilih?')) return;
                fetch('<?php echo e(route("participants.bulkSession")); ?>', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        event_id: <?php echo e(optional($selectedEvent)->id ?? 'null'); ?>, session_id: document.getElementById('bulkSession').value,
                        field: bField.value, value: bValue.value || null, only_unassigned: document.getElementById('bulkOnlyNew').checked ? 1 : 0
                    })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    alert(data.message || 'Selesai.');
                    if (data.success) location.reload();
                }).catch(function () { alert('Gagal menghubungi server.'); });
            });
        }

        document.querySelectorAll('.cert-category-select').forEach(function (select) {
            select.addEventListener('change', function () {
                var id = select.dataset.id;
                fetch('<?php echo e(url("admin/participants")); ?>/' + id + '/certificate-category', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ certificate_category_id: select.value || null })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (!data.success) alert(data.message || 'Gagal menyimpan kategori sertifikat.');
                }).catch(function () { alert('Gagal menghubungi server.'); });
            });
        });

        // ---- Filter & pencarian (diproses di server) ----
        var filterForm = document.getElementById('filterForm');
        document.querySelectorAll('.auto-submit').forEach(function (el) {
            el.addEventListener('change', function () { (el.form || filterForm).submit(); });
        });
        var qInput = document.getElementById('q'), qTimer;
        if (qInput) {
            if (qInput.value) { var l = qInput.value.length; qInput.focus(); qInput.setSelectionRange(l, l); }
            qInput.addEventListener('input', function () { clearTimeout(qTimer); qTimer = setTimeout(function () { filterForm.submit(); }, 550); });
        }
        // nomor kursi: simpan juga saat menekan Enter / pindah kolom, tampilkan tanda berhasil
        document.querySelectorAll('.seat-form input[name="seat_number"]').forEach(function (inp) {
            var orig = inp.value;
            inp.addEventListener('change', function () { if (inp.value !== orig) inp.form.requestSubmit(); });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/participants/index.blade.php ENDPATH**/ ?>