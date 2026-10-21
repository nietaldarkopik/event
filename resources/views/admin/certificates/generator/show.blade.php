@extends('adminlte::page')

@section('title', 'Penerbitan Sertifikat')

@php
    $cats = $event->certificateCategories;
    $catsReady = $cats->filter(fn($c) => $c->template_id)->count();
    $sigs = $event->certificateSignatures->count();
    $steps = [
        ['Aktifkan sertifikat pada event', $hasCertificateFlag, 'Nyalakan "Aktifkan Sertifikat" di wizard event (step 4).', route('admin.events.wizard.edit', $event->id)],
        ['Buat kategori sertifikat + pilih template', $catsReady > 0, 'Kategori = peran penerima (mis. Peserta, Panitia), masing-masing memakai satu template.', route('events.certificates.settings', $event->id)],
        ['Atur tanda tangan & logo', $sigs > 0, 'Dipakai pada variabel nama dan jabatan penandatangan di template.', route('events.certificates.settings', $event->id)],
        ['Tetapkan kategori ke peserta', $stats['with_category'] > 0 || $catsReady > 0, 'Per peserta di menu Peserta, atau sekaligus lewat "Kategori bawaan" di bawah.', route('admin.participants.index', ['event_id' => $event->id])],
    ];
@endphp

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-0">Penerbitan Sertifikat</h1>
        <small class="text-muted">{{ $event->title }}</small>
    </div>
    <div>
        <a href="{{ route('events.certificates.settings', $event->id) }}" class="btn btn-outline-primary"><i class="fas fa-sliders-h"></i> Kategori, Tanda Tangan & Logo</a>
        <a href="{{ route('certificate-generator.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Pilih Event Lain</a>
    </div>
</div>
@stop

@section('js')
<script>
    $('#sec-all').on('click', function (e) { e.preventDefault(); $('.sec-check').prop('checked', true); });
    $('#sec-none').on('click', function (e) { e.preventDefault(); $('.sec-check').prop('checked', false); });
</script>
@stop

@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>@endif
@if(session('error'))<div class="alert alert-warning alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>@endif

