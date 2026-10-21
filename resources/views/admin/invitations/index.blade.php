@extends('adminlte::page')

@section('title', 'Undangan & Kode')

@section('content_header')
<h1>Undangan & Kode QR</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('invitations.index') }}" method="GET">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Filter Event</label>
                        <select name="event_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Unik</th>
                        <th>Peserta</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>QR Code</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invitations as $index => $invitation)
                        <tr>
                            <td>{{ $invitations->firstItem() + $index }}</td>
                            <td><span class="badge badge-dark">{{ $invitation->unique_code }}</span></td>
                            <td>
                                {{ $invitation->participant->name }}<br>
                                <small class="text-muted">{{ $invitation->participant->whatsapp }}</small>
                            </td>
                            <td>{{ $invitation->event->title }}</td>
                            <td>
                                <span class="badge badge-{{ $invitation->is_active ? 'success' : 'danger' }}">
                                    {{ $invitation->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($invitation->unique_code);
                                @endphp
                                {!! $qrCode !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data undangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $invitations->withQueryString()->links() }}
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop