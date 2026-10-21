@extends('adminlte::page')

@section('title', 'Sertifikat Terbit')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-0">Sertifikat Terbit</h1>
        <small class="text-muted">{{ $certificates->count() }} sertifikat{{ $selectedEvent ? ' · ' . $selectedEvent->title : '' }}</small>
    </div>
    <div>
        <a href="{{ route('certificates.export', array_filter(['event_id' => optional($selectedEvent)->id])) }}" class="btn btn-outline-secondary"><i class="fas fa-file-csv"></i> Ekspor + Tautan</a>
        <a href="{{ route('certificate-generator.index') }}" class="btn btn-primary"><i class="fas fa-award"></i> Terbitkan Sertifikat</a>
    </div>
</div>
@stop

@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>@endif

<div class="card">
    <div class="card-body">
        <div class="row mb-3 align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                    <input type="search" id="list-search" class="form-control" placeholder="Cari nomor, nama peserta, NIM, kategori...">
                </div>
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <form method="GET">
                    <select name="event_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua event</option>
                        @foreach($events as $e)<option value="{{ $e->id }}" {{ optional($selectedEvent)->id == $e->id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($e->title, 60) }}</option>@endforeach
                    </select>
                </form>
            </div>
            <div class="col-md-3 text-md-right text-muted small" id="list-info"></div>
        </div>

        <table class="table table-hover no-tidy" id="mainTable" style="width:100%">
            <thead>
                <tr>
                    <th style="width:6px"></th>
                    <th>Sertifikat</th>
                    <th>Penerima</th>
                    <th>Kategori</th>
                    <th>Terbit</th>
                    <th>Status</th>
                    <th class="text-right" style="width:170px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($certificates as $c)
                    <tr>
                        <td></td>
                        <td data-order="{{ $c->certificate_number }}">
                            <div class="list-title">{{ $c->certificate_number }}</div>
                            <div class="list-sub">{{ \Illuminate\Support\Str::limit(optional($c->event)->title, 44) }}</div>
                        </td>
                        <td data-search="{{ $c->display_name }} {{ optional($c->participant)->nim }} {{ $c->isCommittee() ? 'panitia' : '' }}">
                            <div>{{ $c->display_name }} @if($c->isCommittee())<span class="list-chip">Panitia</span>@endif</div>
                            <div class="list-sub">
                                @if($c->isCommittee()){{ collect([optional($c->committee)->position, optional($c->committee)->section, $c->display_unit])->filter()->implode(' · ') }}
                                @else{{ optional($c->participant)->nim ?: $c->display_unit }}@endif
                            </div>
                        </td>
                        <td>{{ optional($c->category)->name ?: '—' }}</td>
                        <td data-order="{{ $c->issued_at }}" class="text-nowrap">{{ $c->issued_at ? $c->issued_at->translatedFormat('d M Y, H.i') : '—' }}</td>
                        <td data-order="{{ $c->is_revoked ? 1 : 0 }}">
                            @if($c->is_revoked)<span class="badge badge-danger" title="{{ $c->revoke_reason }}">Dicabut</span>@else<span class="badge badge-success">Aktif</span>@endif
                        </td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('certificates.download', $c->id) }}" target="_blank" class="btn btn-sm btn-danger" title="Unduh PDF"><i class="fas fa-file-pdf"></i></a>
                            <a href="{{ route('certificates.verify', $c->certificate_hash) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Halaman verifikasi publik"><i class="fas fa-shield-alt"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-info copy-link" data-link="{{ route('certificates.public_download', $c->unique_code) }}" title="Salin tautan unduh untuk penerima"><i class="fas fa-link"></i></button>
                            <form action="{{ route('certificates.revoke', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $c->is_revoked ? 'Pulihkan sertifikat ini?' : 'Cabut sertifikat ini? Halaman verifikasi akan menandainya tidak berlaku.' }}');">
                                @csrf
                                <button class="btn btn-sm btn-outline-{{ $c->is_revoked ? 'success' : 'warning' }}" title="{{ $c->is_revoked ? 'Pulihkan' : 'Cabut' }}"><i class="fas fa-{{ $c->is_revoked ? 'undo' : 'ban' }}"></i></button>
                            </form>
                            <form action="{{ route('certificates.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sertifikat ini secara permanen?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>#mainTable_wrapper .dataTables_filter, #mainTable_wrapper .dataTables_info { display: none; }</style>
@stop

@section('js')
@include('admin.partials.list-assets', ['orderCol' => 4, 'orderDir' => 'desc'])
<script>
$(document).on('click', '.copy-link', function () {
    var b = $(this), link = b.data('link');
    var done = function () { b.html('<i class="fas fa-check"></i>'); setTimeout(function () { b.html('<i class="fas fa-link"></i>'); }, 1200); };
    if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(link).then(done); }
    else { var t = $('<input>').val(link).appendTo('body').select(); document.execCommand('copy'); t.remove(); done(); }
});
</script>
@stop
