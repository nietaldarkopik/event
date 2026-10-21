@extends('layouts.public')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0"><i class="fas fa-certificate mr-2"></i> Ambil Sertifikat</h3>
                    </div>
                    <div class="card-body p-5">
                        <p class="text-muted text-center mb-4">
                            Masukkan kode unik yang Anda terima melalui WhatsApp atau Email untuk mengunduh sertifikat
                            digital Anda.
                        </p>

                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('certificates.retrieve') }}" method="POST">
                            @csrf
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold">Kode Pengambilan</label>
                                <input type="text" name="unique_code" class="form-control form-control-lg text-center"
                                    placeholder="Contoh: ABC123XYZ456" required autocomplete="off">
                                <small class="form-text text-muted">
                                    Kode ini bersifat rahasia dan hanya dapat digunakan oleh pemiliknya.
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block shadow">
                                <i class="fas fa-search mr-2"></i> Cari Sertifikat
                            </button>
                        </form>

                        <div class="mt-5 text-center">
                            <hr>
                            <p class="small text-muted mb-0">Atau gunakan fitur pencarian publik</p>
                            <a href="{{ route('certificates.check') }}" class="btn btn-link">Cek Status Sertifikat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection