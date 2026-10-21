@extends('adminlte::page')

@section('title', 'Keuangan')

@section('content_header')
    <h1>Laporan Keuangan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('finance.index') }}" method="GET">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Filter Event Berbayar</label>
                        <select name="event_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Peserta</th>
                        <th>Event</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $index }}</td>
                            <td>
                                {{ $payment->participant->name }}<br>
                                <small class="text-muted">{{ $payment->participant->whatsapp }}</small>
                                <br>
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $payment->participant->whatsapp) }}?text={{ urlencode('Halo ' . $payment->participant->name . ', mengenai pembayaran event ' . ($payment->event->title ?? '-') . '...') }}"
                                   target="_blank" 
                                   class="text-success small font-weight-bold">
                                    <i class="fab fa-whatsapp"></i> Follow Up
                                </a>
                            </td>
                            <td>{{ $payment->event->title ?? '-' }}</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>
                                @if($payment->evidence_file)
                                    <a href="{{ asset('storage/' . $payment->evidence_file) }}" target="_blank" class="btn btn-xs btn-info">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $payment->status == 'approved' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                        Update
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" class="dropdown-item text-success" 
                                            onclick="openApproveModal('{{ route('finance.updateStatus', $payment->id) }}', '{{ $payment->participant->name }}')">
                                            Set Lunas (Approve)
                                        </button>
                                        <form action="{{ route('finance.updateStatus', $payment->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="failed">
                                            <button class="dropdown-item text-danger" type="submit">Set Gagal</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approveForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="status" value="approved">
                <div class="modal-body">
                    <p>Konfirmasi pembayaran untuk peserta: <strong id="participantName"></strong></p>
                    
                    <div class="form-group">
                        <label for="payment_method">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Tunai (Cash)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="evidence_file">Upload Bukti Pembayaran (Opsional jika Cash)</label>
                        <input type="file" name="evidence_file" id="evidence_file" class="form-control-file" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF. Max: 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Approve & Generate Tiket</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
<script>
    function openApproveModal(actionUrl, participantName) {
        $('#approveForm').attr('action', actionUrl);
        $('#participantName').text(participantName);
        $('#approveModal').modal('show');
    }
</script>
@endsection
        <div class="mt-3">
            {{ $payments->withQueryString()->links() }}
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop
