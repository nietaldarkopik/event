@extends('adminlte::page')

@section('title', 'Edit Galeri Foto')

@section('content_header')
    <h1>Edit Galeri Foto</h1>
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

        <form action="{{ route('admin.galleries.update', $gallery->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="event_id">Event</label>
                <select class="form-control" id="event_id" name="event_id" required>
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $rel)
                        <option value="{{ $rel->id }}" {{ isset($gallery) && $gallery->event_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="image">Gambar URL</label>
                <input type="text" class="form-control" id="image" name="image" value="{{ isset($gallery) ? $gallery->image : old('image') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="title">Judul Foto</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ isset($gallery) ? $gallery->title : old('title') }}" required>
            </div>
            <div class="form-group">
                <label for="sort_order">Urutan</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ isset($gallery) ? $gallery->sort_order : old('sort_order') }}" >
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
