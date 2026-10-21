@extends('adminlte::page')

@section('title', $event->exists ? 'Wizard Event: ' . $event->title : 'Wizard Event Baru')

@php
    $dt = function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d\TH:i') : ''; };
    $wizardData = [
        'eventId' => $event->exists ? $event->id : null,
        'event' => $event->exists ? [
            'title' => $event->title, 'category_id' => $event->category_id, 'organization_id' => $event->organization_id,
            'short_description' => $event->short_description, 'description' => $event->description, 'rules' => $event->rules,
            'organizer_unit' => $event->organizer_unit, 'contact_whatsapp' => $event->contact_whatsapp,
            'venue_name' => $event->venue_name, 'address' => $event->address, 'city' => $event->city, 'province' => $event->province,
            'location_link' => $event->location_link, 'meeting_link' => $event->meeting_link,
            'registration_start' => $dt($event->registration_start), 'registration_end' => $dt($event->registration_end),
            'start_date' => $dt($event->start_date), 'end_date' => $dt($event->end_date),
            'max_participants' => $event->max_participants,
            'certificate_enabled' => (int) $event->certificate_enabled, 'attendance_required' => (int) $event->attendance_required,
            'visibility' => $event->visibility ?: 'PUBLIC',
            'invitation_template_id' => $event->invitation_template_id,
            'session_mode' => $event->session_mode ?: 'none',
            'banner_url' => $event->banner_image ? asset('storage/' . $event->banner_image) : null,
            'status' => $event->status,
        ] : null,
        'sessions' => $sessions->map(function ($s) use ($dt) {
            return ['id' => $s->id, 'title' => $s->title, 'description' => $s->description, 'room' => $s->room,
                'start_datetime' => $dt($s->start_datetime), 'end_datetime' => $dt($s->end_datetime),
                'max_participants' => $s->max_participants, 'speaker_ids' => $s->speakers->pluck('id')->all()];
        })->values(),
        'tickets' => $tickets->map(function ($t) use ($dt) {
            return ['id' => $t->id, 'name' => $t->name, 'price' => $t->price, 'quota' => $t->quota,
                'sale_start' => $dt($t->sale_start), 'sale_end' => $dt($t->sale_end), 'description' => $t->description];
        })->values(),
        'sponsors' => $sponsors->map(function ($s) {
            return ['id' => $s->id, 'name' => $s->name, 'website' => $s->website, 'level' => $s->level,
                'logo_url' => $s->logo ? asset('storage/' . $s->logo) : null];
        })->values(),
        'committees' => $committees->map(function ($c) {
            return ['id' => $c->id, 'name' => $c->display_name, 'unit' => $c->unit, 'position' => $c->position, 'section' => $c->section];
        })->values(),
        'speakers' => $speakers,
        'users' => $users,
        'urls' => [
            'start' => route('admin.events.wizard.start'),
            'save' => route('admin.events.wizard.save', ['event' => '__ID__']),
            'banner' => route('admin.events.wizard.banner', ['event' => '__ID__']),
            'itemSave' => route('admin.events.wizard.item.save', ['event' => '__ID__', 'type' => '__TYPE__']),
            'itemDelete' => route('admin.events.wizard.item.delete', ['event' => '__ID__', 'type' => '__TYPE__', 'id' => '__ITEM__']),
            'publish' => route('admin.events.wizard.publish', ['event' => '__ID__']),
            'relation' => route('admin.events.wizard.relation', ['kind' => '__KIND__']),
            'image' => route('admin.events.wizard.image'),
            'certSettings' => route('events.certificates.settings', ['event' => '__ID__']),
            'certGen' => route('certificate-generator.show', ['event' => '__ID__']),
            'edit' => route('admin.events.wizard.edit', ['event' => '__ID__']),
            'index' => route('admin.events.index'),
        ],
    ];
@endphp

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>{{ $event->exists ? 'Lanjutkan Input Event' : 'Tambah Event Baru' }}</h1>
    <div>
        <span id="wz-status" class="badge badge-secondary p-2 mr-2"><i class="fas fa-info-circle"></i> Isi judul untuk mulai menyimpan otomatis</span>
        <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Keluar</a>
    </div>
</div>
@stop

@section('content')
<div class="alert alert-light border small mb-3">
    <i class="fas fa-magic text-primary"></i>
    Data <b>tersimpan otomatis</b> setiap kali Anda berhenti mengetik. Tidak perlu lengkap dulu &mdash;
    event disimpan sebagai <b>DRAFT</b> dan bisa dilanjutkan kapan saja dari daftar event.
</div>

<ul class="nav nav-pills mb-3" id="wz-nav">
    <li class="nav-item"><a class="nav-link" href="#" data-go="1"><span class="wz-ic"></span> 1. Info Dasar</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-go="2"><span class="wz-ic"></span> 2. Waktu &amp; Lokasi</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-go="3"><span class="wz-ic"></span> 3. Sesi &amp; Pembicara</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-go="4"><span class="wz-ic"></span> 4. Tiket &amp; Pengaturan</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-go="5"><span class="wz-ic"></span> 5. Panitia &amp; Sponsor</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-go="6"><span class="wz-ic"></span> 6. Review &amp; Publikasi</a></li>
</ul>

<div class="card card-primary card-outline">
<div class="card-body">

