@extends('adminlte::page')

@section('title', 'Daftar Absensi Event')

@section('content_header')
<h1>Kelola Absensi Event</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pilih Event untuk Absensi</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Nama Event</th>
                    <th>Jadwal</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events as $index => $event)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><b>{{ $event->title }}</b></td>
                        <td>{{ $event->start_datetime->format('d M Y H:i') }}</td>
                        <td>{{ $event->location }}</td>
                        <td>
                            <a href="{{ route('attendance.event', $event->id) }}" class="btn btn-sm btn-info"
                                title="Lihat Daftar Hadir">
                                <i class="fas fa-list"></i> Detail
                            </a>
                            <a href="{{ route('attendance.scanner') }}?event_id={{ $event->id }}"
                                class="btn btn-sm btn-primary" title="Scan Barcode">
                                <i class="fas fa-qrcode"></i> Scan
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Scan Terakhir (Semua Event)</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Event</th>
                    <th>Peserta</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->scanned_at }}</td>
                        <td>{{ $log->event?->title ?? '-' }}</td>
                        <td>{{ $log->participant?->name ?? '(peserta dihapus)' }}</td>
                        <td>
                            @php
                                $label = [
                                    'check_in' => 'Absensi Datang',
                                    'check_out' => 'Absensi Pulang',
                                    'consumption' => 'Absensi Pulang'
                                ][$log->type] ?? 'Hadir';
                            @endphp
                            <span class="badge badge-success">{{ $label }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-2">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@stop