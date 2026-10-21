@extends('adminlte::page')

@section('title', 'Daftar Sponsor')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Daftar Sponsor</h1>
    <a href="{{ route('admin.sponsors.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Sponsor</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if($selectedEvent ?? null)
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span>Menampilkan data untuk event: <strong>{{ $selectedEvent->title ?? $selectedEvent->name }}</strong></span>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <table class="table table-bordered table-striped" id="mainTable">
            <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th>Event</th>
                    <th>Nama Sponsor</th>
                    <th>Logo URL</th>
                    <th>Website</th>
                    <th>Level</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->event->name ?? $item->event->full_name ?? $item->event->title ?? $item->event_id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->logo }}</td>
                        <td>{{ $item->website }}</td>
                        <td>{{ $item->level }}</td>
                        <td>
                            <a href="{{ route('admin.sponsors.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.sponsors.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
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
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#mainTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });
</script>
@stop
