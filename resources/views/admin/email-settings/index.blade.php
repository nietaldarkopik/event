@extends('adminlte::page')

@section('title', 'Email Settings')

@section('content_header')
    <h1>Email Settings</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Email Settings</h3>
                    <a href="{{ route('admin.email-settings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Email Setting
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Host</th>
                                    <th>Username</th>
                                    <th>Port</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emailSettings as $setting)
                                    <tr>
                                        <td>{{ $setting->name }}</td>
                                        <td>{{ $setting->host }}</td>
                                        <td>{{ $setting->username }}</td>
                                        <td>{{ $setting->port }}</td>
                                        <td>
                                            @if($setting->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                    <div class="btn-group">
                                        @if(!$setting->is_active)
                                            <form action="{{ route('admin.email-settings.toggle-active', $setting->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Aktifkan">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-info" title="Test Email" data-toggle="modal" data-target="#testEmailModal{{ $setting->id }}">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>

                                        <!-- Test Email Modal -->
                                        <div class="modal fade" id="testEmailModal{{ $setting->id }}" tabindex="-1" role="dialog" aria-labelledby="testEmailModalLabel{{ $setting->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="testEmailModalLabel{{ $setting->id }}">Test Email Configuration</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('admin.email-settings.test-email', $setting->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="target_email{{ $setting->id }}">Email Tujuan</label>
                                                                <input type="email" class="form-control" id="target_email{{ $setting->id }}" name="target_email" required placeholder="Masukkan email tujuan" value="{{ $setting->username }}">
                                                                <small class="form-text text-muted">Email akan dikirim ke alamat ini untuk testing</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Kirim Test Email</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.email-settings.edit', $setting->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.email-settings.destroy', $setting->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus email setting ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada email settings.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection