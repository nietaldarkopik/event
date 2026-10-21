<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Presensi - {{ $event->title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-unwim.png') }}">
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
    </style>
</head>
<body>
<div class="top">
    <img src="{{ asset('images/logo-unwim.png') }}" alt="UNWIM">
    <h1>{{ $event->title }}</h1>
    <small>Presensi Mandiri Peserta</small>
</div>

<div class="card">
@if(!empty($success))
    <div class="ok">
        <div class="ic">✓</div>
        <h2>{{ $success['type'] }} berhasil</h2>
        <div style="font-size:1.15rem;font-weight:700">{{ $success['name'] }}</div>
        <div class="meta">{{ $success['time'] }} WIB</div>
        @if(!empty($success['session']))<div class="meta" style="margin-top:.2rem">{{ $success['session'] }}</div>@endif
        @if($success['late'])
            <div class="box warn" style="margin-top:1rem">Tercatat terlambat {{ $success['late'] }} menit dari jadwal mulai. Tetap dihitung hadir.</div>
        @endif
        <p class="meta" style="margin-top:1rem">Anda bisa menutup halaman ini. Jangan lupa presensi pulang di akhir acara bila diminta panitia.</p>
    </div>
@else
    @if(!empty($error))<div class="box err">{{ $error }}</div>@endif
    @if($errors->any())<div class="box err">{{ $errors->first() }}</div>@endif

    <h2>Cara presensi</h2>
    <ul class="steps">
        <li><span class="n">1</span><span><b>Salin kode undangan dulu</b><br><span class="meta">Buka pesan WhatsApp / email undangan Anda, lalu salin (copy) kodenya (contoh: EVT123ABC). Bisa juga memakai nomor WhatsApp yang didaftarkan.</span></span></li>
        <li class="{{ $hasProof ? 'done' : '' }}"><span class="n">{{ $hasProof ? '✓' : '2' }}</span><span><b>Scan QR di layar monitor lokasi acara</b><br><span class="meta">QR ditampilkan panitia di layar / meja registrasi dan berganti tiap menit. Presensi hanya bisa dilakukan setelah scan QR ini.</span></span></li>
        <li class="{{ $hasProof ? '' : 'off' }}"><span class="n">3</span><span><b>Tempel kode &amp; tekan Presensi</b><br><span class="meta">Tempel (paste) kode yang sudah disalin, pilih Datang saat tiba atau Pulang saat meninggalkan acara, lalu tekan tombol Presensi.</span></span></li>
    </ul>

    @if($closedReason)
        <div class="box warn">{{ $closedReason }}</div>
    @elseif(!$hasProof)
        <div class="box warn"><b>Belum terverifikasi di lokasi.</b> Pastikan kode undangan sudah disalin, lalu buka kamera HP dan scan QR di layar monitor panitia. Halaman ini akan terbuka otomatis.</div>
    @endif

    <form method="POST" action="{{ route('attendance.self.submit', $event->slug) }}" autocomplete="off">
        @csrf
        <div class="seg">
            <label><input type="radio" name="type" value="check_in" checked><span>Datang</span></label>
            <label><input type="radio" name="type" value="check_out"><span>Pulang</span></label>
        </div>
        <label for="code">Kode undangan / No. WhatsApp</label>
        <input type="text" id="code" name="code" value="{{ $old_code ?? old('code') }}" placeholder="EVT123ABC" required @if($closedReason || !$hasProof) disabled @endif>
        <button type="submit" @if($closedReason || !$hasProof) disabled @endif>Presensi Sekarang</button>
    </form>

    <div class="help">
        @if($window)
            Presensi mandiri dibuka mulai <b>{{ $window[0]->translatedFormat('H:i') }} WIB</b> sampai acara selesai (<b>{{ $window[1]->translatedFormat('H:i') }} WIB</b>). Datang terlambat tetap bisa presensi selama acara berlangsung.<br>
        @endif
        Kode tidak ditemukan atau QR layar tidak terlihat? Tunjukkan QR Code undangan Anda ke petugas, atau hubungi panitia{{ $event->contact_whatsapp ? ': ' . $event->contact_whatsapp : '' }}.
    </div>
@endif
</div>
</body>
</html>
