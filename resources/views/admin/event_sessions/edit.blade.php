@extends('adminlte::page')

@section('title', 'Edit Sesi Event')

@section('content_header')
    <h1>Edit Sesi Event</h1>
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

        <form action="{{ route('admin.event-sessions.update', $eventsession->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header"><h3 class="card-title">Detail Informasi Utama</h3></div>
                <div class="card-body">
            <div class="form-group">
                <label for="title">Judul Sesi</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ isset($eventsession) ? $eventsession->title : old('title') }}" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi Sesi</label>
                <textarea class="form-control" id="description" name="description" rows="4" >{{ isset($eventsession) ? $eventsession->description : old('description') }}</textarea>
            </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Meta & Pengaturan</h3></div>
                <div class="card-body">
            <div class="form-group">
                <label for="event_id">Event</label>
                <select class="form-control" id="event_id" name="event_id" required>
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $rel)
                        <option value="{{ $rel->id }}" {{ isset($eventsession) && $eventsession->event_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="room">Ruangan</label>
                <input type="text" class="form-control" id="room" name="room" value="{{ isset($eventsession) ? $eventsession->room : old('room') }}" >
            </div>
            <div class="form-group">
                <label for="start_datetime">Waktu Mulai</label>
                <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" value="{{ isset($eventsession) ? $eventsession->start_datetime : old('start_datetime') }}" >
            </div>
            <div class="form-group">
                <label for="end_datetime">Waktu Selesai</label>
                <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" value="{{ isset($eventsession) ? $eventsession->end_datetime : old('end_datetime') }}" >
            </div>
            <div class="form-group">
                <label for="max_participants">Maks. Peserta</label>
                <input type="number" class="form-control" id="max_participants" name="max_participants" value="{{ isset($eventsession) ? $eventsession->max_participants : old('max_participants') }}" >
            </div>
                </div>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.event-sessions.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
