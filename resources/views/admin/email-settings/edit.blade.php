@extends('adminlte::page')

@section('title', 'Edit Email Setting')

@section('content_header')
    <h1>Edit Email Setting</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Email Setting</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.email-settings.update', $setting->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nama Setting *</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $setting->name) }}" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mailer">Mailer *</label>
                                    <select name="mailer" id="mailer" class="form-control" required>
                                        <option value="smtp" {{ old('mailer', $setting->mailer) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ old('mailer', $setting->mailer) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        <option value="mailgun" {{ old('mailer', $setting->mailer) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="ses" {{ old('mailer', $setting->mailer) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
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
                                    <input type="text" name="host" id="host" class="form-control" value="{{ old('host', $setting->host) }}" required>
                                    @error('host')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="port">Port *</label>
                                    <input type="number" name="port" id="port" class="form-control" value="{{ old('port', $setting->port) }}" required>
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
                                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $setting->username) }}" required>
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="encryption">Encryption</label>
                                    <select name="encryption" id="encryption" class="form-control">
                                        <option value="" {{ old('encryption', $setting->encryption) == '' ? 'selected' : '' }}>None</option>
                                        <option value="tls" {{ old('encryption', $setting->encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('encryption', $setting->encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                    @error('encryption')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_address">From Address *</label>
                                    <input type="email" name="from_address" id="from_address" class="form-control" value="{{ old('from_address', $setting->from_address) }}" required>
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
                                    <input type="text" name="from_name" id="from_name" class="form-control" value="{{ old('from_name', $setting->from_name) }}" required>
                                    @error('from_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', $setting->is_active) ? 'checked' : '' }}>
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
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $setting->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('admin.email-settings.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection