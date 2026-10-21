@extends('adminlte::page')

@section('title', 'Manajemen Role & Permission')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="mb-1"><i class="fas fa-user-shield text-primary"></i> Manajemen Role & Permission</h1>
        <p class="mb-0 text-muted small">Kelola Peran (Role) dan Matriks Hak Akses (Permission) Aplikasi secara Manual</p>
    </div>
    <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary mb-2">
        <i class="fas fa-users-cog"></i> Ke Manajemen User
    </a>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Daftar Role Sistem</h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px" class="text-center">#</th>
                            <th>Nama Role</th>
                            <th>Guard</th>
                            <th class="text-center">Total Permission</th>
                            <th class="text-center" style="width: 220px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $index => $r)
                            <tr>
                                <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                <td>
                                    <strong class="text-dark">{{ $r->name }}</strong>
                                    @if(in_array($r->name, ['super_admin', 'admin_event', 'admin_sertifikat', 'admin_keuangan']))
                                        <span class="badge badge-info ml-1">Sistem</span>
                                    @endif
                                </td>
                                <td><code>{{ $r->guard_name }}</code></td>
                                <td class="text-center">
                                    <span class="badge badge-success px-2 py-1" style="font-size: 13px;">
                                        <i class="fas fa-key"></i> {{ $r->permissions_count }} Permissions
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('settings.roles.edit', $r->id) }}" class="btn btn-sm btn-info shadow-sm" title="Atur Matriks Permission">
                                        <i class="fas fa-sliders-h"></i> Atur Hak Akses
                                    </a>
                                    @if(!in_array($r->name, ['super_admin']))
                                        <form action="{{ route('settings.roles.destroy', $r->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role {{ $r->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Hapus Role">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">Belum ada data Role.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle"></i> Tambah Role Baru</h3>
            </div>
            <form action="{{ route('settings.roles.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="role_name">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="role_name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: operator_scanner" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Spasi akan otomatis diubah menjadi karakter underscore (_).</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-save"></i> Buat Role Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
