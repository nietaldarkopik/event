@extends('adminlte::page')

@section('title', 'Edit Kategori Event')

@section('content_header')
    <h1>Edit Kategori Event</h1>
@stop

@section('content')
<div class="card card-warning card-outline">
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

        <form action="{{ route('admin.event-categories.update', $eventcategory->id) }}" method="POST">
            @csrf
            @method('PUT')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="form-group">
                <label for="name">Nama Kategori</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ isset($eventcategory) ? $eventcategory->name : old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="icon">Icon Class</label>
                <input type="text" class="form-control" id="icon" name="icon" value="{{ isset($eventcategory) ? $eventcategory->icon : old('icon') }}" >
            </div>
            <div class="form-group">
                <label for="color">Warna Hex/Class</label>
                <input type="text" class="form-control" id="color" name="color" value="{{ isset($eventcategory) ? $eventcategory->color : old('color') }}" >
            </div>
        </div>
    </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Perbarui</button>
                    <a href="{{ route('admin.event-categories.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
