@extends('adminlte::page')

@section('title', 'Tambah Template Sertifikat')

@section('content_header')
<h1>Tambah Template Baru</h1>
@stop

@section('content')
<form action="{{ route('certificate-templates.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Konfigurasi Template</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Template</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe Event</label>
                                <select name="event_type" class="form-control" required>
                                    <option value="seminar">Seminar</option>
                                    <option value="workshop">Workshop</option>
                                    <option value="wisuda">Wisuda</option>
                                    <option value="rapat">Rapat Internal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Orientasi</label>
                                <select name="orientation" class="form-control" required>
                                    <option value="landscape">Landscape</option>
                                    <option value="portrait">Portrait</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Background Image (JPG/PNG)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="background_file" id="bgFile" required
                                    accept="image/*">
                                <label class="custom-file-label" for="bgFile">Pilih file</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Layout Text Alignment</label>
                        <select name="layout" class="form-control">
                            <option value="center">Center (Tengah)</option>
                            <option value="left">Left (Kiri)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Style Font</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Font Judul</label>
                        <input type="text" name="font_title" class="form-control" placeholder="Arial, sans-serif">
                    </div>
                    <div class="form-group">
                        <label>Font Body</label>
                        <input type="text" name="font_body" class="form-control" placeholder="Times New Roman, serif">
                    </div>
                    <div class="form-group">
                        <label>Warna Teks</label>
                        <input type="color" name="text_color" class="form-control" value="#000000">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block">Simpan Template</button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    $('.custom-file-input').on('change', function () {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@stop