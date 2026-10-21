<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - EVENTUNWIM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-unwim.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 4 (AdminLTE Dependencies) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- Deferred CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" media="print" onload="this.media='all'">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" media="print" onload="this.media='all'">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom-public.css') }}">
    @yield('css')
</head>

<body class="bg-light">

    <!-- Navbar -->
    <!-- Navbar -->
    @unless(isset($hideMenu) && $hideMenu)
        <nav class="navbar navbar-expand-lg sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}"><img src="{{ asset('images/logo-unwim.png') }}" alt="Logo UNWIM" style="height:36px;width:auto;margin-right:8px">EVENT<span>UNWIM</span></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/events') }}">Jelajah Event</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('events.calendar') }}">Kalender</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/cek-sertifikat') }}">Cek Sertifikat</a></li>

                    </ul>
                </div>
            </div>
        </nav>
    @endunless

    @yield('content')

    <!-- Footer -->
    <!-- Footer -->
    @unless(isset($hideMenu) && $hideMenu)
        <footer class="footer">
            <div class="container text-center">
                <h5>Universitas Winaya Mukti</h5>
                <p class="mb-0">Platform Manajemen Event & Sertifikasi Digital</p>
                <small class="text-muted text-light">&copy; {{ date('Y') }} EVENTUNWIM. All rights reserved.</small>
            </div>
        </footer>
    @endunless

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
    <script defer>
        document.addEventListener("DOMContentLoaded", function() {
            AOS.init({
                duration: 800,
                once: true
            });
        });
    </script>
    @yield('js')
</body>

</html>