<!DOCTYPE html>
<html lang="vi">
<head>    
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="refresh" content="1800">
    <meta name="revisit-after" content="1 days">
    <meta http-equiv="content-language" content="vi">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="address=no">
    <meta name="apple-mobile-web-os-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="referrer" content="no-referrer-when-downgrade">

    <!-- Preload fonts CSS sớm -->
    <link rel="preload" as="style" href="{{ Vite::asset('resources/css/fonts.css', 'build/frontend') }}">

    <!-- Load fonts CSS -->
    @vite('resources/css/fonts.css', 'build/frontend', ['defer' => true])

    @vite([
        'resources/css/bootstrap.css',
        request()->device === 'mobile' ? 'resources/css/mobile/main.css' : 'resources/css/web/main.css',
    ], 'build/frontend', ['defer' => true, 'async' => true])
    @vite([
        'resources/js/jquery.js',
        'resources/js/lazysizes.js',  
        request()->device === 'mobile' ? 'resources/js/mobile/main.js' : 'resources/js/web/main.js',
    ], 'build/frontend', ['defer' => true, 'async' => true])

    @stack('styles')
    @turnstileScripts
</head>
<body>
    <div class="site-container">
        <main>
            @yield('content')
        </main>
        @stack('scripts')
    </div>
</body>
</html>