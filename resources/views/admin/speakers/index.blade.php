@extends('adminlte::page')

@section('title', 'Daftar Pembicara')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-0">Pembicara</h1>
        <small class="text-muted">{{ $items->count() }} pembicara</small>
    </div>
    <a href="{{ route('admin.speakers.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Pembicara</a>
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
            <div class="col-md-6 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                    <input type="search" id="list-search" class="form-control" placeholder="Cari nama, instansi, jabatan, email...">
                </div>
            </div>
            <div class="col-md-6 text-md-right text-muted small" id="list-info"></div>
        </div>

        <table class="table table-hover no-tidy" id="mainTable" style="width:100%">
            <thead>
                <tr>
                    <th style="width:28px"></th>
                    <th>Pembicara</th>
                    <th>Kontak</th>
                    <th class="text-center">Sesi</th>
                    <th class="text-right" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @php
                        $photo = $item->photo ? (preg_match('#^https?://#i', $item->photo) ? $item->photo : asset('storage/' . $item->photo)) : null;
                    @endphp
                    <tr>
                        <td class="list-toggle" title="Lihat biografi & tautan"><i class="fas fa-chevron-right"></i></td>
                        <td data-order="{{ $item->name }}" data-search="{{ $item->name }} {{ $item->company }} {{ $item->title }}">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm mr-3" @if($photo) style="background-image:url('{{ $photo }}')" @endif>@unless($photo)<i class="fas fa-user"></i>@endunless</div>
                                <div>
                                    <div class="list-title">{{ $item->name }}</div>
                                    <div class="list-sub">{{ collect([$item->title, $item->company])->filter()->implode(' · ') ?: '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td data-search="{{ $item->email }} {{ $item->phone }}">
                            @if($item->email)<div class="small"><i class="far fa-envelope text-muted mr-1"></i>{{ $item->email }}</div>@endif
                            @if($item->phone)<div class="small"><i class="fab fa-whatsapp text-muted mr-1"></i>{{ $item->phone }}</div>@endif
                            @if(!$item->email && !$item->phone)<span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-center" data-order="{{ $item->sessions_count }}"><span class="badge badge-light border">{{ $item->sessions_count }}</span></td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('admin.speakers.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.speakers.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pembicara ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <div class="d-none list-detail">
                                <div class="mb-2"><b class="small text-muted text-uppercase">Biografi</b>
                                    <div class="rich-content">{!! $item->bio ? \App\Support\Html::render($item->bio) : '<span class="text-muted">Belum ada biografi.</span>' !!}</div></div>
                                <div class="small">
                                    @if($item->linkedin)<a href="{{ $item->linkedin }}" target="_blank" rel="noopener" class="mr-3"><i class="fab fa-linkedin"></i> LinkedIn</a>@endif
                                    @if($item->instagram)<a href="{{ $item->instagram }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i> Instagram</a>@endif
                                </div>
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
@include('admin.partials.list-assets', ['orderCol' => 1, 'orderDir' => 'asc'])
@stop
