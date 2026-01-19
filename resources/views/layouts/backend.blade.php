<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-width="fullwidth" data-menu-styles="light" loader="disable">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ config('app.locale') }}">
    <script>window.APP_LOCALE = '{{ config('app.locale') }}';</script>
    <title>Admin Panel</title>
    <link rel="shortcut icon" href="https://static-cms-sggp.epicdn.me/v4/web/styles/img/favicon.ico" type="image/x-icon">
    
    <!-- Preload fonts CSS sớm -->
    <link rel="preload" as="style" href="{{ Vite::asset('resources/css/fonts.css', 'build/backend') }}">

    <!-- Load fonts CSS -->
    @vite('resources/css/fonts.css', 'build/backend', ['defer' => true])

    @vite([ 
        'resources/css/toastify.css', 
        'resources/css/bootstrap.css', 
        'resources/css/fontawesomepro.css', 
        'resources/css/choices.css', 
        'resources/css/app.css',
    ], 'build/backend')

    @vite('resources/js/jquery.js', 'build/backend') 

    @vite([ 
        'resources/js/toastify.js', 
        'resources/js/app.js', 
        'resources/js/defaultmenu.min.js', 
    ], 'build/backend', ['defer' => true, 'async' => true])

    @stack('styles') 
</head>
<body>
    <div class="page">
        @include('backend.partials.header')

        @include('backend.partials.sidebar')

        <div class="main-content app-content">
            <div class="container-fluid">
                @include('backend.partials.breadcrumb')
                
                @yield('content')
            </div>
        </div>

        @include('backend.partials.footer')
    </div>
    <div id="responsive-overlay"></div>

    @stack('scripts')
</body>
</html>