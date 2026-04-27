@extends('tablar::page')

@section('title', 'Welcome to Tablar')

@section('content')
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark">
                <img src="{{ asset('assets/static/logo.svg') }}" height="36" alt="Tablar"
                     onerror="this.style.display='none'">
            </a>
        </div>

        <div class="card card-md">
            <div class="card-body text-center">
                <h2 class="h2 mb-3">Welcome to Tablar</h2>
                <p class="text-secondary mb-4">
                    A Laravel dashboard preset built on
                    <a href="https://tabler.io" target="_blank" rel="noopener">Tabler</a>.
                </p>

                @auth
                    <a href="{{ url('/home') }}" class="btn btn-primary w-100">
                        Go to Dashboard
                    </a>
                @else
                    <div class="row g-2">
                        @if (Route::has('login'))
                            <div class="col">
                                <a href="{{ route('login') }}" class="btn btn-primary w-100">Login</a>
                            </div>
                        @endif
                        @if (Route::has('register'))
                            <div class="col">
                                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Register</a>
                            </div>
                        @endif
                    </div>
                @endauth
            </div>
        </div>

        <div class="text-center text-secondary mt-4">
            Laravel v{{ Illuminate\Foundation\Application::VERSION }}
        </div>
    </div>
</div>
@endsection
