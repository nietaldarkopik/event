@extends('adminlte::page')

@section('title', 'Tambah Event')

@section('content_header')
    <h1>Tambah Event</h1>
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

        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header"><h3 class="card-title">Detail Informasi Utama</h3></div>
                <div class="card-body">
            <div class="form-group">
                <label for="title">Judul Event</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ isset($event) ? $event->title : old('title') }}" required>
            </div>
            <div class="form-group">
                <label for="short_description">Deskripsi Singkat</label>
                <textarea class="form-control" id="short_description" name="short_description" rows="4" >{{ isset($event) ? $event->short_description : old('short_description') }}</textarea>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi Lengkap</label>
                <textarea class="form-control" id="description" name="description" rows="4" >{{ isset($event) ? $event->description : old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label for="venue_name">Nama Tempat</label>
                <input type="text" class="form-control" id="venue_name" name="venue_name" value="{{ isset($event) ? $event->venue_name : old('venue_name') }}" >
            </div>
            <div class="form-group">
                <label for="address">Alamat Tempat</label>
                <textarea class="form-control" id="address" name="address" rows="4" >{{ isset($event) ? $event->address : old('address') }}</textarea>
            </div>
            <div class="form-group">
                <label for="city">Kota</label>
                <input type="text" class="form-control" id="city" name="city" value="{{ isset($event) ? $event->city : old('city') }}" >
            </div>
            <div class="form-group">
                <label for="province">Provinsi</label>
                <input type="text" class="form-control" id="province" name="province" value="{{ isset($event) ? $event->province : old('province') }}" >
            </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Meta & Pengaturan</h3></div>
                <div class="card-body">
            <div class="form-group">
                <label for="organization_id">Organisasi</label>
                <select class="form-control" id="organization_id" name="organization_id" >
                    <option value="">-- Pilih Organisasi --</option>
                    @foreach($organizations as $rel)
                        <option value="{{ $rel->id }}" {{ isset($event) && $event->organization_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="category_id">Kategori</label>
                <select class="form-control" id="category_id" name="category_id" >
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categorys as $rel)
                        <option value="{{ $rel->id }}" {{ isset($event) && $event->category_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="created_by">Dibuat Oleh</label>
                <select class="form-control" id="created_by" name="created_by" >
                    <option value="">-- Pilih Dibuat Oleh --</option>
                    @foreach($creators as $rel)
                        <option value="{{ $rel->id }}" {{ isset($event) && $event->created_by == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="contact_whatsapp">No. WA Panitia</label>
                <input type="text" class="form-control" id="contact_whatsapp" name="contact_whatsapp" value="{{ isset($event) ? $event->contact_whatsapp : old('contact_whatsapp') }}" >
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" class="form-control" id="slug" name="slug" value="{{ isset($event) ? $event->slug : old('slug') }}" >
            </div>
            <div class="form-group">
                <label for="banner_image">Upload Banner</label>
                <input type="file" class="form-control-file" id="banner_image" name="banner_image" accept="image/*">
                <small class="form-text text-muted">JPG/PNG/GIF/WEBP, maks 2MB.</small>
            </div>
            <div class="form-group">
                <label for="banner">atau Banner URL (opsional, dipakai jika tidak upload)</label>
                <input type="text" class="form-control" id="banner" name="banner" value="{{ isset($event) ? $event->banner : old('banner') }}" >
            </div>
            <div class="form-group">
                <label for="registration_start">Registrasi Mulai</label>
                <input type="datetime-local" class="form-control" id="registration_start" name="registration_start" value="{{ isset($event) ? $event->registration_start : old('registration_start') }}" >
            </div>
            <div class="form-group">
                <label for="registration_end">Registrasi Selesai</label>
                <input type="datetime-local" class="form-control" id="registration_end" name="registration_end" value="{{ isset($event) ? $event->registration_end : old('registration_end') }}" >
            </div>
            <div class="form-group">
                <label for="start_date">Tanggal Mulai</label>
                <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="{{ isset($event) ? $event->start_date : old('start_date') }}" >
            </div>
            <div class="form-group">
                <label for="end_date">Tanggal Selesai</label>
                <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="{{ isset($event) ? $event->end_date : old('end_date') }}" >
            </div>
            <div class="form-group">
                <label for="max_participants">Kuota Peserta</label>
                <input type="number" class="form-control" id="max_participants" name="max_participants" value="{{ isset($event) ? $event->max_participants : old('max_participants') }}" >
            </div>
            <div class="form-group form-check mt-4">
                <input type="hidden" name="certificate_enabled" value="0">
                <input type="checkbox" class="form-check-input" id="certificate_enabled" name="certificate_enabled" value="1" {{ isset($event) && $event->certificate_enabled ? 'checked' : '' }}>
                <label class="form-check-label font-weight-bold" for="certificate_enabled">Aktifkan Sertifikat</label>
            </div>
            <div class="form-group form-check mt-4">
                <input type="hidden" name="attendance_required" value="0">
                <input type="checkbox" class="form-check-input" id="attendance_required" name="attendance_required" value="1" {{ isset($event) && $event->attendance_required ? 'checked' : '' }}>
                <label class="form-check-label font-weight-bold" for="attendance_required">Wajib Absen</label>
            </div>
            <div class="form-group">
                <label for="visibility">Visibilitas</label>
                <select class="form-control" id="visibility" name="visibility" required>
                    <option value="PUBLIC" {{ isset($event) && $event->visibility == 'PUBLIC' ? 'selected' : '' }}>PUBLIC</option>
                    <option value="PRIVATE" {{ isset($event) && $event->visibility == 'PRIVATE' ? 'selected' : '' }}>PRIVATE</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="DRAFT" {{ isset($event) && $event->status == 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                    <option value="PUBLISHED" {{ isset($event) && $event->status == 'PUBLISHED' ? 'selected' : '' }}>PUBLISHED</option>
                    <option value="REGISTRATION_OPEN" {{ isset($event) && $event->status == 'REGISTRATION_OPEN' ? 'selected' : '' }}>REGISTRATION_OPEN</option>
                    <option value="REGISTRATION_CLOSED" {{ isset($event) && $event->status == 'REGISTRATION_CLOSED' ? 'selected' : '' }}>REGISTRATION_CLOSED</option>
                    <option value="FINISHED" {{ isset($event) && $event->status == 'FINISHED' ? 'selected' : '' }}>FINISHED</option>
                </select>
            </div>
                </div>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
