@extends('tablar::auth.layout')
@section('title', 'Forgot password')
@section('content')
    <div class="container container-tight py-4">
        <div class="text-center mb-1 mt-5">
            <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark">
                <img src="{{asset(config('tablar.auth_logo.img.path','assets/logo.svg'))}}" height="36"
                     alt=""></a>
        </div>
        <form class="card card-md" action="{{ route('password.email') }}" method="post" novalidate>
            @csrf
            <div class="card-body">
                <h2 class="h2 text-center mb-4">@lang('Forgot password')</h2>
                <p class="text-secondary mb-4 text-center">@lang('Enter your email address and we will send you a reset link.')</p>
                <div class="mb-3">
                    <label class="form-label" for="email">@lang('Email address')</label>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        placeholder="your@email.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="off">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">@lang('Send me new password')</button>
                </div>
            </div>
        </form>
        <div class="text-center text-muted mt-3">
            Forget it, <a href="{{ route('login') }}" tabindex="-1">send me back</a> to the sign in screen.
        </div>
    </div>
@endsection
