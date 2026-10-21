@extends('adminlte::page')

@section('title', 'Template Undangan')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Template Undangan Digital</h1>
    <a href="{{ route('invitation-templates.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat
        Template Baru</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped" id="templatesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Template</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Digunakan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                    <tr>
                        <td>{{ $template->id }}</td>
                        <td>{{ $template->name }}</td>
                        <td>
                            @php
                                $categoryColors = [
                                    'seminar' => 'info',
                                    'workshop' => 'warning',
                                    'rapat' => 'secondary',
                                    'wisuda' => 'success',
                                    'kuliah_umum' => 'primary',
                                    'lomba' => 'danger',
                                    'pelatihan' => 'dark',
                                    'pengabdian' => 'indigo'
                                ];
                                $types = \App\Models\Event::getEventTypes();
                            @endphp
                            <span class="badge badge-{{ $categoryColors[$template->category] ?? 'secondary' }}">
                                {{ $types[$template->category] ?? ucfirst($template->category) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $template->is_active ? 'success' : 'secondary' }}">
                                {{ $template->is_active ? 'Aktif' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ $template->events->count() }} Event</td>
                        <td>
                            <a href="{{ route('invitation-templates.builder', $template->id) }}"
                                class="btn btn-sm btn-primary" title="Builder">
                                <i class="fas fa-paint-brush"></i> Builder
                            </a>
                            <a href="{{ route('invitation-templates.preview', $template->id) }}" class="btn btn-sm btn-info"
                                title="Preview" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('invitation-templates.edit', $template->id) }}" class="btn btn-sm btn-warning"
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('invitation-templates.destroy', $template->id) }}" method="POST"
                                style="display:inline;" onsubmit="return confirm('Yakin menghapus template ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" {{ $template->events->count() > 0 ? 'disabled' : '' }}>
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
        $('#templatesTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25
        });
    });
</script>
@stop