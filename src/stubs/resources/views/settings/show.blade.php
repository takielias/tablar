@extends('tablar::page')

@section('title', __('Settings'))

@section('content')
    <div class="container container-tight py-4">
        @if (session('status') === 'password-updated')
            <div class="alert alert-success" role="alert">
                {{ __('Password updated.') }}
            </div>
        @endif

        {{-- Appearance --}}
        <div class="card card-md mb-3">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ __('Appearance') }}</h3>
                <p class="text-secondary mb-3">{{ __('Choose how Tablar looks. System follows your operating system.') }}</p>
                @include('tablar::partials.settings.appearance')
            </div>
        </div>

        {{-- Update password --}}
        <div class="card card-md mb-3">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ __('Update password') }}</h3>

                <form method="POST" action="{{ route('settings.password') }}" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="current_password">{{ __('Current password') }}</label>
                        <input
                            type="password"
                            class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                            id="current_password"
                            name="current_password"
                            autocomplete="current-password"
                            required>
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">{{ __('New password') }}</label>
                        <input
                            type="password"
                            class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            required>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">{{ __('Confirm new password') }}</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            required>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Save password') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete account --}}
        <div class="card card-md border-danger">
            <div class="card-body">
                <h3 class="card-title text-danger mb-3">{{ __('Delete account') }}</h3>
                <p class="text-secondary mb-3">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently removed. Confirm your password to proceed.') }}
                </p>

                <form method="POST" action="{{ route('settings.destroy') }}" autocomplete="off">
                    @csrf
                    @method('DELETE')

                    <div class="mb-3">
                        <label class="form-label" for="delete_password">{{ __('Password') }}</label>
                        <input
                            type="password"
                            class="form-control @error('password', 'deleteAccount') is-invalid @enderror"
                            id="delete_password"
                            name="password"
                            autocomplete="current-password"
                            required>
                        @error('password', 'deleteAccount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-danger">{{ __('Delete my account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
