@extends('adminlte::page')

@section('title', 'Edit Template Sertifikat')

@section('content_header')
<h1>Edit Template</h1>
@stop

@section('content')
<form action="{{ route('certificate-templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Konfigurasi Template</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Template</label>
                        <input type="text" name="name" class="form-control" value="{{ $template->name }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe Event</label>
                                <select name="event_type" class="form-control" required>
                                    <option value="seminar" {{ $template->event_type == 'seminar' ? 'selected' : '' }}>
                                        Seminar</option>
                                    <option value="workshop" {{ $template->event_type == 'workshop' ? 'selected' : '' }}>
                                        Workshop</option>
                                    <option value="wisuda" {{ $template->event_type == 'wisuda' ? 'selected' : '' }}>
                                        Wisuda</option>
                                    <option value="rapat" {{ $template->event_type == 'rapat' ? 'selected' : '' }}>Rapat
                                        Internal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Orientasi</label>
                                <select name="orientation" class="form-control" required>
                                    <option value="landscape" {{ $template->orientation == 'landscape' ? 'selected' : '' }}>Landscape</option>
                                    <option value="portrait" {{ $template->orientation == 'portrait' ? 'selected' : '' }}>
                                        Portrait</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Background Image</label>
                        <br>
                        <img src="{{ asset('storage/' . $template->background_file) }}" alt="Current BG" width="150"
                            class="mb-2 border">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="background_file" id="bgFile"
                                    accept="image/*">
                                <label class="custom-file-label" for="bgFile">Ganti file (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Layout Text Alignment</label>
                        <select name="layout" class="form-control">
                            <option value="center" {{ $template->layout == 'center' ? 'selected' : '' }}>Center (Tengah)
                            </option>
                            <option value="left" {{ $template->layout == 'left' ? 'selected' : '' }}>Left (Kiri)</option>
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
                        <input type="text" name="font_title" class="form-control" value="{{ $template->font_title }}">
                    </div>
                    <div class="form-group">
                        <label>Font Body</label>
                        <input type="text" name="font_body" class="form-control" value="{{ $template->font_body }}">
                    </div>
                    <div class="form-group">
                        <label>Warna Teks</label>
                        <input type="color" name="text_color" class="form-control" value="{{ $template->text_color }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning btn-block">Update Template</button>
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