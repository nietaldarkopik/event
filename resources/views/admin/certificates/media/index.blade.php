@extends('adminlte::page')

@section('title', 'Galeri Media Sertifikat')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Galeri Media Sertifikat</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
        <i class="fas fa-upload mr-1"></i> Unggah Media
    </button>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Koleksi Media</h3>
        <div class="card-tools">
            <form action="{{ route('admin.certificate-media.index') }}" method="GET" class="input-group input-group-sm"
                style="width: 250px;">
                <select name="type" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="logo" {{ request('type') == 'logo' ? 'selected' : '' }}>Logo</option>
                    <option value="signature" {{ request('type') == 'signature' ? 'selected' : '' }}>Tanda Tangan</option>
                    <option value="stamp" {{ request('type') == 'stamp' ? 'selected' : '' }}>Cap / Stempel</option>
                    <option value="background" {{ request('type') == 'background' ? 'selected' : '' }}>Background</option>
                </select>
            </form>
        </div>
    </div>
    <div class="card-body">
        @if($media->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-images fa-4x text-light mb-3"></i>
                <p class="text-muted">Belum ada media yang diunggah.</p>
            </div>
        @else
            <div class="row">
                @foreach($media as $m)
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 gallery-item">
                            <div class="position-relative overflow-hidden bg-light d-flex align-items-center justify-content-center"
                                style="height: 180px;">
                                <img src="{{ asset('storage/' . $m->file_path) }}" class="img-fluid"
                                    style="max-height: 100%; object-fit: contain;">
                                <div class="gallery-overlay">
                                    <form action="{{ route('admin.certificate-media.destroy', $m->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus media ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle shadow">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-2 text-center">
                                <p class="mb-0 font-weight-bold text-truncate small">{{ $m->name }}</p>
                                <span
                                    class="badge {{ $m->type == 'logo' ? 'badge-info' : ($m->type == 'signature' ? 'badge-primary' : ($m->type == 'stamp' ? 'badge-warning' : 'badge-secondary')) }} extra-small">
                                    {{ ucfirst($m->type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $media->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.certificate-media.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="uploadModalLabel">Unggah Media Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Media</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Logo UNWIM" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Media</label>
                        <select name="type" class="form-control" required>
                            <option value="logo">Logo</option>
                            <option value="signature">Tanda Tangan</option>
                            <option value="stamp">Cap / Stempel</option>
                            <option value="background">Background</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>File Gambar</label>
                        <input type="file" name="file" class="form-control-file" accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG. Max: 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary shadow-sm">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
    .gallery-item {
        transition: transform 0.2s;
    }

    .gallery-item:hover {
        transform: translateY(-5px);
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        display: none;
        align-items: center;
        justify-content: center;
    }

    .gallery-item:hover .gallery-overlay {
        display: flex;
    }

    .extra-small {
        font-size: 10px;
        padding: 2px 5px;
    }
</style>
@stop