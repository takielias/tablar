@section('body')
    <body class="layout-boxed">
    <div class="page">
        <!-- Top Navbar -->
        @include('tablar::partials.navbar.topbar')
        <div class="page-wrapper">
            <!-- Page Content -->
            @hasSection('content')
                @yield('content')
            @endif
            <!-- Page Error -->
            @include('tablar::error')
            @include('tablar::partials.footer.bottom')
        </div>
    </div>
    </body>
@stop
