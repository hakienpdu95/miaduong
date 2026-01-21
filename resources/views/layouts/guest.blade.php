<!DOCTYPE html>
<html lang="vi" class="anoi">
<head>
    <title>@yield('title', config('app.name', 'Cổng thông tin'))</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/web/styles/img/favicon.jpg') }}" type="image/x-icon">

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
    @livewireStyles
    @turnstileScripts
</head>
<body>
    <div class="site-container">
        @include('layouts.partials.header')
        <main class="py-4">
            {{ $slot }}
        </main>
        @include('layouts.partials.footer')
        @stack('scripts')
        @livewireScripts
    </div>
    <script>
        window.socialConfig = {
            google: {
                redirect: "{{ route('social.redirect', 'google') }}?popup=1",
            },
            facebook: {
                redirect: "{{ route('social.redirect', 'facebook') }}?popup=1",
            },
            zalo: {
                redirect: "{{ route('social.redirect', 'zalo') }}?popup=1",
            }
        };
    </script>
</body>
</html>