<?php $__env->startSection('title', 'Log WhatsApp'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-1"><i class="fab fa-whatsapp text-success"></i> Log WhatsApp</h1>
        <p class="mb-0 text-muted small">Pantau semua notifikasi WA approve registrasi, QR kehadiran, status terkirim/gagal</p>
    </div>
    <a href="<?php echo e(route('admin.participants.index')); ?>" class="btn btn-outline-primary mb-2">
        <i class="fas fa-arrow-left"></i> Kembali ke Peserta
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-lg-3 col-6 mb-2">
        <div class="small-box bg-info">
            <div class="inner"><h3><?php echo e($stats['total'] ?? 0); ?></h3><p>Total Log WA</p></div>
            <div class="icon"><i class="fas fa-history"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-2">
        <div class="small-box bg-success">
            <div class="inner"><h3><?php echo e($stats['sent'] ?? 0); ?></h3><p>Berhasil (Sent)</p></div>
            <div class="icon"><i class="fas fa-check"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-2">
        <div class="small-box bg-danger">
            <div class="inner"><h3><?php echo e($stats['failed'] ?? 0); ?></h3><p>Gagal (Failed)</p></div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-2">
        <div class="small-box bg-<?php echo e(($waStatus['ready'] ?? false) ? 'success' : 'warning'); ?>">
            <div class="inner">
                <h3 class="text-nowrap">
                    <?php echo ($waStatus['ready'] ?? false) ? '<i class="fas fa-signal"></i> ONLINE' : '<i class="fas fa-sync"></i> OFFLINE/QR'; ?>

                </h3>
                <p>Status WA Service <?php if(!($waStatus['ready'] ?? false)): ?><small class="text-light">(belum scan QR pairing)</small><?php endif; ?></p>
            </div>
            <div class="icon"><i class="fab fa-whatsapp"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header with-border">
        <h3 class="card-title">Daftar Riwayat Pengiriman WhatsApp</h3>
        <?php if(!($waStatus['ready'] ?? false)): ?>
            <div class="card-tools">
                <span class="badge badge-warning p-2"><i class="fas fa-exclamation-circle"></i> WA Service Belum Terhubung (Scan QR dulu di terminal wa-service)</span>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover table-striped" id="waLogTable">
            <thead class="thead-light">
                <tr>
                    <th style="width:60px">ID</th>
                    <th>Waktu</th>
                    <th>Peserta</th>
                    <th>No. HP/WA</th>
                    <th>Tipe Pesan</th>
                    <th>QR/Image</th>
                    <th>Status</th>
                    <th>Respon / Error Detail</th>
                    <th style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center font-weight-bold"><?php echo e($log->id); ?></td>
                        <td data-order="<?php echo e($log->created_at->timestamp); ?>">
                            <?php echo e($log->created_at->format('d M Y H:i:s')); ?>

                            <?php if($log->sent_at && $log->sent_at->notEqualTo($log->created_at)): ?>
                                <div class="text-xs text-muted">Sent: <?php echo e($log->sent_at->format('H:i:s')); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($log->participant): ?>
                                <strong><?php echo e($log->participant->name); ?></strong>
                                <div class="text-xs text-muted">ID #<?php echo e($log->participant_id); ?> • <?php echo e($log->participant->institution ?? ''); ?></div>
                            <?php else: ?>
                                <span class="text-muted"><em>Peserta terhapus (ID #<?php echo e($log->participant_id); ?>)</em></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="https://wa.me/<?php echo e(preg_replace('/^0/', '62', $log->phone)); ?>" target="_blank" class="text-success">
                                <i class="fab fa-whatsapp"></i> <?php echo e($log->phone); ?>

                            </a>
                        </td>
                        <td>
                            <?php
                                $labels = [
                                    'registration_approved_qr' => ['info', 'Approve + QR Hadir'],
                                    'registration_rejected' => ['secondary', 'Notif Ditolak'],
                                    'invitation' => ['info', 'Undangan'],
                                    'reminder' => ['warning', 'Reminder'],
                                    'certificate_link' => ['success', 'Link Sertifikat'],
                                    'payment_approved_qr' => ['primary', 'Pembayaran + QR'],
                                ];
                                [$badge, $label] = $labels[str_replace('_resend','',$log->message_type)] ?? ['dark', ucwords(str_replace('_',' ',$log->message_type))];
                            ?>
                            <span class="badge badge-<?php echo e($badge); ?> p-2"><?php echo e($label); ?></span>
                            <?php if(str_ends_with($log->message_type,'_resend')): ?>
                                <span class="badge badge-outline-dark ml-1">↻ Resend</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($log->image_url): ?>
                                <a href="<?php echo e($log->image_url); ?>" target="_blank" title="Buka gambar / QR">
                                    <img src="<?php echo e($log->image_url); ?>" alt="QR" class="rounded border" style="width:40px;height:40px;object-fit:cover">
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($log->status == 'sent'): ?>
                                <span class="badge badge-success p-2"><i class="fas fa-check-circle"></i> TERKIRIM</span>
                            <?php elseif($log->status == 'pending'): ?>
                                <span class="badge badge-warning p-2"><i class="fas fa-clock"></i> PENDING</span>
                            <?php else: ?>
                                <span class="badge badge-danger p-2"><i class="fas fa-times-circle"></i> GAGAL</span>
                            <?php endif; ?>
                        </td>
                        <td style="max-width:320px;">
                            <?php if($log->response_message): ?>
                                <small class="text-<?php echo e($log->status === 'sent' ? 'success' : 'danger'); ?>"><?php echo e(\Illuminate\Support\Str::limit($log->response_message, 160)); ?></small>
                                <?php if(strlen($log->response_message) > 160): ?>
                                    <button class="btn btn-link btn-xs p-0 d-block" data-toggle="modal" data-target="#modalResp<?php echo e($log->id); ?>">Selengkapnya &raquo;</button>
                                    <div class="modal fade" id="modalResp<?php echo e($log->id); ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg"><div class="modal-content">
                                            <div class="modal-header"><h5 class="modal-title">Detail Respon WA #<?php echo e($log->id); ?></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                            <div class="modal-body text-wrap text-break"><pre style="white-space:pre-wrap;font-family:inherit"><?php echo e($log->response_message); ?></pre></div>
                                        </div></div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small"><em>Tidak ada respon</em></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($log->status !== 'sent'): ?>
                                <form action="<?php echo e(route('admin.whatsapp.resend', $log->id)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin MENGIRIM ULANG pesan WA ini ke nomor <?php echo e($log->phone); ?>?');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-primary mb-1" title="Kirim Ulang">
                                        <i class="fas fa-sync-alt"></i> Resend
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fab fa-whatsapp fa-3x mb-2 d-block"></i>
                            Belum ada riwayat pengiriman WA.<br>
                            <small>Approve salah satu registrasi di menu Registrasi untuk memulai pengiriman notifikasi WA QR kehadiran.</small>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <div class="float-right">
            <?php echo e($logs->onEachSide(1)->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function() {
    $('#waLogTable').DataTable({
        paging: false, searching: true, autoWidth: false, responsive: true,
        order: [[1, 'desc']],
        columns: [null, null, null, null, null, { orderable: false }, null, { orderable: false }, { orderable: false }]
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/whatsapp/index.blade.php ENDPATH**/ ?>