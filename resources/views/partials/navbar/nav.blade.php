@if($layoutHelper->isLayoutTopnavEnabled())
    @include('tablar::partials.navbar.topbar')
@else
    @include('tablar::partials.navbar.sidebar')
@endif
