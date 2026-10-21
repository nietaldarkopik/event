@extends('adminlte::page')

@section('title', 'Tambah Unit')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Tambah Unit</h1>
    <a href="{{ route('units.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('units.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Unit <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Kode (Opsional)</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                    value="{{ old('code') }}">
                @error('code')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Jenis Unit <span class="text-danger">*</span></label>
                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="rektorat" {{ old('type') == 'rektorat' ? 'selected' : '' }}>Rektorat</option>
                    <option value="fakultas" {{ old('type') == 'fakultas' ? 'selected' : '' }}>Fakultas</option>
                    <option value="prodi" {{ old('type') == 'prodi' ? 'selected' : '' }}>Program Studi</option>
                </select>
                @error('type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Parent (Atasan)</label>
                <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                    <option value="">-- Tidak ada (Top Level) --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }} ({{ ucfirst($parent->type) }})
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Aktif</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@stop

