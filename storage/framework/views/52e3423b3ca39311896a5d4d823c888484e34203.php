<?php if(\App\Services\SessionService::usesChoice($event)): ?>
    <?php
        $sessionOptions = \App\Services\SessionService::overview($event);
        $firstOpen = $sessionOptions->first(fn($s) => !$s->is_full);
    ?>
    <div class="form-group">
        <label class="font-weight-bold">Pilih Sesi <span class="text-danger">*</span></label>
        <?php $__currentLoopData = $sessionOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="custom-control custom-radio border rounded p-2 pl-5 mb-2 <?php echo e($s->is_full ? 'bg-light text-muted' : ''); ?>">
                <input type="radio" class="custom-control-input" id="sess-<?php echo e($event->id); ?>-<?php echo e($s->id); ?>-<?php echo e($loop->parent->index ?? 0); ?>" name="session_id" value="<?php echo e($s->id); ?>"
                    <?php echo e($s->is_full ? 'disabled' : ''); ?>

                    <?php echo e(old('session_id', $firstOpen ? $firstOpen->id : null) == $s->id && !$s->is_full ? 'checked' : ''); ?> required>
                <label class="custom-control-label d-block" for="sess-<?php echo e($event->id); ?>-<?php echo e($s->id); ?>-<?php echo e($loop->parent->index ?? 0); ?>">
                    <b><?php echo e($s->title); ?></b>
                    <?php if($s->start_datetime): ?>
                        <br><small><?php echo e(\Carbon\Carbon::parse($s->start_datetime)->translatedFormat('l, d F Y')); ?> &middot;
                            <?php echo e(\Carbon\Carbon::parse($s->start_datetime)->format('H.i')); ?><?php if($s->end_datetime): ?>&ndash;<?php echo e(\Carbon\Carbon::parse($s->end_datetime)->format('H.i')); ?><?php endif; ?> WIB</small>
                    <?php endif; ?>
                    <?php if($s->room): ?><br><small><?php echo e($s->room); ?></small><?php endif; ?>
                    <br>
                    <?php if($s->is_full): ?>
                        <span class="badge badge-danger">Penuh</span>
                    <?php elseif($s->remaining !== null): ?>
                        <span class="badge badge-<?php echo e($s->remaining <= 10 ? 'warning' : 'success'); ?>">Sisa <?php echo e($s->remaining); ?> kursi</span>
                    <?php else: ?>
                        <span class="badge badge-success">Tersedia</span>
                    <?php endif; ?>
                </label>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $__errorArgs = ['session_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger small"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
<?php endif; ?>
<?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/public/events/_session_picker.blade.php ENDPATH**/ ?>