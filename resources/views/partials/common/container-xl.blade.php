<div class="container-xl">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
        @include('tablar::partials.common.logo')
    </h1>
    <div class="navbar-nav flex-row order-md-last">

        <div class="nav-item d-none d-md-flex me-3">
            <div class="btn-list">
                @include('tablar::partials.header.header-button')
            </div>
        </div>

        <div class="d-none d-md-flex">
            @include('tablar::partials.header.theme-mode')
            @include('tablar::partials.header.notifications')
        </div>

        @include('tablar::partials.header.top-right')
    </div>
</div>
