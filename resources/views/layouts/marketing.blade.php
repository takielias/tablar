@section('body')
    <body class="body-marketing body-gradient">
    @include('tablar::partials.navbar.top-bar')
    @hasSection('content')
        @yield('content')
    @endif
    <!-- Page Error -->
    @include('tablar::error')
    @include('tablar::partials.footer.marketing-bottom')
    </body>
@stop
