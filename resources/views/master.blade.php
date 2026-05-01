<!doctype html>
<html lang="{{ Config::get('app.locale') }}" {!! config('tablar.layout') == 'rtl' ? 'dir="rtl"' : '' !!}>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Theme bootstrap — runs before any CSS to prevent FOUC.
         Supports 'light', 'dark', and 'auto' (follows prefers-color-scheme).

         Syncs our user-facing key `tablar.theme` (light/dark/auto) with
         Tabler core's internal key `tabler-theme` (light/dark) so its
         own theme switcher in tabler-theme.js stays consistent and
         doesn't strip the data-bs-theme attribute on us. --}}
    <script>
        (function () {
            var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

            function resolve(value) {
                if (value === 'auto' || !value) {
                    return media && media.matches ? 'dark' : 'light';
                }
                return value;
            }

            function apply(value) {
                var resolved = resolve(value);
                document.documentElement.setAttribute('data-bs-theme', resolved);
                // Keep Tabler core's storage key in sync so tabler-theme.js
                // (loaded later via Vite) doesn't reset data-bs-theme.
                try {
                    localStorage.setItem('tabler-theme', resolved);
                } catch (e) { /* storage disabled — non-fatal */ }
            }

            apply(localStorage.getItem('tablar.theme') || 'auto');

            if (media && typeof media.addEventListener === 'function') {
                media.addEventListener('change', function () {
                    var current = localStorage.getItem('tablar.theme') || 'auto';
                    if (current === 'auto') {
                        apply(current);
                    }
                });
            }

            window.addEventListener('tablar:theme-change', function (e) {
                if (e && e.detail && typeof e.detail.value === 'string') {
                    apply(e.detail.value);
                }
            });

            // Re-apply after Vite finishes loading Tabler core, since
            // tabler-theme.js may have stripped data-bs-theme during init.
            window.addEventListener('load', function () {
                apply(localStorage.getItem('tablar.theme') || 'auto');
            });

            // Legacy click toggle (preserved): clicking any element with
            // data-bs-theme-value updates localStorage and re-applies the theme.
            document.addEventListener('click', function (e) {
                var trigger = e.target.closest('[data-bs-theme-value]');
                if (!trigger || trigger.tagName === 'INPUT' || trigger.tagName === 'LABEL') {
                    return;
                }
                e.preventDefault();
                var value = trigger.getAttribute('data-bs-theme-value');
                localStorage.setItem('tablar.theme', value);
                apply(value);
            });
        })();
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
