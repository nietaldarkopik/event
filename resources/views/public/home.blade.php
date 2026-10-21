@extends('layouts.public')

@section('title', 'Beranda')

@section('content')

    <!-- Hero Carousel -->
    <div id="heroCarousel" class="carousel slide hero-carousel" data-ride="carousel" data-interval="5000">
        <ol class="carousel-indicators">
            <li data-target="#heroCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#heroCarousel" data-slide-to="1"></li>
            <li data-target="#heroCarousel" data-slide-to="2"></li>
            <li data-target="#heroCarousel" data-slide-to="3"></li>
        </ol>
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item hero-slide active">
                <img src="{{ asset('images/slideshow/slide-1.jpg') }}" class="d-block w-100 h-100 hero-slide-img"
                    alt="UNWIM Sambut Mahasiswa Baru Tahun Akademik 2026/2027, Cetak Generasi Berilmu dan Berdampak">
            </div>
            <!-- Slide 2 -->
            <div class="carousel-item hero-slide">
                <img src="{{ asset('images/slideshow/slide-2.jpg') }}" class="d-block w-100 h-100 hero-slide-img" loading="lazy"
                    alt="UNWIM Dorong Pengembangan Pola Kemitraan Agribisnis Tembakau melalui Business Matching Temu Bisnis">
            </div>
            <!-- Slide 3 -->
            <div class="carousel-item hero-slide">
                <img src="{{ asset('images/slideshow/slide-3.jpg') }}" class="d-block w-100 h-100 hero-slide-img" loading="lazy"
                    alt="UNWIM Gelar Semiloka Penulisan Karya Ilmiah bagi Mahasiswa Program Magister">
            </div>
            <!-- Slide 4 -->
            <div class="carousel-item hero-slide">
                <img src="{{ asset('images/slideshow/slide-4.jpg') }}" class="d-block w-100 h-100 hero-slide-img" loading="lazy"
                    alt="Rangkaian PKKMB 2026: UNWIM Bekali Mahasiswa Baru Karakter dan Kedisiplinan Lewat Outbound di Ciwidey">
            </div>
        </div>
        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <!-- Stats & Categories -->
    <div class="container" style="margin-top: -50px; position: relative; z-index: 10;">
        <div class="row">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-ultra p-4 h-100 d-flex flex-row align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-4 shadow"
                        style="min-width: 60px; height: 60px;">
                        <i class="fas fa-rocket fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold mb-1">Akses Cepat</h5>
                        <p class="text-muted small mb-0">Daftar event dalam hitungan detik.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-ultra p-4 h-100 d-flex flex-row align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-4 shadow"
                        style="min-width: 60px; height: 60px;">
                        <i class="fas fa-qrcode fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold mb-1">Absensi QR</h5>
                        <p class="text-muted small mb-0">Scan barcode untuk kehadiran.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-ultra p-4 h-100 d-flex flex-row align-items-center">
                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center mr-4 shadow"
                        style="min-width: 60px; height: 60px;">
                        <i class="fas fa-file-contract fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold mb-1">E-Sertifikat</h5>
                        <p class="text-muted small mb-0">Download sertifikat kapan saja.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="container py-5 mt-4">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-right">
            <div>
                <h6 class="text-primary font-weight-bold text-uppercase letter-spacing-2 mb-2">Jadwal Terdekat</h6>
                <h2 class="font-weight-bold display-4">Event Mendatang</h2>
            </div>
            <a href="{{ route('events.index') }}"
                class="btn btn-outline-primary rounded-pill font-weight-bold px-4 d-none d-md-inline-block">Lihat Semua</a>
        </div>

        <div class="row">
            @foreach($featuredEvents as $index => $event)
                <div class="col-lg-4 col-md-6 mb-5" data-aos="fade-up" data-aos-delay="{{ 100 * ($index + 1) }}">
                    <div class="card-ultra h-100 d-flex flex-column">
                        <div class="card-img-wrapper">
                            <span
                                class="badge badge-light position-absolute m-3 px-3 py-2 font-weight-bold shadow-sm text-primary z-index-1"
                                style="z-index: 2; top:0; right:0;">
                                {{ ucfirst($event->event_type) }}
                            </span>
                            @if($event->banner_url)
                                <img src="{{ $event->banner_url }}" class="card-img-top card-img-zoom"
                                    alt="{{ $event->title }}" style="height: 250px; object-fit: cover;" loading="lazy">
                            @else
                                <div class="bg-dark d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fas fa-image fa-3x text-secondary"></i>
                                </div>
                            @endif
                            <!-- Date overlay -->
                            <div class="position-absolute bg-white shadow-sm rounded text-center p-2 m-3"
                                style="top: 0; left: 0; min-width: 60px; z-index: 2;">
                                <span
                                    class="d-block h5 font-weight-bold mb-0 text-dark">{{ \Carbon\Carbon::parse($event->start_datetime)->format('d') }}</span>
                                <span class="d-block small text-uppercase text-muted font-weight-bold"
                                    style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($event->start_datetime)->format('M') }}</span>
                            </div>
                        </div>
                        <div class="card-body pt-4 px-4 pb-4 flex-grow-1 d-flex flex-column">
                            <h5 class="font-weight-bold text-dark mb-3">
                                <a href="{{ route('events.show', $event->slug) }}"
                                    class="text-decoration-none text-dark stretched-link hover-primary-color">
                                    {{ Str::limit($event->title, 50) }}
                                </a>
                            </h5>
                            <p class="text-muted small mb-4 flex-grow-1 line-clamp-3">
                                {{ \App\Support\Html::plain($event->description, 100) }}
                            </p>

                            <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="far fa-clock mr-2 text-warning"></i>
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} WIB
                                </div>
                                @if($event->price > 0)
                                    <span class="font-weight-bold text-primary">
                                        Rp {{ number_format($event->price / 1000, 0) }}k
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center d-md-none mt-4">
            <a href="{{ route('events.index') }}"
                class="btn btn-outline-primary btn-block rounded-pill font-weight-bold">Lihat Semua Event</a>
        </div>
    </div>

    <!-- CTA Banner -->
    <div class="container py-5" data-aos="zoom-in">
        <div class="rounded-lg position-relative overflow-hidden p-5 text-white shadow-lg"
            style="background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); border-radius: 30px;">
            <div class="position-relative z-index-1 text-center">
                <h2 class="font-weight-bold mb-3">Siap Menjadi Bagian dari Event Seru?</h2>
                <p class="lead mb-4 opacity-90">Jangan sampai ketinggalan informasi. Daftar sekarang dan dapatkan
                    manfaatnya.</p>
                <a href="{{ url('/events') }}"
                    class="btn btn-light text-primary font-weight-bold px-5 py-3 rounded-pill shadow icon-hover-move">
                    Mulai Jelajah <i class="fas fa-arrow-right ml-2 icon-move"></i>
                </a>
            </div>
            <!-- Decor -->
            <div class="position-absolute"
                style="top: -50%; left: -10%; width: 500px; height: 500px; background: rgba(255,255,255,0.1); border-radius: 50%;">
            </div>
            <div class="position-absolute"
                style="bottom: -50%; right: -10%; width: 400px; height: 400px; background: rgba(255,255,255,0.1); border-radius: 50%;">
            </div>
        </div>
    </div>

    <!-- Layout Specific Styles -->
    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .hover-primary-color:hover {
            color: var(--primary) !important;
            transition: color 0.2s;
        }

        .icon-hover-move:hover .icon-move {
            transform: translateX(5px);
            transition: transform 0.2s;
        }

        /* Carousel Custom */
        .hero-slide {
            height: 750px;
            background: #0f172a;
        }

        /* Slide images already carry their own caption and gradient; keep the caption area in view */
        .hero-slide-img {
            object-fit: cover;
            object-position: center 85%;
        }

        @media (max-width: 768px) {
            .hero-slide {
                height: auto;
                aspect-ratio: 3 / 2;
            }
        }

        .carousel-indicators {
            bottom: 120px;
            /* Lift indicators above the cards */
        }

        @media (max-width: 768px) {
            .carousel-indicators {
                bottom: 100px;
            }
        }
    </style>
@endsection