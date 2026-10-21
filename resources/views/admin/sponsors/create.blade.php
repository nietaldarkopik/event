@extends('adminlte::page')

@section('title', 'Tambah Sponsor')

@section('content_header')
    <h1>Tambah Sponsor</h1>
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

        <form action="{{ route('admin.sponsors.store') }}" method="POST">
            @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="event_id">Event</label>
                <select class="form-control" id="event_id" name="event_id" required>
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $rel)
                        <option value="{{ $rel->id }}" {{ isset($sponsor) && $sponsor->event_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="logo">Logo URL</label>
                <input type="text" class="form-control" id="logo" name="logo" value="{{ isset($sponsor) ? $sponsor->logo : old('logo') }}" >
            </div>
            <div class="form-group">
                <label for="level">Level</label>
                <select class="form-control" id="level" name="level" required>
                    <option value="GOLD" {{ isset($sponsor) && $sponsor->level == 'GOLD' ? 'selected' : '' }}>GOLD</option>
                    <option value="SILVER" {{ isset($sponsor) && $sponsor->level == 'SILVER' ? 'selected' : '' }}>SILVER</option>
                    <option value="BRONZE" {{ isset($sponsor) && $sponsor->level == 'BRONZE' ? 'selected' : '' }}>BRONZE</option>
                    <option value="MEDIA" {{ isset($sponsor) && $sponsor->level == 'MEDIA' ? 'selected' : '' }}>MEDIA</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Nama Sponsor</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ isset($sponsor) ? $sponsor->name : old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="website">Website</label>
                <input type="text" class="form-control" id="website" name="website" value="{{ isset($sponsor) ? $sponsor->website : old('website') }}" >
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('admin.sponsors.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
