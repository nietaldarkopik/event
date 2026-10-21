<!DOCTYPE html>
<html>

<head>
    <title>Sertifikat {{ $certificate->display_name }}</title>
    <style>
        body {
            font-family: '{{ $certificate->template->font_body ?? "serif" }}';
            margin: 0;
            padding: 0;
            text-align:
                {{ $certificate->template->layout }}
            ;
            width: 100%;
            height: 100%;
        }

        .container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .content {
            position: absolute;
            top: 30%;
            left: 10%;
            right: 10%;
            color:
                {{ $certificate->template->text_color }}
            ;
        }

        h1 {
            font-family: '{{ $certificate->template->font_title ?? "sans-serif" }}';
            font-size: 48px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 40px;
        }

        h3 {
            font-size: 36px;
            margin: 20px 0;
            border-bottom: 2px solid
                {{ $certificate->template->text_color }}
            ;
            display: inline-block;
            padding-bottom: 10px;
        }

        .qrcode {
            position: absolute;
            bottom: 50px;
            right: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        @if($certificate->template->background_file)
            <img src="{{ public_path('storage/' . $certificate->template->background_file) }}" class="background">
        @endif

        <div class="content">
            <h2>Diberikan Kepada:</h2>
            <h3>{{ $certificate->display_name }}</h3>
            <p>Atas partisipasinya sebagai Peserta dalam acara:</p>
            <h1>{{ $certificate->event->title }}</h1>
            <p>{{ \Carbon\Carbon::parse($certificate->event->start_datetime)->isoFormat('D MMMM Y') }}</p>

            <p style="margin-top: 50px;">Nomor: {{ $certificate->certificate_number }}</p>
        </div>

        <div class="qrcode">
            <img src="data:image/svg+xml;base64, {!! $qrcode !!} " width="100">
        </div>
    </div>
</body>

</html>