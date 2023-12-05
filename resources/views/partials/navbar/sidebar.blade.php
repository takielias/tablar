<!-- Sidebar -->
<aside class="{{$layoutData['cssClasses'] ?? 'navbar navbar-vertical navbar-expand-lg'}}"
       @if(config('tablar.layout_light_sidebar') !== null)
           data-bs-theme="{{ config('tablar.layout_light_sidebar') ? 'light' : 'dark' }}"
    @endif
>
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            @include('tablar::partials.common.logo')
        </h1>
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item d-none d-lg-flex me-3">
                <div class="btn-list">
                    @include('tablar::partials.header.header-button')
                </div>
            </div>
            <div class="d-none d-lg-flex">
                @include('tablar::partials.header.theme-mode')
                @include('tablar::partials.header.notifications')
            </div>
            @include('tablar::partials.header.top-right')
        </div>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                @each('tablar::partials.navbar.dropdown-item',$tablar->menu('sidebar'), 'item')
            </ul>
        </div>
    </div>
</aside>

@if(config('tablar.layout_enable_top_header'))
    @include('tablar::partials.header.sidebar-top')
@endif
