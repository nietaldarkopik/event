@extends('adminlte::page')

@section('title', 'Daftar Registrasi Event')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Daftar Registrasi Event</h1>
    <a href="{{ route('admin.registrations.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Registrasi Event</a>
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
                    <th>Event</th>
                    <th>Peserta</th>
                    <th>Jenis Tiket</th>
                    <th>Nomor Registrasi</th>
                    <th>Status</th>
                    <th>Waktu Registrasi</th>
                    <th>Waktu Check-in</th>
                    <th>Catatan</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->event->name ?? $item->event->full_name ?? $item->event->title ?? $item->event_id }}</td>
                        <td>{{ $item->participant->name ?? $item->participant->full_name ?? $item->participant->title ?? $item->participant_id }}</td>
                        <td>{{ $item->ticketType->name ?? $item->ticketType->full_name ?? $item->ticketType->title ?? $item->ticket_type_id }}</td>
                        <td>{{ $item->registration_number }}</td>
                        <td>
                            @if($item->status == 'PENDING')
                                <span class="badge badge-warning text-dark">PENDING</span>
                            @elseif($item->status == 'APPROVED')
                                <span class="badge badge-success">APPROVED</span>
                            @elseif($item->status == 'REJECTED')
                                <span class="badge badge-danger">REJECTED</span>
                            @elseif($item->status == 'CANCELLED')
                                <span class="badge badge-secondary">CANCELLED</span>
                            @elseif($item->status == 'ATTENDED')
                                <span class="badge badge-primary">ATTENDED</span>
                            @else
                                <span class="badge badge-light">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td>{{ $item->registered_at ? \Carbon\Carbon::parse($item->registered_at)->format('d-m-Y H:i') : '-' }}</td>
                        <td>{{ $item->checked_in_at ? \Carbon\Carbon::parse($item->checked_in_at)->format('d-m-Y H:i') : '-' }}</td>
                        <td>{{ $item->notes ?? '-' }}</td>
                        <td>
                            @if($item->status == 'PENDING')
                                <form action="{{ route('admin.registrations.approve', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin APPROVE registrasi ini?\nQR Code kehadiran akan dikirim via WhatsApp ke peserta!');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success mb-1" title="Approve & Kirim WA QR Code">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.registrations.reject', $item->id) }}" method="POST" class="d-inline" onsubmit="var reasonField = this.querySelector('input[name=reason]'); var reason = prompt('Masukkan alasan penolakan:'); if (!reason) return false; reasonField.value = reason; return confirm('Yakin ingin MENOLAK registrasi ini? Notifikasi penolakan akan dikirim via WhatsApp!');">
                                    @csrf
                                    <input type="hidden" name="reason" value="">
                                    <button type="submit" class="btn btn-sm btn-danger mb-1" title="Reject & Kirim WA Notif Penolakan">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.registrations.show', $item->id) }}" class="btn btn-sm btn-info mb-1" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.registrations.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.registrations.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1" title="Hapus">
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
