<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title'); ?> - EVENTUNWIM</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo-unwim.png')); ?>">

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
    <link rel="stylesheet" href="<?php echo e(asset('css/custom-public.css')); ?>">
    <?php echo $__env->yieldContent('css'); ?>
</head>

<body class="bg-light">

    <!-- Navbar -->
    <!-- Navbar -->
    <?php if (! (isset($hideMenu) && $hideMenu)): ?>
        <nav class="navbar navbar-expand-lg sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo e(url('/')); ?>"><img src="<?php echo e(asset('images/logo-unwim.png')); ?>" alt="Logo UNWIM" style="height:36px;width:auto;margin-right:8px">EVENT<span>UNWIM</span></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('/')); ?>">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('/events')); ?>">Jelajah Event</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo e(route('events.calendar')); ?>">Kalender</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('/cek-sertifikat')); ?>">Cek Sertifikat</a></li>

                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>

    <!-- Footer -->
    <!-- Footer -->
    <?php if (! (isset($hideMenu) && $hideMenu)): ?>
        <footer class="footer">
            <div class="container text-center">
                <h5>Universitas Winaya Mukti</h5>
                <p class="mb-0">Platform Manajemen Event & Sertifikasi Digital</p>
                <small class="text-muted text-light">&copy; <?php echo e(date('Y')); ?> EVENTUNWIM. All rights reserved.</small>
            </div>
        </footer>
    <?php endif; ?>

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
    <?php echo $__env->yieldContent('js'); ?>
</body>

</html><?php /**PATH /home/server/domains/event.unwim.ac.id/public_html/resources/views/layouts/public.blade.php ENDPATH**/ ?>