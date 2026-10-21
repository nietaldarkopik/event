@extends('adminlte::page')

@section('title', 'Tambah Email Setting')

@section('content_header')
    <h1>Tambah Email Setting</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Email Setting</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.email-settings.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nama Setting *</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mailer">Mailer *</label>
                                    <select name="mailer" id="mailer" class="form-control" required>
                                        <option value="smtp" {{ old('mailer') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ old('mailer') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        <option value="mailgun" {{ old('mailer') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="ses" {{ old('mailer') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    </select>
                                    @error('mailer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="host">SMTP Host *</label>
                                    <input type="text" name="host" id="host" class="form-control" value="{{ old('host') }}" required>
                                    @error('host')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="port">Port *</label>
                                    <input type="number" name="port" id="port" class="form-control" value="{{ old('port', 587) }}" required>
                                    @error('port')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username *</label>
                                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required>
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password *</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="encryption">Encryption</label>
                                    <select name="encryption" id="encryption" class="form-control">
                                        <option value="" {{ old('encryption') == '' ? 'selected' : '' }}>None</option>
                                        <option value="tls" {{ old('encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                    @error('encryption')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_address">From Address *</label>
                                    <input type="email" name="from_address" id="from_address" class="form-control" value="{{ old('from_address') }}" required>
                                    @error('from_address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_name">From Name *</label>
                                    <input type="text" name="from_name" id="from_name" class="form-control" value="{{ old('from_name') }}" required>
                                    @error('from_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active') ? 'checked' : '' }}>
                                        <label for="is_active" class="form-check-label">Aktifkan email setting ini</label>
                                    </div>
                                    @error('is_active')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('admin.email-settings.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection