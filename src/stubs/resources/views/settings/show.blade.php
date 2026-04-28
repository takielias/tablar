@extends('tablar::page')

@section('title', __('Settings'))

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">{{ __('Settings') }}</h2>
                <div class="text-secondary mt-1">
                    {{ __('Manage appearance, password, and account state.') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if (session('status') === 'password-updated')
            <div class="alert alert-success" role="alert">
                {{ __('Password updated.') }}
            </div>
        @endif

        @php($activeTab = old('_tab', request()->query('tab', 'appearance')))

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#tab-appearance"
                           class="nav-link {{ $activeTab === 'appearance' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           role="tab"
                           aria-selected="{{ $activeTab === 'appearance' ? 'true' : 'false' }}">
                            <i class="ti ti-palette me-1"></i>{{ __('Appearance') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab-password"
                           class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           role="tab"
                           aria-selected="{{ $activeTab === 'password' ? 'true' : 'false' }}">
                            <i class="ti ti-lock me-1"></i>{{ __('Password') }}
                        </a>
                    </li>
                    <li class="nav-item ms-auto" role="presentation">
                        <a href="#tab-danger"
                           class="nav-link text-danger {{ $activeTab === 'danger' ? 'active' : '' }}"
                           data-bs-toggle="tab"
                           role="tab"
                           aria-selected="{{ $activeTab === 'danger' ? 'true' : 'false' }}">
                            <i class="ti ti-trash me-1"></i>{{ __('Delete account') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">

                    {{-- Appearance --}}
                    <div class="tab-pane {{ $activeTab === 'appearance' ? 'active show' : '' }}" id="tab-appearance" role="tabpanel">
                        <p class="text-secondary mb-3">{{ __('Choose how Tablar looks. System follows your operating system.') }}</p>
                        @include('tablar::partials.settings.appearance')
                    </div>

                    {{-- Password --}}
                    <div class="tab-pane {{ $activeTab === 'password' ? 'active show' : '' }}" id="tab-password" role="tabpanel">
                        <form method="POST" action="{{ route('settings.password') }}" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_tab" value="password">

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

                    {{-- Danger zone --}}
                    <div class="tab-pane {{ $activeTab === 'danger' ? 'active show' : '' }}" id="tab-danger" role="tabpanel">
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-title">{{ __('Delete account') }}</h4>
                            <div class="text-secondary">
                                {{ __('Once your account is deleted, all of its resources and data will be permanently removed. Confirm your password to proceed.') }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('settings.destroy') }}" autocomplete="off">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="_tab" value="danger">

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
        </div>
    </div>
</div>
@endsection
