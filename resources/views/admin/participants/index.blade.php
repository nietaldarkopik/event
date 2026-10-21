@extends('adminlte::page')

@section('title', 'Daftar Peserta')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Daftar Peserta</h1>
    <div>
        <a href="{{ route('attendance.scanner') }}{{ optional($selectedEvent)->id ? '?event_id=' . $selectedEvent->id : '' }}" class="btn btn-outline-success">
            <i class="fas fa-camera"></i> Scan Presensi
        </a>
        <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#importModal">
            <i class="fas fa-file-import"></i> Impor CSV
        </button>
        <a href="{{ route('admin.participants.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Peserta</a>
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
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

@php
    $statusMap = ['approved' => ['Disetujui', 'success'], 'pending' => ['Menunggu', 'warning'], 'pending_payment' => ['Menunggu Bayar', 'warning'], 'rejected' => ['Ditolak', 'danger']];
    $hasSessions = $selectedEvent && $sessions->count();
    $hasCerts = $selectedEvent && $certificateCategories->count();
    $filtered = request()->filled('q') || request()->filled('status') || request()->filled('session_id') || request()->filled('presence');
@endphp

<div class="row mb-3">
    @foreach([['Total peserta', $stats['total'], 'users', 'primary'], ['Disetujui', $stats['approved'], 'check-circle', 'success'], ['Menunggu', $stats['waiting'], 'hourglass-half', 'warning'], ['Sudah hadir', $stats['present'], 'clipboard-check', 'info']] as [$lbl, $num, $ico, $clr])
        <div class="col-6 col-md-3 mb-2">
            <div class="p-stat">
                <span class="p-stat-ic text-{{ $clr }}"><i class="fas fa-{{ $ico }}"></i></span>
                <div><div class="p-stat-n">{{ number_format($num) }}</div><div class="p-stat-l">{{ $lbl }}</div></div>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-body pb-2">
        <form method="GET" id="filterForm">
            <div class="form-row align-items-center">
                <div class="col-lg-3 mb-2">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                        <input type="search" name="q" id="q" value="{{ $q }}" class="form-control" placeholder="Cari nama, WhatsApp, email, NIM, atau kode..." autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <select name="event_id" class="form-control auto-submit">
                        <option value="">Semua event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ optional($selectedEvent)->id == $event->id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($event->title, 55) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-2 mb-2">
                    <select name="status" class="form-control auto-submit">
                        <option value="">Semua status</option>
                        @foreach($statusMap as $k => [$lbl])<option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $lbl }}</option>@endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-2 mb-2">
                    <select name="presence" class="form-control auto-submit">
                        <option value="">Semua kehadiran</option>
                        <option value="present" {{ request('presence') === 'present' ? 'selected' : '' }}>Sudah hadir</option>
                        <option value="absent" {{ request('presence') === 'absent' ? 'selected' : '' }}>Belum hadir</option>
                    </select>
                </div>
                @if($hasSessions)
                    <div class="col-6 col-lg-2 mb-2">
                        <select name="session_id" class="form-control auto-submit">
                            <option value="">Semua sesi</option>
                            <option value="none" {{ request('session_id') === 'none' ? 'selected' : '' }}>Belum bersesi</option>
                            @foreach($sessions as $sess)<option value="{{ $sess->id }}" {{ (string) request('session_id') === (string) $sess->id ? 'selected' : '' }}>{{ $sess->title }}</option>@endforeach
                        </select>
                    </div>
                @endif
                <div class="col-auto mb-2">
                    @if($filtered)<a href="{{ route('admin.participants.index', array_filter(['event_id' => optional($selectedEvent)->id])) }}" class="btn btn-link text-muted" title="Hapus semua filter"><i class="fas fa-times"></i> Reset</a>@endif
                </div>
            </div>
        </form>
    </div>
    @if($selectedEvent && $sessions->count())
        <div class="card-body border-bottom bg-light">
            <div class="d-flex flex-wrap align-items-center mb-2">
                <b class="mr-3"><i class="fas fa-layer-group text-primary"></i> Sesi:</b>
                @foreach($sessions as $sess)
                    <span class="badge badge-light border mr-2 p-2">
                        {{ $sess->title }}
                        @if($sess->start_datetime)({{ \Carbon\Carbon::parse($sess->start_datetime)->format('H.i') }}@if($sess->end_datetime)&ndash;{{ \Carbon\Carbon::parse($sess->end_datetime)->format('H.i') }}@endif)@endif
                        &middot; <b>{{ $sess->taken }}</b>@if($sess->max_participants) / {{ $sess->max_participants }}@endif peserta
                        @if($sess->is_full)<span class="text-danger">penuh</span>@endif
                    </span>
                @endforeach
                <span class="badge badge-secondary ml-1">Mode: {{ ['none' => 'Tanpa sesi', 'admin' => 'Ditetapkan panitia', 'choose' => 'Dipilih peserta'][$selectedEvent->session_mode ?? 'none'] ?? '-' }}</span>
            </div>
            <form id="bulkSessionForm" class="form-inline">
                <span class="mr-2 small text-muted">Tetapkan massal:</span>
                <select id="bulkField" class="form-control form-control-sm mr-2">
                    <option value="all">Semua peserta</option>
                    @if(!empty($criteria['fakultas']) && count($criteria['fakultas']))<option value="fakultas">Fakultas</option>@endif
                    @if(!empty($criteria['program_studi']) && count($criteria['program_studi']))<option value="program_studi">Program Studi</option>@endif
                    @if(!empty($criteria['type']) && count($criteria['type']))<option value="type">Tipe</option>@endif
                </select>
                <select id="bulkValue" class="form-control form-control-sm mr-2" style="display:none; max-width:240px"></select>
                <span class="mr-2 small text-muted">&rarr; ke</span>
                <select id="bulkSession" class="form-control form-control-sm mr-2">
                    @foreach($sessions as $sess)<option value="{{ $sess->id }}">{{ $sess->title }}</option>@endforeach
                </select>
                <div class="form-check mr-2 small">
                    <input type="checkbox" class="form-check-input" id="bulkOnlyNew" checked>
                    <label class="form-check-label" for="bulkOnlyNew">hanya yang belum bersesi</label>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
            </form>
        </div>
    @endif
    <div class="card-body pt-2 pb-0">
        <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
            <span>Menampilkan <b>{{ $participants->firstItem() ?: 0 }}&ndash;{{ $participants->lastItem() ?: 0 }}</b> dari <b>{{ number_format($participants->total()) }}</b> peserta{{ $filtered ? ' (hasil filter)' : '' }}</span>
            <form method="GET" class="form-inline">
                @foreach(request()->except(['per_page', 'page']) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                <label class="mr-2 mb-0">Baris</label>
                <select name="per_page" class="form-control form-control-sm auto-submit">
                    @foreach([20, 50, 100] as $n)<option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>@endforeach
                </select>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 no-tidy p-table">
            <thead>
                <tr>
                    <th>Peserta</th>
                    @unless($selectedEvent)<th>Event</th>@endunless
                    <th>Kode &middot; Kursi</th>
                    @if($hasSessions)<th style="min-width:150px">Sesi</th>@endif
                    <th>Kehadiran</th>
                    @if($hasCerts)<th style="min-width:150px">Kategori Sertifikat</th>@endif
                    <th>Status</th>
                    <th class="text-right p-sticky">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $item)
                    @php
                        $checkIn = $item->attendanceLogs->firstWhere('type', 'check_in');
                        $checkOut = $item->attendanceLogs->first(fn ($log) => in_array($log->type, ['check_out', 'consumption']));
                        [$sLabel, $sColor] = $statusMap[$item->status] ?? [$item->status ?: '-', 'secondary'];
                        $sub = collect([$item->nim, $item->program_studi ?: optional($item->unitRelation)->name, $item->fakultas, $item->jabatan, $item->unit])->filter()->unique()->take(2)->implode(' · ');
                    @endphp
                    <tr>
                        <td>
                            <div class="p-name">{{ $item->name }} @if($item->type)<span class="list-chip">{{ ucfirst($item->type) }}</span>@endif</div>
                            @if($sub)<div class="list-sub">{{ \Illuminate\Support\Str::limit($sub, 60) }}</div>@endif
                            <div class="list-sub">
                                @if($item->whatsapp)<i class="fab fa-whatsapp"></i> {{ $item->whatsapp }}@endif
                                @if($item->email)<span class="ml-2"><i class="far fa-envelope"></i> {{ \Illuminate\Support\Str::limit($item->email, 28) }}</span>@endif
                            </div>
                        </td>
                        @unless($selectedEvent)<td class="small">{{ \Illuminate\Support\Str::limit($item->event->title ?? '-', 34) }}</td>@endunless
                        <td class="text-nowrap">
                            <code class="p-code">{{ optional($item->invitation)->unique_code ?: '—' }}</code>
                            <form class="seat-form mt-1" data-id="{{ $item->id }}">
                                <div class="input-group input-group-sm" style="width:118px">
                                    <input type="text" name="seat_number" class="form-control" placeholder="Kursi" value="{{ $item->seat_number }}">
                                    <div class="input-group-append"><button type="submit" class="btn btn-outline-secondary" title="Simpan nomor kursi"><i class="fas fa-check"></i></button></div>
                                </div>
                            </form>
                        </td>
                        @if($hasSessions)
                            <td>
                                <select class="form-control form-control-sm session-select" data-id="{{ $item->id }}">
                                    <option value="">Belum ditetapkan</option>
                                    @foreach($sessions as $sess)
                                        <option value="{{ $sess->id }}" {{ $item->session_id == $sess->id ? 'selected' : '' }}>{{ $sess->title }}</option>
                                    @endforeach
                                </select>
                            </td>
                        @endif
                        <td class="text-nowrap small">
                            @if($checkIn)
                                <div><span class="text-success"><i class="fas fa-sign-in-alt"></i></span> {{ $checkIn->scanned_at->format('d/m H.i') }}</div>
                                <div class="{{ $checkOut ? '' : 'text-muted' }}"><i class="fas fa-sign-out-alt"></i> {{ $checkOut ? $checkOut->scanned_at->format('d/m H.i') : 'belum pulang' }}</div>
                            @else
                                <span class="text-muted">Belum hadir</span>
                            @endif
                        </td>
                        @if($hasCerts)
                            <td>
                                <select class="form-control form-control-sm cert-category-select" data-id="{{ $item->id }}">
                                    <option value="">Belum diatur</option>
                                    @foreach($certificateCategories as $cat)
                                        <option value="{{ $cat->id }}" {{ $item->certificate_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        @endif
                        <td><span class="badge badge-{{ $sColor }} px-2 py-1">{{ $sLabel }}</span></td>
                        <td class="text-right text-nowrap p-sticky">
                            @if($item->status !== 'approved')
                                <form action="{{ route('participants.approve', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui peserta ini? Undangan + QR akan dikirim ke WhatsApp.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui &amp; kirim undangan"><i class="fas fa-check"></i> Setujui</button>
                                </form>
                            @else
                                <form action="{{ route('participants.resend-whatsapp', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Kirim ulang undangan + QR ke WhatsApp peserta ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Kirim ulang undangan ke WhatsApp"><i class="fab fa-whatsapp"></i></button>
                                </form>
                            @endif
                            <a href="{{ route('admin.participants.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Detail &amp; QR"><i class="fas fa-eye"></i></a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" title="Menu lainnya"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('admin.participants.edit', $item->id) }}"><i class="fas fa-edit fa-fw mr-1"></i> Edit data</a>
                                    @if($item->event_id)
                                        <a class="dropdown-item" href="{{ route('attendance.event', $item->event_id) }}"><i class="fas fa-clipboard-check fa-fw mr-1"></i> Presensi event ini</a>
                                        <a class="dropdown-item" href="{{ route('certificate-generator.show', $item->event_id) }}"><i class="fas fa-award fa-fw mr-1"></i> Sertifikat event ini</a>
                                        <a class="dropdown-item" href="{{ route('admin.events.wizard.edit', $item->event_id) }}"><i class="fas fa-calendar fa-fw mr-1"></i> Buka event</a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('admin.participants.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus peserta ini beserta undangan dan datanya?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash fa-fw mr-1"></i> Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                        {{ $filtered ? 'Tidak ada peserta yang cocok dengan filter.' : 'Belum ada peserta.' }}
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
        <small class="text-muted">Halaman {{ $participants->currentPage() }} dari {{ $participants->lastPage() }}</small>
        {{ $participants->links() }}
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('participants.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Impor Peserta dari CSV</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Cara import peserta:</strong>
                        <ol class="mb-0 pl-3">
                            <li>Unduh template CSV di bawah ini.</li>
                            <li>Isi data peserta pada template tersebut, satu peserta per baris. Jangan hapus atau ubah baris judul (header) paling atas.</li>
                            <li>Kolom <code>unit</code> diisi sesuai nama pada daftar <em>Master Data Unit</em> di bawah (fakultas atau program studi), boleh dikosongkan jika tidak relevan (mis. peserta umum).</li>
                            <li>Pilih event tujuan, lalu unggah berkas CSV yang sudah diisi.</li>
                            <li>Peserta dengan email yang sudah terdaftar di event yang sama akan dilewati (tidak diimpor ulang).</li>
                        </ol>
                    </div>
                    <a href="{{ route('participants.import-template') }}" class="btn btn-sm btn-outline-success mb-3">
                        <i class="fas fa-download"></i> Unduh Template CSV
                    </a>
                    <div class="form-group">
                        <label for="import_event_id">Event</label>
                        <select class="form-control" id="import_event_id" name="event_id" required>
                            <option value="">-- Pilih Event --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="import_status_option">Status Peserta Setelah Diimpor</label>
                        <select class="form-control" id="import_status_option" name="status_option">
                            <option value="auto">Otomatis (ikuti pengaturan event: gratis = langsung disetujui, berbayar = menunggu pembayaran)</option>
                            <option value="approved">Langsung Disetujui</option>
                            <option value="pending">Menunggu Persetujuan (Pending)</option>
                        </select>
                        <small class="text-muted d-block mt-1">Peserta berstatus <b>Menunggu</b> tidak langsung dapat kode QR/undangan — panitia bisa menyetujuinya nanti lewat tombol "Setujui" di daftar peserta.</small>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="import_send_wa" name="send_wa" value="1" checked>
                        <label class="form-check-label" for="import_send_wa">Kirim WhatsApp (kode registrasi + QR) otomatis ke peserta yang langsung disetujui</label>
                    </div>
                    <div class="form-group" id="import_wa_message_group">
                        <label for="import_wa_message">Isi Pesan WhatsApp</label>
                        <textarea class="form-control" id="import_wa_message" name="wa_message" rows="8">{{ $defaultWaTemplate }}</textarea>
                        <small class="text-muted d-block mt-1">
                            Placeholder yang bisa dipakai: <code>{nama}</code>, <code>{event}</code>, <code>{tanggal}</code>, <code>{lokasi}</code>, <code>{kode}</code>, <code>{link}</code> — otomatis diganti data masing-masing peserta. Gambar QR tetap dilampirkan terpisah.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="import_file">Berkas CSV</label>
                        <input type="file" class="form-control-file" id="import_file" name="file" accept=".csv,.txt" required>
                        <small class="text-muted d-block mt-1">
                            Kolom yang didukung (baris pertama harus header, urutan bebas):
                            <strong>nama, email, whatsapp, tipe, nim, program_studi, fakultas, jabatan, unit, no_kursi, kode_registrasi</strong>.<br>
                            &bull; <strong>nama</strong>, <strong>whatsapp</strong>, <strong>tipe</strong> wajib diisi.<br>
                            &bull; <strong>tipe</strong>: mahasiswa, dosen, tendik, atau umum.<br>
                            &bull; <strong>nim</strong>: khusus mahasiswa. <strong>jabatan</strong>: khusus dosen/tendik.<br>
                            &bull; <strong>unit</strong>: nama fakultas/prodi sesuai Master Data Unit di bawah (opsional).<br>
                            &bull; <strong>no_kursi</strong>: nomor kursi (opsional), harus unik per event — baris dengan kursi yang sudah dipakai akan dilewati.<br>
                            &bull; <strong>kode_registrasi</strong>: kode undangan/QR yang sudah ditentukan sendiri (opsional, kosongkan untuk digenerate otomatis). Harus unik di seluruh sistem, dan hanya berlaku jika status peserta langsung <strong>Disetujui</strong> — untuk status Pending, kode baru tetap dibuat otomatis saat panitia menyetujui nanti.
                        </small>
                    </div>
                    <div class="form-group mb-0">
                        <a class="d-inline-block mb-1" data-toggle="collapse" href="#unitMasterData" role="button" aria-expanded="false" aria-controls="unitMasterData">
                            <i class="fas fa-list"></i> Lihat Master Data Unit (Fakultas &amp; Program Studi)
                        </a>
                        <div class="collapse" id="unitMasterData">
                            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nama Unit</th>
                                            <th>Jenis</th>
                                            <th>Induk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($units as $unit)
                                            <tr>
                                                <td>{{ $unit->name }}</td>
                                                <td>{{ $unit->type }}</td>
                                                <td>{{ $unit->parent->name ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-muted">Belum ada master data unit.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .p-stat { background: #fff; border: 1px solid #e3e8e5; border-radius: 10px; padding: .7rem 1rem; display: flex; align-items: center; box-shadow: 0 1px 2px rgba(16,24,20,.05); }
    .p-stat-ic { font-size: 1.4rem; width: 42px; height: 42px; border-radius: 10px; background: #f3f6f4; display: flex; align-items: center; justify-content: center; margin-right: .8rem; }
    .p-stat-n { font-size: 1.35rem; font-weight: 700; line-height: 1.1; color: #14532d; }
    .p-stat-l { font-size: .75rem; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; }
    .p-table thead th { background: #f0f4f1; font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; color: #374151; border-top: 0; white-space: nowrap; }
    .p-table td { vertical-align: middle; padding: .65rem .75rem; }
    .p-name { font-weight: 600; color: #14532d; line-height: 1.25; }
    .p-code { background: #f3f6f4; color: #166534; padding: .1rem .4rem; border-radius: 5px; font-size: .78rem; }
    .p-sticky { position: sticky; right: 0; background: #fff; box-shadow: -8px 0 10px -8px rgba(16,24,20,.18); z-index: 2; }
    .p-table thead .p-sticky { background: #f0f4f1; }
    .p-table tbody tr:hover .p-sticky { background: #f0fdf4; }
    .p-table .dropdown-menu { z-index: 1050; }
</style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        var sendWaCheckbox = document.getElementById('import_send_wa');
        var statusOptionSelect = document.getElementById('import_status_option');
        var waMessageGroup = document.getElementById('import_wa_message_group');
        function toggleWaMessageGroup() {
            var willSendWa = sendWaCheckbox.checked && statusOptionSelect.value !== 'pending';
            waMessageGroup.style.display = willSendWa ? '' : 'none';
        }
        if (sendWaCheckbox && statusOptionSelect && waMessageGroup) {
            toggleWaMessageGroup();
            sendWaCheckbox.addEventListener('change', toggleWaMessageGroup);
            statusOptionSelect.addEventListener('change', toggleWaMessageGroup);
        }

        document.querySelectorAll('.seat-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var id = form.dataset.id;
                var seat = form.querySelector('[name="seat_number"]').value;
                fetch('{{ url("admin/participants") }}/' + id + '/seat-number', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ seat_number: seat })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (!data.success) { alert(data.message || 'Gagal menyimpan nomor kursi.'); return; }
                    var inp = form.querySelector('input'); inp.style.boxShadow = '0 0 0 3px rgba(22,163,74,.35)'; setTimeout(function () { inp.style.boxShadow = ''; }, 900);
                }).catch(function () { alert('Gagal menghubungi server.'); });
            });
        });


        // ---- Sesi peserta ----
        document.querySelectorAll('.session-select').forEach(function (select) {
            select.addEventListener('change', function () {
                fetch('{{ url("admin/participants") }}/' + select.dataset.id + '/session', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ session_id: select.value || null })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (!data.success) { alert(data.message || 'Gagal menyimpan sesi.'); return; }
                    if (data.over_quota) alert('Perhatian: sesi ini sekarang melebihi kuota yang ditetapkan.');
                    select.style.boxShadow = '0 0 0 3px rgba(22,163,74,.35)';
                    setTimeout(function () { select.style.boxShadow = ''; }, 900);
                }).catch(function () { alert('Gagal menghubungi server.'); });
            });
        });

        var bulkForm = document.getElementById('bulkSessionForm');
        if (bulkForm) {
            var criteria = @json($criteria ?? []);
            var bField = document.getElementById('bulkField'), bValue = document.getElementById('bulkValue');
            bField.addEventListener('change', function () {
                var list = criteria[bField.value] || [];
                bValue.innerHTML = list.map(function (v) { return '<option>' + String(v).replace(/</g, '&lt;') + '</option>'; }).join('');
                bValue.style.display = bField.value === 'all' ? 'none' : '';
            });
            bulkForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var label = bField.value === 'all' ? 'SEMUA peserta' : ('peserta dengan ' + bField.options[bField.selectedIndex].text + ' "' + bValue.value + '"');
                if (!confirm('Tetapkan ' + label + (document.getElementById('bulkOnlyNew').checked ? ' yang belum bersesi' : '') + ' ke sesi terpilih?')) return;
                fetch('{{ route("participants.bulkSession") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        event_id: {{ optional($selectedEvent)->id ?? 'null' }}, session_id: document.getElementById('bulkSession').value,
                        field: bField.value, value: bValue.value || null, only_unassigned: document.getElementById('bulkOnlyNew').checked ? 1 : 0
                    })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    alert(data.message || 'Selesai.');
                    if (data.success) location.reload();
                }).catch(function () { alert('Gagal menghubungi server.'); });
            });
        }

        document.querySelectorAll('.cert-category-select').forEach(function (select) {
            select.addEventListener('change', function () {
                var id = select.dataset.id;
                fetch('{{ url("admin/participants") }}/' + id + '/certificate-category', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ certificate_category_id: select.value || null })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (!data.success) alert(data.message || 'Gagal menyimpan kategori sertifikat.');
                }).catch(function () { alert('Gagal menghubungi server.'); });
            });
        });

        // ---- Filter & pencarian (diproses di server) ----
        var filterForm = document.getElementById('filterForm');
        document.querySelectorAll('.auto-submit').forEach(function (el) {
            el.addEventListener('change', function () { (el.form || filterForm).submit(); });
        });
        var qInput = document.getElementById('q'), qTimer;
        if (qInput) {
            if (qInput.value) { var l = qInput.value.length; qInput.focus(); qInput.setSelectionRange(l, l); }
            qInput.addEventListener('input', function () { clearTimeout(qTimer); qTimer = setTimeout(function () { filterForm.submit(); }, 550); });
        }
        // nomor kursi: simpan juga saat menekan Enter / pindah kolom, tampilkan tanda berhasil
        document.querySelectorAll('.seat-form input[name="seat_number"]').forEach(function (inp) {
            var orig = inp.value;
            inp.addEventListener('change', function () { if (inp.value !== orig) inp.form.requestSubmit(); });
        });
    });
</script>
@stop
