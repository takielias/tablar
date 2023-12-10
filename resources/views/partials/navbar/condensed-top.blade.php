<!-- Navbar -->

<header class="{{$layoutData['cssClasses'] ?? 'navbar navbar-expand-md d-print-none'}}"
        @if(config('tablar.layout_light_topbar') !== null)
            data-bs-theme="{{ config('tablar.layout_light_topbar') ? 'light' : 'dark' }}"
    @endif
>
    @include('tablar::partials.common.condensed-container-xl')
</header>
