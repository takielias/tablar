@php
    $stickyTopClass = config('tablar.sticky_top_nav_bar') ? 'sticky-top' : '';
    $layoutData['cssClasses'] =  'navbar navbar-expand-md '.$stickyTopClass.' d-print-none';
@endphp
@section('body')
    <body>
    <div class="page">
        <!-- Top Navbar -->
        @include('tablar::partials.navbar.topbar')
        <div class="page-wrapper">
            <!-- Page Content -->
            @yield('content')
            @include('tablar::partials.footer.bottom')
        </div>
    </div>
    </body>
@stop
