@extends('layouts.public', ['hideMenu' => $isPreview ?? false])

@section('title', 'Undangan Digital - ' . $invitation->event->title)

@php
    $tcfg = $templateConfig ?? [];
    // luminansi 0..1 dari warna hex / rgb(a)
    $lum = function ($c) {
        $c = trim((string) $c);
        if (preg_match('/^#([0-9a-f]{3})$/i', $c, $m)) { $c = '#' . $m[1][0].$m[1][0].$m[1][1].$m[1][1].$m[1][2].$m[1][2]; }
        if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $c, $m)) { [$r, $g, $b] = [hexdec($m[1]), hexdec($m[2]), hexdec($m[3])]; }
        elseif (preg_match('/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i', $c, $m)) { [$r, $g, $b] = [(int) $m[1], (int) $m[2], (int) $m[3]]; }
        else { return 1; }
        return (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;
    };
    $isDarkCard = $lum($tcfg['colors']['card_bg'] ?? '#fffdf8') < 0.45;
    $onPrimary = $lum($tcfg['colors']['primary'] ?? '#166534') > 0.6 ? '#1f2937' : '#fbf3da';
    $bodyFont = $tcfg['typography']['font'] ?? 'Cormorant Garamond';
    $serifFonts = ['Cormorant Garamond', 'Playfair Display', 'Lora', 'Merriweather', 'Georgia'];
    $headingFont = in_array($bodyFont, $serifFonts, true) ? 'Playfair Display' : $bodyFont;
    $preloaded = ['Cormorant Garamond', 'Playfair Display', 'Inter', 'Poppins'];
    $btnCfg = $tcfg['buttons'] ?? null;
    $btnClasses = $btnCfg ? trim(
        (!empty($btnCfg['shape']) ? 'btn-shape-' . $btnCfg['shape'] : '') . ' ' .
        (!empty($btnCfg['size']) ? 'btn-size-' . $btnCfg['size'] : '') . ' ' .
        (($btnCfg['shadow'] ?? true) === false ? 'btn-shadow-none' : '')
    ) : '';
