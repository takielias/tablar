@extends('tablar::auth.layout')
@section('title', 'Reset password')
@section('content')
    <div class="container container-tight py-4">
        <div class="text-center mb-1 mt-5">
            <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark">
                <img src="{{asset(config('tablar.auth_logo.img.path','assets/logo.svg'))}}" height="36"
                     alt=""></a>
        </div>
        <form class="card card-md" method="POST" action="{{ route('password.request') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">@lang('Reset password')</h2>
                <p class="text-secondary mb-4 text-center">@lang('Set a new password for your account.')</p>

                <div class="mb-3">
                    <label class="form-label" for="email">@lang('Email address')</label>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        placeholder="your@email.com"
                        value="{{ $email ?? old('email') }}"
                        required
                        autofocus
                        autocomplete="off">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">@lang('Password')</label>
                    <div class="input-group input-group-flat">
                        <input
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Password"
                            autocomplete="new-password"
                            required>
                        <a href="#" class="input-group-text link-secondary"
                           data-password-toggle
                           role="button"
                           aria-label="Show password"
                           title="Show password">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" data-icon-show
                                 width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="2"/>
                                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" data-icon-hide
                                 width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/>
                                <path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-4 0 -7.333 -2.333 -10 -7c1.369 -2.395 2.913 -4.175 4.632 -5.341m2.45 -1.32a9.484 9.484 0 0 1 2.918 -.339c4 0 7.333 2.333 10 7c-.778 1.361 -1.612 2.524 -2.503 3.488"/>
                                <path d="M3 3l18 18"/>
                            </svg>
                        </a>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password-confirm">@lang('Confirm Password')</label>
                    <div class="input-group input-group-flat">
                        <input
                            type="password"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            placeholder="Confirm Password"
                            name="password_confirmation"
                            id="password-confirm"
                            autocomplete="new-password">
                        <a href="#" class="input-group-text link-secondary"
                           data-password-toggle
                           role="button"
                           aria-label="Show password"
                           title="Show password">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" data-icon-show
                                 width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="2"/>
                                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" data-icon-hide
                                 width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/>
                                <path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-4 0 -7.333 -2.333 -10 -7c1.369 -2.395 2.913 -4.175 4.632 -5.341m2.45 -1.32a9.484 9.484 0 0 1 2.918 -.339c4 0 7.333 2.333 10 7c-.778 1.361 -1.612 2.524 -2.503 3.488"/>
                                <path d="M3 3l18 18"/>
                            </svg>
                        </a>
                    </div>
                    @error('password_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">@lang('Reset Password')</button>
                </div>
            </div>
        </form>
        <div class="text-center text-muted mt-3">
            <a href="{{ route('login') }}" tabindex="-1">Back to sign in</a>
        </div>
    </div>
@endsection
