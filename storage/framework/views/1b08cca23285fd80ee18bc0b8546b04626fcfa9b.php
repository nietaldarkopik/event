<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layar Presensi - <?php echo e($event->title); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        body { margin:0; min-height:100vh; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; background:linear-gradient(135deg,#14532d,#166534); color:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:24px; }
        img.logo { height:64px; }
        h1 { font-size:clamp(1.3rem,3vw,2.2rem); margin:.7rem 0 .2rem; }
        .sub { opacity:.85; margin-bottom:1.2rem; font-size:1.05rem; }
        .qr { background:#fff; padding:18px; border-radius:20px; display:inline-block; line-height:0; box-shadow:0 14px 40px rgba(0,0,0,.35); }
        .qr svg { width:min(62vh,80vw); height:min(62vh,80vw); }
        .bar { width:min(62vh,80vw); height:6px; background:rgba(255,255,255,.25); border-radius:6px; margin:18px auto 6px; overflow:hidden; }
        .bar i { display:block; height:100%; background:#facc15; width:100%; transition:width 1s linear; }
        .steps { display:flex; gap:1rem; margin-top:1.2rem; flex-wrap:wrap; justify-content:center; max-width:900px; }
        .steps div { background:rgba(255,255,255,.12); border-radius:14px; padding:.8rem 1.1rem; font-size:1rem; min-width:220px; }
        .steps b { color:#facc15; }
        .closed { background:#fef3c7; color:#92400e; padding:1rem 1.4rem; border-radius:14px; margin-bottom:1rem; max-width:640px; }
        .adm { position:fixed; top:12px; left:14px; font-size:.8rem; }
        .adm a { color:#fde68a; }
        .url { font-size:.85rem; opacity:.7; margin-top:.8rem; word-break:break-all; }
    </style>
</head>
<body>
<div class="adm"><a href="<?php echo e(route('attendance.event', $event->id)); ?>">&larr; Kembali ke daftar presensi</a></div>
<img class="logo" src="<?php echo e(asset('images/logo-unwim.png')); ?>" alt="UNWIM">
<h1><?php echo e($event->title); ?></h1>
<div class="sub">Presensi Mandiri Peserta
    <?php if($window): ?> &middot; dibuka <?php echo e($window[0]->translatedFormat('H:i')); ?> s.d. <?php echo e($window[1]->translatedFormat('H:i')); ?> WIB <?php endif; ?>
</div>

<div id="closed" class="closed" <?php if(!$closedReason): ?> style="display:none" <?php endif; ?>><?php echo e($closedReason); ?></div>
<div class="qr" id="qr"></div>
<div class="bar"><i id="bar"></i></div>
<div class="sub" style="margin:0;font-size:.85rem">QR berganti otomatis tiap 1 menit &mdash; foto/tangkapan layar lama tidak berlaku</div>

<div class="steps">
    <div><b>1.</b> <b>Salin kode undangan</b> dari pesan WhatsApp/email</div>
    <div><b>2.</b> Scan QR ini dengan kamera HP</div>
    <div><b>3.</b> <b>Tempel kode</b>, lalu tekan <b>Presensi Sekarang</b></div>
</div>
<div class="url" id="url"></div>

<script>
(function () {
    var timer, left = 60, total = 60;
    function load() {
        fetch("<?php echo e(route('attendance.token', $event->id)); ?>", {headers: {'Accept': 'application/json'}, credentials: 'same-origin'})
            .then(function (r) { return r.json(); })
            .then(function (d) {
                document.getElementById('qr').innerHTML = d.svg;
                document.getElementById('url').textContent = d.url.split('?')[0];
                var c = document.getElementById('closed');
                if (d.closed) { c.textContent = d.closed; c.style.display = ''; } else { c.style.display = 'none'; }
                left = total = d.expires_in;
            })
            .catch(function () { /* coba lagi pada siklus berikutnya */ });
    }
    load();
    setInterval(function () {
        left--;
        document.getElementById('bar').style.width = Math.max(0, left / total * 100) + '%';
        if (left <= 0) { left = total; load(); }
    }, 1000);
})();
</script>
</body>
</html>
<?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/admin/attendance/display.blade.php ENDPATH**/ ?>