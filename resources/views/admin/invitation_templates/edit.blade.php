@extends('adminlte::page')

@section('title', 'Edit Template Undangan')

@section('content_header')
<h1>Edit Template: {{ $template->name }}</h1>
@stop

@section('content')
<div class="card">
    <form action="{{ route('invitation-templates.update', $template->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Untuk mengubah desain template, gunakan <a
                    href="{{ route('invitation-templates.builder', $template->id) }}" class="alert-link">Template
                    Builder</a>.
            </div>

            <div class="form-group">
                <label for="name">Nama Template <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $template->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Kategori Event</label>
                <div class="form-control-plaintext">
                    @php
                        $currentCategory = $categories->firstWhere('slug', $template->category);
                    @endphp
                    <span class="badge badge-info">
                        {{ $currentCategory ? $currentCategory->name : ucfirst($template->category) }}
                    </span>
                    <small class="text-muted ml-2">(Kategori tidak dapat diubah)</small>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" class="form-control"
                    rows="3">{{ old('description', $template->description) }}</textarea>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $template->is_active) ? 'checked' : '' }} value="1">
                    <label class="custom-control-label" for="is_active">Aktifkan Template</label>
                </div>
                <small class="form-text text-muted">Template aktif dapat dipilih saat membuat event.</small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Template</button>
            <a href="{{ route('invitation-templates.builder', $template->id) }}" class="btn btn-info"><i
                    class="fas fa-paint-brush"></i> Buka Builder</a>
            <a href="{{ route('invitation-templates.index') }}" class="btn btn-secondary"><i
                    class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@stop