<div class="row">
    <div class="col-lg-4">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Kesiapan</h3></div>
            <div class="card-body pt-2">
                @foreach($steps as [$label, $ok, $hint, $link])
                    <div class="d-flex mb-3">
                        <div class="mr-3 pt-1"><i class="fas fa-{{ $ok ? 'check-circle text-success' : 'circle text-muted' }}"></i></div>
                        <div>
                            <div class="font-weight-600">{{ $label }}</div>
                            <div class="small text-muted">{{ $hint }}</div>
                            @unless($ok)<a href="{{ $link }}" class="small">Atur sekarang &rarr;</a>@endunless
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Ringkasan Peserta</h3></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">Peserta (tidak ditolak) <b>{{ $stats['total'] }}</b></li>
                <li class="list-group-item d-flex justify-content-between">Sudah punya kategori <b>{{ $stats['with_category'] }}</b></li>
                <li class="list-group-item d-flex justify-content-between">Sudah hadir (presensi datang) <b>{{ $stats['present'] }}</b></li>
                <li class="list-group-item d-flex justify-content-between">Sertifikat sudah terbit <b>{{ $stats['issued'] }}</b></li>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Terbitkan Sertifikat</h3></div>
            <div class="card-body">
                <form action="{{ route('certificate-generator.generate', $event->id) }}" method="POST" onsubmit="return confirm('Terbitkan sertifikat sesuai pilihan ini?');">
                    @csrf
                    <div class="form-group">
                        <label>Kategori bawaan untuk peserta yang belum punya kategori</label>
                        <select name="default_category_id" class="form-control">
                            <option value="">— Jangan terbitkan untuk yang belum punya kategori —</option>
                            @foreach($cats as $c)
                                <option value="{{ $c->id }}" {{ $c->template_id ? '' : 'disabled' }}>{{ $c->name }}{{ $c->template ? ' · ' . $c->template->name : ' (template belum dipilih)' }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kategori ini otomatis ditetapkan ke peserta yang masih kosong. Peserta yang sudah punya kategori tidak diubah.</small>
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="only_present" name="only_present" value="1" {{ ($event->attendance_required ?? false) || $stats['present'] > 0 ? 'checked' : '' }}>
                        <label class="custom-control-label" for="only_present">Hanya untuk peserta yang hadir (sudah presensi datang)</label>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-magic"></i> Terbitkan Sertifikat</button>
                    <span class="text-muted small ml-2">Aman dijalankan berulang: yang sudah terbit tidak digandakan.</span>
                </form>
            </div>
        </div>

        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-tie mr-1"></i> Sertifikat Panitia
                    <span class="badge badge-light border ml-1">{{ $committeeStats['issued'] }} / {{ $committeeStats['total'] }} sudah terbit</span></h3>
            </div>
            <div class="card-body">
                @if($committeeStats['total'] === 0)
                    <p class="text-muted mb-2">Event ini belum punya data panitia. Isi di wizard event, <b>step 5 (Panitia &amp; Sponsor)</b>.</p>
                    <a href="{{ route('admin.events.wizard.edit', $event->id) }}" class="btn btn-sm btn-outline-primary">Buka wizard event</a>
                @else
                    <form action="{{ route('certificate-generator.generateCommittee', $event->id) }}" method="POST" onsubmit="return confirm('Terbitkan sertifikat panitia untuk seksi yang dipilih?');">
                        @csrf
                        <div class="form-group">
                            <label>Kategori sertifikat panitia</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">— Pilih kategori (mis. Panitia) —</option>
                                @foreach($cats as $c)
                                    <option value="{{ $c->id }}" {{ $c->template_id ? '' : 'disabled' }}>{{ $c->name }}{{ $c->template ? ' · ' . $c->template->name : ' (template belum dipilih)' }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Buat kategori khusus (mis. "Panitia") dengan template sendiri di <a href="{{ route('events.certificates.settings', $event->id) }}">pengaturan sertifikat</a>.
                                Variabel template: <code>{nama_peserta}</code> <code>{jabatan_panitia}</code> <code>{seksi}</code> <code>{unit}</code> <code>{peran_event}</code>.</small>
                        </div>
                        <div class="form-group mb-2">
                            <label class="mb-1">Seksi yang menerima
                                <a href="#" class="small ml-2" id="sec-all">pilih semua</a> · <a href="#" class="small" id="sec-none">kosongkan</a></label>
                            <div class="row">
                                @foreach($committeeSections as $name => $info)
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input sec-check" id="sec-{{ $loop->index }}" name="sections[]" value="{{ $name }}" checked>
                                            <label class="custom-control-label" for="sec-{{ $loop->index }}">{{ $name }}
                                                <span class="text-muted small">({{ $info['issued'] }}/{{ $info['total'] }})</span></label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if($committeeSections->sum('unnamed') > 0)
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="include_patrons" name="include_patrons" value="1">
                                <label class="custom-control-label" for="include_patrons">Sertakan Pelindung (hanya jabatan, tanpa nama orang)</label>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-warning"><i class="fas fa-award"></i> Terbitkan Sertifikat Panitia</button>
                        <span class="text-muted small ml-2">Aman diulang: yang sudah terbit tidak digandakan.</span>
                    </form>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Sertifikat Terbit ({{ $event->certificates->count() }})</h3>
                <a href="{{ route('certificates.index', ['event_id' => $event->id]) }}" class="btn btn-sm btn-outline-secondary">Kelola semua &rarr;</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>No. Sertifikat</th><th>Peserta</th><th>Kategori</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($event->certificates->take(25) as $cert)
                            <tr>
                                <td class="small">{{ $cert->certificate_number }}</td>
                                <td>{{ $cert->display_name }} @if($cert->isCommittee())<span class="badge badge-warning">Panitia</span>@endif</td>
                                <td>{{ optional($cert->category)->name ?: '—' }}</td>
                                <td class="text-right"><a href="{{ route('certificates.download', $cert->id) }}" class="btn btn-xs btn-danger" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada sertifikat terbit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
