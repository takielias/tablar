@php
    $layoutData['theme'] = 'dark';
    $layoutData['cssClasses'] =  'navbar navbar-vertical navbar-expand-lg';
@endphp
@section('body')
    <body class=" layout-fluid">
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
