@extends('adminlte::page')

@section('title', 'Edit Pembicara')

@section('content_header')
    <h1>Edit Pembicara</h1>
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

        <form action="{{ route('admin.speakers.update', $speaker->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header"><h3 class="card-title">Detail Informasi Utama</h3></div>
                <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ isset($speaker) ? $speaker->name : old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="title">Gelar/Jabatan</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ isset($speaker) ? $speaker->title : old('title') }}" >
            </div>
            <div class="form-group">
                <label for="bio">Biografi</label>
                <textarea class="form-control" id="bio" name="bio" rows="4" >{{ isset($speaker) ? $speaker->bio : old('bio') }}</textarea>
            </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Meta & Pengaturan</h3></div>
                <div class="card-body">
            <div class="form-group">
                <label for="company">Perusahaan/Instansi</label>
                <input type="text" class="form-control" id="company" name="company" value="{{ isset($speaker) ? $speaker->company : old('company') }}" >
            </div>
            <div class="form-group">
                <label for="photo">Foto URL</label>
                <input type="text" class="form-control" id="photo" name="photo" value="{{ isset($speaker) ? $speaker->photo : old('photo') }}" >
            </div>
            <div class="form-group">
                <label for="linkedin">Linkedin</label>
                <input type="text" class="form-control" id="linkedin" name="linkedin" value="{{ isset($speaker) ? $speaker->linkedin : old('linkedin') }}" >
            </div>
            <div class="form-group">
                <label for="instagram">Instagram</label>
                <input type="text" class="form-control" id="instagram" name="instagram" value="{{ isset($speaker) ? $speaker->instagram : old('instagram') }}" >
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ isset($speaker) ? $speaker->email : old('email') }}" >
            </div>
            <div class="form-group">
                <label for="phone">WhatsApp</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ isset($speaker) ? $speaker->phone : old('phone') }}" >
            </div>
                </div>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.speakers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
