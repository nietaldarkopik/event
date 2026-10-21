<?php $__env->startSection('title', 'Daftar Absensi Event'); ?>

<?php $__env->startSection('content_header'); ?>
<h1>Kelola Absensi Event</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pilih Event untuk Absensi</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Nama Event</th>
                    <th>Jadwal</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><b><?php echo e($event->title); ?></b></td>
                        <td><?php echo e($event->start_datetime->format('d M Y H:i')); ?></td>
                        <td><?php echo e($event->location); ?></td>
                        <td>
                            <a href="<?php echo e(route('attendance.event', $event->id)); ?>" class="btn btn-sm btn-info"
                                title="Lihat Daftar Hadir">
                                <i class="fas fa-list"></i> Detail
                            </a>
                            <a href="<?php echo e(route('attendance.scanner')); ?>?event_id=<?php echo e($event->id); ?>"
                                class="btn btn-sm btn-primary" title="Scan Barcode">
                                <i class="fas fa-qrcode"></i> Scan
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Scan Terakhir (Semua Event)</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Event</th>
                    <th>Peserta</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($log->scanned_at); ?></td>
                        <td><?php echo e($log->event?->title ?? '-'); ?></td>
                        <td><?php echo e($log->participant?->name ?? '(peserta dihapus)'); ?></td>
                        <td>
                            <?php
                                $label = [
                                    'check_in' => 'Absensi Datang',
                                    'check_out' => 'Absensi Pulang',
                                    'consumption' => 'Absensi Pulang'
                                ][$log->type] ?? 'Hadir';
                            ?>
                            <span class="badge badge-success"><?php echo e($label); ?></span>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="p-2">
            <?php echo e($logs->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/attendance/index.blade.php ENDPATH**/ ?>