@extends('adminlte::page')

@section('title', 'Daftar Pembayaran')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Daftar Pembayaran</h1>
    <a href="{{ route('admin.payments.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Pembayaran</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if($selectedEvent ?? null)
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span>Menampilkan data untuk event: <strong>{{ $selectedEvent->title ?? $selectedEvent->name }}</strong></span>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <table class="table table-bordered table-striped" id="mainTable">
            <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th>Registrasi</th>
                    <th>Nomor Invoice</th>
                    <th>Metode Pembayaran</th>
                    <th>Payment Gateway</th>
                    <th>Transaction ID</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Waktu Bayar</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->registration->name ?? $item->registration->full_name ?? $item->registration->title ?? $item->registration_id }}</td>
                        <td>{{ $item->invoice_number }}</td>
                        <td>{{ $item->payment_method }}</td>
                        <td>{{ $item->payment_gateway }}</td>
                        <td>{{ $item->transaction_id }}</td>
                        <td>{{ $item->amount }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->paid_at }}</td>
                        <td>
                            <a href="{{ route('admin.payments.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.payments.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#mainTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });
</script>
@stop
