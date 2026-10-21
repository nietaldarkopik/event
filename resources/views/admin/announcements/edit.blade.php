@extends('adminlte::page')

@section('title', 'Edit Pengumuman')

@section('content_header')
    <h1>Edit Pengumuman</h1>
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

        <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="event_id">Event</label>
                <select class="form-control" id="event_id" name="event_id" required>
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $rel)
                        <option value="{{ $rel->id }}" {{ isset($announcement) && $announcement->event_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="content">Konten Pengumuman</label>
                <textarea class="form-control" id="content" name="content" rows="4" required>{{ isset($announcement) ? $announcement->content : old('content') }}</textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="title">Judul Pengumuman</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ isset($announcement) ? $announcement->title : old('title') }}" required>
            </div>
            <div class="form-group">
                <label for="published_at">Waktu Publikasi</label>
                <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="{{ isset($announcement) ? $announcement->published_at : old('published_at') }}" >
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
