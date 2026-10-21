@extends('adminlte::page')

@section('title', 'Manajemen User')

@section('content_header')
<h1>Manajemen User & Hak Akses</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Pengguna</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $u)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if($u->role == 'super_admin') <span class="badge badge-danger">Super Admin</span>
                                    @elseif($u->role == 'admin_event') <span class="badge badge-primary">Admin Event</span>
                                    @elseif($u->role == 'admin_keuangan') <span class="badge badge-success">Admin
                                        Keuangan</span>
                                    @elseif($u->role == 'admin_sertifikat') <span class="badge badge-warning">Admin
                                        Sertifikat</span>
                                    @else <span class="badge badge-secondary">{{ $u->role }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($u->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-default" data-toggle="modal"
                                        data-target="#editUser{{ $u->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(auth()->id() != $u->id)
                                        <form action="{{ route('settings.users.destroy', $u->id) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editUser{{ $u->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit User</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="{{ route('settings.users.update', $u->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Nama</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $u->name }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ $u->email }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Password (Kosongkan jika tidak ubah)</label>
                                                    <input type="password" name="password" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label>Role</label>
                                                    <select name="role" class="form-control">
                                                        <option value="super_admin" {{ $u->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                                        <option value="admin_event" {{ $u->role == 'admin_event' ? 'selected' : '' }}>Admin Event</option>
                                                        <option value="admin_keuangan" {{ $u->role == 'admin_keuangan' ? 'selected' : '' }}>Admin Keuangan</option>
                                                        <option value="admin_sertifikat" {{ $u->role == 'admin_sertifikat' ? 'selected' : '' }}>Admin Sertifikat</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="activeSwitch{{ $u->id }}" name="is_active" {{ $u->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="activeSwitch{{ $u->id }}">Akun Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Tambah User Baru</h3>
            </div>
            <form action="{{ route('settings.users.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="super_admin">Super Admin</option>
                            <option value="admin_event">Admin Event</option>
                            <option value="admin_keuangan">Admin Keuangan</option>
                            <option value="admin_sertifikat">Admin Sertifikat</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block">Tambah User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop