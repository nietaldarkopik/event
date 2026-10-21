<?php $__env->startSection('title', 'Manajemen User'); ?>

<?php $__env->startSection('content_header'); ?>
<h1>Manajemen User & Hak Akses</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Pengguna</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($index + 1); ?></td>
                                <td><?php echo e($u->name); ?></td>
                                <td><?php echo e($u->email); ?></td>
                                <td>
                                    <?php if($u->role == 'super_admin'): ?> <span class="badge badge-danger">Super Admin</span>
                                    <?php elseif($u->role == 'admin_event'): ?> <span class="badge badge-primary">Admin Event</span>
                                    <?php elseif($u->role == 'admin_keuangan'): ?> <span class="badge badge-success">Admin
                                        Keuangan</span>
                                    <?php elseif($u->role == 'admin_sertifikat'): ?> <span class="badge badge-warning">Admin
                                        Sertifikat</span>
                                    <?php else: ?> <span class="badge badge-secondary"><?php echo e($u->role); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($u->is_active): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-default" data-toggle="modal"
                                        data-target="#editUser<?php echo e($u->id); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if(auth()->id() != $u->id): ?>
                                        <form action="<?php echo e(route('settings.users.destroy', $u->id)); ?>" method="POST"
                                            style="display:inline;" onsubmit="return confirm('Hapus user ini?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-xs btn-danger"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editUser<?php echo e($u->id); ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit User</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="<?php echo e(route('settings.users.update', $u->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Nama</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="<?php echo e($u->name); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="<?php echo e($u->email); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Password (Kosongkan jika tidak ubah)</label>
                                                    <input type="password" name="password" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label>Role</label>
                                                    <select name="role" class="form-control">
                                                        <option value="super_admin" <?php echo e($u->role == 'super_admin' ? 'selected' : ''); ?>>Super Admin</option>
                                                        <option value="admin_event" <?php echo e($u->role == 'admin_event' ? 'selected' : ''); ?>>Admin Event</option>
                                                        <option value="admin_keuangan" <?php echo e($u->role == 'admin_keuangan' ? 'selected' : ''); ?>>Admin Keuangan</option>
                                                        <option value="admin_sertifikat" <?php echo e($u->role == 'admin_sertifikat' ? 'selected' : ''); ?>>Admin Sertifikat</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="activeSwitch<?php echo e($u->id); ?>" name="is_active" <?php echo e($u->is_active ? 'checked' : ''); ?>>
                                                        <label class="custom-control-label"
                                                            for="activeSwitch<?php echo e($u->id); ?>">Akun Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Tambah User Baru</h3>
            </div>
            <form action="<?php echo e(route('settings.users.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="super_admin">Super Admin</option>
                            <option value="admin_event">Admin Event</option>
                            <option value="admin_keuangan">Admin Keuangan</option>
                            <option value="admin_sertifikat">Admin Sertifikat</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block">Tambah User</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/settings/users/index.blade.php ENDPATH**/ ?>