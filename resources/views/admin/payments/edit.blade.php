@extends('adminlte::page')

@section('title', 'Edit Pembayaran')

@section('content_header')
    <h1>Edit Pembayaran</h1>
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

        <form action="{{ route('admin.payments.update', $payment->id) }}" method="POST">
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
                <label for="registration_id">Registrasi</label>
                <select class="form-control" id="registration_id" name="registration_id" required>
                    <option value="">-- Pilih Registrasi --</option>
                    @foreach($registrations as $rel)
                        <option value="{{ $rel->id }}" {{ isset($payment) && $payment->registration_id == $rel->id ? 'selected' : '' }}>{{ $rel->name ?? $rel->full_name ?? $rel->title ?? $rel->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="invoice_number">Nomor Invoice</label>
                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{ isset($payment) ? $payment->invoice_number : old('invoice_number') }}" required>
            </div>
            <div class="form-group">
                <label for="payment_method">Metode Pembayaran</label>
                <input type="text" class="form-control" id="payment_method" name="payment_method" value="{{ isset($payment) ? $payment->payment_method : old('payment_method') }}" >
            </div>
            <div class="form-group">
                <label for="payment_gateway">Payment Gateway</label>
                <input type="text" class="form-control" id="payment_gateway" name="payment_gateway" value="{{ isset($payment) ? $payment->payment_gateway : old('payment_gateway') }}" >
            </div>
            <div class="form-group">
                <label for="transaction_id">Transaction ID</label>
                <input type="text" class="form-control" id="transaction_id" name="transaction_id" value="{{ isset($payment) ? $payment->transaction_id : old('transaction_id') }}" >
            </div>
            <div class="form-group">
                <label for="amount">Jumlah</label>
                <input type="number" class="form-control" id="amount" name="amount" value="{{ isset($payment) ? $payment->amount : old('amount') }}" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="UNPAID" {{ isset($payment) && $payment->status == 'UNPAID' ? 'selected' : '' }}>UNPAID</option>
                    <option value="PAID" {{ isset($payment) && $payment->status == 'PAID' ? 'selected' : '' }}>PAID</option>
                    <option value="FAILED" {{ isset($payment) && $payment->status == 'FAILED' ? 'selected' : '' }}>FAILED</option>
                    <option value="REFUND" {{ isset($payment) && $payment->status == 'REFUND' ? 'selected' : '' }}>REFUND</option>
                </select>
            </div>
            <div class="form-group">
                <label for="paid_at">Waktu Bayar</label>
                <input type="datetime-local" class="form-control" id="paid_at" name="paid_at" value="{{ isset($payment) ? $payment->paid_at : old('paid_at') }}" >
            </div>
                </div>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
