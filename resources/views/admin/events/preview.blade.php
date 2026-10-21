<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview Detail Event: {{ $event->title }}</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE Theme Style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .preview-header {
            background-color: #f4f6f9;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
            <div class="container">
                <a href="#" class="navbar-brand">
                    <span class="brand-text font-weight-light">EVENT<b>UNWIM</b> Preview</span>
                </a>
            </div>
        </nav>
        <!-- /.navbar -->

        <div class="content-wrapper">
            <div class="preview-header text-center">
                <div class="container">
                    <h1 class="m-0">Detail Event: {{ $event->title }}</h1>
                    <p class="text-muted mt-2">
                        Halaman ini adalah <strong>Preview Draft</strong> untuk keperluan review internal.
                    </p>
                </div>
            </div>

            <div class="content">
                <div class="container">
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
                                            <b>Waktu Mulai</b> <a
                                                class="float-right">{{ $event->start_datetime->format('d M Y H:i') }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Waktu Selesai</b> <a
                                                class="float-right">{{ $event->end_datetime->format('d M Y H:i') }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Harga</b> <a class="float-right">Rp
                                                {{ number_format($event->price, 0, ',', '.') }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Internal Only</b> <a
                                                class="float-right">{{ $event->is_internal ? 'Ya' : 'Tidak' }}</a>
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
                                    <h3 class="card-title">Statistik & Kontak</h3>
                                </div>
                                <div class="card-body">
                                    <p>Unit Penyelenggara: <strong>{{ $event->organizer_unit ?? '-' }}</strong></p>
                                    <p>Contact Person: <strong>{{ $event->contact_whatsapp ?? '-' }}</strong></p>
                                    <hr>
                                    <p>Total Peserta (Saat Ini): <strong>{{ $event->participants->count() }}</strong>
                                    </p>
                                </div>
                            </div>

                            @if($event->event_type == 'wisuda')
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">PIC Fakultas</h3>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li>Pertanian: {{ $event->wa_pertanian ?? '-' }}</li>
                                            <li>Kehutanan: {{ $event->wa_kehutanan ?? '-' }}</li>
                                            <li>Teknik: {{ $event->wa_teknik ?? '-' }}</li>
                                            <li>Ekonomi: {{ $event->wa_ekonomi ?? '-' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                Preview Mode
            </div>
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">EVENTUNWIM</a>.</strong> All rights reserved.
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>

</html>