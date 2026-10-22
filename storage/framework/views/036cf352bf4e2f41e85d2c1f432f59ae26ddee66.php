<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Presensi - <?php echo e($event->title); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo-unwim.png')); ?>">
    <style>
        :root { --p:#15803d; --pd:#14532d; --soft:#dcfce7; --bg:#f1f5f2; --t:#1f2937; --m:#6b7280; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background:var(--bg); color:var(--t); }
        .top { background: linear-gradient(135deg,#14532d,#166534); color:#fff; padding:22px 16px 56px; text-align:center; }
        .top img { height:46px; }
        .top h1 { font-size:1.05rem; margin:.6rem 0 .15rem; font-weight:700; }
        .top small { opacity:.85; }
        .card { background:#fff; max-width:480px; margin:-34px auto 24px; border-radius:16px; box-shadow:0 8px 28px rgba(0,0,0,.12); padding:22px 20px; }
        h2 { font-size:1.05rem; margin:0 0 .75rem; }
        .steps { list-style:none; padding:0; margin:0 0 1rem; }
        .steps li { display:flex; gap:.7rem; padding:.55rem 0; font-size:.92rem; align-items:flex-start; }
        .steps .n { flex:0 0 26px; height:26px; border-radius:50%; background:var(--soft); color:var(--pd); font-weight:700; display:flex; align-items:center; justify-content:center; font-size:.85rem; }
        .steps li.done .n { background:var(--p); color:#fff; }
        .steps li.off { opacity:.55; }
        label { display:block; font-weight:600; font-size:.88rem; margin:.9rem 0 .35rem; }
        input[type=text] { width:100%; padding:.85rem 1rem; font-size:1.1rem; border:2px solid #d1d9d4; border-radius:12px; letter-spacing:.06em; text-transform:uppercase; }
        input[type=text]:focus { outline:none; border-color:var(--p); box-shadow:0 0 0 4px rgba(21,128,61,.15); }
        .seg { display:flex; gap:.5rem; }
        .seg label { flex:1; margin:0; }
        .seg input { display:none; }
        .seg span { display:block; text-align:center; padding:.7rem; border:2px solid #d1d9d4; border-radius:12px; font-weight:600; cursor:pointer; }
        .seg input:checked + span { border-color:var(--p); background:var(--soft); color:var(--pd); }
        button { width:100%; margin-top:1.1rem; padding:.95rem; font-size:1.05rem; font-weight:700; border:0; border-radius:12px; background:var(--p); color:#fff; cursor:pointer; }
        button:disabled { background:#9ca3af; cursor:not-allowed; }
        .box { border-radius:12px; padding:.85rem 1rem; font-size:.9rem; margin-bottom:1rem; }
        .err { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
        .warn { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
        .ok { text-align:center; }
        .ok .ic { width:68px; height:68px; border-radius:50%; background:var(--p); color:#fff; font-size:2.2rem; display:flex; align-items:center; justify-content:center; margin:0 auto .8rem; }
        .meta { color:var(--m); font-size:.85rem; }
        .help { font-size:.82rem; color:var(--m); margin-top:1rem; border-top:1px solid #e5e7eb; padding-top:.9rem; }
        a { color:var(--p); }
        .ac { position:relative; }
        .aclist { list-style:none; margin:.3rem 0 0; padding:0; border:1px solid #d1d9d4; border-radius:12px; max-height:300px; overflow:auto; background:#fff; }
        .aclist li { padding:.7rem .9rem; cursor:pointer; border-bottom:1px solid #eef2ef; }
        .aclist li:last-child { border-bottom:0; }
        .aclist li:hover, .aclist li.act { background:var(--soft); }
        .aclist .nm { font-weight:700; font-size:.95rem; }
        .aclist .pr { color:var(--m); font-size:.82rem; }
        .aclist .none { color:var(--m); cursor:default; }
        #q { text-transform:none; letter-spacing:0; font-size:1rem; }
        .profile { border:2px solid var(--soft); border-radius:12px; padding:1rem; margin-top:1rem; }
        .profile .pname { font-weight:700; font-size:1.15rem; margin-bottom:.5rem; }
        .profile dl { display:grid; grid-template-columns:110px 1fr; gap:.35rem .6rem; margin:0; font-size:.88rem; }
        .profile dt { color:var(--m); }
        .profile dd { margin:0; font-weight:600; }
        button.ghost { background:#fff; color:var(--t); border:2px solid #d1d9d4; margin-top:.6rem; }
    </style>
</head>
<body>
<div class="top">
    <img src="<?php echo e(asset('images/logo-unwim.png')); ?>" alt="UNWIM">
    <h1><?php echo e($event->title); ?></h1>
    <small>Presensi Mandiri Peserta</small>
</div>

<div class="card">
<?php if(!empty($success)): ?>
    <div class="ok">
        <div class="ic">✓</div>
        <h2><?php echo e($success['type']); ?> berhasil</h2>
        <div style="font-size:1.15rem;font-weight:700"><?php echo e($success['name']); ?></div>
        <div class="meta"><?php echo e($success['time']); ?> WIB</div>
        <?php if(!empty($success['session'])): ?><div class="meta" style="margin-top:.2rem"><?php echo e($success['session']); ?></div><?php endif; ?>
        <?php if($success['late']): ?>
            <div class="box warn" style="margin-top:1rem">Tercatat terlambat <?php echo e($success['late']); ?> menit dari jadwal mulai. Tetap dihitung hadir.</div>
        <?php endif; ?>
        <p class="meta" style="margin-top:1rem">Anda bisa menutup halaman ini. Jangan lupa presensi pulang di akhir acara bila diminta panitia.</p>
    </div>
<?php else: ?>
    <?php if(!empty($error)): ?><div class="box err"><?php echo e($error); ?></div><?php endif; ?>
    <?php if($errors->any()): ?><div class="box err"><?php echo e($errors->first()); ?></div><?php endif; ?>

    <h2>Cara presensi</h2>
    <ul class="steps">
        <li class="<?php echo e($hasProof ? 'done' : ''); ?>"><span class="n"><?php echo e($hasProof ? '✓' : '1'); ?></span><span><b>Scan QR di layar monitor lokasi acara</b><br><span class="meta">QR ditampilkan panitia di layar / meja registrasi dan berganti tiap menit. Presensi hanya bisa dilakukan setelah scan QR ini.</span></span></li>
        <li class="<?php echo e($hasProof ? '' : 'off'); ?>"><span class="n">2</span><span><b>Cari nama Anda</b><br><span class="meta">Ketik minimal 3 huruf nama (boleh sebagian, mis. "eulis sop") atau kode registrasi lengkap, lalu pilih nama Anda dari daftar.</span></span></li>
        <li class="<?php echo e($hasProof ? '' : 'off'); ?>"><span class="n">3</span><span><b>Periksa profil &amp; klik "Saya hadir"</b><br><span class="meta">Pastikan nama dan program studi sesuai, pilih Datang atau Pulang, lalu tekan tombol.</span></span></li>
    </ul>

    <?php if($closedReason): ?>
        <div class="box warn"><?php echo e($closedReason); ?></div>
    <?php elseif(!$hasProof): ?>
        <div class="box warn"><b>Belum terverifikasi di lokasi.</b> Pastikan kode undangan sudah disalin, lalu buka kamera HP dan scan QR di layar monitor panitia. Halaman ini akan terbuka otomatis.</div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('attendance.self.submit', $event->slug)); ?>" autocomplete="off" id="attForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="invitation_id" id="invId" value="">
        <div class="seg">
            <label><input type="radio" name="type" value="check_in" checked><span>Datang</span></label>
            <label><input type="radio" name="type" value="check_out"><span>Pulang</span></label>
        </div>
        <div id="pickStep">
            <label for="q">Cari nama atau kode registrasi</label>
            <div class="ac">
                <input type="text" id="q" placeholder="Ketik nama / kode registrasi" autocomplete="off" <?php if($closedReason || !$hasProof): ?> disabled <?php endif; ?>>
                <ul id="acList" class="aclist" hidden></ul>
            </div>
            <div class="meta" id="acHint" style="margin-top:.4rem"></div>
        </div>
        <div id="profileStep" hidden>
            <div class="profile">
                <div class="pname" id="pfName"></div>
                <dl>
                    <dt>NIM</dt><dd id="pfNim"></dd>
                    <dt>Program Studi</dt><dd id="pfProdi"></dd>
                    <dt>Fakultas</dt><dd id="pfFak"></dd>
                    <dt>Sesi</dt><dd id="pfSes"></dd>
                    <dt>No. WhatsApp</dt><dd id="pfWa"></dd>
                </dl>
                <div class="box warn" id="pfDone" hidden>Anda sudah tercatat Presensi Datang.</div>
            </div>
            <button type="submit" id="btnHadir" <?php if($closedReason || !$hasProof): ?> disabled <?php endif; ?>>Ya, ini saya &mdash; Saya hadir</button>
            <button type="button" id="btnBack" class="ghost">Bukan saya, cari lagi</button>
        </div>
    </form>

    <div class="help">
        <?php if($window): ?>
            Presensi mandiri dibuka mulai <b><?php echo e($window[0]->translatedFormat('H:i')); ?> WIB</b> sampai acara selesai (<b><?php echo e($window[1]->translatedFormat('H:i')); ?> WIB</b>). Datang terlambat tetap bisa presensi selama acara berlangsung.<br>
        <?php endif; ?>
        Kode tidak ditemukan atau QR layar tidak terlihat? Tunjukkan QR Code undangan Anda ke petugas, atau hubungi panitia<?php echo e($event->contact_whatsapp ? ': ' . $event->contact_whatsapp : ''); ?>.
    </div>
<?php endif; ?>
</div>
<script>
(function () {
    var q = document.getElementById('q'); if (!q) return;
    var list = document.getElementById('acList'), hint = document.getElementById('acHint');
    var pick = document.getElementById('pickStep'), prof = document.getElementById('profileStep');
    var inv = document.getElementById('invId'), timer = null, seq = 0, items = [], active = -1;
    var url = <?php echo json_encode(route('attendance.self.search', $event->slug), 512) ?>;
    function esc(t) { var d = document.createElement('div'); d.textContent = t == null ? '' : t; return d.innerHTML; }
    function render() {
        list.innerHTML = '';
        if (!items.length) { list.innerHTML = '<li class="none">Nama tidak ditemukan. Coba sebagian nama lain atau kode registrasi lengkap.</li>'; }
        items.forEach(function (it, i) {
            var li = document.createElement('li'); li.dataset.i = i;
            li.innerHTML = '<div class="nm">' + esc(it.name) + '</div><div class="pr">' + esc([it.prodi, it.fakultas].filter(Boolean).join(' · ') || it.jabatan || it.unit || '') + '</div>';
            list.appendChild(li);
        });
        list.hidden = false;
    }
    function search() {
        var v = q.value.trim(); inv.value = '';
        if (v.length < 3) { list.hidden = true; hint.textContent = v ? 'Ketik minimal 3 huruf.' : ''; return; }
        var my = ++seq; hint.textContent = 'Mencari...';
        fetch(url + '?q=' + encodeURIComponent(v), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (x) {
                if (my !== seq) return;
                if (!x.ok) { hint.textContent = x.j.error || 'Gagal mencari.'; list.hidden = true; return; }
                hint.textContent = ''; items = x.j.results || []; active = -1; render();
            }).catch(function () { if (my === seq) hint.textContent = 'Gagal mencari. Periksa koneksi Anda.'; });
    }
    function choose(i) {
        var it = items[i]; if (!it) return;
        inv.value = it.id;
        document.getElementById('pfName').textContent = it.name;
        document.getElementById('pfNim').textContent = it.nim || '-';
        document.getElementById('pfProdi').textContent = it.prodi || it.jabatan || '-';
        document.getElementById('pfFak').textContent = it.fakultas || it.unit || '-';
        document.getElementById('pfSes').textContent = it.session || '-';
        document.getElementById('pfWa').textContent = it.whatsapp || '-';
        document.getElementById('pfDone').hidden = !it.checked_in;
        pick.hidden = true; prof.hidden = false; list.hidden = true;
    }
    q.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(search, 250); });
    q.addEventListener('keydown', function (e) {
        if (list.hidden || !items.length) return;
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault(); active = (active + (e.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length;
            [].forEach.call(list.children, function (li, i) { li.classList.toggle('act', i === active); });
        } else if (e.key === 'Enter') { e.preventDefault(); choose(active >= 0 ? active : (items.length === 1 ? 0 : -1)); }
    });
    list.addEventListener('click', function (e) { var li = e.target.closest('li[data-i]'); if (li) choose(+li.dataset.i); });
    document.getElementById('btnBack').addEventListener('click', function () { prof.hidden = true; pick.hidden = false; inv.value = ''; q.focus(); });
    document.getElementById('attForm').addEventListener('submit', function (e) { if (!inv.value) e.preventDefault(); });
})();
</script>
</body>
</html>
<?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/public/events/self_attendance.blade.php ENDPATH**/ ?>