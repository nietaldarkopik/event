@extends('adminlte::page')

@section('title', 'Daftar Kategori Event')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Daftar Kategori Event</h1>
    <a href="{{ route('admin.event-categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Kategori Event</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
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
                    <th>Nama Kategori</th>
                    <th>Icon Class</th>
                    <th>Warna Hex/Class</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->icon }}</td>
                        <td>{{ $item->color }}</td>
                        <td>
                            <a href="{{ route('admin.event-categories.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.event-categories.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
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
