<!DOCTYPE html>
<html>

<head>
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .meta {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
        }

        .status-hadir {
            color: green;
            font-weight: bold;
        }

        .status-absen {
            color: red;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Kehadiran Peserta</h1>
        <h3>{{ $event->title }}</h3>
    </div>

    <div class="meta">
        <strong>Tanggal Event:</strong>
        {{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('l, d F Y H:i') }} <br>
        <strong>Lokasi:</strong> {{ $event->location }} <br>
        <strong>Total Peserta Terdaftar:</strong> {{ $participants->count() }} <br>
        <strong>Total Hadir:</strong>
        {{ $participants->filter(function ($p) {
    return $p->attendanceLogs->isNotEmpty(); })->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Peserta</th>
                <th width="20%">Kontak</th>
                <th width="20%">Absensi Datang</th>
                <th width="20%">Absensi Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $index => $p)
                @php
                    $checkIn = $p->attendanceLogs->where('type', 'check_in')->first();
                    $secondaryLog = null;
                    if ($event->event_type == 'wisuda') {
                        $secondaryLog = $p->attendanceLogs->where('type', 'consumption')->first();
                    } else {
                        $secondaryLog = $p->attendanceLogs->where('type', 'check_out')->first();
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->name }}</td>
                    <td>
                        {{ $p->email }} <br>
                        <small>{{ $p->whatsapp }}</small>
                    </td>
                    <td>
                        @if($checkIn)
                            <span class="status-hadir">HADIR</span>
                            <br><small>{{ \Carbon\Carbon::parse($checkIn->scanned_at)->format('H:i') }}</small>
                        @else
                            <span class="status-absen">-</span>
                        @endif
                    </td>
                    <td>
                        @if($secondaryLog)
                            <span class="status-hadir">OK</span>
                            <br><small>{{ \Carbon\Carbon::parse($secondaryLog->scanned_at)->format('H:i') }}</small>
                        @else
                            <span class="status-absen">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem EVENTUNWIM pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>