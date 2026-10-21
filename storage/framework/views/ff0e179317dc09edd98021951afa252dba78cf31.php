<?php $__env->startSection('title', 'Scan QR Code'); ?>

<?php $__env->startSection('content_header'); ?>
<h1>Scan QR Code Kehadiran</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-12 text-center mb-3">
        <?php if($event): ?>
            <div class="alert alert-info">
                <h4><i class="fas fa-calendar-check"></i> Mode Scan: <?php echo e($event->title); ?></h4>
                <p>Scanner ini hanya akan menerima QR Code untuk event ini. Kamera tidak jalan? Ketik kode yang ditunjukkan peserta di kolom bawah.</p>
                <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('attendance.event', $event->id)); ?>"><i class="fas fa-user-edit"></i> Cari peserta &amp; set manual</a>
                <a class="btn btn-sm btn-outline-warning" target="_blank" href="<?php echo e(route('attendance.display', $event->id)); ?>"><i class="fas fa-tv"></i> Layar presensi mandiri</a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <h4><i class="fas fa-exclamation-triangle"></i> Mode Scan: Global</h4>
                <p>Scanner ini menerima QR Code semua event.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-body text-center">
                <div class="mb-4">
                    <label class="d-block mb-2">Pilih Tipe Scan:</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-primary active px-4 py-2">
                            <input type="radio" name="scan_type" value="check_in" autocomplete="off" checked>
                            <i class="fas fa-sign-in-alt"></i> Absensi Datang
                        </label>
                        <label class="btn btn-outline-info px-4 py-2">
                            <?php
                                $valueKedua = ($event && $event->event_type == 'wisuda') ? 'consumption' : 'check_out';
                                $iconKedua = ($event && $event->event_type == 'wisuda') ? 'fa-utensils' : 'fa-sign-out-alt';
                            ?>
                            <input type="radio" name="scan_type" value="<?php echo e($valueKedua); ?>" autocomplete="off">
                            <i class="fas <?php echo e($iconKedua); ?>"></i> Absensi Pulang
                        </label>
                    </div>
                </div>

                <div id="reader" width="600px"></div>
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" id="manualCode" class="form-control" placeholder="Atau ketik kode manual...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" onclick="manualScan()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Hasil Scan Terakhir</h3>
            </div>
            <div class="card-body" id="scanResult">
                <div class="alert alert-light text-center">Belum ada data scan.</div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    const csrfToken = '<?php echo e(csrf_token()); ?>';
    const currentEventId = '<?php echo e($event ? $event->id : ""); ?>';

    // Prevents the same QR from being submitted repeatedly while it sits in
    // front of the camera: html5-qrcode fires onScanSuccess on every decoded
    // frame (up to `fps` times/sec), not just once per code.
    let isProcessing = false;
    let lastCode = null;
    let lastScanTime = 0;
    const RESCAN_COOLDOWN_MS = 4000;

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;

        const now = Date.now();
        if (decodedText === lastCode && (now - lastScanTime) < RESCAN_COOLDOWN_MS) {
            return;
        }

        processCode(decodedText);
    }

    function onScanFailure(error) {
        // console.warn(`Code scan error = ${error}`);
    }

    // Size the scan box relative to the camera preview instead of a fixed
    // 250px square, which renders too small on phone screens where the
    // preview is much wider/taller.
    function responsiveQrbox(viewfinderWidth, viewfinderHeight) {
        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
        const size = Math.floor(minEdge * 0.8);
        return { width: size, height: size };
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: responsiveQrbox, aspectRatio: 1.0 });
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    function manualScan() {
        let code = document.getElementById('manualCode').value;
        if (code) processCode(code);
        document.getElementById('manualCode').value = '';
    }

    function processCode(code) {
        if (isProcessing) return;
        isProcessing = true;
        lastCode = code;
        lastScanTime = Date.now();

        let scanType = $('input[name="scan_type"]:checked').val();

        $.ajax({
            url: "<?php echo e(route('attendance.process')); ?>",
            type: "POST",
            data: {
                _token: csrfToken,
                qrcode: code,
                event_id: currentEventId,
                scan_type: scanType
            },
            success: function (response) {
                let alertClass = 'alert-success';
                let iconClass = 'fa-check';

                if(scanType == 'consumption') {
                    alertClass = 'alert-warning';
                    iconClass = 'fa-utensils';
                } else if(scanType == 'check_out') {
                    alertClass = 'alert-info';
                    iconClass = 'fa-sign-out-alt';
                }

                $('#scanResult').html(`
                        <div class="alert ${alertClass}">
                            <h4><i class="icon fas ${iconClass}"></i> ${response.message}</h4>
                            <p><strong>Nama:</strong> ${response.participant}</p>
                            <p><strong>Event:</strong> ${response.event}</p>
                            <p><strong>Waktu:</strong> ${response.time}</p>
                            ${response.session ? `<p><strong>Sesi:</strong> ${response.session}</p>` : ''}
                            ${response.warning ? `<p class="mb-0 text-danger"><i class="fas fa-exclamation-triangle"></i> ${response.warning}</p>` : ''}
                        </div>
                    `);

                // Audio feedback could go here
            },
            error: function (xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan system';
                $('#scanResult').html(`
                        <div class="alert alert-danger">
                            <h4><i class="icon fas fa-ban"></i> Gagal!</h4>
                            <p>${msg}</p>
                        </div>
                    `);
            },
            complete: function () {
                // Small delay so a person still in frame doesn't get
                // re-submitted the instant the lock releases.
                setTimeout(function () { isProcessing = false; }, 1500);
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/attendance/scanner.blade.php ENDPATH**/ ?>