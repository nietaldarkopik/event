@extends('adminlte::page')

@section('title', 'Edit Log Kehadiran')

@section('content_header')
    <h1>Edit Log Kehadiran</h1>
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

        <form action="{{ route('admin.attendance-logs.update', $attendancelog->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="registration_id">Registrasi</label>
                <select class="form-control" id="registration_id" name="registration_id" required>
                    <option value="">-- Pilih Registrasi --</option>
                    @foreach($registrations as $rel)
                        <option value="{{ $rel->id }}" {{ isset($attendancelog) && $attendancelog->registration_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="scanner_user_id">Scanner (User)</label>
                <select class="form-control" id="scanner_user_id" name="scanner_user_id" >
                    <option value="">-- Pilih Scanner (User) --</option>
                    @foreach($scanners as $rel)
                        <option value="{{ $rel->id }}" {{ isset($attendancelog) && $attendancelog->scanner_user_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="scan_time">Waktu Scan</label>
                <input type="datetime-local" class="form-control" id="scan_time" name="scan_time" value="{{ isset($attendancelog) ? $attendancelog->scan_time : old('scan_time') }}" >
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="session_id">Sesi</label>
                <select class="form-control" id="session_id" name="session_id" >
                    <option value="">-- Pilih Sesi --</option>
                    @foreach($sessions as $rel)
                        <option value="{{ $rel->id }}" {{ isset($attendancelog) && $attendancelog->session_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="device">Device/Browser</label>
                <input type="text" class="form-control" id="device" name="device" value="{{ isset($attendancelog) ? $attendancelog->device : old('device') }}" >
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="SUCCESS" {{ isset($attendancelog) && $attendancelog->status == 'SUCCESS' ? 'selected' : '' }}>SUCCESS</option>
                    <option value="DUPLICATE" {{ isset($attendancelog) && $attendancelog->status == 'DUPLICATE' ? 'selected' : '' }}>DUPLICATE</option>
                    <option value="INVALID" {{ isset($attendancelog) && $attendancelog->status == 'INVALID' ? 'selected' : '' }}>INVALID</option>
                </select>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.attendance-logs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
