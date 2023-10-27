<body>
@php
    $layoutData['cssClasses'] =  'navbar navbar-vertical navbar-expand-lg';
@endphp
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
