@extends('adminlte::page')

@section('title', 'Penerbitan Sertifikat')

@section('content_header')
<div>
    <h1 class="mb-0">Penerbitan Sertifikat</h1>
    <small class="text-muted">Pilih event untuk menerbitkan sertifikat peserta</small>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 no-tidy">
            <thead>
                <tr><th>Event</th><th>Jadwal</th><th class="text-center">Peserta</th><th class="text-center">Sertifikat Terbit</th><th class="text-right">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <td>
                            <div class="list-title">{{ $event->title }}</div>
                            <div class="list-sub">
                                @if($event->event_type)<span class="list-chip">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</span>@endif
                                @if($event->certificate_enabled || $event->allow_certificate)<span class="badge badge-success">Sertifikat aktif</span>@else<span class="badge badge-secondary">Sertifikat belum diaktifkan</span>@endif
                            </div>
                        </td>
                        <td class="text-nowrap">{{ $event->start_datetime ? \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d M Y') : '—' }}</td>
                        <td class="text-center">{{ $event->participants()->count() }}</td>
                        <td class="text-center"><b>{{ $event->certificates()->count() }}</b></td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('certificate-generator.show', $event->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-award"></i> Kelola</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada event yang dipublikasikan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
