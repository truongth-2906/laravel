@extends('frontend.index')

@section('title', __('Reset Password'))

@section('content')
    <div class="reset-password">
        <div class="d-flex justify-content-center align-items-center flex-column">
            <img src="{{ asset('/img/icon-key.svg') }}" alt="icon key">
            <div class="font-30 font-weight-600 text-uppercase mt-3">
                @lang('FORGOT PASSWORD?')
            </div>

            <span class="font-16 font-weight-400 text-center color-475467 mt-3">
                @lang('Please enter the email you used to register your account and we will resend the link so you can create your new password.')
            </span>

            <div class="w-100 mt-3">
                <form action="{{ route('frontend.auth.password.email') }}" method="POST">
                    @csrf
                    <span class="font-14 font-weight-600 color-344054">
                        @lang('Email')
                    </span>
                    <input type="email" name="email" id="email" class="form-control"
                           value="{{ old('email') }}" placeholder="{{ __('Enter email') }}"
                           maxlength="255" required autofocus autocomplete="email"/>
                    <button class="btn w-100 mt-3 btn-screen-reset-password" type="submit">
                        @lang('Send Password Reset Link')
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
