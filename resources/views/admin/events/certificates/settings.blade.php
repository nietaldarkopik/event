@extends('adminlte::page')

@section('title', 'Pengaturan Sertifikat - ' . $event->title)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-certificate text-primary"></i> Pengaturan Sertifikat: {{ $event->title }}</h1>
    <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
        Kembali</a>
</div>
@stop

<!-- Global Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-images mr-2"></i> Galeri Media</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="gallery-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat media...</p>
                </div>
                <div id="gallery-content" class="row">
                    <!-- Dynamic content -->
                </div>
                <div id="gallery-empty" class="text-center py-5" style="display: none;">
                    <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                    <p class="text-muted">Tidak ada media untuk tipe ini di galeri.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    let currentGalleryTarget = ''; // Which hidden input to fill
    let currentGalleryType = ''; // Which type to filter

    function openGallery(type, target) {
        currentGalleryType = type;
        currentGalleryTarget = target;

        const modal = $('#galleryModal');
        const content = $('#gallery-content');
        const loading = $('#gallery-loading');
        const empty = $('#gallery-empty');

        content.empty().hide();
        empty.hide();
        loading.show();
        modal.appendTo('body').modal('show');

        $.get("{{ route('certificate-media.api') }}", { type: type }, function(data) {
            loading.hide();
            if (data.length === 0) {
                empty.show();
            } else {
                data.forEach(item => {
                    const col = $(`
                        <div class="col-md-3 col-sm-4 mb-3">
                            <div class="card h-100 shadow-sm border-0 gallery-select-item" onclick="selectFromGallery('${item.url}', '${item.url.replace(window.location.origin + '/storage/', '')}')">
                                <div class="bg-light d-flex align-items-center justify-content-center p-2" style="height: 120px; cursor: pointer;">
                                    <img src="${item.url}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                </div>
                                <div class="card-footer p-2 text-center bg-white border-0">
                                    <small class="text-truncate d-block">${item.name}</small>
                                </div>
                            </div>
                        </div>
                    `);
                    content.append(col);
                });
                content.show();
            }
        });
    }

    function selectFromGallery(fullUrl, relativePath) {
        // Set the hidden input
        if (currentGalleryTarget === 'signature') {
            $('#signature_gallery_path').val(relativePath);
            $('#selected-signature-preview').show().find('img').attr('src', fullUrl);
            $('#sig-file-input').val(''); // Clear file input
        } else if (currentGalleryTarget === 'stamp') {
            $('#stamp_gallery_path').val(relativePath);
            $('#selected-stamp-preview').show().find('img').attr('src', fullUrl);
            $('#stamp-file-input').val('');
        } else if (currentGalleryTarget === 'logo') {
            $('#logo_gallery_path').val(relativePath);
            $('#selected-logo-preview').show().find('img').attr('src', fullUrl);
            $('#logo-file-input').val('');
        }

        $('#galleryModal').modal('hide');
    }

    // Reset previews when opening upload modals
    $('#addSignatureModal').on('show.bs.modal', function() {
        $('#signature_gallery_path').val('');
        $('#stamp_gallery_path').val('');
        $('#selected-signature-preview, #selected-stamp-preview').hide();
    });

    $('#addLogoModal').on('show.bs.modal', function() {
        $('#logo_gallery_path').val('');
        $('#selected-logo-preview').hide();
    });
