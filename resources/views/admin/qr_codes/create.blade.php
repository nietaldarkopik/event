@extends('adminlte::page')

@section('title', 'Tambah QR Code Tiket')

@section('content_header')
    <h1>Tambah QR Code Tiket</h1>
@stop

@section('content')
<div class="card card-primary card-outline">
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

        <form action="{{ route('admin.qr-codes.store') }}" method="POST">
            @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="registration_id">Registrasi</label>
                <select class="form-control" id="registration_id" name="registration_id" required>
                    <option value="">-- Pilih Registrasi --</option>
                    @foreach($registrations as $rel)
                        <option value="{{ $rel->id }}" {{ isset($qrcode) && $qrcode->registration_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="expired_at">Expired At</label>
                <input type="datetime-local" class="form-control" id="expired_at" name="expired_at" value="{{ isset($qrcode) ? $qrcode->expired_at : old('expired_at') }}" >
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="code">QR Code</label>
                <input type="text" class="form-control" id="code" name="code" value="{{ isset($qrcode) ? $qrcode->code : old('code') }}" required>
            </div>
            <div class="form-group form-check mt-4">
                <input type="hidden" name="is_used" value="0">
                <input type="checkbox" class="form-check-input" id="is_used" name="is_used" value="1" {{ isset($qrcode) && $qrcode->is_used ? 'checked' : '' }}>
                <label class="form-check-label font-weight-bold" for="is_used">Telah Digunakan</label>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('admin.qr-codes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
