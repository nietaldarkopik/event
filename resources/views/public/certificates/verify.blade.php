@extends('layouts.public')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 overflow-hidden">
                    @if($status == 'VALID')
                        <div class="bg-success text-white text-center py-5">
                            <i class="fas fa-check-circle fa-5x mb-3"></i>
                            <h2>SENTIFIKAT VALID</h2>
                            <p class="mb-0">Dokumen Terverifikasi Secara Resmi</p>
                        </div>
                    @elseif($status == 'REVOKED')
                        <div class="bg-danger text-white text-center py-5">
                            <i class="fas fa-ban fa-5x mb-3"></i>
                            <h2>SERTIFIKAT DICABUT</h2>
                            <p class="mb-0">Status: Tidak Berlaku</p>
                        </div>
                    @else
                        <div class="bg-secondary text-white text-center py-5">
                            <i class="fas fa-times-circle fa-5x mb-3"></i>
                            <h2>DATA TIDAK DITEMUKAN</h2>
                            <p class="mb-0">Keaslian Dokumen Tidak Dapat Dijamin</p>
                        </div>
                    @endif

                    <div class="card-body p-4 p-md-5">
                        <div
                            class="alert {{ $status == 'VALID' ? 'alert-success' : ($status == 'REVOKED' ? 'alert-danger' : 'alert-warning') }} mb-4">
                            {{ $message }}
                        </div>

                        @if(isset($certificate))
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h6 class="text-muted text-uppercase small">Diberikan Kepada</h6>
                                    <p class="h5 font-weight-bold">{{ $certificate->display_name }}</p>

                                    <h6 class="text-muted text-uppercase small mt-4">Nama Acara</h6>
                                    <p class="h5">{{ $certificate->event->title }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted text-uppercase small">Nomor Sertifikat</h6>
                                    <p class="h5 font-italic">{{ $certificate->certificate_number }}</p>

                                    <h6 class="text-muted text-uppercase small mt-4">Tanggal Terbit</h6>
                                    <p class="h5">{{ \Carbon\Carbon::parse($certificate->issued_at)->isoFormat('D MMMM Y') }}
                                    </p>
                                </div>
                            </div>

                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <i class="fas fa-info-circle text-info fa-2x"></i>
                                        </div>
                                        <div class="col">
                                            <p class="mb-0 small text-muted">
                                                Data ini diambil langsung dari database sistem e-sertifikat Universitas Winaya
                                                Mukti.
                                                Pemalsuan dokumen dapat dikenakan sanksi sesuai hukum yang berlaku.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary shadow-sm mr-2">
                                <i class="fas fa-home mr-1"></i> Beranda
                            </a>
                            @if(isset($certificate) && $status == 'VALID')
                                <a href="{{ route('certificates.public_download', $certificate->unique_code) }}"
                                    class="btn btn-primary shadow">
                                    <i class="fas fa-download mr-1"></i> Download Ulang
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-white text-center py-3">
                        <small class="text-muted">Digital Certificate Verification System &copy; {{ date('Y') }} Universitas
                            Winaya Mukti</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .card {
            border-radius: 15px;
        }

        .bg-success {
            background: linear-gradient(135deg, #28a745, #218838) !important;
        }

        .bg-danger {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
        }
    </style>
@endsection