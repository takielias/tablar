@php
    $layoutData['cssClasses'] =  'navbar navbar-vertical navbar-expand-lg navbar-transparent';
@endphp
@section('body')
    <body>
    <div class="page">
        <!-- Sidebar -->
        @include('tablar::partials.navbar.sidebar')
        <div class="page-wrapper">
            <!-- Page Content -->
            @yield('content')
            @include('tablar::partials.footer.bottom')
        </div>
    </div>
    </body>
@stop
