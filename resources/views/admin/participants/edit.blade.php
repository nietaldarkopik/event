@extends('adminlte::page')

@section('title', 'Edit Peserta')

@section('content_header')
    <h1>Edit Peserta</h1>
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

        <form action="{{ route('admin.participants.update', $participant->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-outline card-info">
                        <div class="card-header"><h3 class="card-title">Data Peserta</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="event_id">Event <span class="text-danger">*</span></label>
                                <select class="form-control" id="event_id" name="event_id" required>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ old('event_id', $participant->event_id) == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $participant->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="whatsapp">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $participant->whatsapp) }}" placeholder="628...">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $participant->email) }}">
                            </div>
                            <div class="form-group">
                                <label for="type">Tipe Peserta <span class="text-danger">*</span></label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="mahasiswa" {{ old('type', $participant->type) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                    <option value="dosen" {{ old('type', $participant->type) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                    <option value="tendik" {{ old('type', $participant->type) == 'tendik' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                                    <option value="umum" {{ old('type', $participant->type) == 'umum' ? 'selected' : '' }}>Umum</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nim">NIM</label>
                                <input type="text" class="form-control" id="nim" name="nim" value="{{ old('nim', $participant->nim) }}">
                            </div>
                            <div class="form-group">
                                <label for="program_studi">Program Studi</label>
                                <input type="text" class="form-control" id="program_studi" name="program_studi" value="{{ old('program_studi', $participant->program_studi) }}">
                            </div>
                            <div class="form-group">
                                <label for="fakultas">Fakultas</label>
                                <input type="text" class="form-control" id="fakultas" name="fakultas" value="{{ old('fakultas', $participant->fakultas) }}">
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ old('jabatan', $participant->jabatan) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-outline card-secondary">
                        <div class="card-header"><h3 class="card-title">Unit, Kursi &amp; Status</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="unit_id">Unit (Fakultas/Prodi)</label>
                                <select class="form-control" id="unit_id" name="unit_id">
                                    <option value="">-- Tidak ada --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id', $participant->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($eventSessions->count())
                                <div class="form-group">
                                    <label for="session_id">Sesi</label>
                                    <select class="form-control" id="session_id" name="session_id">
                                        <option value="">-- Belum ditetapkan --</option>
                                        @foreach($eventSessions as $es)
                                            <option value="{{ $es->id }}" {{ old('session_id', $participant->session_id) == $es->id ? 'selected' : '' }}>{{ $es->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="seat_number">Nomor Kursi</label>
                                <input type="text" class="form-control" id="seat_number" name="seat_number" value="{{ old('seat_number', $participant->seat_number) }}">
                            </div>
                            <div class="form-group mb-0">
                                <label>Status saat ini</label>
                                <div>
                                    @php
                                        $statusMap = ['approved' => 'success', 'pending' => 'warning', 'pending_payment' => 'warning'];
                                        $statusLabel = ['approved' => 'Disetujui', 'pending' => 'Menunggu', 'pending_payment' => 'Menunggu Pembayaran'];
                                    @endphp
                                    <span class="badge badge-{{ $statusMap[$participant->status] ?? 'secondary' }}">
                                        {{ $statusLabel[$participant->status] ?? $participant->status }}
                                    </span>
                                </div>
                                <small class="text-muted">Ubah status lewat tombol Setujui di halaman daftar peserta.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
