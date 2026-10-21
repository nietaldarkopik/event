@extends('adminlte::page')

@section('title', 'Detail Peserta')

@section('content_header')
<h1>Detail Peserta</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center mb-4">
                    {!! $qrcode !!}
                </div>
                <h3 class="profile-username text-center">{{ $participant->name }}</h3>
                <p class="text-muted text-center">{{ $participant->invitation->unique_code ?? '-' }}</p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Event</b> <a class="float-right">{{ $participant->event->title }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Tipe</b> <a class="float-right">{{ ucfirst($participant->type) }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>WhatsApp</b> <a class="float-right">{{ $participant->whatsapp }}</a>
                    </li>
                    @if($participant->type == 'mahasiswa')
                        <li class="list-group-item">
                            <b>Prodi</b> <a class="float-right">{{ $participant->program_studi }}</a>
                        </li>
                    @endif
                </ul>
                <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary btn-block">Kembali</a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#invitation" data-toggle="tab">Undangan &
                            QR</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="invitation">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h4>Kartu Akses / Undangan</h4>
                                <p>Tunjukkan QR Code ini kepada petugas penerima tamu.</p>
                                <hr>
                                <div class="alert alert-light border">
                                    <p>Gunakan tombol di bawah untuk mengirim ulang informasi pendaftaran dan kode akses ke nomor WhatsApp peserta.</p>
                                </div>
                                <form action="{{ route('participants.resend-whatsapp', $participant->id) }}" method="POST" onsubmit="return confirm('Kirim undangan ke WhatsApp {{ $participant->whatsapp }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fab fa-whatsapp"></i> Kirim Kode & QR ke WhatsApp
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
