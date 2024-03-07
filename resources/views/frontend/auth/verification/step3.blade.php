@extends('frontend.index')

@section('title', __('Verify Your E-mail Address'))

@section('content')
    <div class="d-flex justify-content-center align-items-center flex-column mt-5">
        <div class="d-flex justify-content-center align-content-center mt-5 verified-email">
            <img src="{{ asset('/img/verify-email.svg') }}" alt="">
        </div>
        <div class="mt-3">
            <h2 class="login-title">@lang('Email verified')</h2>
        </div>
        <div class="mt-2 text-center color-000000">
            <span>@lang('Thank you for verifying your account. Click on')</span>
            <br>
            <span>@lang('the button below to login.')</span>
        </div>
        <div class="mt-4">
            <a href="{{ route('frontend.auth.login') }}"
               class="btn btn-general-action hover-button color-2200A5 font-size-16 pl-5 pr-5 w-100">
                <span>@lang('LOGIN')</span>
            </a>
        </div>
    </div>
@endsection
