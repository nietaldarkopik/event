@extends('adminlte::page')

@section('title', 'Edit Jenis Tiket')

@section('content_header')
    <h1>Edit Jenis Tiket</h1>
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

        <form action="{{ route('admin.ticket-types.update', $tickettype->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header"><h3 class="card-title">Detail Informasi Utama</h3></div>
                <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Tiket</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ isset($tickettype) ? $tickettype->name : old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="description">Keterangan</label>
                <textarea class="form-control" id="description" name="description" rows="4" >{{ isset($tickettype) ? $tickettype->description : old('description') }}</textarea>
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
                        <option value="{{ $rel->id }}" {{ isset($tickettype) && $tickettype->event_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="price">Harga</label>
                <input type="number" class="form-control" id="price" name="price" value="{{ isset($tickettype) ? $tickettype->price : old('price') }}" required>
            </div>
            <div class="form-group">
                <label for="quota">Kuota Tiket</label>
                <input type="number" class="form-control" id="quota" name="quota" value="{{ isset($tickettype) ? $tickettype->quota : old('quota') }}" required>
            </div>
            <div class="form-group">
                <label for="sale_start">Penjualan Mulai</label>
                <input type="datetime-local" class="form-control" id="sale_start" name="sale_start" value="{{ isset($tickettype) ? $tickettype->sale_start : old('sale_start') }}" >
            </div>
            <div class="form-group">
                <label for="sale_end">Penjualan Selesai</label>
                <input type="datetime-local" class="form-control" id="sale_end" name="sale_end" value="{{ isset($tickettype) ? $tickettype->sale_end : old('sale_end') }}" >
            </div>
                </div>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.ticket-types.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
