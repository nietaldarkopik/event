@extends('layouts.public')

@section('title', 'Cek Sertifikat')

@section('content')
    <style>
        /* Hide Footer & Force Fullscreen */
        .footer {
            display: none !important;
        }

        body {
            overflow: hidden;
            background: #0f172a;
        }

        /* Custom Scrollbar for the main content area */
        .scrollable-content {
            height: 100vh;
            overflow-y: auto;
            scroll-behavior: smooth;
            padding-top: 80px;
            /* Reduced offset to lift content up */
            padding-bottom: 150px;
            /* Increased bottom padding for breathing room */
        }

        .scrollable-content::-webkit-scrollbar {
            width: 8px;
        }

        .scrollable-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .scrollable-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .scrollable-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>

    <!-- Background w/ Gradient -->
    <div class="position-fixed w-100 h-100" style="z-index: -1; top: 0; left: 0; background: #0f172a;">
        <div class="position-absolute"
            style="top: -10%; right: -5%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);">
        </div>
        <div class="position-absolute"
            style="bottom: 0; left: 0; width: 100%; height: 50%; background: linear-gradient(to top, #0f172a 0%, transparent 100%);">
        </div>
    </div>

    <!-- Scrollable Content Wrapper -->
    <div class="scrollable-content d-flex flex-column align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Header Section -->
                <div class="col-lg-8 text-center mb-4" data-aos="fade-down"> <!-- Reduced mb-5 to mb-4 -->
                    <span
                        class="badge badge-light text-primary px-4 py-2 rounded-pill mb-4 shadow-sm font-weight-bold letter-spacing-1">
                        OFFICIAL VERIFICATION
                    </span>
                    <h1 class="display-4 font-heading font-weight-bold text-white mb-3">
                        Validasi <span class="text-warning">E-Sertifikat</span>
                    </h1>
                    <!-- FIXED: Changed text color to text-light and opacity-90 for better visibility -->
                    <p class="lead text-light opacity-90 mx-auto" style="max-width: 600px; font-weight: 300;">
                        Verifikasi keaslian dokumen akademik Anda secara real-time melalui sistem terintegrasi Universitas
                        Winaya Mukti.
                    </p>
                </div>

                <!-- Search Card -->
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-ultra p-5 border-0 shadow-2xl">
                        <form action="{{ route('certificates.search') }}" method="GET" id="searchForm">
                            <div class="form-group mb-4">
                                <label
                                    class="font-weight-bold text-dark mb-3 ml-2 small text-uppercase letter-spacing-2 text-muted">Nomor
                                    Dokumen / Identitas</label>
                                <div
                                    class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden bg-white border">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0 pl-4 pr-3">
                                            <i class="fas fa-search text-primary fa-lg"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="query" id="searchInput"
                                        class="form-control bg-white border-0 h-auto py-3 font-weight-bold text-dark"
                                        placeholder="Masukkan Nomor Sertifikat..." value="{{ $search ?? '' }}" required
                                        style="font-size: 1.1rem; outline: none; box-shadow: none;">
                                    <!-- NEW: Scan Button inside Input Group -->
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-light border-0 text-dark font-weight-bold px-4"
                                            id="btnScanQR" title="Scan QR Code">
                                            <i class="fas fa-qrcode fa-lg text-primary"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit"
                                class="btn btn-primary-modern btn-block btn-lg rounded-pill font-weight-bold shadow-lg py-3 hover-scale">
                                Verifikasi Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Search Results -->
            @if(isset($certificates))
                <div class="row justify-content-center mt-5 pb-5" id="results">
                    <div class="col-lg-9 animate__animated animate__fadeInUp">
                        @if($certificates->count() > 0)
                            <div class="d-flex align-items-center justify-content-between mb-4 text-white">
                                <h5 class="font-weight-bold mb-0"><i class="fas fa-check-circle text-success mr-2"></i> Hasil
                                    Validasi</h5>
                                <span class="badge badge-success px-3 py-2 rounded-pill shadow-sm">{{ $certificates->count() }}
                                    Dokumen Valid</span>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                @foreach($certificates as $cert)
                                    <div class="card bg-white border-0 shadow-sm rounded-lg mb-3 hover-lift overflow-hidden">
                                        <div class="card-body p-4">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center mb-3 mb-md-0">
                                                    <div class="bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                        style="width: 70px; height: 70px; background: #eff6ff;">
                                                        <i class="fas fa-certificate fa-2x"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span
                                                            class="badge badge-primary rounded-pill px-3 py-1 small font-weight-bold mr-2">{{ $cert->event->event_type }}</span>
                                                        <small
                                                            class="text-muted font-weight-bold">{{ \Carbon\Carbon::parse($cert->created_at)->format('d F Y') }}</small>
                                                    </div>
                                                    <h5 class="font-weight-bold text-dark mb-1">{{ $cert->event->title }}</h5>
                                                    <p class="mb-0 text-muted small">Diberikan kepada: <strong
                                                            class="text-dark">{{ $cert->display_name }}</strong></p>
                                                    <div class="mt-2 text-primary small font-monospace">
                                                        ID: {{ $cert->certificate_number }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3 text-center text-md-right mt-3 mt-md-0">
                                                    <a href="{{ route('certificates.download', $cert->id) }}" target="_blank"
                                                        class="btn btn-outline-primary rounded-pill px-4 btn-sm font-weight-bold">
                                                        <i class="fas fa-download mr-1"></i> Unduh PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-success h-1 w-100"></div> <!-- Valid indicator line -->
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div class="modal fade" id="qrScanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-qrcode mr-2"></i> Scan QR Code</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0 bg-dark position-relative">
                    <div id="reader" style="width: 100%;"></div>
                    <div class="position-absolute w-100 text-center text-white small p-2"
                        style="bottom: 0; background: rgba(0,0,0,0.5);">
                        Arahkan kamera ke QR Code Sertifikat
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnScan = document.getElementById('btnScanQR');
            const searchInput = document.getElementById('searchInput');
            let html5QrcodeScanner = null;

            // QR Code Scanner Logic
            btnScan.addEventListener('click', function () {
                $('#qrScanModal').modal('show');
            });

            $('#qrScanModal').on('shown.bs.modal', function () {
                if (html5QrcodeScanner === null) {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader",
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                            /* verbose= */ false
                    );
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                }
            });

            $('#qrScanModal').on('hidden.bs.modal', function () {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().catch(error => {
                        console.error("Failed to clear html5QrcodeScanner. ", error);
                    });
                    html5QrcodeScanner = null;
                }
            });

            function onScanSuccess(decodedText, decodedResult) {
                $('#qrScanModal').modal('hide');
                searchInput.value = decodedText;
                // Auto submit form after scan
                document.getElementById('searchForm').submit();
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning.
            }

            // AUTO-SHOW RESULT MODALS
            @if(isset($certificates))
                @if($certificates->count() > 0)
                    $('#validModal').modal('show');
                @else
                    $('#invalidModal').modal('show');
                @endif
            @endif
            });
    </script>

    <!-- Modal Valid (Asli) -->
    @if(isset($certificates) && $certificates->count() > 0)
        @php $firstCert = $certificates->first(); @endphp
        <div class="modal fade" id="validModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
                    <div class="modal-header bg-success text-white border-0 justify-content-center py-4">
                        <div class="text-center">
                            <div class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-check-circle fa-4x text-success"></i>
                            </div>
                            <h4 class="modal-title font-weight-bold display-5">Sertifikat Terverifikasi Asli</h4>
                            <p class="mb-0 opacity-90">Dokumen ini terdaftar resmi di database kami.</p>
                        </div>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <div class="card border-0 shadow-sm rounded-lg mb-0">
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li
                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                        <span class="text-muted small text-uppercase font-weight-bold">No. Sertifikat</span>
                                        <span
                                            class="font-weight-bold text-dark font-monospace text-right">{{ $firstCert->certificate_number }}</span>
                                    </li>
                                    <li
                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                        <span class="text-muted small text-uppercase font-weight-bold">Nama Peserta</span>
                                        <span
                                            class="font-weight-bold text-primary text-right">{{ $firstCert->display_name }}</span>
                                    </li>
                                    <li
                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                        <span class="text-muted small text-uppercase font-weight-bold">Nama Event</span>
                                        <span
                                            class="font-weight-bold text-dark text-right">{{ Str::limit($firstCert->event->title, 30) }}</span>
                                    </li>
                                    <li
                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                        <span class="text-muted small text-uppercase font-weight-bold">Tanggal Event</span>
                                        <span
                                            class="font-weight-bold text-dark text-right">{{ \Carbon\Carbon::parse($firstCert->event->start_datetime)->format('d F Y') }}</span>
                                    </li>
                                    <li
                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                        <span class="text-muted small text-uppercase font-weight-bold">Lokasi Event</span>
                                        <span
                                            class="font-weight-bold text-dark text-right">{{ $firstCert->event->location }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-success rounded-pill px-5 font-weight-bold shadow-lg"
                            data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Invalid (Palsu) -->
    <div class="modal fade" id="invalidModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
                <div class="modal-header bg-danger text-white border-0 justify-content-center py-4">
                    <div class="text-center">
                        <div class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                            style="width: 80px; height: 80px;">
                            <i class="fas fa-times-circle fa-4x text-danger"></i>
                        </div>
                        <h4 class="modal-title font-weight-bold">Sertifikat TIDAK Terdaftar</h4>
                        <p class="mb-0 opacity-90">Sertifikat Anda Palsu atau Belum Terupdate.</p>
                    </div>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted">
                        Kami tidak menemukan data dengan nomor referensi tersebut di sistem kami.
                        Pastikan Anda memasukkan nomor yang benar atau hubungi panitia penyelenggara.
                    </p>
                    <div class="alert alert-warning border-0 small mt-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Hati-hati terhadap pemalsuan dokumen.
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-outline-danger rounded-pill px-5 font-weight-bold"
                        data-dismiss="modal">Coba Lagi</button>
                </div>
            </div>
        </div>
    </div>
@endsection