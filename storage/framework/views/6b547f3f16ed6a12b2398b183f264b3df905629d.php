<?php $__env->startSection('title', 'Manajemen Role & Permission'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-1"><i class="fas fa-user-shield text-primary"></i> Manajemen Role & Permission</h1>
        <p class="mb-0 text-muted small">Kelola Peran (Role) dan Matriks Hak Akses (Permission) Aplikasi secara Manual</p>
    </div>
    <a href="<?php echo e(route('settings.users.index')); ?>" class="btn btn-outline-secondary mb-2">
        <i class="fas fa-users-cog"></i> Ke Manajemen User
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

<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Daftar Role Sistem</h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px" class="text-center">#</th>
                            <th>Nama Role</th>
                            <th>Guard</th>
                            <th class="text-center">Total Permission</th>
                            <th class="text-center" style="width: 220px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?php echo e($index + 1); ?></td>
                                <td>
                                    <strong class="text-dark"><?php echo e($r->name); ?></strong>
                                    <?php if(in_array($r->name, ['super_admin', 'admin_event', 'admin_sertifikat', 'admin_keuangan'])): ?>
                                        <span class="badge badge-info ml-1">Sistem</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo e($r->guard_name); ?></code></td>
                                <td class="text-center">
                                    <span class="badge badge-success px-2 py-1" style="font-size: 13px;">
                                        <i class="fas fa-key"></i> <?php echo e($r->permissions_count); ?> Permissions
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('settings.roles.edit', $r->id)); ?>" class="btn btn-sm btn-info shadow-sm" title="Atur Matriks Permission">
                                        <i class="fas fa-sliders-h"></i> Atur Hak Akses
                                    </a>
                                    <?php if(!in_array($r->name, ['super_admin'])): ?>
                                        <form action="<?php echo e(route('settings.roles.destroy', $r->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role <?php echo e($r->name); ?>?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Hapus Role">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">Belum ada data Role.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle"></i> Tambah Role Baru</h3>
            </div>
            <form action="<?php echo e(route('settings.roles.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <div class="form-group">
                        <label for="role_name">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="role_name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Contoh: operator_scanner" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">Spasi akan otomatis diubah menjadi karakter underscore (_).</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-save"></i> Buat Role Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/settings/roles/index.blade.php ENDPATH**/ ?>