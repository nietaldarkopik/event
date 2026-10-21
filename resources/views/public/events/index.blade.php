@extends('layouts.public')

@section('title', 'Jelajah Event')

@section('content')
    <!-- Header Section -->
    <div class="position-relative py-5 bg-secondary text-white overflow-hidden"
        style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);">
        <div class="position-absolute"
            style="top: -50px; right: -50px; width: 300px; height: 300px; background: rgba(79, 70, 229, 0.2); border-radius: 50%; filter: blur(60px);">
        </div>
        <div class="container position-relative z-index-1 text-center pt-5 pb-5" style="padding-bottom: 120px !important;">
            <span
                class="badge badge-warning px-3 py-2 font-weight-bold mb-3 d-inline-block animate__animated animate__fadeInDown">DISCOVERY</span>
            <h1 class="display-3 font-heading font-weight-bold mb-3 animate__animated animate__fadeInUp">Temukan Event Seru
            </h1>
            <p class="lead opacity-75 animate__animated animate__fadeInUp animate__delay-1s"
                style="max-width: 700px; margin: 0 auto;">
                Ikuti seminar, workshop, dan kegiatan pengembangan diri terbaik di kampus.
            </p>
        </div>
    </div>

    <div class="container" style="margin-top: -80px; position: relative; z-index: 10;">
        <!-- Filter Bar -->
        <div class="card border-0 shadow-lg mb-5 animate__animated animate__fadeInUp"
            style="border-radius: 50px; overflow: hidden;">
            <div class="card-body p-2 bg-white">
                <form action="{{ route('events.index') }}" method="GET" class="d-lg-flex align-items-center">

                    <!-- Kategori (Native Select with Premium Style) -->
                    <div class="flex-grow-1 px-4 py-3 border-right-lg position-relative group-hover-bg">
                        <label class="small text-muted font-weight-bold text-uppercase mb-1 letter-spacing-1">
                            <i class="fas fa-th-large text-primary mr-1"></i> Kategori Event
                        </label>
                        <div class="position-relative">
                            <select name="category"
                                class="form-control border-0 font-weight-bold text-dark p-0 h-auto shadow-none"
                                onchange="this.form.submit()"
                                style="font-size: 1.1rem; cursor: pointer; appearance: none; -webkit-appearance: none; background: transparent; padding-right: 20px;">
                                <option value="">Semua Kategori</option>
                                @foreach(\Modules\Category\Models\EventCategory::all() as $cat)
                                    <option value="{{ $cat->id }}" {{ (request('category') == $cat->id || request('type') == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Custom Arrow -->
                            <div class="position-absolute"
                                style="right: 0; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <i class="fas fa-chevron-down text-primary small"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Divider Mobile -->
                    <div class="d-lg-none border-top w-100 my-2"></div>

                    <!-- Pencarian -->
                    <div class="flex-grow-1 px-4 py-3 position-relative group-hover-bg">
                        <label class="small text-muted font-weight-bold text-uppercase mb-1 letter-spacing-1">
                            <i class="fas fa-search text-primary mr-1"></i> Kata Kunci
                        </label>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control border-0 font-weight-bold text-dark p-0 h-auto shadow-none"
                            style="font-size: 1.1rem;" placeholder="Cari judul event...">
                    </div>

                    <!-- Button -->
                    <div class="p-2">
                        <button
                            class="btn btn-primary-modern rounded-pill px-5 py-3 shadow-md w-100 w-lg-auto font-weight-bold hover-scale"
                            type="submit">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <style>
            .border-right-lg {
                border-right: 1px solid #f1f5f9;
            }

            @media (max-width: 991.98px) {
                .border-right-lg {
                    border-right: none;
                }
            }

            .group-hover-bg {
                transition: background 0.3s;
                border-radius: 30px;
            }

            .group-hover-bg:hover {
                background: #f8fafc;
            }
        </style>

        <div class="row">
            @forelse($events as $index => $event)
                <div class="col-lg-4 col-md-6 mb-5" data-aos="fade-up" data-aos-delay="{{ 100 * ($index % 3) }}">
                    <div class="card-ultra h-100 d-flex flex-column bg-white">
                        <div class="card-img-wrapper position-relative">
                            @if($event->banner_url)
                                <img src="{{ $event->banner_url }}" class="card-img-top card-img-zoom"
                                    alt="{{ $event->title }}" style="height: 260px; object-fit: cover;" loading="lazy">
                            @else
                                <div class="bg-light text-secondary d-flex align-items-center justify-content-center"
                                    style="height: 260px;">
                                    <i class="fas fa-image fa-3x opacity-25"></i>
                                </div>
                            @endif

                            <div class="position-absolute" style="top: 15px; left: 15px;">
                                <span
                                    class="badge badge-light shadow-sm text-uppercase px-3 py-2 font-weight-bold text-primary rounded-pill">
                                    {{ ucfirst($event->event_type) }}
                                </span>
                            </div>

                            @if($event->price > 0)
                                <div class="position-absolute bg-dark text-white shadow-lg px-3 py-2 font-weight-bold"
                                    style="bottom: 20px; right: 0; border-radius: 10px 0 0 10px;">
                                    Rp {{ number_format($event->price, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        <div class="card-body pt-4 px-4 pb-4 flex-grow-1 d-flex flex-column">
                            <!-- Date Badge Mini -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3 text-center lh-1">
                                    <span
                                        class="d-block h4 font-weight-bold text-primary mb-0">{{ \Carbon\Carbon::parse($event->start_datetime)->format('d') }}</span>
                                    <span
                                        class="d-block small text-uppercase font-weight-bold text-muted">{{ \Carbon\Carbon::parse($event->start_datetime)->format('M') }}</span>
                                </div>
                                <div class="border-left pl-3">
                                    <h5 class="card-title font-weight-bold text-dark mb-0" style="line-height: 1.4;">
                                        <a href="{{ route('events.show', $event->slug) }}"
                                            class="text-decoration-none text-dark stretched-link hover-primary-color">
                                            {{ Str::limit($event->title, 50) }}
                                        </a>
                                    </h5>
                                </div>
                            </div>

                            <p class="card-text text-muted mb-4 small flex-grow-1 line-clamp-3">
                                {{ \App\Support\Html::plain($event->description, 90) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                                <div class="d-flex align-items-center text-muted small font-weight-600">
                                    <i class="fas fa-map-marker-alt mr-2 text-danger"></i>
                                    {{ Str::limit($event->location, 20) }}
                                </div>
                                <small class="text-muted"><i class="far fa-clock mr-1"></i>
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="p-5" data-aos="zoom-in">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png"
                            alt="Empty" class="mb-3" style="width: 200px; opacity: 0.8;">
                        <h4 class="font-weight-bold text-dark">Oops! Belum ada event.</h4>
                        <p class="text-muted">Event dengan kategori ini belum tersedia saat ini.</p>
                        <a href="{{ route('events.index') }}"
                            class="btn btn-outline-primary rounded-pill px-4 shadow-sm mt-3">Reset Filter</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $events->appends(request()->query())->links() }}
        </div>
    </div>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection