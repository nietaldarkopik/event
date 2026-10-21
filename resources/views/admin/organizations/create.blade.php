@extends('adminlte::page')

@section('title', 'Tambah Organisasi')

@section('content_header')
    <h1>Tambah Organisasi</h1>
@stop

@section('content')
<div class="card card-primary card-outline">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.organizations.store') }}" method="POST">
            @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Nama Organisasi</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ isset($organization) ? $organization->name : old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ isset($organization) ? $organization->email : old('email') }}" >
            </div>
            <div class="form-group">
                <label for="website">Website</label>
                <input type="text" class="form-control" id="website" name="website" value="{{ isset($organization) ? $organization->website : old('website') }}" >
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="logo">Logo URL</label>
                <input type="text" class="form-control" id="logo" name="logo" value="{{ isset($organization) ? $organization->logo : old('logo') }}" >
            </div>
            <div class="form-group">
                <label for="phone">No. Telepon</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ isset($organization) ? $organization->phone : old('phone') }}" >
            </div>
            <div class="form-group">
                <label for="address">Alamat</label>
                <textarea class="form-control" id="address" name="address" rows="4" >{{ isset($organization) ? $organization->address : old('address') }}</textarea>
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
