@extends('adminlte::page')

@section('title', 'Laporan')

@section('content_header')
<h1>Pusat Laporan</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_events'] }}</h3>
                <p>Total Event</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['total_participants'] }}</h3>
                <p>Total Peserta</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Rp {{ number_format($stats['total_income'] / 1000, 0) }}k</h3>
                <p>Total Pemasukan</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['active_events'] }}</h3>
                <p>Event Aktif</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-export"></i> Export Data</h3>
    </div>
    <div class="card-body">
        <p>Silakan pilih jenis laporan yang ingin diunduh.</p>

        <form action="{{ route('reports.export.participants') }}" method="GET" class="form-inline">
            <div class="form-group mb-2">
                <label for="event_id" class="sr-only">Event</label>
                <select name="event_id" class="form-control">
                    <option value="">-- Semua Event --</option>
                    @foreach(\App\Models\Event::all() as $event)
                        <option value="{{ $event->id }}">{{ $event->title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-2 ml-2">
                <i class="fas fa-download"></i> Download CSV Peserta
            </button>
        </form>
    </div>
</div>
@stop