{{-- STEP 1 --}}
<div class="wz-step" data-step="1">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Judul Event <span class="text-danger">*</span></label>
                <input type="text" class="form-control" data-event-field="title" maxlength="255">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea class="form-control" rows="2" data-event-field="short_description" maxlength="1000"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Deskripsi Lengkap</label>
                <textarea class="form-control" rows="8" data-event-field="description" data-rich="full"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Syarat &amp; Ketentuan</label>
                <textarea class="form-control" rows="5" data-event-field="rules" data-rich="full"></textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Kategori</label>
                <select class="form-control" data-event-field="category_id">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Organisasi / Penyelenggara</label>
                <select class="form-control" data-event-field="organization_id">
                    <option value="">-- Pilih Organisasi --</option>
                    @foreach($organizations as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Unit Penyelenggara</label>
                <input type="text" class="form-control" data-event-field="organizer_unit" maxlength="191">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>No. WA Panitia</label>
                <input type="text" class="form-control" data-event-field="contact_whatsapp" maxlength="191">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Banner</label>
                <div id="wz-banner-preview" class="mb-2" style="display:none"><img src="" class="img-fluid rounded border"></div>
                <input type="file" class="form-control-file" id="wz-banner" accept="image/*">
                <small class="text-muted">JPG/PNG/GIF/WEBP, maks 2MB. Terupload otomatis.</small>
                <div class="text-danger small" id="wz-banner-err"></div>
            </div>
        </div>
    </div>
</div>

{{-- STEP 2 --}}
<div class="wz-step" data-step="2" style="display:none">
    <div class="row">
        <div class="col-md-6">
            <h5>Waktu</h5>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Event Mulai</label>
                    <input type="datetime-local" class="form-control" data-event-field="start_date">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group col-md-6">
                    <label>Event Selesai</label>
                    <input type="datetime-local" class="form-control" data-event-field="end_date">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group col-md-6">
                    <label>Registrasi Mulai</label>
                    <input type="datetime-local" class="form-control" data-event-field="registration_start">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group col-md-6">
                    <label>Registrasi Selesai</label>
                    <input type="datetime-local" class="form-control" data-event-field="registration_end">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-group">
                <label>Kuota Peserta</label>
                <input type="number" min="1" class="form-control" data-event-field="max_participants" placeholder="Kosongkan bila tanpa batas">
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-6">
            <h5>Lokasi</h5>
            <div class="form-group">
                <label>Nama Tempat</label>
                <input type="text" class="form-control" data-event-field="venue_name" maxlength="255">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea class="form-control" rows="2" data-event-field="address"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Kota</label>
                    <input type="text" class="form-control" data-event-field="city" maxlength="100">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group col-md-6">
                    <label>Provinsi</label>
                    <input type="text" class="form-control" data-event-field="province" maxlength="100">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-group">
                <label>Link Lokasi (Google Maps)</label>
                <input type="url" class="form-control" data-event-field="location_link" placeholder="https://">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Link Meeting Online (bila hybrid/online)</label>
                <input type="url" class="form-control" data-event-field="meeting_link" placeholder="https://">
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
</div>

{{-- STEP 3 --}}
<div class="wz-step" data-step="3" style="display:none">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div><h5 class="mb-0">Sesi / Rundown &amp; Pembicara</h5>
            <small class="text-muted">Tiap sesi tersimpan otomatis begitu judulnya terisi. Pembicara dipilih dari daftar atau ditambah langsung.</small></div>
        <button type="button" class="btn btn-sm btn-primary" data-add="sessions"><i class="fas fa-plus"></i> Tambah Sesi</button>
    </div>
    <div class="card card-body bg-light mb-3">
        <label class="mb-1">Peserta mengikuti sesi tertentu?</label>
        <select class="form-control" data-event-field="session_mode">
            <option value="none">Tidak, semua peserta mengikuti seluruh acara</option>
            <option value="admin">Ya, sesi ditetapkan panitia (mis. per fakultas)</option>
            <option value="choose">Ya, peserta memilih sendiri saat mendaftar</option>
        </select>
        <small class="text-muted mt-1">
            Untuk event seperti <b>wisuda 2 sesi</b>: buat tiap sesi di bawah (judul, jam mulai &amp; selesai). Isi <b>Kuota</b> sesi bila pilihan peserta dibatasi kapasitas;
            kosongkan kuota bila peserta bebas memilih. Presensi, undangan, dan pesan WhatsApp akan mengikuti jam sesi masing-masing peserta.
        </small>
        <div class="invalid-feedback"></div>
    </div>
    <div class="wz-needs-event text-muted" style="display:none"><i class="fas fa-lock"></i> Isi judul event di step 1 dulu.</div>
    <div id="wz-list-sessions"></div>
</div>
</div>

{{-- STEP 4 --}}
<div class="wz-step" data-step="4" style="display:none">
    <div class="row">
        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div><h5 class="mb-0">Tipe Tiket</h5>
                    <small class="text-muted">Opsional. Kosongkan untuk event gratis tanpa tiket.</small></div>
                <button type="button" class="btn btn-sm btn-primary" data-add="tickets"><i class="fas fa-plus"></i> Tambah Tiket</button>
            </div>
            <div class="wz-needs-event text-muted" style="display:none"><i class="fas fa-lock"></i> Isi judul event di step 1 dulu.</div>
            <div id="wz-list-tickets"></div>
        </div>
        <div class="col-md-5">
            <h5>Pengaturan</h5>
            <div class="form-group">
                <label>Visibilitas</label>
                <select class="form-control" data-event-field="visibility">
                    <option value="PUBLIC">PUBLIC &mdash; tampil di halaman publik</option>
                    <option value="PRIVATE">PRIVATE &mdash; hanya yang punya link</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Template Undangan Digital</label>
                <select class="form-control" data-event-field="invitation_template_id">
                    <option value="">-- Tampilan standar --</option>
                    @foreach($templates as $t)<option value="{{ $t->id }}">{{ $t->name }} ({{ $t->category }})</option>@endforeach
                </select>
                <small class="text-muted">Mengatur tampilan undangan yang dikirim ke peserta. <a href="{{ route('invitation-templates.index') }}" target="_blank">Kelola template</a></small>
                <div class="invalid-feedback"></div>
            </div>
            <div class="custom-control custom-switch mb-2">
                <input type="checkbox" class="custom-control-input" id="wz-cert" data-event-field="certificate_enabled">
                <label class="custom-control-label" for="wz-cert">Aktifkan Sertifikat</label>
            </div>
            <div id="wz-cert-hint" class="alert alert-light border small py-2 mb-2" style="display:none">
                Sertifikat aktif. Setelah event berjalan:
                <a href="#" id="wz-cert-settings" target="_blank">atur kategori, tanda tangan &amp; logo</a> &middot;
                <a href="#" id="wz-cert-gen" target="_blank">terbitkan sertifikat</a>
            </div>
            <div class="custom-control custom-switch mb-2">
                <input type="checkbox" class="custom-control-input" id="wz-att" data-event-field="attendance_required">
                <label class="custom-control-label" for="wz-att">Wajib Absen</label>
            </div>
        </div>
    </div>
</div>

{{-- STEP 5 --}}
<div class="wz-step" data-step="5" style="display:none">
    <div class="wz-needs-event text-muted mb-2" style="display:none"><i class="fas fa-lock"></i> Isi judul event di step 1 dulu.</div>
    <div class="row">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div><h5 class="mb-0">Panitia <span class="badge badge-light border" id="wz-committee-count"></span></h5><small class="text-muted">Tulis nama langsung (tidak perlu akun pengguna). Opsional.</small></div>
                <button type="button" class="btn btn-sm btn-primary" data-add="committees"><i class="fas fa-plus"></i> Tambah Panitia</button>
            </div>
            <input type="search" class="form-control form-control-sm mb-2" id="wz-committee-filter" placeholder="Cari nama, seksi, jabatan, atau asal...">
            <div id="wz-list-committees"></div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div><h5 class="mb-0">Sponsor</h5><small class="text-muted">Opsional. Logo terupload otomatis.</small></div>
                <button type="button" class="btn btn-sm btn-primary" data-add="sponsors"><i class="fas fa-plus"></i> Tambah Sponsor</button>
            </div>
            <div id="wz-list-sponsors"></div>
        </div>
    </div>
</div>

{{-- STEP 6 --}}
<div class="wz-step" data-step="6" style="display:none">
    <h5>Ringkasan</h5>
    <div id="wz-review"></div>
    <div class="alert alert-danger mt-3" id="wz-publish-err" style="display:none"></div>
    <div class="mt-3">
        <button type="button" class="btn btn-success" id="wz-publish"><i class="fas fa-bullhorn"></i> Publikasikan Event</button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Simpan sebagai Draft &amp; Keluar</a>
    </div>
</div>

</div>
<div class="card-footer d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary" id="wz-prev"><i class="fas fa-chevron-left"></i> Sebelumnya</button>
    <button type="button" class="btn btn-primary" id="wz-next">Selanjutnya <i class="fas fa-chevron-right"></i></button>
</div>
</div>

{{-- Template baris --}}
<template id="tpl-sessions">
    <div class="card card-outline card-info wz-row" data-type="sessions"><div class="card-body pb-2">
        <div class="d-flex"><div class="flex-fill">
            <div class="form-row">
                <div class="form-group col-md-6"><label class="small mb-0">Judul Sesi *</label><input class="form-control form-control-sm" data-f="title" maxlength="255"></div>
                <div class="form-group col-md-3"><label class="small mb-0">Mulai</label><input type="datetime-local" class="form-control form-control-sm" data-f="start_datetime"></div>
                <div class="form-group col-md-3"><label class="small mb-0">Selesai</label><input type="datetime-local" class="form-control form-control-sm" data-f="end_datetime"></div>
                <div class="form-group col-md-4"><label class="small mb-0">Ruangan</label><input class="form-control form-control-sm" data-f="room" maxlength="150"></div>
                <div class="form-group col-md-2"><label class="small mb-0">Kuota</label><input type="number" min="1" class="form-control form-control-sm" data-f="max_participants"></div>
                <div class="form-group col-md-6"><label class="small mb-0">Pembicara</label><select multiple class="form-control form-control-sm wz-speaker-select" data-f="speaker_ids"></select></div>
                <div class="form-group col-12 mb-1"><label class="small mb-0">Deskripsi</label><textarea rows="3" class="form-control form-control-sm" data-f="description" data-rich="compact"></textarea></div>
            </div>
            <div class="small wz-row-state text-muted"></div>
        </div>
        <button type="button" class="btn btn-sm btn-link text-danger wz-del" title="Hapus"><i class="fas fa-trash"></i></button></div>
    </div></div>
</template>

<template id="tpl-tickets">
    <div class="card card-outline card-info wz-row" data-type="tickets"><div class="card-body pb-2">
        <div class="d-flex"><div class="flex-fill">
            <div class="form-row">
                <div class="form-group col-md-6"><label class="small mb-0">Nama Tiket *</label><input class="form-control form-control-sm" data-f="name" maxlength="100" placeholder="mis. Umum / Mahasiswa"></div>
                <div class="form-group col-md-3"><label class="small mb-0">Harga (Rp)</label><input type="number" min="0" step="1000" class="form-control form-control-sm" data-f="price" placeholder="0 = gratis"></div>
                <div class="form-group col-md-3"><label class="small mb-0">Kuota</label><input type="number" min="1" class="form-control form-control-sm" data-f="quota"></div>
                <div class="form-group col-md-6"><label class="small mb-0">Penjualan Mulai</label><input type="datetime-local" class="form-control form-control-sm" data-f="sale_start"></div>
                <div class="form-group col-md-6"><label class="small mb-0">Penjualan Selesai</label><input type="datetime-local" class="form-control form-control-sm" data-f="sale_end"></div>
                <div class="form-group col-12 mb-1"><label class="small mb-0">Keterangan</label><input class="form-control form-control-sm" data-f="description"></div>
            </div>
            <div class="small wz-row-state text-muted"></div>
        </div>
        <button type="button" class="btn btn-sm btn-link text-danger wz-del" title="Hapus"><i class="fas fa-trash"></i></button></div>
    </div></div>
</template>

<template id="tpl-committees">
    <div class="wz-row border rounded bg-white px-2 pt-2 pb-1 mb-2" data-type="committees">
        <div class="d-flex align-items-start">
            <div class="flex-fill">
                <div class="form-row">
                    <div class="form-group col-md-4 mb-1"><input class="form-control form-control-sm" data-f="name" maxlength="200" placeholder="Nama lengkap *"></div>
                    <div class="form-group col-md-3 mb-1"><input class="form-control form-control-sm" data-f="section" maxlength="120" placeholder="Seksi (mis. Seksi Acara)"></div>
                    <div class="form-group col-md-3 mb-1"><input class="form-control form-control-sm" data-f="position" maxlength="100" placeholder="Jabatan (Ketua, Anggota, ...)"></div>
                    <div class="form-group col-md-2 mb-1"><input class="form-control form-control-sm" data-f="unit" maxlength="150" placeholder="Asal (FEB, Rektorat)"></div>
                </div>
                <div class="small wz-row-state text-muted"></div>
            </div>
            <button type="button" class="btn btn-sm btn-link text-danger wz-del" title="Hapus"><i class="fas fa-trash"></i></button>
        </div>
    </div>
</template>

<template id="tpl-sponsors">
    <div class="card card-outline card-info wz-row" data-type="sponsors"><div class="card-body pb-2">
        <div class="d-flex"><div class="flex-fill">
            <div class="form-row">
                <div class="form-group col-md-7"><label class="small mb-0">Nama Sponsor *</label><input class="form-control form-control-sm" data-f="name" maxlength="255"></div>
                <div class="form-group col-md-5"><label class="small mb-0">Level</label><select class="form-control form-control-sm" data-f="level"><option value="">-</option><option>GOLD</option><option>SILVER</option><option>BRONZE</option><option>MEDIA</option></select></div>
                <div class="form-group col-md-12"><label class="small mb-0">Website</label><input type="url" class="form-control form-control-sm" data-f="website" placeholder="https://"></div>
                <div class="form-group col-md-12 mb-1"><label class="small mb-0">Logo</label><div class="wz-logo mb-1"></div><input type="file" accept="image/*" class="form-control-file" data-f="logo"></div>
            </div>
            <div class="small wz-row-state text-muted"></div>
        </div>
        <button type="button" class="btn btn-sm btn-link text-danger wz-del" title="Hapus"><i class="fas fa-trash"></i></button></div>
    </div></div>
</template>
@stop

@section('css')
<style>
    #wz-nav .nav-link { background:#f4f6f9; margin:0 4px 4px 0; color:#495057; }
    #wz-nav .nav-link.active { background:#007bff; color:#fff; }
    #wz-nav .wz-ic { display:inline-block; width:1em; }
    .wz-ok { color:#28a745; } .wz-todo { color:#adb5bd; }
    #wz-nav .nav-link.active .wz-ok { color:#fff; }
    .rp { position: relative; }
    .rp-box { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; min-height: 31px; height: auto !important; padding: 3px 6px !important; cursor: text; background: #fff; }
    .rp-chip { background: #dcfce7; color: #14532d; border-radius: 14px; padding: 1px 4px 1px 10px; font-size: .82rem; display: inline-flex; align-items: center; max-width: 100%; }
    .rp-chip b { cursor: pointer; margin-left: 6px; width: 18px; height: 18px; line-height: 17px; text-align: center; border-radius: 50%; font-weight: 700; color: #166534; }
    .rp-chip b:hover { background: #166534; color: #fff; }
    .rp-input { border: 0; outline: 0; flex: 1 1 90px; min-width: 90px; font-size: .85rem; background: transparent; padding: 2px 2px; }
    .rp-menu { position: absolute; left: 0; right: 0; top: 100%; margin-top: 2px; z-index: 1060; background: #fff; border: 1px solid #d1d9d4; border-radius: 8px; box-shadow: 0 10px 24px rgba(16,24,20,.15); max-height: 250px; overflow: auto; }
    .rp-item { padding: 6px 10px; cursor: pointer; font-size: .86rem; display: flex; justify-content: space-between; }
    .rp-item:hover, .rp-item.hl { background: #f0fdf4; }
    .rp-item.sel { color: #166534; font-weight: 600; }
    .rp-add { padding: 8px 10px; cursor: pointer; font-size: .86rem; color: #15803d; border-top: 1px solid #e5e7eb; font-weight: 600; }
    .rp-add:hover { background: #f0fdf4; }
    .rp-empty { padding: 8px 10px; color: #9ca3af; font-size: .84rem; }
    .rp-form { padding: 10px; }
    .rp-form label { font-size: .75rem; margin: 6px 0 2px; }
    .wz-row-state.err { color:#dc3545 !important; }
    .wz-row-state.ok { color:#28a745 !important; }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
window.WZ = @json($wizardData);
</script>
<script>
(function () {
    var W = window.WZ, U = W.urls;
    var csrf = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    var TYPES = {sessions: 'title', tickets: 'name', sponsors: 'name', committees: 'name'};
    var eventId = W.eventId;
    var creating = null;                 // promise pembuatan draft yang sedang berjalan
    var pending = 0, failed = 0, lastSaved = null;
    var dirty = {};                      // field event yang belum tersimpan
    var timers = {};
    var step = 1;

    function url(tpl, map) {
        var s = tpl.replace('__ID__', eventId);
        Object.keys(map || {}).forEach(function (k) { s = s.replace(k, map[k]); });
        return s;
    }
    function ajax(opts) {
        opts.headers = {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'};
        return $.ajax(opts);
    }
    function errMsg(xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            return Object.keys(xhr.responseJSON.errors).map(function (k) { return xhr.responseJSON.errors[k][0]; }).join(' ');
        }
        if (xhr.status === 419) return 'Sesi login habis, muat ulang halaman.';
        return 'Gagal menyimpan (' + xhr.status + ').';
    }

    // ---------- indikator status ----------
    function paint() {
        var $s = $('#wz-status');
        if (pending > 0) $s.attr('class', 'badge badge-info p-2 mr-2').html('<i class="fas fa-sync fa-spin"></i> Menyimpan...');
        else if (failed > 0) $s.attr('class', 'badge badge-danger p-2 mr-2').html('<i class="fas fa-exclamation-triangle"></i> ' + failed + ' perubahan gagal disimpan');
        else if (lastSaved) $s.attr('class', 'badge badge-success p-2 mr-2').html('<i class="fas fa-check"></i> Tersimpan ' + lastSaved);
        else $s.attr('class', 'badge badge-secondary p-2 mr-2').html('<i class="fas fa-info-circle"></i> Isi judul untuk mulai menyimpan otomatis');
    }
    function begin() { pending++; paint(); }
    function done(ok, at) { pending--; if (ok && at) lastSaved = at; paint(); }

    window.addEventListener('beforeunload', function (e) {
        if (pending > 0 || Object.keys(dirty).length || $('.wz-row').filter(function () { return this._dirty; }).length) {
            e.preventDefault(); e.returnValue = '';
        }
    });

    // ---------- field event ----------
    function fieldVal($el) {
        if ($el.is(':checkbox')) return $el.is(':checked') ? 1 : 0;
        return $el.val();
    }
    function fillEventFields() {
        var ev = W.event || {};
        $('[data-event-field]').each(function () {
            var $el = $(this), k = $el.data('event-field');
            if ($el.is(':checkbox')) $el.prop('checked', !!ev[k]);
            else if (ev[k] !== undefined && ev[k] !== null) $el.val(ev[k]);
        });
        if (ev.banner_url) $('#wz-banner-preview').show().find('img').attr('src', ev.banner_url);
    }
    function queueEventField($el) {
        var k = $el.data('event-field');
        dirty[k] = fieldVal($el);
        $el.removeClass('is-invalid');
        clearTimeout(timers.event);
        timers.event = setTimeout(flushEvent, 700);
    }
    function flushEvent() {
        clearTimeout(timers.event);
        if (window.tinymce) tinymce.triggerSave();
        if (!Object.keys(dirty).length) return $.Deferred().resolve().promise();
        var batch = dirty; dirty = {};

        if (!eventId) {
            // draft baru hanya dibuat jika judul sudah ada
            var title = ($('[data-event-field="title"]').val() || '').trim();
            if (!title) { Object.assign(batch, dirty); dirty = batch; return $.Deferred().resolve().promise(); }
            if (creating) { Object.assign(dirty, batch); return creating; }
            // sertakan semua isi form saat ini agar tidak ada yang tertinggal
            $('[data-event-field]').each(function () { batch[$(this).data('event-field')] = fieldVal($(this)); });
            begin();
            creating = ajax({url: U.start, method: 'POST', data: batch}).done(function (r) {
                eventId = r.id; W.eventId = r.id;
                history.replaceState(null, '', url(U.edit, {}));
                $('.wz-needs-event').hide();
                done(true, r.saved_at);
                $('.wz-row').each(function () { if (this._dirty) scheduleRow($(this)); });
                if (Object.keys(dirty).length) flushEvent();
            }).fail(function (xhr) {
                Object.assign(dirty, batch); failed++; done(false);
                markFieldErrors(xhr);
            }).always(function () { creating = null; });
            return creating;
        }

        begin();
        return ajax({url: url(U.save), method: 'PATCH', data: batch}).done(function (r) {
            done(true, r.saved_at);
            if (failed > 0) failed = Math.max(0, failed - 1);
        }).fail(function (xhr) {
            // simpan ulang field yang gagal agar tidak hilang dari antrian
            Object.keys(batch).forEach(function (k) { if (!(k in dirty)) dirty[k] = batch[k]; });
            failed++; done(false); markFieldErrors(xhr);
        });
    }
    function markFieldErrors(xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, function (k, msgs) {
                var $el = $('[data-event-field="' + k + '"]');
                $el.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').first().text(msgs[0]);
                // field tidak valid dibuang dari antrian supaya tidak menahan field lain
                delete dirty[k];
            });
        }
    }

    $(document).on('input change', '[data-event-field]', function () { queueEventField($(this)); refreshBadges(); });

    // ---------- banner ----------
    $('#wz-banner').on('change', function () {
        var f = this.files[0]; if (!f) return;
        $('#wz-banner-err').text('');
        var upload = function () {
            var fd = new FormData(); fd.append('banner_image', f);
            begin();
            ajax({url: url(U.banner), method: 'POST', data: fd, processData: false, contentType: false}).done(function (r) {
                $('#wz-banner-preview').show().find('img').attr('src', r.url); done(true, r.saved_at);
            }).fail(function (xhr) { $('#wz-banner-err').text(errMsg(xhr)); failed++; done(false); });
        };
        if (eventId) return upload();
        if (!($('[data-event-field="title"]').val() || '').trim()) {
            $('#wz-banner-err').text('Isi judul event dulu sebelum upload banner.'); this.value = ''; return;
        }
        flushEvent().then(upload);
    });



    // ---------- editor teks kaya (TinyMCE): tabel, font, warna, gambar, tautan ----------
    function richInit($ta) {
        if (!window.tinymce || !$ta.length || $ta.data('rich-on')) return;
        $ta.data('rich-on', true);
        if (!$ta.attr('id')) $ta.attr('id', 'rt-' + Math.random().toString(36).slice(2, 9));
        var compact = $ta.data('rich') === 'compact';
        var uploadImage = function (blobInfo) {
            return new Promise(function (resolve, reject) {
                var fd = new FormData(); fd.append('file', blobInfo.blob(), blobInfo.filename());
                ajax({url: U.image, method: 'POST', data: fd, processData: false, contentType: false})
                    .done(function (r) { resolve(r.location); })
                    .fail(function (xhr) { reject({message: errMsg(xhr), remove: true}); });
            });
        };
        tinymce.init({
            target: $ta[0],
            height: compact ? 190 : ($ta.data('rich-h') || 330),
            menubar: false, branding: false, promotion: false, statusbar: !compact, license_key: 'gpl',
            language: 'id', language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@23.10.9/langs6/id.js',
            plugins: 'table lists link autolink image code fullscreen searchreplace charmap',
            toolbar: compact
                ? 'bold italic underline | bullist numlist | link table | removeformat'
                : 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image link | blockquote hr | removeformat | code fullscreen',
            toolbar_mode: 'sliding',
            font_family_formats: 'Poppins=Poppins,sans-serif; Arial=arial,helvetica,sans-serif; Georgia=georgia,serif; Times New Roman=times new roman,times,serif; Verdana=verdana,geneva,sans-serif; Courier New=courier new,courier,monospace',
            font_size_formats: '10pt 12pt 14pt 16pt 18pt 24pt 32pt',
            table_default_attributes: {border: '1'}, table_default_styles: {'border-collapse': 'collapse', width: '100%'},
            convert_urls: false, relative_urls: false, remove_script_host: false,
            images_upload_handler: uploadImage, automatic_uploads: true, paste_data_images: true,
            file_picker_types: 'image',
            file_picker_callback: function (cb) {
                var inp = document.createElement('input'); inp.type = 'file'; inp.accept = 'image/*';
                inp.onchange = function () {
                    var f = inp.files[0]; if (!f) return;
                    var fd = new FormData(); fd.append('file', f);
                    ajax({url: U.image, method: 'POST', data: fd, processData: false, contentType: false})
                        .done(function (r) { cb(r.location, {alt: f.name.replace(/\.[^.]+$/, '')}); })
                        .fail(function (xhr) { alert(errMsg(xhr)); });
                };
                inp.click();
            },
            content_style: 'body{font-family:Poppins,Arial,sans-serif;font-size:14px;line-height:1.6} img{max-width:100%;height:auto} table{border-collapse:collapse} td,th{border:1px solid #cfd6d2;padding:4px 8px}',
            setup: function (ed) {
                var last = $ta.val();
                var push = function () {
                    ed.save();
                    if ($ta.val() !== last) { last = $ta.val(); $ta.trigger('input'); }
                };
                ed.on('input change keyup undo redo ExecCommand', push);
                ed.on('init', function () { last = ed.getContent(); $ta.val(last); });
            }
        });
    }
    function richDestroy($scope) {
        if (!window.tinymce) return;
        $scope.find('textarea[data-rich]').each(function () { var ed = this.id && tinymce.get(this.id); if (ed) ed.remove(); });
    }

    // ---------- pemilih relasi (cari, hapus, tambah baru di tempat) ----------
    var REL = {
        speaker:      {label: 'pembicara', create: true, fields: [['name', 'Nama pembicara *'], ['company', 'Instansi'], ['title', 'Jabatan']]},
        category:     {label: 'kategori', create: true, fields: [['name', 'Nama kategori *']]},
        organization: {label: 'organisasi', create: true, fields: [['name', 'Nama organisasi *'], ['email', 'Email'], ['phone', 'Telepon']]},
        user:         {label: 'anggota', create: false},
        template:     {label: 'template', create: false}
    };
    var relSelects = {};   // kind => [<select>]

    function relOptions($sel) {
        return $sel.find('option').map(function () { return {id: this.value, name: $(this).text()}; }).get().filter(function (o) { return o.id !== ''; });
    }

    function enhance($sel, kind) {
        if ($sel.data('rp')) return;
        var cfg = REL[kind], multiple = $sel.prop('multiple');
        (relSelects[kind] = relSelects[kind] || []).push($sel[0]);
        var $w = $('<div class="rp"><div class="form-control form-control-sm rp-box"><span class="rp-chips"></span><input type="text" class="rp-input" autocomplete="off"></div><div class="rp-menu" style="display:none"></div></div>');
        $sel.after($w).hide().data('rp', $w);
        var $box = $w.find('.rp-box'), $input = $w.find('.rp-input'), $menu = $w.find('.rp-menu'), $chips = $w.find('.rp-chips');

        function selectedIds() { var v = $sel.val(); return multiple ? (v || []).map(String) : (v ? [String(v)] : []); }
        function paintChips() {
            $chips.empty();
            var ids = selectedIds(), opts = relOptions($sel);
            ids.forEach(function (id) {
                var o = opts.filter(function (x) { return x.id === id; })[0]; if (!o) return;
                $('<span class="rp-chip"></span>').text(o.name).append($('<b title="Hapus">&times;</b>').attr('data-id', id)).appendTo($chips);
            });
            $input.attr('placeholder', ids.length ? (multiple ? 'Tambah ' + cfg.label + '...' : '') : 'Cari / pilih ' + cfg.label + '...');
            if (!multiple) $input.toggle(!ids.length);
        }
        function commit(ids) {
            if (multiple) $sel.val(ids); else $sel.val(ids[0] || '');
            paintChips(); $sel.trigger('change');
        }
        function renderMenu() {
            var q = $input.val().trim().toLowerCase(), ids = selectedIds(), opts = relOptions($sel), html = $('<div></div>'), shown = 0, exact = false;
            opts.forEach(function (o) {
                if (o.name.toLowerCase() === q) exact = true;
                if (q && o.name.toLowerCase().indexOf(q) === -1) return;
                shown++;
                var on = ids.indexOf(o.id) > -1;
                $('<div class="rp-item"></div>').toggleClass('sel', on).attr('data-id', o.id).append($('<span></span>').text(o.name)).append(on ? '<span>&#10003;</span>' : '').appendTo(html);
            });
            if (!shown) html.append($('<div class="rp-empty"></div>').text(q ? 'Tidak ada yang cocok.' : 'Belum ada data.'));
            if (cfg.create && q && !exact) html.append($('<div class="rp-add"></div>').attr('data-name', $input.val().trim()).text('+ Tambah ' + cfg.label + ' baru: "' + $input.val().trim() + '"'));
            else if (cfg.create && !q) html.append('<div class="rp-add" data-name="">+ Tambah ' + cfg.label + ' baru</div>');
            $menu.empty().append(html.children()).show();
        }
        function showForm(name) {
            var f = $('<div class="rp-form"></div>');
            cfg.fields.forEach(function (fl, i) {
                f.append($('<label></label>').text(fl[1]));
                f.append($('<input type="text" class="form-control form-control-sm">').attr('data-k', fl[0]).val(i === 0 ? name : ''));
            });
            f.append('<div class="text-danger small mt-1 rp-err"></div><div class="mt-2"><button type="button" class="btn btn-sm btn-primary rp-save">Simpan &amp; pilih</button> <button type="button" class="btn btn-sm btn-link rp-cancel">Batal</button></div>');
            $menu.empty().append(f).show(); f.find('input').first().focus();
        }
        function close() { $menu.hide(); $input.val(''); }

        $box.on('click', function (e) { if (!$(e.target).is('b')) { $input.focus(); renderMenu(); } });
        $input.on('focus input', renderMenu);
        $input.on('keydown', function (e) {
            if (e.key === 'Backspace' && !$input.val() && multiple) { var ids = selectedIds(); ids.pop(); commit(ids); }
            if (e.key === 'Escape') close();
            if (e.key === 'Enter') { e.preventDefault(); var first = $menu.find('.rp-item').first(); if (first.length) first.trigger('mousedown'); else $menu.find('.rp-add').first().trigger('mousedown'); }
        });
        $chips.on('click', 'b', function () { var id = String($(this).data('id')); commit(selectedIds().filter(function (x) { return x !== id; })); });
        $menu.on('mousedown', '.rp-item', function (e) {
            e.preventDefault(); var id = String($(this).data('id')), ids = selectedIds();
            if (multiple) { commit(ids.indexOf(id) > -1 ? ids.filter(function (x) { return x !== id; }) : ids.concat(id)); renderMenu(); $input.focus(); }
            else { commit([id]); close(); }
        });
        $menu.on('mousedown', '.rp-add', function (e) { e.preventDefault(); showForm($(this).attr('data-name') || ''); });
        $menu.on('click', '.rp-cancel', function () { renderMenu(); });
        $menu.on('mousedown', function (e) { if ($(e.target).is('input')) return; });
        $menu.on('click', '.rp-save', function () {
            var data = {}; $menu.find('.rp-form input').each(function () { data[$(this).data('k')] = $(this).val(); });
            if (!(data.name || '').trim()) { $menu.find('.rp-err').text('Nama wajib diisi.'); return; }
            var $btn = $(this).prop('disabled', true); begin();
            ajax({url: U.relation.replace('__KIND__', kind), method: 'POST', data: data}).done(function (r) {
                done(true, new Date().toLocaleTimeString('id-ID'));
                (relSelects[kind] || []).forEach(function (el) {
                    if (!$(el).find('option[value="' + r.id + '"]').length) $(el).append($('<option></option>').val(r.id).text(r.name));
                });
                if (kind === 'speaker') { W.speakers.push({id: r.id, name: r.name}); }
                var ids = selectedIds(); commit(multiple ? ids.concat(String(r.id)) : [String(r.id)]); close();
            }).fail(function (xhr) { failed++; done(false); $btn.prop('disabled', false); $menu.find('.rp-err').text(errMsg(xhr)); });
        });
        // Klik di dalam widget ditandai lebih dulu: isi menu sering diganti saat diklik, sehingga e.target
        // sudah terlepas dari halaman ketika event sampai ke document dan tidak bisa dicek lewat closest().
        var inside = false;
        $w.on('mousedown', function () { inside = true; });
        $(document).on('mousedown', function () { if (inside) { inside = false; return; } $menu.hide(); $input.val(''); });
        $sel.on('rp-refresh', paintChips);
        paintChips();
    }

    // ---------- baris relasi ----------
    function optionsFor(list, sel, text) {
        return list.map(function (o) { return '<option value="' + o.id + '">' + $('<div>').text(o[text]).html() + '</option>'; }).join('');
    }
    function addRow(type, data) {
        var $row = $($('#tpl-' + type).html());
        if (type === 'sessions') $row.find('.wz-speaker-select').html(optionsFor(W.speakers, null, 'name'));
        data = data || {};
        $row.find('[data-f]').each(function () {
            var $el = $(this), k = $el.data('f');
            if ($el.is(':file')) return;
            if (data[k] !== undefined && data[k] !== null) $el.val(data[k]);
        });
        $('#wz-list-' + type).append($row);   // harus ada di DOM sebelum widget dipasang
        if (type === 'sessions') { enhance($row.find('.wz-speaker-select'), 'speaker'); richInit($row.find('textarea[data-rich]')); }
        if (data.id) $row[0]._id = data.id;
        if (type === 'sponsors' && data.logo_url) $row.find('.wz-logo').html('<img src="' + data.logo_url + '" style="max-height:48px">');
        refreshBadges();
        return $row;
    }
    function rowState($row, cls, text) { $row.find('.wz-row-state').first().attr('class', 'small wz-row-state ' + cls).text(text); }

    function scheduleRow($row) {
        var row = $row[0]; row._dirty = true;
        clearTimeout(row._t);
        row._t = setTimeout(function () { saveRow($row); }, 800);
    }
    function saveRow($row) {
        var row = $row[0], type = $row.data('type'), key = TYPES[type];
        var keyVal = $row.find('[data-f="' + key + '"]').val();
        if (!keyVal || !String(keyVal).trim()) { rowState($row, '', 'Belum disimpan: isi ' + 'nama' + ' dulu.'); row._dirty = false; return; }
        if (!eventId) {
            rowState($row, '', 'Menunggu event dibuat (isi judul di step 1).'); row._dirty = true; return;
        }
        if (row._saving) { row._again = true; return; }      // simpan ulang setelah request berjalan selesai
        row._saving = true; row._dirty = false;

        var fd = new FormData();
        if (row._id) fd.append('id', row._id);
        $row.find('[data-f]').each(function () {
            var $el = $(this), k = $el.data('f');
            if ($el.is(':file')) { if (this.files[0]) fd.append(k, this.files[0]); return; }
            if ($el.is('select[multiple]')) { ($el.val() || []).forEach(function (v) { fd.append(k + '[]', v); }); if (!($el.val() || []).length) fd.append(k, ''); return; }
            fd.append(k, $el.val());
        });
        begin(); rowState($row, '', 'Menyimpan...');
        ajax({url: url(U.itemSave, {'__TYPE__': type}), method: 'POST', data: fd, processData: false, contentType: false}).done(function (r) {
            row._id = r.id; done(true, r.saved_at);
            rowState($row, 'ok', 'Tersimpan ' + r.saved_at);
            if (r.logo_url) { $row.find('.wz-logo').html('<img src="' + r.logo_url + '" style="max-height:48px">'); $row.find('[data-f="logo"]').val(''); }
            if (failed > 0) failed = Math.max(0, failed - 1);
        }).fail(function (xhr) {
            failed++; done(false); rowState($row, 'err', errMsg(xhr)); row._dirty = true;
        }).always(function () {
            row._saving = false; refreshBadges();
            if (row._again) { row._again = false; saveRow($row); }
        });
    }
    // field kosong di sebuah baris yang belum punya id jangan disimpan; yang sudah tersimpan cukup diupdate
    $(document).on('input change', '.wz-row [data-f]', function () { scheduleRow($(this).closest('.wz-row')); });

    $(document).on('click', '.wz-del', function () {
        var $row = $(this).closest('.wz-row'), row = $row[0], type = $row.data('type');
        if (row._id) {
            if (!confirm('Hapus data ini?')) return;
            clearTimeout(row._t);
            begin();
            ajax({url: url(U.itemDelete, {'__TYPE__': type, '__ITEM__': row._id}), method: 'DELETE'}).done(function (r) {
                done(true, r.saved_at); richDestroy($row); $row.remove(); refreshBadges();
            }).fail(function (xhr) { failed++; done(false); rowState($row, 'err', errMsg(xhr)); });
        } else { clearTimeout(row._t); richDestroy($row); $row.remove(); refreshBadges(); }
    });

    $('[data-add]').on('click', function () {
        if (!eventId) { go(1); $('[data-event-field="title"]').addClass('is-invalid').focus().closest('.form-group').find('.invalid-feedback').first().text('Isi judul event dulu.'); return; }
        var $r = addRow($(this).data('add')); $r.find('[data-f]').first().focus();
    });

    // ---------- navigasi step ----------
    function stepDone(n) {
        var v = function (k) { return ($('[data-event-field="' + k + '"]').val() || '').trim(); };
        var cnt = function (t) { return $('#wz-list-' + t + ' .wz-row').filter(function () { return this._id; }).length; };
        switch (n) {
            case 1: return !!(v('title') && v('category_id'));
            case 2: return !!(v('start_date') && v('end_date') && (v('venue_name') || v('address')));
            case 3: return cnt('sessions') > 0;
            case 4: return cnt('tickets') > 0 || $('#wz-cert').is(':checked') || $('#wz-att').is(':checked');
            case 5: return cnt('committees') + cnt('sponsors') > 0;
        }
        return false;
    }
    function refreshBadges() {
        $('#wz-nav a').each(function () {
            var n = +$(this).data('go'), $ic = $(this).find('.wz-ic');
            if (n === 6) { $ic.html(''); return; }
            $ic.html(stepDone(n) ? '<i class="fas fa-check-circle wz-ok"></i>' : '<i class="far fa-circle wz-todo"></i>');
        });
    }
    function go(n) {
        n = Math.max(1, Math.min(6, n));
        if (window.tinymce) tinymce.triggerSave();
        // pastikan perubahan step sebelumnya sudah terkirim sebelum pindah
        flushEvent();
        $('.wz-row').each(function () { if (this._dirty) { clearTimeout(this._t); saveRow($(this)); } });
        step = n;
        $('.wz-step').hide().filter('[data-step="' + n + '"]').show();
        $('#wz-nav a').removeClass('active').filter('[data-go="' + n + '"]').addClass('active');
        $('#wz-prev').prop('disabled', n === 1);
        $('#wz-next').toggle(n < 6);
        $('.wz-needs-event').toggle(!eventId);
        if (n === 6) renderReview();
        refreshBadges();
        window.scrollTo({top: 0, behavior: 'smooth'});
        try { sessionStorage.setItem('wz-step-' + (eventId || 'new'), n); } catch (e) {}
    }
    $('#wz-nav a').on('click', function (e) { e.preventDefault(); go(+$(this).data('go')); });
    $('#wz-prev').on('click', function () { go(step - 1); });
    $('#wz-next').on('click', function () {
        if (step === 1 && !eventId && !($('[data-event-field="title"]').val() || '').trim()) {
            $('[data-event-field="title"]').addClass('is-invalid').focus().closest('.form-group').find('.invalid-feedback').first().text('Judul wajib diisi untuk memulai.');
            return;
        }
        go(step + 1);
    });

    // ---------- review & publish ----------
    function esc(s) { return $('<div>').text(s == null ? '' : s).html(); }
    function renderReview() {
        var v = function (k) { return ($('[data-event-field="' + k + '"]').val() || '').trim(); };
        var sel = function (k) { return $('[data-event-field="' + k + '"] option:selected').text(); };
        var rows = [
            ['Judul', v('title'), 1], ['Kategori', v('category_id') ? sel('category_id') : '', 1],
            ['Mulai', v('start_date'), 2], ['Selesai', v('end_date'), 2], ['Lokasi', v('venue_name') || v('address'), 2],
        ];
        var html = '<table class="table table-sm">';
        rows.forEach(function (r) {
            html += '<tr><th style="width:180px">' + r[0] + '</th><td>' + (r[1] ? esc(r[1]) : '<span class="text-danger">belum diisi</span> <a href="#" data-go-link="' + r[2] + '">isi sekarang</a>') + '</td></tr>';
        });
        var c = function (t) { return $('#wz-list-' + t + ' .wz-row').filter(function () { return this._id; }).length; };
        html += '<tr><th>Sesi</th><td>' + c('sessions') + '</td></tr><tr><th>Tipe Tiket</th><td>' + c('tickets') + '</td></tr>' +
                '<tr><th>Panitia</th><td>' + c('committees') + '</td></tr><tr><th>Sponsor</th><td>' + c('sponsors') + '</td></tr></table>';
        $('#wz-review').html(html);
    }
    $(document).on('click', '[data-go-link]', function (e) { e.preventDefault(); go(+$(this).data('go-link')); });

    $('#wz-publish').on('click', function () {
        var $b = $(this).prop('disabled', true); $('#wz-publish-err').hide();
        $.when(flushEvent()).always(function () {
            ajax({url: url(U.publish), method: 'POST'}).done(function (r) { window.location = r.redirect; })
            .fail(function (xhr) {
                $('#wz-publish-err').text((xhr.responseJSON && xhr.responseJSON.message) || errMsg(xhr)).show(); $b.prop('disabled', false);
            });
        });
    });

    // ---------- petunjuk sertifikat ----------
    function certHint() {
        var on = $('#wz-cert').is(':checked') && !!eventId;
        $('#wz-cert-hint').toggle(on);
        if (on) { $('#wz-cert-settings').attr('href', url(U.certSettings)); $('#wz-cert-gen').attr('href', url(U.certGen)); }
    }
    $('#wz-cert').on('change', certHint);

    // ---------- filter & hitung panitia ----------
    function committeeFilter() {
        var q = ($('#wz-committee-filter').val() || '').toLowerCase().trim(), n = 0, shown = 0;
        $('#wz-list-committees .wz-row').each(function () {
            n++;
            var txt = $(this).find('[data-f]').map(function () { return this.value; }).get().join(' ').toLowerCase();
            var ok = !q || txt.indexOf(q) > -1; $(this).toggle(ok); if (ok) shown++;
        });
        $('#wz-committee-count').text(q ? shown + ' dari ' + n : n + ' orang');
    }
    $('#wz-committee-filter').on('input', committeeFilter);
    $(document).on('input change click', '#wz-list-committees, #wz-list-committees [data-f], [data-add="committees"], .wz-del', function () { setTimeout(committeeFilter, 50); });

    // ---------- init ----------
    fillEventFields();
    richInit($('[data-event-field="description"]')); richInit($('[data-event-field="rules"]'));
    enhance($('[data-event-field="category_id"]'), 'category');
    enhance($('[data-event-field="organization_id"]'), 'organization');
    enhance($('[data-event-field="invitation_template_id"]'), 'template');
    ['sessions', 'tickets', 'sponsors', 'committees'].forEach(function (t) {
        (W[t] || []).forEach(function (d) { addRow(t, d); });
    });
    committeeFilter(); certHint();
    var start = 1;
    try { start = +sessionStorage.getItem('wz-step-' + (eventId || 'new')) || 1; } catch (e) {}
    go(eventId ? start : 1);
    paint();
    if (eventId) { lastSaved = null; }
})();
</script>
@stop