</script>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="certificateTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="categories-tab" data-toggle="pill" href="#categories"
                            role="tab"><i class="fas fa-tags"></i> Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="signatures-tab" data-toggle="pill" href="#signatures" role="tab"><i
                                class="fas fa-signature"></i> Tanda Tangan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="logos-tab" data-toggle="pill" href="#logos" role="tab"><i
                                class="fas fa-images"></i> Logo & Sponsor</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- TAB 1: CATEGORIES -->
                    <div class="tab-pane fade show active" id="categories" role="tabpanel">
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Daftar Kategori Sertifikat</h5>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCategoryModal">
                                <i class="fas fa-plus"></i> Tambah Kategori
                            </button>
                        </div>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Kategori</th>
                                    <th>Template</th>
                                    <th>Prefix No.</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($event->certificateCategories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->template->name ?? '-' }}</td>
                                        <td>{{ $category->certificate_prefix ?: '-' }}</td>
                                        <td>
                                            <form action="{{ route('events.certificates.destroyCategory', $category->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada kategori.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- TAB 2: SIGNATURES -->
                    <div class="tab-pane fade" id="signatures" role="tabpanel">
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Tanda Tangan Digital</h5>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSignatureModal">
                                <i class="fas fa-plus"></i> Tambah Tanda Tangan
                            </button>
                        </div>
                        <div class="row">
                            @forelse($event->certificateSignatures as $sig)
                                <div class="col-md-4">
                                    <div class="card card-outline card-info">
                                        <div class="card-body text-center">
                                            <img src="{{ asset('storage/' . $sig->signature_file) }}" alt="Sign"
                                                style="max-height: 100px; margin-bottom: 15px;">
                                            @if($sig->stamp_image_path)
                                                <div class="mt-2">
                                                    <small class="badge badge-secondary">Stempel Tersedia</small>
                                                </div>
                                            @endif
                                            <h6 class="mt-3"><strong>{{ $sig->signer_name }}</strong></h6>
                                            <p class="text-muted small">{{ $sig->signer_position }}</p>
                                            <form action="{{ route('events.certificates.destroySignature', $sig->id) }}"
                                                method="POST" onsubmit="return confirm('Hapus tanda tangan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-block btn-sm mt-3"><i
                                                        class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-5">
                                    Belum ada tanda tangan.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- TAB 3: LOGOS -->
                    <div class="tab-pane fade" id="logos" role="tabpanel">
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Logo Event & Sponsor</h5>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addLogoModal">
                                <i class="fas fa-plus"></i> Tambah Logo
                            </button>
                        </div>
                        <div class="row">
                            @forelse($event->certificateLogos as $logo)
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <img src="{{ asset('storage/' . $logo->image_path) }}" alt="Logo"
                                                class="img-fluid" style="height: 80px; object-fit: contain;">
                                            <div class="mt-2">
                                                <span class="badge badge-info">{{ ucfirst($logo->logo_type) }}</span>
                                            </div>
                                            <form action="{{ route('events.certificates.destroyLogo', $logo->id) }}"
                                                method="POST" class="mt-2" onsubmit="return confirm('Hapus logo ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-link text-danger btn-sm p-0"><i
                                                        class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-5">
                                    Belum ada logo.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->
<!-- Modal Add Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('events.certificates.storeCategory', $event->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Sertifikat</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Peserta, Narasumber"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Template Canvas</label>
                        <select name="template_id" class="form-control">
                            <option value="">-- Canvas Kosong (Tanpa Template) --</option>
                            @foreach($templates as $tpl)
                                <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ ucfirst($tpl->orientation) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Prefix Nomor (Opsional)</label>
                        <input type="text" name="prefix" class="form-control" placeholder="Contoh: PSTR">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Signature -->
<div class="modal fade" id="addSignatureModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('events.certificates.storeSignature', $event->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tanda Tangan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Penandatangan</label>
                        <input type="text" name="signer_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="signer_position" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>File Tanda Tangan (PNG Transparan Disarankan)</label>
                        <div class="input-group">
                            <input type="file" name="signature_file" class="form-control-file" accept="image/*"
                                id="sig-file-input">
                            <div class="input-group-append mt-1">
                                <button type="button" class="btn btn-info btn-sm"
                                    onclick="openGallery('signature', 'signature')">
                                    <i class="fas fa-search mr-1"></i> Pilih dari Galeri
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="signature_gallery_path" id="signature_gallery_path">
                        <div id="selected-signature-preview" class="mt-2" style="display:none">
                            <small class="text-success"><i class="fas fa-check-circle"></i> Terpilih dari galeri</small>
                            <img src="" style="height:50px; display:block" class="img-thumbnail">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>File Stempel (Opsional)</label>
                        <div class="input-group">
                            <input type="file" name="stamp_image" class="form-control-file" accept="image/*"
                                id="stamp-file-input">
                            <div class="input-group-append mt-1">
                                <button type="button" class="btn btn-info btn-sm"
                                    onclick="openGallery('stamp', 'stamp')">
                                    <i class="fas fa-search mr-1"></i> Pilih dari Galeri
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="stamp_gallery_path" id="stamp_gallery_path">
                        <div id="selected-stamp-preview" class="mt-2" style="display:none">
                            <small class="text-success"><i class="fas fa-check-circle"></i> Terpilih dari galeri</small>
                            <img src="" style="height:50px; display:block" class="img-thumbnail">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Logo -->
<div class="modal fade" id="addLogoModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('events.certificates.storeLogo', $event->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Logo</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipe Logo</label>
                        <select name="logo_type" class="form-control" required>
                            <option value="event">Logo Event Utama</option>
                            <option value="organizer">Logo Penyelenggara</option>
                            <option value="sponsor">Logo Sponsor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>File Gambar</label>
                        <div class="input-group">
                            <input type="file" name="logo" class="form-control-file" accept="image/*"
                                id="logo-file-input">
                            <div class="input-group-append mt-1">
                                <button type="button" class="btn btn-info btn-sm" onclick="openGallery('logo', 'logo')">
                                    <i class="fas fa-search mr-1"></i> Pilih dari Galeri
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="logo_gallery_path" id="logo_gallery_path">
                        <div id="selected-logo-preview" class="mt-2" style="display:none">
                            <small class="text-success"><i class="fas fa-check-circle"></i> Terpilih dari galeri</small>
                            <img src="" style="height:50px; display:block" class="img-thumbnail">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
