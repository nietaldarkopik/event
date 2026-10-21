@extends('adminlte::page')

@section('title', 'Daftar Event')

@php
    $statusMap = [
        'DRAFT' => ['Draft', 'secondary'],
        'PUBLISHED' => ['Dipublikasi', 'info'],
        'REGISTRATION_OPEN' => ['Registrasi Dibuka', 'success'],
        'REGISTRATION_CLOSED' => ['Registrasi Ditutup', 'warning'],
        'FINISHED' => ['Selesai', 'dark'],
    ];
    $fmt = function ($d, $f = 'd M Y, H:i') { return $d ? \Carbon\Carbon::parse($d)->translatedFormat($f) : null; };
@endphp

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-0">Daftar Event</h1>
        <small class="text-muted">{{ $items->count() }} event &middot; {{ $items->where('status', 'DRAFT')->count() }} draft</small>
    </div>
    <div>
        <a href="{{ route('admin.events.wizard.create') }}" class="btn btn-primary"><i class="fas fa-magic"></i> Tambah Event (Wizard)</a>
        <a href="{{ route('admin.events.create') }}" class="btn btn-outline-secondary"><i class="fas fa-plus"></i> Form Biasa</a>
    </div>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="row mb-3 align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                    <input type="search" id="ev-search" class="form-control" placeholder="Cari judul, kategori, lokasi, penyelenggara...">
                </div>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <select id="ev-status" class="form-control">
                    <option value="">Semua status</option>
                    @foreach($statusMap as $k => $v)<option value="{{ $v[0] }}">{{ $v[0] }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4 text-md-right text-muted small" id="ev-info"></div>
        </div>

        <table class="table table-hover ev-table" id="mainTable" style="width:100%">
            <thead>
                <tr>
                    <th style="width:28px" data-orderable="false"></th>
                    <th>Event</th>
                    <th>Jadwal</th>
                    <th>Lokasi</th>
                    <th class="text-center">Pendaftar</th>
                    <th>Status</th>
                    <th class="text-right" data-orderable="false" style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @php
                        [$sLabel, $sColor] = $statusMap[$item->status] ?? [$item->status, 'secondary'];
                        $banner = $item->banner_url;
                    @endphp
                    <tr>
                        <td class="ev-toggle text-muted" title="Detail lengkap"><i class="fas fa-chevron-right"></i></td>
                        <td data-order="{{ $item->title }}" data-search="{{ $item->title }} {{ $item->category->name ?? '' }} {{ $item->organization->name ?? '' }} {{ $item->short_description }}">
                            <div class="d-flex align-items-center">
                                <div class="ev-thumb mr-3" @if($banner) style="background-image:url('{{ $banner }}')" @endif>
                                    @unless($banner)<i class="fas fa-calendar-alt"></i>@endunless
                                </div>
                                <div>
                                    <div class="ev-title">{{ $item->title }}</div>
                                    <div class="ev-sub">
                                        @if($item->category)<span class="ev-chip">{{ $item->category->name }}</span>@endif
                                        {{ $item->organization->name ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td data-order="{{ optional($item->start_date)->timestamp }}" class="text-nowrap">
                            @if($item->start_date)
                                <div class="font-weight-500">{{ $fmt($item->start_date, 'd M Y') }}</div>
                                <div class="ev-sub">{{ $fmt($item->start_date, 'H:i') }}@if($item->end_date) &ndash; {{ $fmt($item->end_date, $item->start_date->isSameDay($item->end_date) ? 'H:i' : 'd M, H:i') }}@endif</div>
                            @else
                                <span class="text-muted">Belum diatur</span>
                            @endif
                        </td>
                        <td>
                            @if($item->venue_name || $item->city)
                                <div>{{ \Illuminate\Support\Str::limit($item->venue_name, 38) }}</div>
                                <div class="ev-sub">{{ $item->city }}</div>
                            @else
                                <span class="text-muted">&mdash;</span>
                            @endif
                        </td>
                        <td class="text-center" data-order="{{ $item->registrations_count }}">
                            <b>{{ $item->registrations_count }}</b>@if($item->max_participants)<span class="text-muted"> / {{ $item->max_participants }}</span>@endif
                        </td>
                        <td data-search="{{ $sLabel }}" data-order="{{ $sLabel }}">
                            <span class="badge badge-{{ $sColor }} px-2 py-1">{{ $sLabel }}</span>
                            <div class="ev-sub mt-1">
                                <i class="fas fa-{{ $item->visibility === 'PRIVATE' ? 'lock' : 'globe' }}"></i> {{ $item->visibility === 'PRIVATE' ? 'Privat' : 'Publik' }}
                            </div>
                        </td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('admin.events.wizard.edit', $item->id) }}" class="btn btn-sm {{ $item->status === 'DRAFT' ? 'btn-primary' : 'btn-outline-primary' }}" title="{{ $item->status === 'DRAFT' ? 'Lanjutkan input (wizard)' : 'Edit via wizard' }}"><i class="fas fa-magic"></i></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" title="Menu lainnya"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('admin.events.edit', $item->id) }}"><i class="fas fa-edit fa-fw mr-1"></i> Edit (form biasa)</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.participants.index', ['event_id' => $item->id]) }}"><i class="fas fa-users fa-fw mr-1"></i> Peserta</a>
                                    @if($item->is_paid ?? false)<a class="dropdown-item" href="{{ route('finance.index') }}"><i class="fas fa-money-bill fa-fw mr-1"></i> Pembayaran</a>@endif
                                    <a class="dropdown-item" href="{{ route('attendance.event', $item->id) }}"><i class="fas fa-clipboard-check fa-fw mr-1"></i> Presensi &amp; Kehadiran</a>
                                    <a class="dropdown-item" href="{{ route('certificate-generator.show', $item->id) }}"><i class="fas fa-award fa-fw mr-1"></i> Sertifikat</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.event-sessions.index', ['event_id' => $item->id]) }}"><i class="fas fa-calendar-alt fa-fw mr-1"></i> Sesi Event</a>
                                    <a class="dropdown-item" href="{{ route('admin.sponsors.index', ['event_id' => $item->id]) }}"><i class="fas fa-handshake fa-fw mr-1"></i> Sponsor</a>
                                    <a class="dropdown-item" href="{{ route('admin.galleries.index', ['event_id' => $item->id]) }}"><i class="fas fa-images fa-fw mr-1"></i> Galeri</a>
                                    <a class="dropdown-item" href="{{ route('admin.announcements.index', ['event_id' => $item->id]) }}"><i class="fas fa-bullhorn fa-fw mr-1"></i> Pengumuman</a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('admin.events.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus event ini beserta data terkait?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash fa-fw mr-1"></i> Hapus</button>
                                    </form>
                                </div>
                            </div>
                            {{-- Detail lengkap, ditampilkan saat baris dibuka --}}
                            <div class="d-none ev-detail">
                                <dl class="ev-dl">
                                    <div><dt>Slug</dt><dd>{{ $item->slug ?: '-' }}</dd></div>
                                    <div><dt>Dibuat oleh</dt><dd>{{ $item->creator->name ?? '-' }}</dd></div>
                                    <div><dt>No. WA Panitia</dt><dd>{{ $item->contact_whatsapp ?: '-' }}</dd></div>
                                    <div><dt>Alamat</dt><dd>{{ trim(($item->address ?? '') . ' ' . ($item->province ?? '')) ?: '-' }}</dd></div>
                                    <div><dt>Registrasi</dt><dd>{{ $fmt($item->registration_start, 'd M Y') ?: '-' }} &ndash; {{ $fmt($item->registration_end, 'd M Y') ?: '-' }}</dd></div>
                                    <div><dt>Sertifikat</dt><dd>{{ $item->certificate_enabled ? 'Aktif' : 'Tidak' }}</dd></div>
                                    <div><dt>Wajib absen</dt><dd>{{ $item->attendance_required ? 'Ya' : 'Tidak' }}</dd></div>
                                    <div class="ev-dl-wide"><dt>Deskripsi singkat</dt><dd>{{ $item->short_description ?: '-' }}</dd></div>
                                </dl>
                            </div>
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
<style>
    .ev-table { border-collapse: separate; border-spacing: 0; }
    .ev-table tbody td { padding: .85rem .75rem; vertical-align: middle; }
    .ev-title { font-weight: 600; color: #14532d; line-height: 1.25; }
    .ev-sub { font-size: .8rem; color: #6b7280; }
    .ev-chip { display: inline-block; background: #dcfce7; color: #166534; border-radius: 20px; padding: 0 .55rem; font-size: .72rem; font-weight: 600; margin-right: .35rem; }
    .ev-thumb { flex: 0 0 52px; width: 52px; height: 52px; border-radius: 10px; background: #e8efe9 center/cover no-repeat; color: #86a38f; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .font-weight-500 { font-weight: 500; }
    .ev-toggle { cursor: pointer; text-align: center; }
    .ev-toggle i { transition: transform .15s; }
    tr.ev-open .ev-toggle i { transform: rotate(90deg); color: #15803d; }
    .ev-child { background: #f8faf8; border-radius: 10px; padding: 1rem 1.25rem; margin: .25rem 0 .5rem 2.2rem; border: 1px solid #e3e8e5; }
    .ev-dl { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .75rem 1.5rem; margin: 0; }
    .ev-dl dt { font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; font-weight: 600; }
    .ev-dl dd { margin: 0; word-break: break-word; }
    .ev-dl-wide { grid-column: 1 / -1; }
    /* kontrol bawaan DataTables dirapikan; pencarian & filter memakai kontrol sendiri di atas */
    #mainTable_wrapper .dataTables_filter, #mainTable_wrapper .dataTables_info { display: none; }
    #mainTable_wrapper .dataTables_length label { font-size: .85rem; color: #6b7280; }
    #mainTable_wrapper .dataTables_length select { border-radius: 8px; }
    table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc { cursor: pointer; }
    #mainTable.dataTable { border-bottom: 0; }
</style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function () {
    var table = $('#mainTable').DataTable({
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[2, 'desc']],
        dom: "<'row'<'col-12'tr>><'row mt-3 align-items-center'<'col-md-4'l><'col-md-8'p>>",
        language: {
            lengthMenu: 'Tampilkan _MENU_ baris',
            zeroRecords: 'Tidak ada event yang cocok dengan pencarian.',
            paginate: {previous: '&lsaquo;', next: '&rsaquo;'}
        },
        columnDefs: [{orderable: false, targets: [0, 6]}],
        drawCallback: function () {
            var i = this.api().page.info();
            $('#ev-info').text(i.recordsDisplay ? ('Menampilkan ' + (i.start + 1) + '–' + i.end + ' dari ' + i.recordsDisplay + ' event') : '');
        }
    });

    // pulihkan kotak cari & filter status dari status yang tersimpan
    var saved = table.state.loaded();
    if (saved) {
        $('#ev-search').val(saved.search ? saved.search.search : '');
        var sc = saved.columns && saved.columns[5];
        if (sc && sc.search && sc.search.search) {
            $('#ev-status').val(sc.search.search.replace(/^\^/, '').replace(/\$$/, '').replace(/\\(.)/g, '$1'));
        }
    }
    $('#ev-search').on('input', function () { table.search(this.value).draw(); });
    $('#ev-status').on('change', function () {
        var v = this.value ? '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$' : '';
        table.column(5).search(v, true, false).draw();
    });

    // baris detail
    $('#mainTable tbody').on('click', 'td.ev-toggle', function () {
        var tr = $(this).closest('tr'), row = table.row(tr);
        if (row.child.isShown()) { row.child.hide(); tr.removeClass('ev-open'); }
        else {
            row.child('<div class="ev-child">' + tr.find('.ev-detail').html() + '</div>').show();
            tr.addClass('ev-open');
        }
    });
});
</script>
@stop
