@extends('adminlte::page')

@section('title', 'Tambah Peserta')

@section('content_header')
    <h1>Tambah Peserta</h1>
@stop

@section('content')
<div class="card card-primary card-outline">
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

        <form action="{{ route('admin.participants.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-outline card-info">
                        <div class="card-header"><h3 class="card-title">Data Peserta</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="event_id">Event <span class="text-danger">*</span></label>
                                <select class="form-control" id="event_id" name="event_id" required>
                                    <option value="">-- Pilih Event --</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="whatsapp">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="628..." required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                            </div>
                            <div class="form-group">
                                <label for="type">Tipe Peserta <span class="text-danger">*</span></label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="mahasiswa" {{ old('type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                    <option value="dosen" {{ old('type') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                    <option value="tendik" {{ old('type') == 'tendik' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                                    <option value="umum" {{ old('type') == 'umum' ? 'selected' : '' }}>Umum</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nim">NIM</label>
                                <input type="text" class="form-control" id="nim" name="nim" value="{{ old('nim') }}" placeholder="Khusus mahasiswa">
                            </div>
                            <div class="form-group">
                                <label for="program_studi">Program Studi</label>
                                <input type="text" class="form-control" id="program_studi" name="program_studi" value="{{ old('program_studi') }}">
                            </div>
                            <div class="form-group">
                                <label for="fakultas">Fakultas</label>
                                <input type="text" class="form-control" id="fakultas" name="fakultas" value="{{ old('fakultas') }}">
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" placeholder="Khusus dosen/tendik">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-outline card-secondary">
                        <div class="card-header"><h3 class="card-title">Unit &amp; Kursi</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="unit_id">Unit (Fakultas/Prodi)</label>
                                <select class="form-control" id="unit_id" name="unit_id">
                                    <option value="">-- Tidak ada --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="sessionGroup" style="display:none">
                                <label for="session_id">Sesi</label>
                                <select class="form-control" id="session_id" name="session_id"></select>
                                <small class="text-muted">Hanya tampil untuk event yang memiliki sesi.</small>
                            </div>
                            <div class="form-group">
                                <label for="seat_number">Nomor Kursi</label>
                                <input type="text" class="form-control" id="seat_number" name="seat_number" value="{{ old('seat_number') }}">
                            </div>
                            <div class="alert alert-light border small mb-0">
                                Peserta pada event gratis otomatis berstatus <b>disetujui</b> dan langsung dikirimi undangan + QR lewat WhatsApp. Peserta pada event berbayar berstatus <b>menunggu pembayaran</b> sampai dikonfirmasi lewat modul Keuangan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

@section('js')
<script>
    (function () {
        var data = @json($sessionsByEvent);
        var old = @json(old('session_id'));
        var ev = document.getElementById('event_id'), grp = document.getElementById('sessionGroup'), sel = document.getElementById('session_id');
        function render() {
            var list = data[ev.value] || [];
            sel.innerHTML = '<option value="">-- Belum ditetapkan --</option>' + list.map(function (s) {
                return '<option value="' + s.id + '"' + (String(old) === String(s.id) ? ' selected' : '') + '>' + s.title.replace(/</g, '&lt;') + '</option>';
            }).join('');
            grp.style.display = list.length ? '' : 'none';
        }
        ev.addEventListener('change', function () { old = null; render(); });
        render();
    })();
</script>
@stop
