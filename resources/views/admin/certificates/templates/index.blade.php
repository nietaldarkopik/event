@extends('adminlte::page')

@section('title', 'Template Sertifikat')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Template Sertifikat</h1>
    <a href="{{ route('certificate-templates.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah
        Template</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tipe Event</th>
                    <th>Orientasi</th>
                    <th>Background</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                    <tr>
                        <td>{{ $template->name }}</td>
                        <td>{{ ucfirst($template->event_type) }}</td>
                        <td>{{ ucfirst($template->orientation) }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $template->background_file) }}" target="_blank">
                                <img src="{{ asset('storage/' . $template->background_file) }}" alt="bg" width="100">
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('certificate-templates.builder', $template->id) }}"
                                class="btn btn-sm btn-info" title="Buka Builder"><i class="fas fa-paint-brush"></i></a>
                            <a href="{{ route('certificate-templates.edit', $template->id) }}"
                                class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('certificate-templates.destroy', $template->id) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('Hapus template ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $templates->links() }}
        </div>
    </div>
</div>
@stop