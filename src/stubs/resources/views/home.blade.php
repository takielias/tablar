@extends('tablar::page')

@section('title', 'Dashboard')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard</h2>
                @auth
                    <div class="text-secondary mt-1">
                        Welcome back, {{ auth()->user()->name }}.
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="empty">
                <p class="empty-title">You're all set</p>
                <p class="empty-subtitle text-secondary">
                    Start building your app. Tablar provides the layout, components,
                    and authentication scaffolding — the dashboard chrome is yours.
                </p>
                <div class="empty-action">
                    <a href="https://github.com/takielias/tablar"
                       target="_blank" rel="noopener"
                       class="btn btn-primary">
                        View documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
