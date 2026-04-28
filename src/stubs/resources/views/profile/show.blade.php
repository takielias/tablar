@extends('tablar::page')

@section('title', __('Profile'))

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">{{ __('Profile') }}</h2>
                <div class="text-secondary mt-1">
                    {{ __('Update your personal details.') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success" role="alert">
                {{ __('Profile updated.') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" autocomplete="off">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label" for="name">{{ __('Name') }}</label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('Email') }}</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            value="{{ $user->email }}"
                            readonly>
                        <small class="form-hint">{{ __('Email is managed by the auth system and cannot be changed here.') }}</small>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
