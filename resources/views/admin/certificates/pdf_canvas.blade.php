<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sertifikat {{ $certificate->display_name }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .canvas-container {
            position: relative;
            width:
                {{ $orientation == 'landscape' ? '297mm' : '210mm' }}
            ;
            height:
                {{ $orientation == 'landscape' ? '210mm' : '297mm' }}
            ;
            background-image: url('{{ $background }}');
            background-size: cover;
            background-position: center;
        }

        .element {
            position: absolute;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="canvas-container">
        @php
            $layout = json_decode($layoutJson, true);
            $objects = $layout['objects'] ?? [];
            // Fabric canvas width/height used during design
            $designWidth = $orientation == 'landscape' ? 1123 : 794;
            $designHeight = $orientation == 'landscape' ? 794 : 1123;

            // Conversion factors to mm (approximate for A4 at 96dpi)
            // But since we use same aspect ratio, we can use percentages
        @endphp

        @foreach($objects as $obj)
            @php
                $left = ($obj['left'] / $designWidth) * 100;
                $top = ($obj['top'] / $designHeight) * 100;
                $fontSize = $obj['fontSize'] ?? 20;
                $fontFamily = $obj['fontFamily'] ?? 'Arial';
                $fill = $obj['fill'] ?? '#000';
                $fontWeight = $obj['fontWeight'] ?? 'normal';
                $fontStyle = $obj['fontStyle'] ?? 'normal';
                $textAlign = $obj['textAlign'] ?? 'left';
                $width = ($obj['width'] / $designWidth) * 100;

                // Scale factor if any
                $scaleX = $obj['scaleX'] ?? 1;
                $scaleY = $obj['scaleY'] ?? 1;
            @endphp

            @if($obj['type'] == 'i-text' || $obj['type'] == 'text')
                <div class="element" style="
                            left: {{ $left }}%;
                            top: {{ $top }}%;
                            font-size: {{ $fontSize * 0.75 }}pt;
                            font-family: {{ $fontFamily }};
                            color: {{ $fill }};
                            font-weight: {{ $fontWeight }};
                            font-style: {{ $fontStyle }};
                            text-align: {{ $textAlign }};
                            width: {{ $width * $scaleX }}%;
                        ">
                    {{ $obj['text'] }}
                </div>
            @elseif($obj['type'] == 'image')
                @php
                    $src = '';
                    if (isset($obj['data']['type']) && $obj['data']['type'] == 'qr') {
                        $src = 'data:image/svg+xml;base64,' . $qrcode;
                    } else {
                        $src = $obj['src'] ?? '';
                    }
                @endphp
                <img src="{{ $src }}" class="element" style="
                            left: {{ $left }}%;
                            top: {{ $top }}%;
                            width: {{ ($obj['width'] * $scaleX / $designWidth) * 100 }}%;
                            height: {{ ($obj['height'] * $scaleY / $designHeight) * 100 }}%;
                        ">
            @endif
        @endforeach
    </div>
</body>

</html>