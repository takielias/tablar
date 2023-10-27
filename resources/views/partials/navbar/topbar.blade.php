@include('tablar::partials.header.top')
<header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    @each('tablar::partials.navbar.dropdown-item', $tablar->menu('sidebar'), 'item')
                </ul>
                {{-- @include('tablar::partials.navbar.search') --}}
            </div>
        </div>
    </div>
</header>
