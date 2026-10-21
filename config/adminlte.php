<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'EVENT UNWIM',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>EVENT</b>UNWIM',
    'logo_img' => 'images/logo-unwim.png',
    'logo_img_class' => 'brand-image',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Logo UNWIM',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'images/logo-unwim.png',
            'alt' => 'Logo UNWIM',
            'class' => '',
            'width' => 80,
            'height' => 80,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Menu yang SENGAJA disembunyikan karena memakai modul `registrations` lama (kosong, tidak dipakai alur pendaftaran
        // sekarang yang tersimpan di `participants`): Registrasi Event (admin/registrations), QR Code Tiket (admin/qr-codes),
        // Log Kehadiran (admin/attendance-logs; log yang dipakai ada di Scan Kehadiran). Halamannya tetap bisa dibuka lewat URL.
        // Untuk menampilkan lagi, tambahkan entri ['text' => ..., 'url' => <url di atas>, 'can' => 'admin_event', 'icon' => ...].
        [
            'text' => 'Dashboard',
            'url' => 'admin/dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        ['header' => 'ORGANIZATION', 'can' => 'super_admin'],
        [
            'text' => 'Organisasi',
            'icon' => 'fas fa-fw fa-building',
            'can' => 'super_admin',
            'url' => 'admin/organizations',
        ],
        [
            'text' => 'Panitia Event',
            'icon' => 'fas fa-fw fa-user-shield',
            'can' => 'super_admin',
            'url' => 'admin/event-committees',
        ],
        ['header' => 'EVENT MANAGEMENT', 'can' => 'admin_event'],
        [
            'text' => 'Kategori Event',
            'icon' => 'fas fa-fw fa-tags',
            'can' => 'admin_event',
            'url' => 'admin/event-categories',
        ],
        [
            'text' => 'Event',
            'icon' => 'fas fa-fw fa-calendar-alt',
            'can' => 'admin_event',
            'url' => 'admin/events',
        ],
        [
            'text' => 'Sesi Event',
            'icon' => 'fas fa-fw fa-clock',
            'can' => 'admin_event',
            'url' => 'admin/event-sessions',
        ],
        [
            'text' => 'Pembicara',
            'icon' => 'fas fa-fw fa-user-tie',
            'can' => 'admin_event',
            'url' => 'admin/speakers',
        ],
        ['header' => 'REGISTRATION & TICKETS', 'can' => 'admin_event'],
        [
            'text' => 'Jenis Tiket',
            'icon' => 'fas fa-fw fa-ticket-alt',
            'can' => 'admin_event',
            'url' => 'admin/ticket-types',
        ],
        [
            'text' => 'Template Undangan',
            'icon' => 'fas fa-fw fa-palette',
            'can' => 'admin_event',
            'url' => 'admin/invitation-templates',
        ],
        [
            'text' => 'Peserta',
            'icon' => 'fas fa-fw fa-user-friends',
            'can' => 'admin_event',
            'url' => 'admin/participants',
        ],
        ['header' => 'PAYMENTS & FINANCE', 'can' => 'admin_keuangan'],
        [
            'text' => 'Pembayaran',
            'icon' => 'fas fa-fw fa-money-bill-wave',
            'can' => 'admin_keuangan',
            'url' => 'admin/finance',
        ],
        ['header' => 'ATTENDANCE', 'can' => 'admin_event'],
        [
            'text' => 'Scan Kehadiran',
            'icon' => 'fas fa-fw fa-camera',
            'can' => 'admin_event',
            'url' => 'admin/attendance',
        ],
        [
            'text' => 'Log WhatsApp',
            'icon' => 'fab fa-fw fa-whatsapp',
            'can' => 'admin_event',
            'url' => 'admin/logs/whatsapp',
        ],
        ['header' => 'CERTIFICATES', 'can' => 'admin_sertifikat'],
        [
            'text' => 'Sertifikat',
            'icon' => 'fas fa-fw fa-award',
            'can' => 'admin_sertifikat',
            'submenu' => [
                ['text' => 'Penerbitan', 'url' => 'admin/certificate-generator', 'icon' => 'fas fa-fw fa-magic', 'can' => 'admin_sertifikat'],
                ['text' => 'Sertifikat Terbit', 'url' => 'admin/certificates', 'icon' => 'fas fa-fw fa-list', 'can' => 'admin_sertifikat'],
                ['text' => 'Template Sertifikat', 'url' => 'admin/certificate-templates', 'icon' => 'fas fa-fw fa-drafting-compass', 'can' => 'admin_sertifikat'],
                ['text' => 'Galeri Media', 'url' => 'admin/certificate-media', 'icon' => 'fas fa-fw fa-images', 'can' => 'admin_sertifikat'],
            ],
        ],
        ['header' => 'PROMOTION & SPONSOR', 'can' => 'admin_event'],
        [
            'text' => 'Sponsor',
            'icon' => 'fas fa-fw fa-handshake',
            'can' => 'admin_event',
            'url' => 'admin/sponsors',
        ],
        [
            'text' => 'Galeri Foto',
            'icon' => 'fas fa-fw fa-images',
            'can' => 'admin_event',
            'url' => 'admin/galleries',
        ],
        [
            'text' => 'Pengumuman',
            'icon' => 'fas fa-fw fa-bullhorn',
            'can' => 'admin_event',
            'url' => 'admin/announcements',
        ],
        ['header' => 'SETTINGS', 'can' => 'manage_settings'],
        [
            'text' => 'Manajemen User & Hak Akses',
            'url' => 'admin/settings/users',
            'icon' => 'fas fa-fw fa-users-cog',
            'can' => 'super_admin',
        ],
        [
            'text' => 'Manajemen Role & Permission',
            'url' => 'admin/settings/roles',
            'icon' => 'fas fa-fw fa-user-shield',
            'can' => 'super_admin',
        ],
        [
            'text' => 'Pengaturan Aplikasi',
            'url' => 'admin/settings',
            'icon' => 'fas fa-fw fa-sliders-h',
            'can' => 'super_admin',
        ],
        [
            'text' => 'Email Settings',
            'url' => 'admin/email-settings',
            'icon' => 'fas fa-fw fa-envelope',
            'can' => 'super_admin',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'UnwimTheme' => [
            'active' => true,
            'files' => [
                ['type' => 'css', 'asset' => true, 'location' => 'css/admin-theme.css'],
                ['type' => 'js', 'asset' => true, 'location' => 'js/admin-tidy.js'],
            ],
        ],
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
