<?php $__env->startSection('title', 'Global Settings'); ?>

<?php $__env->startSection('content_header'); ?>
<h1>Pengaturan Website</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card card-primary card-outline card-tabs">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-general-tab" data-toggle="pill" href="#tabs-general" role="tab"
                    aria-controls="tabs-general" aria-selected="true">Umum</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tabs-contact-tab" data-toggle="pill" href="#tabs-contact" role="tab"
                    aria-controls="tabs-contact" aria-selected="false">Kontak</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('settings.update')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="tab-content" id="custom-tabs-three-tabContent">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="tabs-general" role="tabpanel"
                    aria-labelledby="tabs-general-tab">
                    <div class="form-group">
                        <label>Nama Website</label>
                        <input type="text" name="site_name" class="form-control"
                            value="<?php echo e($settings['site_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Website</label>
                        <textarea name="site_description" class="form-control"
                            rows="3"><?php echo e($settings['site_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Logo Website</label>
                        <?php if(isset($settings['site_logo']) && $settings['site_logo']): ?>
                            <div class="mb-2">
                                <img src="<?php echo e(asset('storage/' . $settings['site_logo'])); ?>" style="height: 50px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="site_logo" class="form-control-file">
                    </div>
                </div>

                <!-- Contact Settings -->
                <div class="tab-pane fade" id="tabs-contact" role="tabpanel" aria-labelledby="tabs-contact-tab">
                    <div class="form-group">
                        <label>Email Resmi</label>
                        <input type="email" name="contact_email" class="form-control"
                            value="<?php echo e($settings['contact_email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="text" name="contact_phone" class="form-control"
                            value="<?php echo e($settings['contact_phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="contact_address" class="form-control"
                            rows="3"><?php echo e($settings['contact_address'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/settings/index.blade.php ENDPATH**/ ?>