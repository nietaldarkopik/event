@extends('adminlte::page')

@section('title', 'Daftar Sesi Event')

@php
    $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d M Y') : null;
    $fmtTime = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('H.i') : null;
@endphp

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-0">Sesi Event</h1>
        <small class="text-muted">{{ $items->count() }} sesi{{ $selectedEvent ? ' · ' . ($selectedEvent->title ?? $selectedEvent->name) : '' }}</small>
    </div>
    <a href="{{ route('admin.event-sessions.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Sesi</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="row mb-3 align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                    <input type="search" id="list-search" class="form-control" placeholder="Cari judul sesi, event, ruangan, pembicara...">
                </div>
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <form method="GET">
                    <select name="event_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua event</option>
                        @foreach($events as $e)
                            <option value="{{ $e->id }}" {{ optional($selectedEvent)->id == $e->id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($e->title, 60) }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="col-md-3 text-md-right text-muted small" id="list-info"></div>
        </div>

        <table class="table table-hover no-tidy" id="mainTable" style="width:100%">
            <thead>
                <tr>
                    <th style="width:28px"></th>
                    <th>Sesi</th>
                    <th>Jadwal</th>
                    <th>Ruangan</th>
                    <th class="text-center">Peserta</th>
                    <th>Pembicara</th>
                    <th class="text-right" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @php $count = $taken[$item->id] ?? 0; @endphp
                    <tr>
                        <td class="list-toggle" title="Lihat deskripsi / rincian acara"><i class="fas fa-chevron-right"></i></td>
                        <td data-order="{{ $item->title }}" data-search="{{ $item->title }} {{ optional($item->event)->title }}">
                            <div class="list-title">{{ $item->title }}</div>
                            <div class="list-sub">{{ \Illuminate\Support\Str::limit(optional($item->event)->title ?? '-', 48) }}</div>
                        </td>
                        <td data-order="{{ $item->start_datetime }}" class="text-nowrap">
                            @if($item->start_datetime)
                                <div>{{ $fmtDate($item->start_datetime) }}</div>
                                <div class="list-sub">{{ $fmtTime($item->start_datetime) }}@if($item->end_datetime) &ndash; {{ $fmtTime($item->end_datetime) }}@endif WIB</div>
                            @else
                                <span class="text-muted">Belum diatur</span>
                            @endif
                        </td>
                        <td>{{ $item->room ?: '—' }}</td>
                        <td class="text-center" data-order="{{ $count }}">
                            <b>{{ $count }}</b>@if($item->max_participants)<span class="text-muted"> / {{ $item->max_participants }}</span>@endif
                            @if($item->max_participants && $count >= $item->max_participants)<div><span class="badge badge-danger">Penuh</span></div>@endif
                        </td>
                        <td>
                            @forelse($item->speakers as $sp)
                                <span class="list-chip">{{ $sp->name }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('admin.event-sessions.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.event-sessions.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sesi ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <div class="d-none list-detail">
                                @if($item->description)
                                    <div class="rich-content">{!! \App\Support\Html::render($item->description) !!}</div>
                                @else
                                    <span class="text-muted">Belum ada deskripsi atau rincian acara.</span>
                                @endif
                            </div>
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
@include('admin.partials.list-assets', ['orderCol' => 2, 'orderDir' => 'asc'])
@stop
