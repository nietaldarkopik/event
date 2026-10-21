@extends('adminlte::page')

@section('title', 'Detail Registrasi Event')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Detail Registrasi Event</h1>
    <div>
        <a href="{{ route('admin.registrations.edit', $registration->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
        <a href="{{ route('admin.registrations.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Detail Informasi Utama</h3></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 200px">Nomor Registrasi</th>
                        <td>{{ $registration->registration_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Event</th>
                        <td>{{ $registration->event->title ?? $registration->event_id }}</td>
                    </tr>
                    <tr>
                        <th>Peserta</th>
                        <td>
                            {{ $registration->participant->name ?? $registration->participant->full_name ?? $registration->participant_id }}
                            @if($registration->participant?->whatsapp)
                                <br><small class="text-muted">WA: {{ $registration->participant->whatsapp }}</small>
                            @endif
                            @if($registration->participant?->email)
                                <br><small class="text-muted">Email: {{ $registration->participant->email }}</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis Tiket</th>
                        <td>{{ $registration->ticketType->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($registration->status == 'PENDING')
                                <span class="badge badge-warning text-dark">PENDING</span>
                            @elseif($registration->status == 'APPROVED')
                                <span class="badge badge-success">APPROVED</span>
                            @elseif($registration->status == 'REJECTED')
                                <span class="badge badge-danger">REJECTED</span>
                            @elseif($registration->status == 'CANCELLED')
                                <span class="badge badge-secondary">CANCELLED</span>
                            @elseif($registration->status == 'ATTENDED')
                                <span class="badge badge-primary">ATTENDED</span>
                            @else
                                <span class="badge badge-light">{{ $registration->status }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Waktu Registrasi</th>
                        <td>{{ $registration->registered_at ? \Carbon\Carbon::parse($registration->registered_at)->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Check-in</th>
                        <td>{{ $registration->checked_in_at ? \Carbon\Carbon::parse($registration->checked_in_at)->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $registration->notes ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($registration->attendanceLogs && $registration->attendanceLogs->count())
            <div class="card card-outline card-primary">
                <div class="card-header"><h3 class="card-title">Log Kehadiran</h3></div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Waktu Scan</th>
                                <th>Perangkat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registration->attendanceLogs as $log)
                                <tr>
                                    <td>{{ $log->scan_time ? \Carbon\Carbon::parse($log->scan_time)->format('d-m-Y H:i') : '-' }}</td>
                                    <td>{{ $log->device ?? '-' }}</td>
                                    <td>{{ $log->status ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-secondary">
            <div class="card-header"><h3 class="card-title">Pembayaran</h3></div>
            <div class="card-body">
                @if($registration->payment)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Invoice</th>
                            <td>{{ $registration->payment->invoice_number }}</td>
                        </tr>
                        <tr>
                            <th>Metode</th>
                            <td>{{ $registration->payment->payment_method ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>Rp {{ number_format($registration->payment->amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $registration->payment->status }}</td>
                        </tr>
                        <tr>
                            <th>Dibayar</th>
                            <td>{{ $registration->payment->paid_at ? \Carbon\Carbon::parse($registration->payment->paid_at)->format('d-m-Y H:i') : '-' }}</td>
                        </tr>
                    </table>
                @else
                    <p class="text-muted mb-0">Belum ada data pembayaran.</p>
                @endif
            </div>
        </div>

        <div class="card card-outline card-success">
            <div class="card-header"><h3 class="card-title">QR Code</h3></div>
            <div class="card-body text-center">
                @if($registration->qrCode)
                    <p class="mb-0">Kode: {{ $registration->qrCode->code ?? '-' }}</p>
                @else
                    <p class="text-muted mb-0">Belum ada QR code.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