@endphp
@section('css')
    @if(!in_array($bodyFont, $preloaded, true))
        <link href="https://fonts.googleapis.com/css2?family={{ str_replace('%20', '+', rawurlencode($bodyFont)) }}:wght@400;500;600;700&display=swap" rel="stylesheet">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color:
                {{ $templateConfig['colors']['primary'] ?? '#166534' }}
            ;
            --secondary-color:
                {{ $templateConfig['colors']['secondary'] ?? '#b8923a' }}
            ;
            --primary-glow: rgba(22, 101, 52, 0.25);
            --secondary-glow: rgba(184, 146, 58, 0.3);
            --bg-color:
                {{ $templateConfig['colors']['bg'] ?? '#ece6d6' }}
            ;
            --card-bg:
                {{ $templateConfig['colors']['card_bg'] ?? '#fffdf8' }}
            ;
            --text-color:
                {{ $templateConfig['colors']['text'] ?? '#3a3629' }}
            ;
            --title-color:
                {{ $templateConfig['colors']['title_color'] ?? '#14532d' }}
            ;
            --title-size:
                {{ $templateConfig['typography']['title_size'] ?? '2rem' }}
            ;
            --body-size:
                {{ $templateConfig['typography']['body_size'] ?? '1rem' }}
            ;
            --font-family: '{{ $bodyFont }}', sans-serif;
            --heading-font: '{{ $headingFont }}', serif;
            --on-primary: {{ $onPrimary }};
            --header-tone: {{ $templateConfig['layout']['header_overlay_color'] ?? '#14532d' }};
            --line-height:
                {{ $templateConfig['typography']['line_height'] ?? '1.5' }}
            ;
            --card-blur:
                {{ $templateConfig['advanced']['card_blur'] ?? '20px' }}
            ;
            --card-opacity:
                {{ $templateConfig['advanced']['card_opacity'] ?? '1' }}
            ;
            --card-bg-image:
                {{ ($templateConfig['advanced']['card_bg_image'] ?? '') ? 'url(' . $templateConfig['advanced']['card_bg_image'] . ')' : 'none' }}
            ;
            --header-bg-image:
                {{ ($templateConfig['layout']['header_bg_image'] ?? '') ? 'url(' . $templateConfig['layout']['header_bg_image'] . ')' : 'none' }}
            ;
            --header-overlay-color:
                {{ $templateConfig['layout']['header_overlay_color'] ?? 'rgba(20, 83, 45, 0)' }}
            ;
            --header-overlay-opacity:
                {{ $templateConfig['layout']['header_overlay_opacity'] ?? '1' }}
            ;
        }

        body {
            font-family: var(--font-family);
            font-size: var(--body-size);
            line-height: var(--line-height);
            color: var(--text-color);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: var(--title-color);
        }

        .invitation-body {
            background: var(--bg-color);
            min-height: 100vh;
            padding: 40px 0;
            color: var(--text-color);
            transition: background 0.3s ease, color 0.3s ease;
        }

        .invitation-card {
            max-width: 480px;
            margin: 0 auto;
            border-radius:
                {{ $templateConfig['advanced']['border_radius'] ?? '10px' }}
            ;
            overflow: hidden;
            background-color: var(--card-bg);
            background-image: var(--card-bg-image);
            background-size: cover;
            background-position: center;
            backdrop-filter: blur(var(--card-blur));
            opacity: var(--card-opacity);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            padding-bottom: 70px;
            /* Space for footer */
        }

        /* Custom CSS from Builder */
        {!! $templateConfig['advanced']['custom_css'] ?? '' !!}

        .invitation-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--primary-color), #d946ef, var(--primary-color));
            z-index: -1;
            border-radius: 37px;
            opacity: 0.2;
        }

        .card-header-deco {
            background: var(--header-bg-image);
            background-size: cover;
            background-position: center;
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .card-header-deco::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--header-overlay-color);
            opacity: var(--header-overlay-opacity);
            z-index: 1;
        }

        .card-header-deco>* {
            position: relative;
            z-index: 2;
        }

        .header-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 150px;
            height: 150px;
            background: white;
            filter: blur(60px);
            opacity: 0.1;
            pointer-events: none;
        }

        .qr-section-wrapper {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 25px;
            margin: 0 20px 0;
            padding: 15px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .qr-placeholder {
            height: 140px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.02);
            border: 2px dashed rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            padding: 10px;
        }

        .qr-placeholder:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .qr-real {
            display: none;
        }

        .qr-container {
            background: white;
            padding: 15px;
            border-radius: 20px;
            box-shadow: 0 0 30px var(--primary-glow);
            transition: transform 0.3s;
        }

        .qr-container:hover {
            transform: scale(1.02);
        }

        .event-info-box {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-icon-circle {
            width: 45px;
            height: 45px;
            background: rgba(79, 70, 229, 0.1);
            color: #818cf8;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
            border: 1px solid rgba(79, 70, 229, 0.2);
        }

        .btn-premium {
            background: linear-gradient(45deg, var(--primary-color), #6366f1);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            color: white;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
            transition: all 0.3s;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(79, 70, 229, 0.4);
            color: white;
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: #cbd5e1;
            border-radius: 15px;
            padding: 12px;
            transition: all 0.3s;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .glass-modal {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            color: white;
        }

        .timeline-bullet {
            width: 10px;
            height: 10px;
            background: var(--primary-color);
            border-radius: 50%;
            margin-right: 15px;
            box-shadow: 0 0 10px var(--primary-glow);
        }

        .border-bottom-glass {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Focus View Styles */
        .invitation-detail-view {
            transition: all 0.5s ease;
        }

        .qr-focus-view {
            display: none;
            text-align: center;
            padding: 40px 20px;
            animation: fadeInScale 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .qr-large-container {
            background: white;
            padding: 30px;
            border-radius: 35px;
            display: inline-block;
            box-shadow: 0 0 50px var(--primary-glow);
            margin-bottom: 30px;
        }

        .qr-large-label {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .btn-back-invitation {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-back-invitation:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Layout Variations */
        /* STANDARD CARD (Default) */
        .layout-card .card-header-deco {
            border-radius: 0 0 30px 30px;
            margin-bottom: 20px;
        }

        /* CENTERED SIMPLE */
        .layout-centered .invitation-card {
            background: rgba(15, 23, 42, 0.5);
            /* More transparent */
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: none;
        }

        .layout-centered .card-header-deco {
            background: transparent;
            border-bottom: none;
            padding-bottom: 10px;
        }

        .layout-centered .header-glow {
            width: 200px;
            height: 200px;
            opacity: 0.15;
            background: var(--primary-color);
        }

        .layout-centered .qr-section-wrapper {
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* FULL WIDTH MODERN */
        .layout-full .invitation-card {
            border-radius: 0;
            max-width: 100%;
            height: 100vh;
            /* Full screen feel */
            margin: 0;
            background: #0f172a;
        }

        .layout-full .card-header-deco {
            height: 30vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            background: linear-gradient(180deg, var(--primary-color) 0%, rgba(15, 23, 42, 0) 100%);
            border-radius: 0;
        }

        .layout-full .qr-section-wrapper {
            margin-top: -50px;
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.3);
        }

        /* SPACING */
        .spacing-compact .card-body {
            padding: 1.5rem !important;
        }

        .spacing-wide .card-body {
            padding: 3rem !important;
        }

        /* BUTTON VARIATIONS */
        /* Shape */
        .btn-shape-rounded .btn,
        .btn-shape-rounded .qr-placeholder {
            border-radius: 12px !important;
        }

        .btn-shape-square .btn,
        .btn-shape-square .qr-placeholder {
            border-radius: 4px !important;
        }

        .btn-shape-pill .btn,
        .btn-shape-pill .qr-placeholder {
            border-radius: 50px !important;
        }

        /* Size */
        .btn-size-small .btn {
            padding: 8px 20px !important;
            font-size: 0.85rem !important;
        }

        .btn-size-medium .btn {
            padding: 12px 25px !important;
            font-size: 1rem !important;
        }

        .btn-size-large .btn {
            padding: 16px 35px !important;
            font-size: 1.15rem !important;
        }

        /* Shadow */
        .btn-shadow-none .btn {
            box-shadow: none !important;
            transform: none !important;
        }

        /* UNWIM Custom Footer */
        /* UNWIM Custom Footer */
        /* UNWIM Custom Footer */
        /* UNWIM Custom Footer - Simplified & Robust */
        .unwim-footer-deco {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 12px;
            /* Slim bar */
            display: flex;
            z-index: 999;
            overflow: hidden;
            border-radius: 0 0 30px 30px;
        }

        .layout-full .unwim-footer-deco {
            border-radius: 0;
        }

        /* Show on ALL layouts now */
        .layout-centered .unwim-footer-deco {
            display: flex !important;
        }

        .unwim-footer-deco .stripe {
            flex: 1;
            /* Equal width */
            height: 100%;
            position: relative;
            transform: skewX(-20deg) scale(1.5);
            /* Slight dynamic skew */
        }

        .stripe-green {
            background: #10b981;
        }

        .stripe-cyan {
            background: #06b6d4;
        }

        .stripe-blue {
            background: #3b82f6;
        }

        .stripe-yellow {
            background: #fbbf24;
        }

        /* ==========================================================
           TEMA ELEGAN: kartu undangan resmi di atas kertas krem.
           Memakai variabel template (warna, font, radius) agar builder tetap berfungsi.
           ========================================================== */
        :root { --gold: var(--secondary-color); --ink: var(--text-color); --paper: var(--card-bg); --line: rgba(184, 146, 58, .45); }

        .invitation-body {
            background:
                radial-gradient(circle at 20% 0%, rgba(255,255,255,.55), transparent 45%),
                radial-gradient(circle at 100% 100%, rgba(184,146,58,.12), transparent 40%),
                var(--bg-color);
            padding: 36px 0 48px;
        }
        body { font-size: calc(var(--body-size) * 1.08); }

        .invitation-card {
            background-color: var(--paper);
            color: var(--ink);
            border: 1px solid var(--line);
            box-shadow: 0 30px 60px -24px rgba(60, 50, 20, .45), 0 2px 0 rgba(255,255,255,.7) inset;
            padding-bottom: 54px;
        }
        .invitation-card::before { display: none; }
        /* bingkai tipis ganda ala undangan cetak */
        .invitation-card::after {
            content: ''; position: absolute; inset: 8px; border: 1px solid var(--line);
            border-radius: calc(10px - 4px); pointer-events: none; z-index: 5;
        }

        /* Header: hijau tua dengan garis emas */
        .card-header-deco {
            background: var(--header-bg-image) center/cover, linear-gradient(165deg, #14532d 0%, #166534 60%, #0f3d21 100%);
            padding: 46px 30px 38px; border-bottom: 3px double var(--gold);
        }
        .layout-card .card-header-deco { border-radius: 0; margin-bottom: 8px; }
        .header-glow { background: var(--gold); opacity: .18; }
        .invitation-card .card-header-deco .text-white,
        .invitation-card .card-header-deco h2 { color: #fbf3da !important; }
        .card-header-deco h5 { font-family: var(--heading-font); font-weight: 600 !important; letter-spacing: .22em !important; font-size: .78rem; text-shadow: none !important; color: #e9d38c !important; }
        .card-header-deco .badge {
            background: transparent !important; border: 1px solid rgba(233, 211, 140, .7); color: #e9d38c !important;
            letter-spacing: .25em; font-size: .62rem; font-weight: 600; padding: .45rem 1rem !important; backdrop-filter: none !important; box-shadow: none !important;
        }
        .card-header-deco h2 {
            font-family: var(--heading-font); font-weight: 600 !important; font-size: 1.35rem; line-height: 1.4;
            letter-spacing: .06em !important; margin: .4rem 0 .2rem !important;
        }
        .card-header-deco h2::after {
            content: ''; display: block; width: 110px; height: 1px; margin: 16px auto 12px;
            background: linear-gradient(90deg, transparent, #e9d38c, transparent);
        }
        .card-header-deco p { font-family: var(--font-family), serif; font-style: italic; font-size: 1.05rem; color: #f3e8c0 !important; opacity: .95 !important; letter-spacing: .08em; }

        /* Teks isi: mengikuti warna template, bukan putih */
        .invitation-card .text-white { color: var(--title-color) !important; }
        .invitation-card .text-white-50 { color: var(--ink) !important; opacity: .68; }
        .invitation-card .text-primary, .invitation-card .text-primary-light { color: var(--primary-color) !important; }
        .invitation-card .text-warning { color: #8a5a12 !important; }
        .invitation-card .letter-spacing-2, .invitation-card .letter-spacing-1 { letter-spacing: .18em; }

        .participant-info-section small { font-family: 'Inter', sans-serif; font-size: .66rem !important; letter-spacing: .28em !important; color: var(--gold) !important; opacity: 1 !important; font-weight: 600; }
        .participant-info-section h3 { font-family: var(--heading-font); font-weight: 600 !important; font-size: 1.7rem; margin: .5rem 0 .4rem !important; }
        .participant-info-section h3::after { content: ''; display: block; width: 46px; height: 2px; background: var(--gold); margin: 12px auto 0; }
        .participant-info-section .badge {
            background: rgba(184,146,58,.12) !important; color: var(--primary-color) !important;
            border: 1px solid var(--line) !important; border-radius: 30px !important; letter-spacing: .2em !important; font-size: .68rem !important;
        }
        .seat-number-section h2 { font-family: var(--heading-font); color: var(--primary-color) !important; text-shadow: none !important; }

        /* QR */
        .qr-section-wrapper {
            background: transparent; border: 0; box-shadow: none !important; padding: 8px 12px 0; margin: 4px 10px 0;
        }
        .qr-placeholder {
            background: #fff; border: 1px solid var(--line); border-radius: 8px !important; height: 128px; color: var(--ink);
            box-shadow: 0 6px 16px -10px rgba(60,50,20,.5);
        }
        .qr-placeholder:hover { background: #fffdf3; border-color: var(--gold); transform: translateY(-2px); }
        .qr-placeholder i { color: var(--primary-color) !important; opacity: .9 !important; }
        .qr-placeholder span { color: var(--primary-color); font-family: 'Inter', sans-serif; letter-spacing: .14em !important; }
        .qr-placeholder small { color: var(--gold) !important; opacity: 1 !important; letter-spacing: .08em; }
        .barcode-section code, .qr-focus-view code { color: var(--primary-color) !important; font-family: 'Inter', monospace; font-style: normal !important; }
        .qr-container, .qr-large-container { border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 14px 30px -14px rgba(60,50,20,.5); background: #fff; }
        .qr-large-label { font-family: var(--heading-font); letter-spacing: .16em; color: var(--primary-color) !important; font-size: 1.2rem; }
        .btn-back-invitation { background: transparent; border: 1px solid var(--primary-color); color: var(--primary-color) !important; border-radius: 8px; }
        .btn-back-invitation:hover { background: var(--primary-color); color: #fff !important; }

        /* Detail acara */
        .event-info-box { background: rgba(184,146,58,.07); border: 1px solid var(--line); border-radius: 8px; padding: 22px 20px; }
        .event-info-box small { font-size: .64rem; letter-spacing: .2em; text-transform: uppercase; color: var(--gold) !important; opacity: 1 !important; font-weight: 600; }
        .event-info-box .font-weight-bold { font-family: var(--heading-font); font-weight: 600 !important; }
        .event-info-box a { color: var(--title-color) !important; }
        .info-icon-circle { background: #fff; color: var(--primary-color); border: 1px solid var(--line); border-radius: 50%; width: 44px; height: 44px; flex: 0 0 44px; }
        .invitation-card .card.bg-transparent { border: 1px dashed var(--line) !important; border-radius: 8px !important; background: rgba(255,255,255,.5) !important; }
        .invitation-card .card.bg-transparent h5 { font-family: 'Playfair Display', serif; letter-spacing: .12em; font-size: .9rem; color: var(--title-color) !important; }

        /* Tombol */
        .btn-premium {
            background: var(--primary-color); border: 1px solid var(--primary-color); border-radius: 8px; letter-spacing: .14em; font-size: .8rem;
            box-shadow: 0 10px 20px -10px var(--primary-glow); color: var(--on-primary);
        }
        .btn-premium:hover { filter: brightness(.9); color: var(--on-primary); box-shadow: 0 14px 24px -10px var(--primary-glow); }
        .btn-glass { background: transparent; border: 1px solid var(--primary-color); color: var(--primary-color) !important; border-radius: 8px; backdrop-filter: none; letter-spacing: .1em; font-size: .78rem; font-weight: 600; }
        .btn-glass:hover { background: var(--primary-color); color: var(--on-primary) !important; border-color: var(--primary-color); }

        /* Catatan rahasia */
        .invitation-card .rounded-xl[style*="245, 158, 11"] { background: rgba(184,146,58,.1) !important; border: 1px solid var(--line) !important; border-radius: 8px !important; }

        /* Footer: strip hijau-emas */
        .unwim-footer-deco { height: 8px; border-radius: 0 !important; }
        .stripe-green { background: var(--primary-color); }
        .stripe-cyan { background: var(--secondary-color); }
        .stripe-blue { background: var(--header-tone); }
        .stripe-yellow { background: #e9d38c; }

        /* Modal rundown */
        .glass-modal { background: var(--paper); color: var(--ink); border: 1px solid var(--line); border-radius: 10px; backdrop-filter: none; box-shadow: 0 30px 60px -20px rgba(0,0,0,.45); }
        .glass-modal .text-white, .glass-modal .close { color: var(--title-color) !important; text-shadow: none; opacity: 1; }
        .glass-modal .modal-title { font-family: var(--heading-font); }
        .glass-modal .text-white-50 { color: var(--ink) !important; opacity: .72; }
        .glass-modal .text-primary { color: var(--primary-color) !important; }
        .timeline-bullet { background: var(--gold); box-shadow: 0 0 0 4px rgba(184,146,58,.2); }
        .border-bottom-glass { border-bottom: 1px solid var(--line) !important; }

        .registered-note { background: rgba(22,101,52,.08); border: 1px solid rgba(22,101,52,.35); color: var(--primary-color); border-radius: 8px; padding: 12px 16px; font-size: .9rem; line-height: 1.5; text-align: center; margin-bottom: 14px; }

        /* Tombol berbentuk pil tidak boleh membuat kotak QR menjadi lonjong */
        .btn-shape-pill .qr-placeholder, .btn-shape-rounded .qr-placeholder, .btn-shape-square .qr-placeholder { border-radius: 14px !important; }
        .btn-shape-square .qr-placeholder { border-radius: 4px !important; }
        /* Footer hak cipta: beri ruang di sisi kiri-kanan */
        .invitation-card > .text-center.pb-4 { padding-left: 28px; padding-right: 28px; font-size: .72rem !important; }

        /* Layout alternatif tetap mengikuti tema (bukan kartu gelap bawaan lama) */
        .layout-centered .invitation-card, .layout-full .invitation-card { background-color: var(--paper); border: 1px solid var(--line); }
        .layout-centered .card-header-deco { background: var(--header-bg-image) center/cover, linear-gradient(165deg, #14532d 0%, #166534 60%, #0f3d21 100%); border-bottom: 3px double var(--gold); }
        .layout-full .invitation-card { height: auto; min-height: 100vh; }
        .layout-full .card-header-deco { height: auto; display: block; background: var(--header-bg-image) center/cover, linear-gradient(165deg, #14532d 0%, #166534 60%, #0f3d21 100%); }
        .layout-centered .qr-section-wrapper, .layout-full .qr-section-wrapper { background: transparent; border: 0; box-shadow: none; }

        /* Template bertema gelap: panel transparan, aksen terang */
        .tone-dark .event-info-box { background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.12); }
        .tone-dark .info-icon-circle { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.14); color: var(--gold); }
        .invitation-card.tone-dark .card.bg-transparent { background: rgba(255,255,255,.04) !important; border-color: rgba(255,255,255,.2) !important; }
        .tone-dark .participant-info-section .badge { background: rgba(255,255,255,.07) !important; color: var(--gold) !important; border-color: rgba(255,255,255,.18) !important; }
        .tone-dark .qr-placeholder { background: #fff; color: #1f2937; }
        .tone-dark .qr-placeholder i, .tone-dark .qr-placeholder span { color: #1f2937 !important; }
        .tone-dark .qr-placeholder small { color: #6b7280 !important; }
        .tone-dark .registered-note { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.2); color: var(--ink); }
        .invitation-card.tone-dark .rounded-xl[style*="245, 158, 11"] { background: rgba(255,255,255,.05) !important; border-color: rgba(255,255,255,.14) !important; }
        .invitation-card.tone-dark .text-warning { color: var(--gold) !important; }
        .tone-dark .barcode-section code, .tone-dark .qr-focus-view code { color: var(--gold) !important; }
        .tone-dark .qr-large-label { color: var(--gold) !important; }
        .tone-dark .btn-back-invitation { border-color: var(--gold); color: var(--gold) !important; }
        .tone-dark .btn-back-invitation:hover { background: var(--gold); color: #111 !important; }
        .tone-dark .btn-glass { border-color: var(--gold); color: var(--gold) !important; }
        .tone-dark .btn-glass:hover { background: var(--gold); color: #111 !important; }
        .tone-dark.invitation-card::after { border-color: rgba(255,255,255,.12); }

        @media (max-width: 520px) {
            .invitation-body { padding: 0; }
            .invitation-card { border-radius: 0 !important; border-left: 0; border-right: 0; }
            .invitation-card::after { inset: 6px; }
        }
    </style>
@endsection

@section('content')
    <div class="invitation-body">
        <div class="container">
            <div id="mainCard"
                class="invitation-card layout-{{ $templateConfig['layout']['header_style'] ?? 'card' }} spacing-{{ $templateConfig['layout']['spacing'] ?? 'normal' }} {{ $isDarkCard ? 'tone-dark' : '' }} {{ $btnClasses }}"
                data-aos="zoom-in">
                <div class="card-header-deco">
                    <div class="header-glow"></div>
                    <!-- UNWIM Logo & Name -->
                    @php
                        $defaultLogoUrl = asset('images/logo-unwim.png');
                        $configuredLogo = $templateConfig['layout']['header_logo'] ?? null;
                        $logoUrl = $configuredLogo ?: $defaultLogoUrl;
                    @endphp
                    <div class="text-center mb-4">
                        <img id="headerLogoImg"
                            src="{{ $logoUrl }}"
                            alt="Logo UNWIM"
                            data-default-logo="{{ $defaultLogoUrl }}"
                            style="width: 100px; height: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));" class="mb-2"
                            onerror="this.src='{{ $defaultLogoUrl }}'; this.onerror=null;">
                        <h5 class="font-weight-bold text-white letter-spacing-1 mb-0"
                            style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">UNIVERSITAS WINAYA MUKTI</h5>
                    </div>

                    <span class="badge badge-primary px-3 py-1 rounded-pill small font-weight-bold mb-3 shadow-lg"
                        style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px);">
                        VIRTUAL TICKET &bull; {{ date('Y') }}
                    </span>
                    <h2 class="font-weight-bold mb-1 letter-spacing-2">{{ strtoupper($invitation->event->title) }}</h2>
                    <p class="small mb-0 font-weight-light" style="opacity: 0.8;">
                        {{ $invitation->event->event_type == 'wisuda' ? 'Sidang Terbuka Senat' : 'Official Invitation' }}
                    </p>
                </div>

                <!-- Focus View (Hidden initially) -->
                <div id="qrFocusView" class="qr-focus-view">
                    <div id="focusLabelMasuk" class="qr-large-label" style="color: #4f46e5; display: none;">ABSENSI DATANG</div>
                    <div id="focusLabelPulang" class="qr-large-label" style="color: #d946ef; display: none;">ABSENSI PULANG</div>
                    <div id="focusLabelKonsumsi" class="qr-large-label" style="color: #fbbf24; display: none;">ABSENSI PULANG</div>

                    <div id="focusQRMasuk" class="qr-large-container" style="display: none;">
                        {!! QrCode::size(280)->margin(1)->generate($invitation->unique_code) !!}
                    </div>
                    <div id="focusQRPulang" class="qr-large-container" style="display: none;">
                        {!! QrCode::size(280)->margin(1)->generate($invitation->unique_code) !!}
                    </div>
                    <div id="focusQRKonsumsi" class="qr-large-container" style="display: none;">
                        {!! QrCode::size(280)->margin(1)->generate($invitation->unique_code) !!}
                    </div>

                    @if(session('registered'))
                        <div class="registered-note">
                            <b>Pendaftaran berhasil!</b><br>
                            Ini QR Code Anda. Screenshot layar ini atau simpan link halaman ini. QR juga dikirim ke WhatsApp Anda.
                        </div>
                    @endif
                    <div class="mt-2">
                        <code class="text-primary-light font-weight-bold"
                            style="letter-spacing: 5px; font-size: 1.5rem;">{{ $invitation->unique_code }}</code>
                    </div>
                    <button class="btn btn-back-invitation" onclick="closeFocusView()">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Undangan
                    </button>
                </div>

                @if(session('registered'))
                    <div class="registered-note" style="margin: 14px 20px 0;">
                        <b>Pendaftaran berhasil!</b> Tunjukkan QR Code di bawah saat presensi. Salinannya dikirim ke WhatsApp Anda.
                    </div>
                @endif
                <div id="invitationDetailView" class="invitation-detail-view barcode-section">
                    <div class="qr-section-wrapper shadow-2xl">
                        <div class="row no-gutters justify-content-center">
                            @php
                                $showMasuk = $templateConfig['sections']['show_barcode_masuk'] ?? true;
                                $showPulang = $templateConfig['sections']['show_barcode_pulang'] ?? false;
                                $showKonsumsi = $templateConfig['sections']['show_barcode_konsumsi'] ?? false;
                                // Force Wisuda defaults if generic toggle not set
                                if ($invitation->event->event_type == 'wisuda') {
                                    $showMasuk = $templateConfig['sections']['show_barcode_masuk'] ?? true;
                                    $showPulang = $templateConfig['sections']['show_barcode_pulang'] ?? false;
                                    $showKonsumsi = $templateConfig['sections']['show_barcode_konsumsi'] ?? true;
                                }

                                $visibleCount = ($showMasuk ? 1 : 0) + ($showPulang ? 1 : 0) + ($showKonsumsi ? 1 : 0);
                                $colClass = $visibleCount === 3 ? 'col-4' : ($visibleCount === 2 ? 'col-6' : 'col-12');
                            @endphp

                            <!-- Datang QR -->
                            <div class="{{ $colClass }} p-2 text-center" id="barcode-masuk-wrapper"
                                style="display: {{ $showMasuk ? 'block' : 'none' }};">
                                <div class="qr-placeholder" onclick="openFocusView('Masuk')">
                                    <i class="fas fa-sign-in-alt mb-2 text-primary"
                                        style="font-size: 1.5rem; opacity: 0.7;"></i>
                                    <span class="font-weight-bold d-block"
                                        style="font-size: 0.7rem; letter-spacing: 1px;">ABSENSI DATANG</span>
                                    <small class="text-white-50 mt-1 d-block" style="font-size: 0.6rem;">Klik
                                        Tampilkan</small>
                                </div>
                            </div>

                            <!-- Konsumsi QR -->
                            <div class="{{ $colClass }} p-2 text-center" id="barcode-konsumsi-wrapper"
                                style="display: {{ $showKonsumsi ? 'block' : 'none' }};">
                                <div class="qr-placeholder" onclick="openFocusView('Konsumsi')">
                                    <i class="fas fa-utensils mb-2"
                                        style="font-size: 1.5rem; opacity: 0.7; color: #fbbf24;"></i>
                                    <span class="font-weight-bold d-block"
                                        style="font-size: 0.7rem; letter-spacing: 1px;">ABSENSI PULANG</span>
                                    <small class="text-white-50 mt-1 d-block" style="font-size: 0.6rem;">Klik
                                        Tampilkan</small>
                                </div>
                            </div>

                            <!-- Pulang QR -->
                            <div class="{{ $colClass }} p-2 text-center" id="barcode-pulang-wrapper"
                                style="display: {{ $showPulang ? 'block' : 'none' }};">
                                <div class="qr-placeholder" onclick="openFocusView('Pulang')">
                                    <i class="fas fa-sign-out-alt mb-2"
                                        style="font-size: 1.5rem; opacity: 0.7; color: #d946ef;"></i>
                                    <span class="font-weight-bold d-block"
                                        style="font-size: 0.7rem; letter-spacing: 1px;">ABSENSI PULANG</span>
                                    <small class="text-white-50 mt-1 d-block" style="font-size: 0.6rem;">Klik
                                        Tampilkan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <code class="text-primary-light font-weight-bold font-italic"
                            style="letter-spacing: 2px; font-size: 0.8rem;">{{ $invitation->unique_code }}</code>
                    </div>
                </div>

                <div class="card-body p-4 pt-5">
                    <div class="text-center mb-5 participant-info-section">
                        <small class="text-white-50 text-uppercase letter-spacing-2">Guest Name</small>
                        <h3 class="font-weight-bold text-white mt-1 mb-2">{{ $invitation->participant->name }}</h3>
                        @if($invitation->event->event_type == 'wisuda')
                            <div class="mt-1 flex flex-col items-center">
                                @if(!empty($invitation->participant->program_studi))
                                    <span
                                        class="text-white-50 text-[12px] uppercase tracking-wide">{{ $invitation->participant->program_studi }}</span>
                                @endif
                                @if(!empty($invitation->participant->fakultas))
                                    <span
                                        class="text-white-50 text-[10px] uppercase font-light opacity-60 tracking-tighter">{{ $invitation->participant->fakultas }}</span>
                                @endif
                            </div>
                        @elseif($invitation->event->event_type == 'rapat')
                            <div class="mt-1 flex flex-col items-center">
                                @if(!empty($invitation->participant->jabatan))
                                    <span
                                        class="text-white-50 text-[13px] font-bold uppercase tracking-widest">{{ $invitation->participant->jabatan }}</span>
                                @endif
                                @if(!empty($invitation->participant->unit))
                                    <span
                                        class="text-white-50 text-[11px] uppercase opacity-70">{{ $invitation->participant->unit }}</span>
                                @endif
                            </div>
                        @else {{-- Seminar / Workshop --}}
                            <div class="mt-2">
                                <span class="badge px-4 py-2 rounded-xl font-weight-bold"
                                    style="background: rgba(79, 70, 229, 0.2); color: #a5b4fc; border: 1px solid rgba(79, 70, 229, 0.3); letter-spacing: 1px; font-size: 0.75rem;">
                                    {{ strtoupper($invitation->participant->type ?? 'PESERTA') }}
                                </span>
                            </div>
                        @endif

                        @if($invitation->participant->seat_number && ($templateConfig['sections']['show_seat_number'] ?? true))
                            <div class="mt-4 seat-number-section">
                                <small class="text-white-50 text-uppercase letter-spacing-2 d-block">NOMOR KURSI</small>
                                <h2 class="font-weight-bold text-white mt-1 display-4"
                                    style="text-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                    {{ $invitation->participant->seat_number }}
                                </h2>
                            </div>
                        @endif
                    </div>

                    <div class="event-info-box">
                        <div class="d-flex align-items-center mb-4">
                            <div class="info-icon-circle"><i class="fas fa-calendar-alt"></i></div>
                            <div>
                                <small class="text-white-50 d-block">Nama Event</small>
                                <div class="font-weight-bold text-white">{{ $invitation->event->title }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="info-icon-circle"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <small class="text-white-50 d-block">Lokasi & Tempat</small>
                                <a href="{{ ($invitation->event->location_link ?? null) ?: 'https://www.google.com/maps/search/?api=1&query=' . urlencode($invitation->event->location) }}"
                                    target="_blank"
                                    class="font-weight-bold text-white text-decoration-none hover-underline">
                                    {{ $invitation->event->location }}
                                    <i class="fas fa-external-link-alt ml-1 small opacity-50"></i>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-0">
                            <div class="info-icon-circle"><i class="fas fa-clock"></i></div>
                            <div>
                                @php $mySession = data_get($invitation->participant, 'session'); @endphp
                                <small class="text-white-50 d-block">{{ $mySession ? 'Sesi Anda' : 'Waktu Pelaksanaan' }}</small>
                                @if($mySession)
                                    <div class="font-weight-bold text-white">{{ $mySession->title }}</div>
                                    <div class="font-weight-bold text-white">
                                        {{ \Carbon\Carbon::parse($mySession->start_datetime ?: $invitation->event->start_datetime)->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="small text-white-50">
                                        {{ \Carbon\Carbon::parse($mySession->start_datetime ?: $invitation->event->start_datetime)->format('H.i') }}@if($mySession->end_datetime) &ndash; {{ \Carbon\Carbon::parse($mySession->end_datetime)->format('H.i') }}@endif WIB
                                        @if($mySession->room) &middot; {{ $mySession->room }}@endif
                                    </div>
                                @else
                                    <div class="font-weight-bold text-white">
                                        {{ \Carbon\Carbon::parse($invitation->event->start_datetime)->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="small text-white-50">
                                        {{ \Carbon\Carbon::parse($invitation->event->start_datetime)->format('H:i') }} WIB
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row no-gutters">
                        @if($invitation->event->rules)
                            <div class="col-12 mb-3">
                                <div class="card bg-transparent border-white-50"
                                    style="border: 1px dashed rgba(255,255,255,0.3); border-radius: 15px;">
                                    <div class="card-body text-left">
                                        <h5 class="text-white font-weight-bold mb-3"><i class="fas fa-info-circle mr-2"></i>
                                            TATA TERTIB</h5>
                                        <div class="text-white-50 small rich-content" style="line-height: 1.6;">
                                            {!! \App\Support\Html::render($invitation->event->rules) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-12 mb-3 rundown-section">
                            <button class="btn btn-premium btn-block shadow-lg" data-toggle="modal"
                                data-target="#rundownModal">
                                <i class="fas fa-stream mr-2"></i> LIHAT RUNDOWN DETAIL
                            </button>
                        </div>
                        <div class="col-12 mb-3">
                            <a href="{{ route('attendance.self', $invitation->event->slug) }}" class="btn btn-glass btn-block">
                                <i class="fas fa-clipboard-check mr-2"></i> CARA PRESENSI DI LOKASI
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('events.show', $invitation->event->slug) }}" class="btn btn-glass btn-block">
                                <i class="fas fa-chevron-left mr-2"></i> KEMBALI KE DETAIL EVENT
                            </a>
                        </div>
                    </div>

                    <div class="mt-5 p-3 rounded-xl text-center"
                        style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.1);">
                        <small class="text-warning font-weight-bold">
                            <i class="fas fa-info-circle mr-1"></i> QR Code bersifat rahasia. Jangan bagikan kepada
                            siapapun.
                        </small>
                    </div>
                </div>

                <div class="text-center pb-4 opacity-50 small text-uppercase letter-spacing-1">
                    &copy; {{ date('Y') }} UNWIM EVENT MANAGEMENT SYSTEM
                </div>

                <!-- Custom Footer UNWIM -->
                <div class="unwim-footer-deco">
                    <div class="stripe stripe-green"></div>
                    <div class="stripe stripe-cyan"></div>
                    <div class="stripe stripe-blue"></div>
                    <div class="stripe stripe-yellow"></div>
                </div>

            </div> <!-- closing mainCard -->
        </div>
    </div>
    </div>

    <!-- Rundown Modal -->
    <div class="modal fade" id="rundownModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content glass-modal border-0 shadow-2xl">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="fas fa-list-ul text-primary mr-2"></i> Agenda & Rundown
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    @php $agendaItems = method_exists($invitation->event, 'agendaItems') ? $invitation->event->agendaItems() : $invitation->event->rundowns->sortBy('order_number'); @endphp
                    @forelse($agendaItems as $rundown)
                        <div class="d-flex align-items-start mb-4 pb-3 border-bottom-glass">
                            <div class="timeline-bullet mt-2"></div>
                            <div>
                                <div class="small font-weight-bold text-primary mb-1">
                                    {{ \Carbon\Carbon::parse($rundown->time_start)->format('H:i') }}@if($rundown->time_end) -
                                    {{ \Carbon\Carbon::parse($rundown->time_end)->format('H:i') }}@endif
                                </div>
                                <div class="text-white-50 font-weight-light">{{ $rundown->agenda }}</div>
                                @if(!empty($rundown->detail))<div class="text-white-50 small mt-1" style="opacity:.8">{{ $rundown->detail }}</div>@endif
                                @if(!empty($rundown->body))<div class="text-white-50 small mt-1 rich-content" style="opacity:.85">{!! \App\Support\Html::render($rundown->body) !!}</div>@endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x mb-3 text-white-50"></i>
                            <p class="text-white-50">Belum ada jadwal yang diatur.</p>
                        </div>
                    @endforelse
                    <button type="button" class="btn btn-glass btn-block mt-3" data-dismiss="modal">Tutup</button>
                </div>

                <!-- Custom Footer UNWIM -->
                <div class="unwim-footer-deco">
                    <div class="stripe stripe-green"></div>
                    <div class="stripe stripe-cyan"></div>
                    <div class="stripe stripe-blue"></div>
                    <div class="stripe stripe-yellow"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        @if(session('registered'))
            // Baru selesai mendaftar: langsung tampilkan QR besar.
            $(function () { setTimeout(function () { openFocusView('Masuk'); }, 400); });
        @endif

        function openFocusView(type) {
            // Hide all labels and containers first
            $('.qr-large-label, .qr-large-container').hide();

            // Show relevant elements
            $(`#focusLabel${type}, #focusQR${type}`).show();

            // Switch view
            $('#invitationDetailView').fadeOut(400, function () {
                $('#qrFocusView').fadeIn(400);
            });
        }

        function closeFocusView() {
            $('#qrFocusView').fadeOut(400, function () {
                $('#invitationDetailView').fadeIn(400);
            });
        }
        // Real-time Preview Listener
        window.addEventListener('message', function (event) {
            if (event.data && event.data.type === 'updateConfig') {
                const config = event.data.config;
                const root = document.documentElement;

                // Update Colors
                if (config.colors) {
                    if (config.colors.primary) root.style.setProperty('--primary-color', config.colors.primary);
                    if (config.colors.secondary) root.style.setProperty('--secondary-color', config.colors.secondary);
                    if (config.colors.bg) root.style.setProperty('--bg-color', config.colors.bg);
                    if (config.colors.card_bg) root.style.setProperty('--card-bg', config.colors.card_bg);
                    if (config.colors.text) root.style.setProperty('--text-color', config.colors.text);
                    if (config.colors.title_color) root.style.setProperty('--title-color', config.colors.title_color);
                }

                // Update Typography
                if (config.typography) {
                    if (config.typography.font) root.style.setProperty('--font-family', `'${config.typography.font}', sans-serif`);
                    if (config.typography.title_size) root.style.setProperty('--title-size', config.typography.title_size);
                    if (config.typography.body_size) root.style.setProperty('--body-size', config.typography.body_size);
                    if (config.typography.line_height) root.style.setProperty('--line-height', config.typography.line_height);
                }

                // Update Layout Classes
                const mainCard = document.getElementById('mainCard');
                if (mainCard && config.layout) {
                    mainCard.className = mainCard.className.replace(/layout-\w+/g, '').replace(/spacing-\w+/g, '');
                    if (config.layout.header_style) mainCard.classList.add(`layout-${config.layout.header_style}`);
                    if (config.layout.spacing) mainCard.classList.add(`spacing-${config.layout.spacing}`);
                }

                // Update Button Style Classes
                if (mainCard && config.buttons) {
                    mainCard.className = mainCard.className.replace(/btn-shape-\w+/g, '').replace(/btn-size-\w+/g, '').replace(/btn-shadow-\w+/g, '');
                    if (config.buttons.shape) mainCard.classList.add(`btn-shape-${config.buttons.shape}`);
                    if (config.buttons.size) mainCard.classList.add(`btn-size-${config.buttons.size}`);
                    if (config.buttons.shadow === false) mainCard.classList.add('btn-shadow-none');
                }

                // Update Sections Visibility
                if (config.sections) {
                    // Standard Sections
                    const participantInfo = document.querySelector('.participant-info-section');
                    if (participantInfo) participantInfo.style.display = config.sections.show_participant_info ? 'block' : 'none';

                    const seatNumber = document.querySelector('.seat-number-section');
                    if (seatNumber) seatNumber.style.display = config.sections.show_seat_number ? 'block' : 'none';

                    const rundownSection = document.querySelector('#rundownSection');
                    if (rundownSection) rundownSection.style.display = config.sections.show_rundown ? 'block' : 'none';

                    // 3-Barcode Logic Visibility & Column Sizing
                    const masukWrap = document.getElementById('barcode-masuk-wrapper');
                    const pulangWrap = document.getElementById('barcode-pulang-wrapper');
                    const konsumsiWrap = document.getElementById('barcode-konsumsi-wrapper');

                    // Determine defaults based on category if needed, but usually config should have them
                    const category = event.data.category || 'seminar';
                    const isWisuda = category === 'wisuda';

                    const showMasuk = config.sections.hasOwnProperty('show_barcode_masuk') ? config.sections.show_barcode_masuk : true;
                    const showPulang = config.sections.hasOwnProperty('show_barcode_pulang') ? config.sections.show_barcode_pulang : false;
                    const showKonsumsi = config.sections.hasOwnProperty('show_barcode_konsumsi') ? config.sections.show_barcode_konsumsi : (isWisuda ? true : false);

                    if (masukWrap) masukWrap.style.display = showMasuk ? 'block' : 'none';
                    if (pulangWrap) pulangWrap.style.display = showPulang ? 'block' : 'none';
                    if (konsumsiWrap) konsumsiWrap.style.display = showKonsumsi ? 'block' : 'none';

                    let visibleCount = 0;
                    if (showMasuk) visibleCount++;
                    if (showPulang) visibleCount++;
                    if (showKonsumsi) visibleCount++;

                    const colClass = visibleCount === 3 ? 'col-4' : (visibleCount === 2 ? 'col-6' : 'col-12');
                    [masukWrap, pulangWrap, konsumsiWrap].forEach(el => {
                        if (el) {
                            el.classList.remove('col-4', 'col-6', 'col-12');
                            el.classList.add(colClass);
                        }
                    });
                }

                // Advanced Custom CSS & Card Styles
                const mainRoot = document.documentElement;
                if (config.advanced) {
                    if (config.advanced.border_radius) {
                        if (mainCard) mainCard.style.borderRadius = config.advanced.border_radius;
                    }
                    if (config.advanced.card_blur) {
                        mainRoot.style.setProperty('--card-blur', config.advanced.card_blur);
                    }
                    if (config.advanced.card_opacity) {
                        mainRoot.style.setProperty('--card-opacity', config.advanced.card_opacity);
                    }
                    if (config.advanced.card_bg_image !== undefined) {
                        mainRoot.style.setProperty('--card-bg-image', config.advanced.card_bg_image ? `url(${config.advanced.card_bg_image})` : 'none');
                    }

                    if (config.advanced.custom_css !== undefined) {
                        let styleTag = document.getElementById('custom-css-preview');
                        if (!styleTag) {
                            styleTag = document.createElement('style');
                            styleTag.id = 'custom-css-preview';
                            document.head.appendChild(styleTag);
                        }
                        styleTag.textContent = config.advanced.custom_css;
                    }
                }

                // Update Header Visuals
                if (config.layout) {
                    if (config.layout.header_bg_image !== undefined) {
                        mainRoot.style.setProperty('--header-bg-image', config.layout.header_bg_image ? `url(${config.layout.header_bg_image})` : 'none');
                    }
                    if (config.layout.header_overlay_color) {
                        mainRoot.style.setProperty('--header-overlay-color', config.layout.header_overlay_color);
                    }
                    if (config.layout.header_overlay_opacity) {
                        mainRoot.style.setProperty('--header-overlay-opacity', config.layout.header_overlay_opacity);
                    }
                    const headerLogoImg = document.getElementById('headerLogoImg');
                    if (headerLogoImg && config.layout.hasOwnProperty('header_logo')) {
                        headerLogoImg.src = config.layout.header_logo || headerLogoImg.dataset.defaultLogo;
                    }
                }
            }
        });
    </script>
@endsection
