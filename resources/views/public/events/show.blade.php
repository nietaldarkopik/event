@extends('layouts.public')

@section('title', $event->title)

@section('css')
    <style>
        .hero-detail {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)), url('{{ $event->banner_url ?: "https://images.unsplash.com/photo-1540575861501-7cf05a4b125a?auto=format&fit=crop&q=80&w=2070" }}');
            background-size: cover;
            background-position: center;
            padding: 120px 0 80px;
            color: white;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
        }

        .timeline-modern {
            position: relative;
            padding-left: 3rem;
        }

        .timeline-modern::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2.5rem;
        }

        .timeline-dot {
            position: absolute;
            left: -3rem;
            width: 32px;
            height: 32px;
            background: white;
            border: 4px solid var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            margin-right: 8px;
            color: white !important;
        }

        .share-btn:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }
    </style>
@endsection

@section('content')
    <section class="event-hero">
        <div class="container" data-aos="fade-up">
            @if($event->banner_image)
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-12">
                        <img src="{{ asset('storage/' . $event->banner_image) }}" alt="{{ $event->title }}"
                            class="img-fluid rounded-20 shadow-2xl animate__animated animate__zoomIn"
                            style="width: 100%; max-height: 500px; object-fit: cover;">
                    </div>
                </div>
            @endif
            <div class="row align-items-center">
                <div class="col-lg-12 animate__animated animate__fadeInLeft">
                    <span
                        class="badge badge-warning px-4 py-2 rounded-pill font-weight-bold mb-3 shadow-sm text-uppercase letter-spacing-1">
                        {{ str_replace('_', ' ', $event->event_type) }}
                    </span>
                    <h1 class="display-4 font-weight-bold mb-5 line-height-tight text-center">
                        {{ $event->title }}
                    </h1>
                    <div class="d-flex flex-wrap align-items-center opacity-90 font-weight-light fs-5">
                        <div class="mr-4 mb-3"><i class="far fa-calendar-check mr-2 text-warning"></i>
                            {{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}</div>
                        <div class="mr-4 mb-3"><i class="far fa-clock mr-2 text-info"></i>
                            {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} WIB</div>
                        <div class="mb-3"><i class="fas fa-map-marker-alt mr-2 text-danger"></i> {{ $event->location }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="container mb-5 mt-4">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8 mb-4">
                    <div class="card glass-card shadow-2xl border-0 overflow-hidden" data-aos="fade-up">
                        <div class="card-body p-5">
                            <div class="mb-5">
                                <h4 class="font-weight-bold mb-4 text-dark d-flex align-items-center">
                                    <i class="fas fa-info-circle text-primary mr-3"></i> Deskripsi Acara
                                </h4>
                                <div class="text-muted fs-5 line-height-relaxed rich-content">{!! \App\Support\Html::render($event->description) !!}</div>
                            </div>

                            @php $agendaItems = $event->agendaItems(); $agendaMultiDay = $agendaItems->map(fn($a) => $a->date ? \Carbon\Carbon::parse($a->date)->toDateString() : null)->filter()->unique()->count() > 1; @endphp
                            @if($agendaItems->count() > 0)
                                <div class="mt-5">
                                    <h4 class="font-weight-bold mb-5 text-dark d-flex align-items-center">
                                        <i class="fas fa-stream text-primary mr-3"></i> Jadwal & Rundown
                                    </h4>
                                    <div class="timeline-modern">
                                        @foreach($agendaItems as $rundown)
                                            <div class="timeline-item">
                                                <div class="timeline-dot shadow-sm">
                                                    <i class="fas fa-circle text-primary small"></i>
                                                </div>
                                                <div class="bg-light p-4 rounded-xl border-left-primary shadow-sm">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge badge-primary-light text-primary font-weight-bold px-3">
                                                            @if($agendaMultiDay && !empty($rundown->date)){{ \Carbon\Carbon::parse($rundown->date)->translatedFormat('d M') }}, @endif{{ \Carbon\Carbon::parse($rundown->time_start)->format('H:i') }}@if($rundown->time_end) - {{ \Carbon\Carbon::parse($rundown->time_end)->format('H:i') }}@endif
                                                        </span>
                                                    </div>
                                                    <h5 class="font-weight-bold text-dark mb-0">{{ $rundown->agenda }}</h5>
                                                    @if(!empty($rundown->detail))<div class="small text-muted mt-2">{{ $rundown->detail }}</div>@endif
                                                    @if(!empty($rundown->body))<div class="small text-muted mt-2 rich-content">{!! \App\Support\Html::render($rundown->body) !!}</div>@endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 120px; z-index: 10;">
                        <div class="card glass-card border-0 shadow-xl" data-aos="fade-left" data-aos-delay="200">
                            <div class="card-body p-4">
                                <h5 class="font-weight-bold text-dark mb-4 px-2">Tiket & Pendaftaran</h5>

                                @if($event->event_type != 'rapat' && $event->event_type != 'wisuda')
                                    <div class="bg-primary-light rounded-xl p-4 mb-4 text-center">
                                        <small class="text-muted d-block text-uppercase font-weight-bold mb-1">Harga
                                            Tiket</small>
                                        <h2 class="font-weight-bold text-primary mb-0">
                                            @if($event->is_paid)
                                                Rp {{ number_format($event->price, 0, ',', '.') }}
                                            @else
                                                GRATIS
                                            @endif
                                        </h2>
                                    </div>
                                @endif

                                @php
                                    $regStart = $event->registration_start ? \Carbon\Carbon::parse($event->registration_start) : null;
                                    $regEnd = $event->registration_end ? \Carbon\Carbon::parse($event->registration_end) : null;
                                    $regState = $event->registrationState();
                                    $regLabel = $regState['label'];
                                    $regClass = ['open' => 'badge-success', 'not_open' => 'badge-secondary', 'closed' => 'badge-danger', 'full' => 'badge-danger'][$regState['state']];
                                @endphp
                                <ul class="list-unstyled mb-4 px-2">
                                    <li class="d-flex justify-content-between mb-3">
                                        <span class="text-muted"><i class="fas fa-users mr-2 text-primary"></i>
                                            Status:</span>
                                        <span class="badge {{ $regClass }} px-3 rounded-pill">{{ $regLabel }}</span>
                                    </li>
                                    @if($regStart || $regEnd)
                                        <li class="mb-3">
                                            <div class="text-muted mb-1"><i class="fas fa-calendar-check mr-2 text-primary"></i> Registrasi:</div>
                                            <div class="d-flex justify-content-between small pl-4">
                                                <span class="text-muted">Dibuka</span>
                                                <span class="font-weight-bold text-dark">{{ $regStart ? $regStart->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between small pl-4 mt-1">
                                                <span class="text-muted">Ditutup</span>
                                                <span class="font-weight-bold text-dark">{{ $regEnd ? $regEnd->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</span>
                                            </div>
                                        </li>
                                    @endif
                                    @php
                                        $hasCertificate = $event->certificate_enabled || $event->allow_certificate;
                                        $hasVenue = filled($event->venue_name) || filled($event->address);
                                        $hasMeeting = filled($event->meeting_link);
                                        $mediaLabel = $hasVenue && $hasMeeting ? 'Hybrid (Luring & Daring)'
                                            : ($hasMeeting ? 'Daring (Online)' : ($hasVenue ? 'Luring (Offline)' : null));
                                    @endphp
                                    @if($event->event_type == 'wisuda' || ($event->event_type != 'rapat' && $hasCertificate))
                                        <li class="d-flex justify-content-between mb-3">
                                            <span class="text-muted"><i class="fas fa-certificate mr-2 text-primary"></i>
                                                Fasilitas:</span>
                                            <span class="font-weight-bold text-dark small">
                                                @if($event->event_type == 'wisuda')
                                                    Ijazah
                                                @else
                                                    E-Sertifikat
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    @if($mediaLabel)
                                        <li class="d-flex justify-content-between {{ $hasMeeting ? 'mb-3' : '' }}">
                                            <span class="text-muted"><i class="fas fa-globe mr-2 text-primary"></i>
                                                Media:</span>
                                            <span class="font-weight-bold text-dark small">{{ $mediaLabel }}</span>
                                        </li>
                                    @endif
                                    @if($hasMeeting)
                                        <li class="d-flex justify-content-between">
                                            <span class="text-muted"><i class="fas fa-link mr-2 text-primary"></i> Link
                                                Meeting:</span>
                                            <a href="{{ $event->meeting_link }}" target="_blank"
                                                class="font-weight-bold text-primary small text-truncate ml-2"
                                                style="max-width: 150px;">
                                                Klik Disini <i class="fas fa-external-link-alt ml-1"></i>
                                            </a>
                                        </li>
                                    @endif
                                </ul>

                                @if(strtolower($event->status) == 'published')
                                    @if($event->event_type != 'rapat')
                                        @if($regState['state'] !== 'open')
                                            <button type="button" class="btn btn-secondary btn-block btn-lg py-3 rounded-xl font-weight-bold mb-2" disabled>
                                                <i class="fas fa-lock mr-2"></i>
                                                {{ ['not_open' => 'Pendaftaran Belum Dibuka', 'closed' => 'Pendaftaran Ditutup', 'full' => 'Kuota Penuh'][$regState['state']] }}
                                            </button>
                                            <p class="small text-muted text-center mb-3">{{ $regState['message'] }}</p>
                                        @elseif($event->event_type == 'wisuda')
                                            <button type="button" data-toggle="modal" data-target="#wisudaRegisterModal"
                                                class="btn btn-primary-modern btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-lg shadow-primary-20 hover-lift mb-3">
                                                Daftar Sekarang <i class="fas fa-university ml-2"></i>
                                            </button>
                                        @else
                                            <button type="button" data-toggle="modal" data-target="#generalRegisterModal"
                                                class="btn btn-primary-modern btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-lg shadow-primary-20 hover-lift mb-3">
                                                Daftar Sekarang <i class="fas fa-edit ml-2"></i>
                                            </button>
                                        @endif

                                        @if(($event->certificate_enabled || $event->allow_certificate) && !in_array($event->event_type, ['rapat', 'wisuda']))
                                            <button type="button" data-toggle="modal" data-target="#claimCertificateModal"
                                                class="btn btn-success btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-sm hover-lift mb-3 animate__animated animate__pulse animate__infinite">
                                                <i class="fas fa-certificate mr-2"></i> Ambil Sertifikat
                                            </button>
                                        @endif

                                        @if($regState['state'] === 'open')
                                        <div class="text-center my-3">
                                            <span class="text-muted small">
                                                @if($event->event_type == 'wisuda')
                                                    Isi formulir pendaftaran wisuda dengan data yang benar
                                                @else
                                                    Isi formulir pendaftaran dengan data yang benar
                                                @endif
                                            </span>
                                        </div>
                                        @endif
                                    @endif

                                    @if(!$event->is_internal && $event->meeting_link)
                                        <a href="{{ $event->meeting_link }}" target="_blank"
                                            class="btn btn-success btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-sm hover-lift mb-3">
                                            <i class="fas fa-video mr-2"></i> Gabung Meeting (Online)
                                        </a>
                                    @endif

                                    <button type="button"
                                        class="btn {{ ($event->event_type == 'rapat' || $event->event_type == 'wisuda') ? 'btn-primary' : 'btn-outline-primary' }} btn-block btn-lg py-3 rounded-xl font-weight-bold hover-lift mb-3"
                                        data-toggle="modal" data-target="#invitationModal">
                                        <i class="fas fa-ticket-alt mr-2"></i> Buka Undangan
                                    </button>
                                @else
                                    <button class="btn btn-secondary btn-block btn-lg py-3 rounded-xl font-weight-bold"
                                        disabled>
                                        Pendaftaran Ditutup
                                    </button>
                                @endif

                                @if(session('success'))
                                    <div class="alert alert-success mt-3 mb-0 rounded-xl">
                                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger mt-3 mb-0 rounded-xl">
                                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                                    </div>
                                @endif

                                @include('components.recaptcha-script')

                                <div class="text-center mt-4 border-top pt-4">
                                    <p class="small text-muted font-weight-bold text-uppercase mb-3 letter-spacing-1">
                                        Bagikan
                                        Event</p>
                                    <div class="d-flex justify-content-center">
                                        <a href="https://wa.me/?text={{ urlencode($event->title . ' - ' . url()->current()) }}"
                                            target="_blank" class="share-btn bg-success shadow-sm">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($event->title) }}&url={{ urlencode(url()->current()) }}"
                                            target="_blank" class="share-btn bg-info shadow-sm">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                            target="_blank" class="share-btn bg-primary shadow-sm">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($event->event_type != 'rapat')
                            @php $attWindow = \App\Services\AttendanceService::selfWindow($event); @endphp
                            <div class="card glass-card border-0 shadow-sm mt-3" data-aos="fade-up" data-aos-delay="300">
                                <div class="card-body p-4">
                                    <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-clipboard-check text-primary mr-2"></i> Cara Presensi di Lokasi</h6>
                                    <ol class="small text-muted pl-3 mb-2">
                                        <li class="mb-1"><b class="text-dark">Lewat petugas:</b> tunjukkan QR Code undangan (dikirim via WhatsApp/email setelah pendaftaran disetujui) ke petugas.</li>
                                        <li><b class="text-dark">Mandiri:</b> di lokasi, scan QR yang ditampilkan panitia di layar, lalu masukkan <b>kode undangan</b> Anda dan tekan Presensi.</li>
                                    </ol>
                                    @if($attWindow)
                                        <div class="small text-muted"><i class="far fa-clock mr-1"></i> Presensi mandiri dibuka {{ $attWindow[0]->translatedFormat('H:i') }} WIB sampai acara selesai. Terlambat tetap bisa presensi selama acara berlangsung.</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="400">
                            @php
                                $questionWaNumber = $event->contact_whatsapp ?? '6282216668383';
                                $questionWaMessage = urlencode("Halo Admin " . ($event->organizer_unit ?? 'EVENTUNWIM') . ", saya ingin bertanya tentang acara: " . $event->title);
                                $questionWaUrl = "https://wa.me/{$questionWaNumber}?text={$questionWaMessage}";
                            @endphp
                            <p class="text-muted small px-4">Ada pertanyaan tentang event ini? <br>
                                <a href="#" onclick="onClickWhatsApp(event, '{{ $questionWaUrl }}', '{{ $event->slug }}')"
                                    class="text-primary font-weight-bold">Hubungi Panitia</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invitation Modal -->
        <div class="modal fade" id="invitationModal" tabindex="-1" role="dialog" aria-labelledby="invitationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content glass-card border-0">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="bg-primary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width: 80px; height: 80px;">
                            <i class="fas fa-key fa-2x text-primary"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">Buka Undangan Digital</h4>
                        <p class="text-muted mb-4 px-4">Masukkan kode unik yang Anda terima melalui WhatsApp dari sistem
                            kami.
                        </p>

                        <form action="{{ route('events.invitation.check') }}" method="POST">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                            <div class="form-group mb-4">
                                <input type="text" name="unique_code"
                                    class="form-control form-control-lg text-center font-weight-bold letter-spacing-2 rounded-xl border-2"
                                    placeholder="KODE-UNIQUE" required style="height: 60px; font-size: 1.25rem;">
                            </div>
                            <button type="submit"
                                class="btn btn-primary btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-lg">
                                Lihat Undangan Saya <i class="fas fa-chevron-right ml-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Wisuda Registration Modal -->
        @if($event->event_type == 'wisuda')
            <div class="modal fade" id="wisudaRegisterModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
                        <div class="modal-header border-0 bg-primary-dark text-white p-4">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas fa-user-graduate mr-2"></i> Form Pendaftaran Wisuda
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('events.register', $event->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                @include('public.events._session_picker', ['event' => $event])
                                <p class="text-muted mb-4">
                                    Lengkapi data di bawah ini dengan benar. Setelah pendaftaran Anda diverifikasi dan memenuhi syarat,
                                    kode undangan dan informasi sertifikat akan dikirim melalui email dan WhatsApp.
                                </p>
                                <div class="form-group">
                                    <label>NIM</label>
                                    <input type="text" name="nim"
                                        class="form-control @error('nim') is-invalid @enderror"
                                        value="{{ old('nim') }}" required>
                                    @error('nim')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Fakultas</label>
                                    <select name="fakultas_id" id="wisuda-fakultas-select"
                                        class="form-control @error('fakultas_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Fakultas --</option>
                                        @foreach(($faculties ?? collect()) as $faculty)
                                            <option value="{{ $faculty->id }}" {{ old('fakultas_id') == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fakultas_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Program Studi</label>
                                    <select name="program_studi_id" id="wisuda-prodi-select"
                                        class="form-control @error('program_studi_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Program Studi --</option>
                                        @foreach(($studyPrograms ?? collect()) as $prodi)
                                            <option value="{{ $prodi->id }}" data-parent="{{ $prodi->parent_id }}"
                                                {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                                {{ $prodi->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('program_studi_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>WhatsApp Aktif</label>
                                    <input type="text" name="whatsapp"
                                        class="form-control @error('whatsapp') is-invalid @enderror"
                                        value="{{ old('whatsapp') }}" required>
                                    @error('whatsapp')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light p-3">
                                <button type="button" class="btn btn-light rounded-pill px-4"
                                    data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    Kirim Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var fakultasSelect = document.getElementById('wisuda-fakultas-select');
                    var prodiSelect = document.getElementById('wisuda-prodi-select');
                    if (!fakultasSelect || !prodiSelect) return;

                    var options = Array.prototype.slice.call(prodiSelect.options);

                    function filterProdi() {
                        var facultyId = fakultasSelect.value;
                        prodiSelect.value = '';
                        options.forEach(function (opt) {
                            if (!opt.value) {
                                opt.hidden = false;
                                opt.disabled = false;
                                return;
                            }
                            var parentId = opt.getAttribute('data-parent');
                            var visible = !facultyId || parentId === facultyId;
                            opt.hidden = !visible;
                            opt.disabled = !visible;
                        });
                    }

                    fakultasSelect.addEventListener('change', filterProdi);
                    filterProdi();
                });
            </script>
        @endif

        <!-- General Registration Modal (non-wisuda, non-rapat events) -->
        @if($event->event_type != 'wisuda' && $event->event_type != 'rapat')
            <div class="modal fade" id="generalRegisterModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
                        <div class="modal-header border-0 bg-primary-dark text-white p-4">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas fa-edit mr-2"></i> Form Pendaftaran
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('events.register', $event->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                @include('public.events._session_picker', ['event' => $event])
                                <p class="text-muted mb-4">
                                    Lengkapi data di bawah ini untuk mendaftar acara <strong>{{ $event->title }}</strong>.
                                </p>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>WhatsApp Aktif</label>
                                    <input type="text" name="whatsapp"
                                        class="form-control @error('whatsapp') is-invalid @enderror"
                                        value="{{ old('whatsapp') }}" required>
                                    @error('whatsapp')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Instansi / Unit Kerja</label>
                                    <input type="text" name="unit"
                                        class="form-control @error('unit') is-invalid @enderror"
                                        value="{{ old('unit') }}" required>
                                    @error('unit')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light p-3">
                                <button type="button" class="btn btn-light rounded-pill px-4"
                                    data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    Kirim Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Faculty Choice Modal for Wisuda -->
        @if($event->event_type == 'wisuda')
            <div class="modal fade" id="facultyModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
                        <div class="modal-header border-0 bg-primary-dark text-white p-4">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-university mr-2"></i> Pilih Fakultas Anda
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            <p class="text-muted mb-4 px-2">Silakan pilih fakultas untuk menghubungi PIC pendaftaran yang
                                sesuai:
                            </p>
                            <div class="row no-gutters">
                                @php
                                    $faculties = [
                                        ['id' => 'wa_pertanian', 'name' => 'Fakultas Pertanian', 'icon' => 'fa-seedling', 'color' => 'success'],
                                        ['id' => 'wa_kehutanan', 'name' => 'Fakultas Kehutanan', 'icon' => 'fa-tree', 'color' => 'teal'],
                                        ['id' => 'wa_teknik', 'name' => 'Fakultas Teknik', 'icon' => 'fa-cog', 'color' => 'primary'],
                                        ['id' => 'wa_ekonomi', 'name' => 'Fakultas Ekonomi', 'icon' => 'fa-chart-line', 'color' => 'warning'],
                                    ];
                                @endphp

                                @foreach($faculties as $fac)
                                    <div class="col-12 mb-3">
                                        @php
                                            $waNum = $event->{$fac['id']} ?? ($event->contact_whatsapp ?? '6282216668383');
                                            $waMsg = urlencode("Halo Admin Wisuda " . $fac['name'] . ", saya ingin mendaftar untuk: " . $event->title);
                                            $waLink = "https://wa.me/{$waNum}?text={$waMsg}";
                                        @endphp
                                        <a href="#" onclick="onClickWhatsApp(event, '{{ $waLink }}', '{{ $event->slug }}')" class="text-decoration-none">
                                            <div
                                                class="faculty-card p-3 rounded-xl bg-white border shadow-sm transition-all hover-scale d-flex align-items-center">
                                                <div class="rounded-circle bg-{{ $fac['color'] }}-light d-flex align-items-center justify-content-center mr-3"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="fas {{ $fac['icon'] }} text-{{ $fac['color'] }} fa-lg"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="font-weight-bold text-dark mb-0">{{ $fac['name'] }}</h6>
                                                    <small class="text-muted">Klik untuk Chat WhatsApp PIC</small>
                                                </div>
                                                <i class="fas fa-chevron-right text-muted small"></i>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light p-3">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .faculty-card:hover {
                    border-color: var(--primary);
                    background: #f8fbff !important;
                    transform: translateY(-2px);
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                }

                .bg-teal-light {
                    background-color: rgba(20, 184, 166, 0.1);
                }

                .text-teal {
                    color: #14b8a6;
                }

                .bg-success-light {
                    background-color: rgba(34, 197, 94, 0.1);
                }

                .bg-primary-light {
                    background-color: rgba(79, 70, 229, 0.1);
                }

                .bg-warning-light {
                    background-color: rgba(245, 158, 11, 0.1);
                }

                .rounded-xl {
                    border-radius: 1rem !important;
                }
            </style>
        @endif

        <!-- Claim Certificate Modal -->
        @if(($event->event_type == 'workshop' || $event->event_type == 'seminar') && $event->allow_certificate)
            <div class="modal fade" id="claimCertificateModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
                        <div class="modal-header border-0 bg-success text-white p-4">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-certificate mr-2"></i> Ambil Sertifikat
                                Digital</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <div class="bg-success-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-file-download fa-2x text-success"></i>
                            </div>
                            <h4 class="font-weight-bold text-dark mb-2">Masukkan Kode Unik</h4>
                            <p class="text-muted mb-4 px-4">Sertifikat Anda siap diunduh. Masukkan kode unik untuk memverifikasi
                                data Anda.</p>

                            <form action="{{ route('events.certificate.claim') }}" method="POST">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <div class="form-group mb-4">
                                    <input type="text" name="unique_code"
                                        class="form-control form-control-lg text-center font-weight-bold letter-spacing-2 rounded-xl border-2"
                                        placeholder="KODE-ANDA" required style="height: 60px; font-size: 1.25rem;">
                                </div>
                                <button type="submit"
                                    class="btn btn-success btn-block btn-lg py-3 rounded-xl font-weight-bold shadow-lg">
                                    Unduh Sertifikat Sekarang <i class="fas fa-download ml-2"></i>
                                </button>
                            </form>
                        </div>
                        <div class="modal-footer border-0 bg-light p-3">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
@endsection
