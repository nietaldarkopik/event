@extends('adminlte::page')

@section('title', 'Buat Template Undangan')

@section('content_header')
<h1>Buat Template Undangan Baru</h1>
@stop

@section('content')
<div class="card">
    <form action="{{ route('invitation-templates.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Template <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="category">Kategori Event <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-control @error('category') is-invalid @enderror"
                    required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" {{ old('category') == $category->slug ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <small class="form-text text-muted">Kategori tidak bisa diubah setelah template dibuat.</small>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" class="form-control"
                    rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }} value="1">
                    <label class="custom-control-label" for="is_active">Aktifkan Template</label>
                </div>
                <small class="form-text text-muted">Template aktif dapat dipilih saat membuat event.</small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Template</button>
            <a href="{{ route('invitation-templates.index') }}" class="btn btn-secondary"><i
                    class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@stop
