<!doctype html>
<html lang="{{ Config::get('app.locale') }}" {!! config('tablar.layout') == 'rtl' ? 'dir="rtl"' : '' !!}>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Theme bootstrap — runs before any CSS to prevent FOUC. --}}
    <script>
        (function () {
            var saved = localStorage.getItem('tablar.theme');
            var theme = saved || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-bs-theme-value]');
            if (!trigger) return;
            e.preventDefault();
            var value = trigger.getAttribute('data-bs-theme-value');
            document.documentElement.setAttribute('data-bs-theme', value);
            localStorage.setItem('tablar.theme', value);
        });
    </script>

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')
    {{-- Title --}}
    <title>
        @yield('title_prefix', config('tablar.title_prefix', ''))
        @yield('title', config('tablar.title', 'Tablar'))
        @yield('title_postfix', config('tablar.title_postfix', ''))
    </title>

    <!-- CSS/JS files -->
    @if(config('tablar','vite'))
        @vite('resources/js/app.js')
    @endif

    {{-- Livewire Styles --}}
    @if(config('tablar.livewire'))
        @livewireStyles
    @endif

    {{-- Custom Stylesheets (post Tablar) --}}
    @yield('tablar_css')

</head>
@yield('body')
@include('tablar::extra.modal')

{{-- Livewire Script --}}
@if(config('tablar.livewire'))
    @livewireScripts
@endif

@yield('tablar_js')
</html>
