@extends('tablar::page')

@section('title', __('Profile'))

@section('content')
    <div class="container container-tight py-4">
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success" role="alert">
                {{ __('Profile updated.') }}
            </div>
        @endif

        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 mb-4">{{ __('Profile') }}</h2>

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
@endsection
