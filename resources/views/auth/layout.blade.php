<!doctype html>
<html lang="{{ Config::get('app.locale') }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- CSS/JS files -->
    @if(config('tablar','vite'))
        @vite('resources/js/app.js')
    @endif
    {{-- Custom Stylesheets (post Tablar) --}}
    @yield('tablar_css')

</head>
<body class=" border-top-wide border-primary d-flex flex-column">
<div class="page page-center">
    @yield('content')
</div>

<script>
    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-password-toggle]');
        if (!trigger) return;
        event.preventDefault();

        var group = trigger.closest('.input-group');
        if (!group) return;
        var input = group.querySelector('input[type="password"], input[data-password-input]');
        if (!input) return;

        var showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';

        var eye = trigger.querySelector('[data-icon-show]');
        var eyeOff = trigger.querySelector('[data-icon-hide]');
        if (eye && eyeOff) {
            eye.style.display = showing ? '' : 'none';
            eyeOff.style.display = showing ? 'none' : '';
        }
        trigger.setAttribute('title', showing ? 'Show password' : 'Hide password');
        trigger.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
</script>

@yield('tablar_js')

</html>
