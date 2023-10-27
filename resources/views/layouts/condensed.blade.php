<body>
@php
    $layoutData['cssClasses'] =  'navbar navbar-expand-md d-print-none';
@endphp

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
