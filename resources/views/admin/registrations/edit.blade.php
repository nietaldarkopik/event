@extends('adminlte::page')

@section('title', 'Edit Registrasi Event')

@section('content_header')
    <h1>Edit Registrasi Event</h1>
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

        <form action="{{ route('admin.registrations.update', $registration->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header"><h3 class="card-title">Detail Informasi Utama</h3></div>
                <div class="card-body">
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
                        <option value="{{ $rel->id }}" {{ isset($registration) && $registration->event_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="participant_id">Peserta</label>
                <select class="form-control" id="participant_id" name="participant_id" required>
                    <option value="">-- Pilih Peserta --</option>
                    @foreach($participants as $rel)
                        <option value="{{ $rel->id }}" {{ isset($registration) && $registration->participant_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="ticket_type_id">Jenis Tiket</label>
                <select class="form-control" id="ticket_type_id" name="ticket_type_id" >
                    <option value="">-- Pilih Jenis Tiket --</option>
                    @foreach($ticketTypes as $rel)
                        <option value="{{ $rel->id }}" {{ isset($registration) && $registration->ticket_type_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="registration_number">Nomor Registrasi</label>
                <input type="text" class="form-control" id="registration_number" name="registration_number" value="{{ isset($registration) ? $registration->registration_number : old('registration_number') }}" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="PENDING" {{ isset($registration) && $registration->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                    <option value="APPROVED" {{ isset($registration) && $registration->status == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                    <option value="REJECTED" {{ isset($registration) && $registration->status == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                    <option value="CANCELLED" {{ isset($registration) && $registration->status == 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                    <option value="ATTENDED" {{ isset($registration) && $registration->status == 'ATTENDED' ? 'selected' : '' }}>ATTENDED</option>
                </select>
            </div>
            <div class="form-group">
                <label for="registered_at">Waktu Registrasi</label>
                <input type="datetime-local" class="form-control" id="registered_at" name="registered_at" value="{{ isset($registration) ? $registration->registered_at : old('registered_at') }}" >
            </div>
            <div class="form-group">
                <label for="checked_in_at">Waktu Check-in</label>
                <input type="datetime-local" class="form-control" id="checked_in_at" name="checked_in_at" value="{{ isset($registration) ? $registration->checked_in_at : old('checked_in_at') }}" >
            </div>
            <div class="form-group">
                <label for="notes">Catatan</label>
                <textarea class="form-control" id="notes" name="notes" rows="4" >{{ isset($registration) ? $registration->notes : old('notes') }}</textarea>
            </div>
                </div>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.registrations.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
