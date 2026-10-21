@extends('adminlte::page')

@section('title', 'Absensi - ' . $event->title)

@section('content_header')
<h1>Absensi: {{ $event->title }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Peserta & Barcode</h3>
        <div class="card-tools">
            <a href="{{ route('attendance.export_pdf', $event->id) }}" class="btn btn-tool btn-danger mr-1"
                target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('attendance.export_csv', $event->id) }}" class="btn btn-tool btn-success mr-1">
                <i class="fas fa-file-csv"></i> CSV
            </a>
            <a href="{{ route('attendance.scanner', ['event_id' => $event->id]) }}" class="btn btn-tool btn-primary mr-1">
                <i class="fas fa-qrcode"></i> Scan / Input Kode
            </a>
            <a href="{{ route('attendance.display', $event->id) }}" class="btn btn-tool btn-warning" target="_blank">
                <i class="fas fa-tv"></i> Layar Presensi Mandiri
            </a>
        </div>
    </div>
    <div class="card-body">
        @php
            $secType = $event->event_type == 'wisuda' ? 'consumption' : 'check_out';
            $hadir = $participants->filter(fn($x) => $x->attendanceLogs->where('type', 'check_in')->count())->count();
            $labels = \App\Services\AttendanceService::METHOD_LABELS;
            $usesSessions = $event->session_mode !== 'none' && $event->sessions()->exists();
            $eventSessions = $usesSessions ? $event->sessions()->get() : collect();
        @endphp
        <div class="row mb-3 align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                    <input type="search" id="attSearch" class="form-control" placeholder="Cari nama, email, WhatsApp, atau kode peserta...">
                </div>
            </div>
            @if($usesSessions)
                <div class="col-md-2 mb-2 mb-md-0">
                    <select id="attSession" class="form-control">
                        <option value="">Semua sesi</option>
                        @foreach($eventSessions as $es)<option value="{{ $es->id }}">{{ $es->title }}</option>@endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-3 mb-2 mb-md-0">
                <select id="attFilter" class="form-control">
                    <option value="">Semua peserta</option>
                    <option value="hadir">Sudah hadir</option>
                    <option value="belum">Belum hadir</option>
                </select>
            </div>
            <div class="col-md-4 text-md-right"><b id="attCount">{{ $hadir }}</b> / {{ $participants->count() }} hadir</div>
        </div>
        <table class="table table-bordered table-hover" id="attendanceTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Peserta</th>
                    <th>Email / WA</th>
                    @if($usesSessions)<th>Sesi</th>@endif
                    <th>Barcode</th>
                    <th>Absensi Datang</th>
                    <th>Absensi Pulang</th>
                    <th style="width:150px">Set Manual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participants as $index => $p)
                    @php
                        $checkIn = $p->attendanceLogs->where('type', 'check_in')->first();
                        $secondaryLog = null;
                        if ($event->event_type == 'wisuda') {
                            $secondaryLog = $p->attendanceLogs->where('type', 'consumption')->first();
                        } else {
                            $secondaryLog = $p->attendanceLogs->where('type', 'check_out')->first();
                        }

                        $invitation = $p->invitation;
                        $qrCode = $invitation ? $invitation->unique_code : '-';
                    @endphp
                    <tr class="att-row" data-pid="{{ $p->id }}" data-session="{{ $p->session_id }}" data-hadir="{{ $checkIn ? 1 : 0 }}" data-search="{{ strtolower($p->name . ' ' . $p->email . ' ' . $p->whatsapp . ' ' . $qrCode . ' ' . $p->nim) }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->name }}</td>
                        <td>
                            {{ $p->email }}<br>
                            <small>{{ $p->whatsapp }}</small>
                        </td>
                        @if($usesSessions)
                            <td class="text-nowrap">{{ $p->session ? $p->session->title : '-' }}</td>
                        @endif
                        <td class="text-center">
                            @if($invitation)
                                {!! QrCode::size(40)->generate($qrCode) !!}
                                <br>
                                <small class="badge badge-light">{{ $qrCode }}</small>
                            @else
                                <span class="badge badge-warning">Belum ada QR</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($checkIn)
                                <span class="badge badge-success">HADIR</span>
                                <br><small>{{ \Carbon\Carbon::parse($checkIn->scanned_at)->format('H:i:s') }}</small>
                                <br><small class="text-muted">{{ $labels[$checkIn->device] ?? 'Scan' }}</small>
                            @else
                                <span class="badge badge-secondary">Belum Hadir</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($secondaryLog)
                                <span class="badge badge-primary">OK</span>
                                <br><small>{{ \Carbon\Carbon::parse($secondaryLog->scanned_at)->format('H:i:s') }}</small>
                                <br><small class="text-muted">{{ $labels[$secondaryLog->device] ?? 'Scan' }}</small>
                            @else
                                <span class="badge badge-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            @if(!$checkIn)
                                <button class="btn btn-sm btn-success att-act" data-type="check_in" data-action="set"><i class="fas fa-user-check"></i> Hadirkan</button>
                            @else
                                @if(!$secondaryLog)
                                    <button class="btn btn-sm btn-outline-info att-act" data-type="{{ $secType }}" data-action="set" title="Tandai pulang"><i class="fas fa-sign-out-alt"></i></button>
                                @endif
                                <button class="btn btn-sm btn-outline-danger att-act" data-type="{{ $secondaryLog ? $secType : 'check_in' }}" data-action="unset" data-confirm="1" title="{{ $secondaryLog ? 'Batalkan pulang' : 'Batalkan kehadiran' }}"><i class="fas fa-undo"></i></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<style>
    /* Print style if needed */
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
@stop

@section('js')
<script>
    $(function () {
        function applyFilter() {
            var q = ($('#attSearch').val() || '').toLowerCase().trim(), f = $('#attFilter').val(), ss = $('#attSession').val();
            $('.att-row').each(function () {
                var $r = $(this), ok = (!q || String($r.data('search')).indexOf(q) > -1) && (!ss || String($r.data('session')) === ss) &&
                    (!f || (f === 'hadir' ? $r.data('hadir') == 1 : $r.data('hadir') == 0));
                $r.toggle(ok);
            });
        }
        $('#attSearch').on('input', applyFilter);
        $('#attFilter, #attSession').on('change', applyFilter);

        $('.att-act').on('click', function () {
            var $b = $(this), $r = $b.closest('tr');
            if ($b.data('confirm') && !confirm('Batalkan presensi peserta ini?')) return;
            $b.prop('disabled', true);
            $.post("{{ route('attendance.manual') }}", {
                _token: '{{ csrf_token() }}', event_id: {{ $event->id }}, participant_id: $r.data('pid'),
                type: $b.data('type'), action: $b.data('action')
            }).done(function () { location.reload(); })
              .fail(function (x) { alert((x.responseJSON && x.responseJSON.message) || 'Gagal menyimpan presensi.'); $b.prop('disabled', false); });
        });
    });
</script>
@stop