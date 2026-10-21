@extends('adminlte::page')

@section('title', 'Log WhatsApp')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-1"><i class="fab fa-whatsapp text-success"></i> Log WhatsApp</h1>
        <p class="mb-0 text-muted small">Pantau semua notifikasi WA approve registrasi, QR kehadiran, status terkirim/gagal</p>
    </div>
    <a href="{{ route('admin.participants.index') }}" class="btn btn-outline-primary mb-2">
        <i class="fas fa-arrow-left"></i> Kembali ke Peserta
    </a>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-lg-3 col-6 mb-2">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ $stats['total'] ?? 0 }}</h3><p>Total Log WA</p></div>
            <div class="icon"><i class="fas fa-history"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-2">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ $stats['sent'] ?? 0 }}</h3><p>Berhasil (Sent)</p></div>
            <div class="icon"><i class="fas fa-check"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-2">
        <div class="small-box bg-danger">
            <div class="inner"><h3>{{ $stats['failed'] ?? 0 }}</h3><p>Gagal (Failed)</p></div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-2">
        <div class="small-box bg-{{ ($waStatus['ready'] ?? false) ? 'success' : 'warning' }}">
            <div class="inner">
                <h3 class="text-nowrap">
                    {!! ($waStatus['ready'] ?? false) ? '<i class="fas fa-signal"></i> ONLINE' : '<i class="fas fa-sync"></i> OFFLINE/QR' !!}
                </h3>
                <p>Status WA Service @if(!($waStatus['ready'] ?? false))<small class="text-light">(belum scan QR pairing)</small>@endif</p>
            </div>
            <div class="icon"><i class="fab fa-whatsapp"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header with-border">
        <h3 class="card-title">Daftar Riwayat Pengiriman WhatsApp</h3>
        @if(!($waStatus['ready'] ?? false))
            <div class="card-tools">
                <span class="badge badge-warning p-2"><i class="fas fa-exclamation-circle"></i> WA Service Belum Terhubung (Scan QR dulu di terminal wa-service)</span>
            </div>
        @endif
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover table-striped" id="waLogTable">
            <thead class="thead-light">
                <tr>
                    <th style="width:60px">ID</th>
                    <th>Waktu</th>
                    <th>Peserta</th>
                    <th>No. HP/WA</th>
                    <th>Tipe Pesan</th>
                    <th>QR/Image</th>
                    <th>Status</th>
                    <th>Respon / Error Detail</th>
                    <th style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $log->id }}</td>
                        <td data-order="{{ $log->created_at->timestamp }}">
                            {{ $log->created_at->format('d M Y H:i:s') }}
                            @if($log->sent_at && $log->sent_at->notEqualTo($log->created_at))
                                <div class="text-xs text-muted">Sent: {{ $log->sent_at->format('H:i:s') }}</div>
                            @endif
                        </td>
                        <td>
                            @if($log->participant)
                                <strong>{{ $log->participant->name }}</strong>
                                <div class="text-xs text-muted">ID #{{ $log->participant_id }} • {{ $log->participant->institution ?? '' }}</div>
                            @else
                                <span class="text-muted"><em>Peserta terhapus (ID #{{ $log->participant_id }})</em></span>
                            @endif
                        </td>
                        <td>
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $log->phone) }}" target="_blank" class="text-success">
                                <i class="fab fa-whatsapp"></i> {{ $log->phone }}
                            </a>
                        </td>
                        <td>
                            @php
                                $labels = [
                                    'registration_approved_qr' => ['info', 'Approve + QR Hadir'],
                                    'registration_rejected' => ['secondary', 'Notif Ditolak'],
                                    'invitation' => ['info', 'Undangan'],
                                    'reminder' => ['warning', 'Reminder'],
                                    'certificate_link' => ['success', 'Link Sertifikat'],
                                    'payment_approved_qr' => ['primary', 'Pembayaran + QR'],
                                ];
                                [$badge, $label] = $labels[str_replace('_resend','',$log->message_type)] ?? ['dark', ucwords(str_replace('_',' ',$log->message_type))];
                            @endphp
                            <span class="badge badge-{{ $badge }} p-2">{{ $label }}</span>
                            @if(str_ends_with($log->message_type,'_resend'))
                                <span class="badge badge-outline-dark ml-1">↻ Resend</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($log->image_url)
                                <a href="{{ $log->image_url }}" target="_blank" title="Buka gambar / QR">
                                    <img src="{{ $log->image_url }}" alt="QR" class="rounded border" style="width:40px;height:40px;object-fit:cover">
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($log->status == 'sent')
                                <span class="badge badge-success p-2"><i class="fas fa-check-circle"></i> TERKIRIM</span>
                            @elseif($log->status == 'pending')
                                <span class="badge badge-warning p-2"><i class="fas fa-clock"></i> PENDING</span>
                            @else
                                <span class="badge badge-danger p-2"><i class="fas fa-times-circle"></i> GAGAL</span>
                            @endif
                        </td>
                        <td style="max-width:320px;">
                            @if($log->response_message)
                                <small class="text-{{ $log->status === 'sent' ? 'success' : 'danger' }}">{{ \Illuminate\Support\Str::limit($log->response_message, 160) }}</small>
                                @if(strlen($log->response_message) > 160)
                                    <button class="btn btn-link btn-xs p-0 d-block" data-toggle="modal" data-target="#modalResp{{$log->id}}">Selengkapnya &raquo;</button>
                                    <div class="modal fade" id="modalResp{{$log->id}}" tabindex="-1">
                                        <div class="modal-dialog modal-lg"><div class="modal-content">
                                            <div class="modal-header"><h5 class="modal-title">Detail Respon WA #{{ $log->id }}</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                            <div class="modal-body text-wrap text-break"><pre style="white-space:pre-wrap;font-family:inherit">{{ $log->response_message }}</pre></div>
                                        </div></div>
                                    </div>
                                @endif
                            @else
                                <span class="text-muted small"><em>Tidak ada respon</em></span>
                            @endif
                        </td>
                        <td>
                            @if($log->status !== 'sent')
                                <form action="{{ route('admin.whatsapp.resend', $log->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin MENGIRIM ULANG pesan WA ini ke nomor {{ $log->phone }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary mb-1" title="Kirim Ulang">
                                        <i class="fas fa-sync-alt"></i> Resend
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fab fa-whatsapp fa-3x mb-2 d-block"></i>
                            Belum ada riwayat pengiriman WA.<br>
                            <small>Approve salah satu registrasi di menu Registrasi untuk memulai pengiriman notifikasi WA QR kehadiran.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <div class="float-right">
            {{ $logs->onEachSide(1)->links() }}
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function() {
    $('#waLogTable').DataTable({
        paging: false, searching: true, autoWidth: false, responsive: true,
        order: [[1, 'desc']],
        columns: [null, null, null, null, null, { orderable: false }, null, { orderable: false }, { orderable: false }]
    });
});
</script>
@stop