@extends('frontend.index')

@section('title', __('Create new password'))

@section('content')
    <div class="confirm-password">
        <div class="d-flex justify-content-center align-items-center flex-column">
            <img src="{{ asset('/img/icon-key.svg') }}" alt="icon-featured">
            <div class="font-28 font-weight-600 text-uppercase mt-3">
                @lang('Create new password')
            </div>

            <span class="font-16 font-weight-400 text-center color-475467 mt-3">
                @lang('Your new password must be different from your current password')
            </span>

            <div class="w-100 mt-3">
                <form action="{{ route('frontend.auth.password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}"/>
                    <input type="hidden" name="email" id="email" class="form-control"
                           value="{{ $email ?? old('email') }}" maxlength="255" required autofocus
                           autocomplete="email"/>
                    <div>
                        <span class="font-16 font-weight-500">@lang('New password')</span>
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="{{ __('Enter password') }}" maxlength="100" required autocomplete="password"/>
                               @error('password')
                                   <div class="text-danger">{{ $message }}</div>
                               @enderror
                    </div>
                    <div class="mt-2">
                        <span class="font-16 font-weight-500">@lang('Confirm new password')</span>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control"
                               placeholder="{{ __('Confirm new password') }}" maxlength="100" required
                               autocomplete="new-password"/>
                    </div>
                    <button class="btn w-100 mt-3 btn-screen-reset-password" type="submit">
                        @lang('Change the password')
                    </button>
                    <div class="text-center mt-4">
                        <a href="{{ route('frontend.auth.login') }}" class="mt-3 text-decoration-none color-475467">
                            <img src="{{ asset('/img/icon-arrow-left.svg') }}" alt="icon-arrow-left">
                            <span class="font-16 font-weight-500 pl-2">
                                @lang('Back to login')
                            </span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

