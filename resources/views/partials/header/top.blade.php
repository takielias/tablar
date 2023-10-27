<header class="{{$layoutData['cssClasses'] ?? 'navbar navbar-expand-md navbar-light d-print-none'}}"
        @if(config('tablar.layout_light_topbar') !== null)
            data-bs-theme="{{ config('tablar.layout_light_topbar') ? 'light' : 'dark' }}"
    @endif
>
    @include('tablar::partials.common.container-xl')
</header>
