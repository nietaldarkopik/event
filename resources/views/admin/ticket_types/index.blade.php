@extends('adminlte::page')

@section('title', 'Jenis Tiket')

@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d M Y, H.i') : null;
    $eventOptions = $items->map(fn($i) => optional($i->event)->title)->filter()->unique()->sort()->values();
@endphp

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-0">Jenis Tiket</h1>
        <small class="text-muted">{{ $items->count() }} jenis tiket</small>
    </div>
    <a href="{{ route('admin.ticket-types.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Tiket</a>
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
                    <input type="search" id="list-search" class="form-control" placeholder="Cari nama tiket atau event...">
                </div>
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <select id="list-filter" class="form-control">
                    <option value="">Semua event</option>
                    @foreach($eventOptions as $o)<option value="{{ $o }}">{{ \Illuminate\Support\Str::limit($o, 60) }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3 text-md-right text-muted small" id="list-info"></div>
        </div>

        <table class="table table-hover no-tidy" id="mainTable" style="width:100%">
            <thead>
                <tr>
                    <th style="width:28px"></th>
                    <th>Event</th>
                    <th>Tiket</th>
                    <th class="text-right">Harga</th>
                    <th class="text-center">Kuota</th>
                    <th>Masa Penjualan</th>
                    <th class="text-right" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td class="list-toggle" title="Lihat keterangan"><i class="fas fa-chevron-right"></i></td>
                        <td data-search="{{ optional($item->event)->title }}"><div class="list-sub">{{ \Illuminate\Support\Str::limit(optional($item->event)->title ?? '-', 44) }}</div></td>
                        <td data-order="{{ $item->name }}"><div class="list-title">{{ $item->name }}</div></td>
                        <td class="text-right text-nowrap" data-order="{{ $item->price }}">
                            @if((float) $item->price > 0)<b>Rp {{ number_format($item->price, 0, ',', '.') }}</b>@else<span class="badge badge-success">Gratis</span>@endif
                        </td>
                        <td class="text-center" data-order="{{ $item->quota }}">{{ $item->quota ?: '∞' }}</td>
                        <td data-order="{{ $item->sale_start }}" class="text-nowrap">
                            @if($item->sale_start || $item->sale_end)
                                <div class="small">{{ $fmt($item->sale_start) ?: '—' }}</div>
                                <div class="list-sub">s.d. {{ $fmt($item->sale_end) ?: '—' }}</div>
                            @else<span class="text-muted">Tanpa batas</span>@endif
                        </td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('admin.ticket-types.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.ticket-types.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tiket ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <div class="d-none list-detail">{!! $item->description ? nl2br(e($item->description)) : '<span class="text-muted">Tidak ada keterangan.</span>' !!}</div>
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
@include('admin.partials.list-assets', ['orderCol' => 1, 'orderDir' => 'asc', 'filterCol' => 1])
@stop
