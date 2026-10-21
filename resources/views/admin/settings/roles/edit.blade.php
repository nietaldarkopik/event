@extends('adminlte::page')

@section('title', 'Atur Permission Role: ' . $role->name)

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
    <div>
        <h1 class="mb-1">
            <i class="fas fa-sliders-h text-info"></i> Matriks Hak Akses: <span class="badge badge-primary">{{ $role->name }}</span>
        </h1>
        <p class="mb-0 text-muted small">Atur dan centang secara manual hak akses (permission) untuk Role ini.</p>
    </div>
    <div>
        <a href="{{ route('settings.roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Role
        </a>
    </div>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<form action="{{ route('settings.roles.update', $role->id) }}" method="POST" id="permissionsForm">
    @csrf
    @method('PUT')

    <div class="card card-outline card-info mb-3">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="permissionSearch" class="form-control" placeholder="Cari nama permission (misal: events, certificates, users)...">
                    </div>
                </div>
                <div class="col-md-6 text-md-right">
                    <button type="button" class="btn btn-sm btn-outline-success mr-1" id="selectAllGlobal">
                        <i class="fas fa-check-double"></i> Centang Semua Fitur
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="deselectAllGlobal">
                        <i class="fas fa-times-circle"></i> Batal Centang Semua
                    </button>
                </div>
            </div>
        </div>
    </div>

    @php $groupIdx = 0; @endphp
    @foreach($groupedPermissions as $groupName => $permissions)
    @php $groupIdx++; @endphp
        <div class="card card-default card-outline mb-4 permission-group-card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-layer-group text-secondary mr-1"></i> {{ $groupName }}
                    <span class="badge badge-pill badge-secondary ml-1">{{ count($permissions) }} Permission</span>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-xs btn-outline-primary select-group-btn" data-target="group-{{ $groupIdx }}">
                        <i class="fas fa-check-square"></i> Pilih Semua Modul Ini
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row group-container" id="group-{{ $groupIdx }}">
                    @foreach($permissions as $p)
                        @php
                            $isChecked = in_array($p->name, $rolePermissions);
                        @endphp
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-2 permission-item" data-name="{{ strtolower($p->name) }}">
                            <div class="custom-control custom-checkbox p-2 border rounded bg-white hover-shadow h-100">
                                <input type="checkbox" 
                                       class="custom-control-input perm-checkbox" 
                                       id="perm_{{ $p->id }}" 
                                       name="permissions[]" 
                                       value="{{ $p->name }}"
                                       {{ $isChecked ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-normal text-break" for="perm_{{ $p->id }}" style="cursor: pointer;">
                                    <code>{{ $p->name }}</code>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <div class="card bg-light border sticky-bottom shadow-lg" style="position: sticky; bottom: 15px; z-index: 1020;">
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <div>
                <span class="font-weight-bold text-dark">Role: {{ $role->name }}</span>
                <span class="text-muted ml-2">(Klik tombol di kanan untuk menyimpan perubahan matriks hak akses)</span>
            </div>
            <button type="submit" class="btn btn-success btn-lg shadow">
                <i class="fas fa-save mr-1"></i> Simpan Hak Akses / Permission
            </button>
        </div>
    </div>
</form>

@stop

@section('js')
<script>
$(document).ready(function() {
    // Filter Search
    $('#permissionSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.permission-item').filter(function() {
            $(this).toggle($(this).attr('data-name').indexOf(value) > -1);
        });

        $('.permission-group-card').each(function() {
            var visibleItems = $(this).find('.permission-item:visible').length;
            $(this).toggle(visibleItems > 0);
        });
    });

    // Select All Global
    $('#selectAllGlobal').click(function() {
        $('.perm-checkbox').prop('checked', true);
    });

    // Deselect All Global
    $('#deselectAllGlobal').click(function() {
        $('.perm-checkbox').prop('checked', false);
    });

    // Select Group
    $('.select-group-btn').click(function() {
        var targetId = $(this).data('target');
        var checkboxes = $('#' + targetId).find('.perm-checkbox');
        var allChecked = checkboxes.filter(':checked').length === checkboxes.length;
        checkboxes.prop('checked', !allChecked);
    });
});
</script>
@stop
