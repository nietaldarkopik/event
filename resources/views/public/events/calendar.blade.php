@extends('layouts.public')

@section('title', 'Kalender Event')

@section('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        :root {
            --wisuda-color: #10b981;
            --workshop-color: #8b5cf6;
            --seminar-color: #f59e0b;
            --rapat-color: #3b82f6;
        }

        /* FullCalendar Premium Styling */
        .fc {
            font-family: 'Poppins', sans-serif;
        }

        .fc-event {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin: 2px 0;
            color: #ffffff !important;
            text-decoration: none !important;
        }

        .fc-event-title,
        .fc-event-time,
        .fc-event-main {
            color: #ffffff !important;
        }

        .fc-event:hover {
            transform: scale(1.03) translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            filter: brightness(1.1);
            z-index: 5;
            color: #ffffff !important;
        }

        .fc-event-title {
            font-weight: 600 !important;
            white-space: normal !important;
            line-height: 1.2;
        }

        .fc-event-time {
            font-weight: 700;
            opacity: 0.9;
        }

        /* Event Type Colors */
        .event-wisuda {
            background: var(--wisuda-color) !important;
        }

        .event-workshop {
            background: var(--workshop-color) !important;
        }

        .event-seminar {
            background: var(--seminar-color) !important;
        }

        .event-rapat {
            background: var(--rapat-color) !important;
        }

        .fc-toolbar-title {
            font-weight: 800 !important;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .fc-button-primary {
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
            border-radius: 12px !important;
            padding: 10px 18px !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s !important;
            text-transform: capitalize !important;
        }

        .fc-button-primary:hover {
            background: #f8fafc !important;
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .fc-button-active {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }

        .calendar-container {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Elegant Filter UI */
        .filter-wrapper {
            margin-top: -50px;
            position: relative;
            z-index: 10;
            margin-bottom: 40px;
        }

        .filter-glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            padding: 20px 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }

        .filter-label {
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .custom-select-premium {
            background-color: #f1f5f9;
            border: 2px solid transparent;
            border-radius: 12px;
            height: 50px;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.3s;
        }

        .custom-select-premium:focus {
            background-color: white;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        /* Hero Section Enhancement */
        .hero-calendar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            padding: 120px 0 140px;
            color: white;
            position: relative;
            border-radius: 0 0 60px 60px;
        }

        .hero-calendar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.15) 0%, transparent 50%);
        }

        /* Legend */
        .legend-item {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #64748b;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
    </style>
@endsection

@section('content')
    <section class="hero-calendar text-center">
        <div class="container" data-aos="zoom-out" data-aos-duration="1000">
            <span class="badge badge-primary px-3 py-2 rounded-pill mb-3 shadow-lg animate__animated animate__fadeInDown"
                style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2);">Schedule
                & Events</span>
            <h1 class="display-3 font-weight-bold mb-3 tracking-tight">Kalender Event</h1>
            <p class="lead opacity-80 mb-0 max-width-600 mx-auto">Pantau jadwal seminar, workshop, dan agenda akademik
                Universitas Winaya Mukti dalam satu tampilan interaktif.</p>
        </div>
    </section>

    <div class="container">
        <!-- Premium Filter -->
        <div class="filter-wrapper" data-aos="fade-up" data-aos-delay="200">
            <div class="filter-glass shadow-2xl">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="filter-label"><i class="far fa-calendar-alt mr-2 text-primary"></i> Pilih
                            Bulan</label>
                        <select id="filterMonth" class="form-control custom-select-premium">
                            <option value="0">Januari</option>
                            <option value="1">Februari</option>
                            <option value="2">Maret</option>
                            <option value="3">April</option>
                            <option value="4">Mei</option>
                            <option value="5">Juni</option>
                            <option value="6">Juli</option>
                            <option value="7">Agustus</option>
                            <option value="8">September</option>
                            <option value="9">Oktober</option>
                            <option value="10">November</option>
                            <option value="11">Desember</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label"><i class="far fa-calendar-check mr-2 text-primary"></i> Pilih
                            Tahun</label>
                        <select id="filterYear" class="form-control custom-select-premium">
                            @php $currentYear = date('Y'); @endphp
                            @for($i = $currentYear - 2; $i <= $currentYear + 3; $i++)
                                <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button id="btnGoTo" class="btn btn-primary-modern btn-block rounded-xl font-weight-bold h-50px"
                            style="height: 50px;">
                            Tampilkan Jadwal <i class="fas fa-search ml-2"></i>
                        </button>
                    </div>
                    <div class="col-md-2 text-md-right mt-3 mt-md-0">
                        <button class="btn btn-outline-light text-primary border-primary rounded-xl h-50px"
                            onclick="location.reload()" style="height: 50px;">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <hr class="my-4" style="opacity: 0.1;">

                <!-- Legend Indicators -->
                <div class="d-flex flex-wrap justify-content-center">
                    <div class="legend-item"><span class="legend-dot" style="background: var(--wisuda-color);"></span>
                        Wisuda</div>
                    <div class="legend-item"><span class="legend-dot" style="background: var(--workshop-color);"></span>
                        Workshop</div>
                    <div class="legend-item"><span class="legend-dot" style="background: var(--seminar-color);"></span>
                        Seminar</div>
                    <div class="legend-item"><span class="legend-dot" style="background: var(--rapat-color);"></span> Rapat
                    </div>
                </div>
            </div>
        </div>

        <div class="calendar-container mb-5" data-aos="fade-up" data-aos-delay="400">
            <div id='calendar'></div>
        </div>
    </div>
@endsection

@section('js')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                height: 'auto',
                dayMaxEvents: 3,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                events: @json($calendarEvents),
                eventClick: function (info) {
                    if (info.event.url) {
                        window.open(info.event.url, "_self");
                        info.jsEvent.preventDefault();
                    }
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false
                },
                datesSet: function (info) {
                    var date = calendar.getDate();
                    document.getElementById('filterMonth').value = date.getMonth();
                    document.getElementById('filterYear').value = date.getFullYear();

                    // Animate title change
                    $('.fc-toolbar-title').addClass('animate__animated animate__fadeIn');
                    setTimeout(() => $('.fc-toolbar-title').removeClass('animate__animated animate__fadeIn'), 500);
                }
            });
            calendar.render();

            document.getElementById('btnGoTo').addEventListener('click', function () {
                var year = document.getElementById('filterYear').value;
                var month = document.getElementById('filterMonth').value;
                var monthStr = (parseInt(month) + 1).toString().padStart(2, '0');
                calendar.gotoDate(year + '-' + monthStr + '-01');

                // Visual feedback
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                setTimeout(() => {
                    this.innerHTML = 'Tampilkan Jadwal <i class="fas fa-search ml-2"></i>';
                }, 600);
            });
        });
    </script>
@endsection