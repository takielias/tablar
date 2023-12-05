<div class="container-xl">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
        @include('tablar::partials.common.logo')
    </h1>
    <div class="navbar-nav flex-row order-md-last">
        <div class="d-none d-md-flex">
            @include('tablar::partials.header.theme-mode')
            @include('tablar::partials.header.notifications')
        </div>
        @include('tablar::partials.header.top-right')
    </div>
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
            <ul class="navbar-nav">
                @each('tablar::partials.navbar.dropdown-item', $tablar->menu('sidebar'), 'item')
            </ul>
        </div>
    </div>
</div>
