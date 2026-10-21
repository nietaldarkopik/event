<?php $__env->startSection('title', 'Daftar Event'); ?>

<?php
    $statusMap = [
        'DRAFT' => ['Draft', 'secondary'],
        'PUBLISHED' => ['Dipublikasi', 'info'],
        'REGISTRATION_OPEN' => ['Registrasi Dibuka', 'success'],
        'REGISTRATION_CLOSED' => ['Registrasi Ditutup', 'warning'],
        'FINISHED' => ['Selesai', 'dark'],
    ];
    $fmt = function ($d, $f = 'd M Y, H:i') { return $d ? \Carbon\Carbon::parse($d)->translatedFormat($f) : null; };
?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-0">Daftar Event</h1>
        <small class="text-muted"><?php echo e($items->count()); ?> event &middot; <?php echo e($items->where('status', 'DRAFT')->count()); ?> draft</small>
    </div>
    <div>
        <a href="<?php echo e(route('admin.events.wizard.create')); ?>" class="btn btn-primary"><i class="fas fa-magic"></i> Tambah Event (Wizard)</a>
        <a href="<?php echo e(route('admin.events.create')); ?>" class="btn btn-outline-secondary"><i class="fas fa-plus"></i> Form Biasa</a>
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

<div class="card">
    <div class="card-body">
        <div class="row mb-3 align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                    <input type="search" id="ev-search" class="form-control" placeholder="Cari judul, kategori, lokasi, penyelenggara...">
                </div>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <select id="ev-status" class="form-control">
                    <option value="">Semua status</option>
                    <?php $__currentLoopData = $statusMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($v[0]); ?>"><?php echo e($v[0]); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4 text-md-right text-muted small" id="ev-info"></div>
        </div>

        <table class="table table-hover ev-table" id="mainTable" style="width:100%">
            <thead>
                <tr>
                    <th style="width:28px" data-orderable="false"></th>
                    <th>Event</th>
                    <th>Jadwal</th>
                    <th>Lokasi</th>
                    <th class="text-center">Pendaftar</th>
                    <th>Status</th>
                    <th class="text-right" data-orderable="false" style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        [$sLabel, $sColor] = $statusMap[$item->status] ?? [$item->status, 'secondary'];
                        $banner = $item->banner_url;
                    ?>
                    <tr>
                        <td class="ev-toggle text-muted" title="Detail lengkap"><i class="fas fa-chevron-right"></i></td>
                        <td data-order="<?php echo e($item->title); ?>" data-search="<?php echo e($item->title); ?> <?php echo e($item->category->name ?? ''); ?> <?php echo e($item->organization->name ?? ''); ?> <?php echo e($item->short_description); ?>">
                            <div class="d-flex align-items-center">
                                <div class="ev-thumb mr-3" <?php if($banner): ?> style="background-image:url('<?php echo e($banner); ?>')" <?php endif; ?>>
                                    <?php if (! ($banner)): ?><i class="fas fa-calendar-alt"></i><?php endif; ?>
                                </div>
                                <div>
                                    <div class="ev-title"><?php echo e($item->title); ?></div>
                                    <div class="ev-sub">
                                        <?php if($item->category): ?><span class="ev-chip"><?php echo e($item->category->name); ?></span><?php endif; ?>
                                        <?php echo e($item->organization->name ?? ''); ?>

                                    </div>
                                </div>
                            </div>
                        </td>
                        <td data-order="<?php echo e(optional($item->start_date)->timestamp); ?>" class="text-nowrap">
                            <?php if($item->start_date): ?>
                                <div class="font-weight-500"><?php echo e($fmt($item->start_date, 'd M Y')); ?></div>
                                <div class="ev-sub"><?php echo e($fmt($item->start_date, 'H:i')); ?><?php if($item->end_date): ?> &ndash; <?php echo e($fmt($item->end_date, $item->start_date->isSameDay($item->end_date) ? 'H:i' : 'd M, H:i')); ?><?php endif; ?></div>
                            <?php else: ?>
                                <span class="text-muted">Belum diatur</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($item->venue_name || $item->city): ?>
                                <div><?php echo e(\Illuminate\Support\Str::limit($item->venue_name, 38)); ?></div>
                                <div class="ev-sub"><?php echo e($item->city); ?></div>
                            <?php else: ?>
                                <span class="text-muted">&mdash;</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center" data-order="<?php echo e($item->registrations_count); ?>">
                            <b><?php echo e($item->registrations_count); ?></b><?php if($item->max_participants): ?><span class="text-muted"> / <?php echo e($item->max_participants); ?></span><?php endif; ?>
                        </td>
                        <td data-search="<?php echo e($sLabel); ?>" data-order="<?php echo e($sLabel); ?>">
                            <span class="badge badge-<?php echo e($sColor); ?> px-2 py-1"><?php echo e($sLabel); ?></span>
                            <div class="ev-sub mt-1">
                                <i class="fas fa-<?php echo e($item->visibility === 'PRIVATE' ? 'lock' : 'globe'); ?>"></i> <?php echo e($item->visibility === 'PRIVATE' ? 'Privat' : 'Publik'); ?>

                            </div>
                        </td>
                        <td class="text-right text-nowrap">
                            <a href="<?php echo e(route('admin.events.wizard.edit', $item->id)); ?>" class="btn btn-sm <?php echo e($item->status === 'DRAFT' ? 'btn-primary' : 'btn-outline-primary'); ?>" title="<?php echo e($item->status === 'DRAFT' ? 'Lanjutkan input (wizard)' : 'Edit via wizard'); ?>"><i class="fas fa-magic"></i></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" title="Menu lainnya"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="<?php echo e(route('admin.events.edit', $item->id)); ?>"><i class="fas fa-edit fa-fw mr-1"></i> Edit (form biasa)</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo e(route('admin.participants.index', ['event_id' => $item->id])); ?>"><i class="fas fa-users fa-fw mr-1"></i> Peserta</a>
                                    <?php if($item->is_paid ?? false): ?><a class="dropdown-item" href="<?php echo e(route('finance.index')); ?>"><i class="fas fa-money-bill fa-fw mr-1"></i> Pembayaran</a><?php endif; ?>
                                    <a class="dropdown-item" href="<?php echo e(route('attendance.event', $item->id)); ?>"><i class="fas fa-clipboard-check fa-fw mr-1"></i> Presensi &amp; Kehadiran</a>
                                    <a class="dropdown-item" href="<?php echo e(route('certificate-generator.show', $item->id)); ?>"><i class="fas fa-award fa-fw mr-1"></i> Sertifikat</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo e(route('admin.event-sessions.index', ['event_id' => $item->id])); ?>"><i class="fas fa-calendar-alt fa-fw mr-1"></i> Sesi Event</a>
                                    <a class="dropdown-item" href="<?php echo e(route('admin.sponsors.index', ['event_id' => $item->id])); ?>"><i class="fas fa-handshake fa-fw mr-1"></i> Sponsor</a>
                                    <a class="dropdown-item" href="<?php echo e(route('admin.galleries.index', ['event_id' => $item->id])); ?>"><i class="fas fa-images fa-fw mr-1"></i> Galeri</a>
                                    <a class="dropdown-item" href="<?php echo e(route('admin.announcements.index', ['event_id' => $item->id])); ?>"><i class="fas fa-bullhorn fa-fw mr-1"></i> Pengumuman</a>
                                    <div class="dropdown-divider"></div>
                                    <form action="<?php echo e(route('admin.events.destroy', $item->id)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus event ini beserta data terkait?');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash fa-fw mr-1"></i> Hapus</button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="d-none ev-detail">
                                <dl class="ev-dl">
                                    <div><dt>Slug</dt><dd><?php echo e($item->slug ?: '-'); ?></dd></div>
                                    <div><dt>Dibuat oleh</dt><dd><?php echo e($item->creator->name ?? '-'); ?></dd></div>
                                    <div><dt>No. WA Panitia</dt><dd><?php echo e($item->contact_whatsapp ?: '-'); ?></dd></div>
                                    <div><dt>Alamat</dt><dd><?php echo e(trim(($item->address ?? '') . ' ' . ($item->province ?? '')) ?: '-'); ?></dd></div>
                                    <div><dt>Registrasi</dt><dd><?php echo e($fmt($item->registration_start, 'd M Y') ?: '-'); ?> &ndash; <?php echo e($fmt($item->registration_end, 'd M Y') ?: '-'); ?></dd></div>
                                    <div><dt>Sertifikat</dt><dd><?php echo e($item->certificate_enabled ? 'Aktif' : 'Tidak'); ?></dd></div>
                                    <div><dt>Wajib absen</dt><dd><?php echo e($item->attendance_required ? 'Ya' : 'Tidak'); ?></dd></div>
                                    <div class="ev-dl-wide"><dt>Deskripsi singkat</dt><dd><?php echo e($item->short_description ?: '-'); ?></dd></div>
                                </dl>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .ev-table { border-collapse: separate; border-spacing: 0; }
    .ev-table tbody td { padding: .85rem .75rem; vertical-align: middle; }
    .ev-title { font-weight: 600; color: #14532d; line-height: 1.25; }
    .ev-sub { font-size: .8rem; color: #6b7280; }
    .ev-chip { display: inline-block; background: #dcfce7; color: #166534; border-radius: 20px; padding: 0 .55rem; font-size: .72rem; font-weight: 600; margin-right: .35rem; }
    .ev-thumb { flex: 0 0 52px; width: 52px; height: 52px; border-radius: 10px; background: #e8efe9 center/cover no-repeat; color: #86a38f; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .font-weight-500 { font-weight: 500; }
    .ev-toggle { cursor: pointer; text-align: center; }
    .ev-toggle i { transition: transform .15s; }
    tr.ev-open .ev-toggle i { transform: rotate(90deg); color: #15803d; }
    .ev-child { background: #f8faf8; border-radius: 10px; padding: 1rem 1.25rem; margin: .25rem 0 .5rem 2.2rem; border: 1px solid #e3e8e5; }
    .ev-dl { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .75rem 1.5rem; margin: 0; }
    .ev-dl dt { font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; font-weight: 600; }
    .ev-dl dd { margin: 0; word-break: break-word; }
    .ev-dl-wide { grid-column: 1 / -1; }
    /* kontrol bawaan DataTables dirapikan; pencarian & filter memakai kontrol sendiri di atas */
    #mainTable_wrapper .dataTables_filter, #mainTable_wrapper .dataTables_info { display: none; }
    #mainTable_wrapper .dataTables_length label { font-size: .85rem; color: #6b7280; }
    #mainTable_wrapper .dataTables_length select { border-radius: 8px; }
    table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc { cursor: pointer; }
    #mainTable.dataTable { border-bottom: 0; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function () {
    var table = $('#mainTable').DataTable({
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[2, 'desc']],
        dom: "<'row'<'col-12'tr>><'row mt-3 align-items-center'<'col-md-4'l><'col-md-8'p>>",
        language: {
            lengthMenu: 'Tampilkan _MENU_ baris',
            zeroRecords: 'Tidak ada event yang cocok dengan pencarian.',
            paginate: {previous: '&lsaquo;', next: '&rsaquo;'}
        },
        columnDefs: [{orderable: false, targets: [0, 6]}],
        drawCallback: function () {
            var i = this.api().page.info();
            $('#ev-info').text(i.recordsDisplay ? ('Menampilkan ' + (i.start + 1) + '–' + i.end + ' dari ' + i.recordsDisplay + ' event') : '');
        }
    });

    // pulihkan kotak cari & filter status dari status yang tersimpan
    var saved = table.state.loaded();
    if (saved) {
        $('#ev-search').val(saved.search ? saved.search.search : '');
        var sc = saved.columns && saved.columns[5];
        if (sc && sc.search && sc.search.search) {
            $('#ev-status').val(sc.search.search.replace(/^\^/, '').replace(/\$$/, '').replace(/\\(.)/g, '$1'));
        }
    }
    $('#ev-search').on('input', function () { table.search(this.value).draw(); });
    $('#ev-status').on('change', function () {
        var v = this.value ? '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$' : '';
        table.column(5).search(v, true, false).draw();
    });

    // baris detail
    $('#mainTable tbody').on('click', 'td.ev-toggle', function () {
        var tr = $(this).closest('tr'), row = table.row(tr);
        if (row.child.isShown()) { row.child.hide(); tr.removeClass('ev-open'); }
        else {
            row.child('<div class="ev-child">' + tr.find('.ev-detail').html() + '</div>').show();
            tr.addClass('ev-open');
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/events/index.blade.php ENDPATH**/ ?>