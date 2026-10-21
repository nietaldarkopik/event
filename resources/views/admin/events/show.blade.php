@extends('adminlte::page')

@section('title', 'Detail Event')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Detail Event: {{ $event->title }}</h1>
    <div>
        @if(strtoupper($event->status) != 'PUBLISHED')
            <button onclick="copyPreviewLink()" class="btn btn-info"><i class="fas fa-link"></i> Salin Link Preview</button>
        @endif
        <a href="{{ route('events.certificates.settings', $event->id) }}" class="btn btn-primary"><i
                class="fas fa-certificate"></i> Pengaturan Sertifikat</a>
        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i>
            Edit</a>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
            Kembali</a>
    </div>
</div>

<script>
    function copyPreviewLink() {
        // Generate signed URL from backend (rendered by Blade)
        const url = "{{ URL::signedRoute('admin.events.preview_detail', $event->slug) }}";
        navigator.clipboard.writeText(url).then(function () {
            alert('Link preview detail berhasil disalin! Silakan bagikan ke pimpinan.');
        }, function (err) {
            console.error('Gagal menyalin: ', err);
        });
    }
</script>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $event->title }}</h3>
                <p class="text-muted text-center">{{ ucfirst($event->event_type) }} |
                    <span
                        class="badge badge-{{ strtoupper($event->status) == 'PUBLISHED' ? 'success' : 'secondary' }}">{{ ucfirst($event->status) }}</span>
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Lokasi</b> <a class="float-right">{{ $event->location }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Waktu Mulai</b> <a class="float-right">{{ $event->start_datetime->format('d M Y H:i') }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Waktu Selesai</b> <a class="float-right">{{ $event->end_datetime->format('d M Y H:i') }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Harga</b> <a class="float-right">Rp {{ number_format($event->price, 0, ',', '.') }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Internal Only</b> <a class="float-right">{{ $event->is_internal ? 'Ya' : 'Tidak' }}</a>
                    </li>
                </ul>
                <p class="text-muted">
                    <div class="rich-content">{!! \App\Support\Html::render($event->description) !!}</div>
                </p>
            </div>
        </div>

        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Rundown Acara</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 150px">Waktu</th>
                            <th>Agenda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->rundowns as $rundown)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($rundown->time_start)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($rundown->time_end)->format('H:i') }}
                                </td>
                                <td>{{ $rundown->agenda }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Statistik Peserta</h3>
            </div>
            <div class="card-body">
                <p>Total Peserta: <strong>{{ $event->participants->count() }}</strong></p>
                <a href="#" class="btn btn-primary btn-block"><b>Lihat Peserta</b></a>
            </div>
        </div>

        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Zona Bahaya</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST"
                    onsubmit="return confirm('Apakah anda yakin ingin menghapus event ini? Data peserta juga akan terhapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block">Hapus Event</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop