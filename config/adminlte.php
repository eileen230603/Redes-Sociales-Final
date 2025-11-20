<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */
    'title' => 'UNI2 Panel',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    */
    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    */
    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    */
    'logo' => '<b>UNI</b>2',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_alt' => 'UNI2 Logo',

    /*
    |--------------------------------------------------------------------------
    | Auth Logo (login/register)
    |--------------------------------------------------------------------------
    */
    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => 'rounded-circle shadow',
            'width' => 70,
            'height' => 70,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    */
    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Cargando UNI2...',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    */
    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => true,
    'usermenu_profile_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => false,

    /*
    |--------------------------------------------------------------------------
    | Auth Views Classes
    |--------------------------------------------------------------------------
    */
    'classes_auth_card' => 'card-outline card-primary shadow-lg',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    */
    'classes_body' => '',
    'classes_brand' => 'bg-primary',
    'classes_brand_text' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */
    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => 'perfil',

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    */
    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items (Sidebar)
    |--------------------------------------------------------------------------
    */
    'menu' => [
        // 🔹 Sección superior
        ['header' => 'NAVEGACIÓN PRINCIPAL'],

        [
            'text' => 'Inicio',
            'url'  => '/home-ong',
            'icon' => 'fas fa-fw fa-home',
        ],
        [
            'text' => 'Eventos',
            'url'  => '/eventos',
            'submenu' => [
                [
                    'text' => 'Lista de Eventos',
                    'url'  => '/ong/eventos',
                ],
                [
                    'text' => 'Dashboard de Eventos',
                    'url'  => '/ong/eventos-dashboard',
                ],
            ],
            'icon' => 'fas fa-calendar-alt',
        ],
        [
            'text' => 'Voluntarios',
            'url'  => '/voluntarios',
            'icon' => 'fas fa-users',
        ],
        [
            'text' => 'Dashboard',
            'url'  => '/ong/dashboard',
            'icon' => 'fas fa-tachometer-alt',
        ],
        [
            'text' => 'Mega Eventos',
            'url'  => '/ong/mega-eventos',
            'icon' => 'fas fa-calendar-check',
        ],
        [
            'text' => 'Reportes',
            'url'  => '/reportes',
            'icon' => 'fas fa-chart-line',
        ],
        [
            'text' => 'Mi Perfil',
            'url'  => '/perfil/ong',
            'icon' => 'fas fa-user-circle',
        ],
        [
            'text' => 'Notificaciones',
            'url'  => '/ong/notificaciones',
            'icon' => 'fas fa-bell',
            'label' => 0,
            'label_color' => 'danger',
        ],
        [
            'text' => 'Configuraciones',
            'icon' => 'fas fa-cogs',
            'submenu' => [
                [
                    'text' => 'Perfil',
                    'url'  => '/perfil/ong',
                    'icon' => 'fas fa-user-circle',
                ],
                [
                    'text' => 'Parámetros',
                    'url'  => '/configuracion',
                    'icon' => 'fas fa-sliders-h',
                ],
            ],
        ],
        ['header' => 'OTRAS OPCIONES'],
        [
            'text' => 'Ir a página pública',
            'url'  => '/home-publica',
            'icon' => 'fas fa-globe-americas',
        ],
        [
            'text' => 'Cerrar sesión',
            'url'  => '/logout',
            'icon' => 'fas fa-sign-out-alt',
            'label_color' => 'danger',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
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
    */
    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'],
                ['type' => 'js', 'asset' => false, 'location' => '//cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js'],
                ['type' => 'css', 'asset' => false, 'location' => '//cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css'],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/chart.js'],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame Mode
    |--------------------------------------------------------------------------
    */
    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    */
    'livewire' => false,
];
