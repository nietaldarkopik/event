@extends('adminlte::page')

@section('title', 'Edit Sertifikat')

@section('content_header')
    <h1>Edit Sertifikat</h1>
@stop

@section('content')
<div class="card card-warning card-outline">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.certificates.update', $certificate->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="registration_id">Registrasi</label>
                <select class="form-control" id="registration_id" name="registration_id" required>
                    <option value="">-- Pilih Registrasi --</option>
                    @foreach($registrations as $rel)
                        <option value="{{ $rel->id }}" {{ isset($certificate) && $certificate->registration_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="certificate_file">File Sertifikat URL/Path</label>
                <input type="text" class="form-control" id="certificate_file" name="certificate_file" value="{{ isset($certificate) ? $certificate->certificate_file : old('certificate_file') }}" >
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="certificate_number">Nomor Sertifikat</label>
                <input type="text" class="form-control" id="certificate_number" name="certificate_number" value="{{ isset($certificate) ? $certificate->certificate_number : old('certificate_number') }}" required>
            </div>
            <div class="form-group">
                <label for="issued_at">Waktu Terbit</label>
                <input type="datetime-local" class="form-control" id="issued_at" name="issued_at" value="{{ isset($certificate) ? $certificate->issued_at : old('issued_at') }}" >
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
