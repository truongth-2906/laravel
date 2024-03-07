@extends('frontend.index')

@section('title', __('Verify Your E-mail Address'))

@section('content')
    <div class="d-flex justify-content-center align-items-center flex-column mt-5">
        <div class="d-flex justify-content-center align-content-center mt-5 verify-email">
            <img src="{{ asset('/img/icon-email.svg') }}" alt="">
        </div>
        <div class="mt-3">
            <h2 class="login-title">@lang('check your email')</h2>
        </div>
        <div class="mt-2 text-center color-000000">
            <span>@lang('We sent a verification link to')</span>
            <br>
            <span>{{ $logged_in_user->email }}</span>
        </div>
        <div class="mt-4">
            <a href="{{ route('frontend.auth.email.verification.step2') }}"
               class="btn btn-general-action hover-button color-2200A5 font-size-16 font-weight-bold pl-5 pr-5">
                <span>@lang('Enter code manually')</span>
            </a>
        </div>
        <div class="d-flex justify-content-center align-items-center mt-4">
            <form action="{{ route('frontend.auth.logout') }}" method="post">
                @csrf
                <button type="submit" class="btn description-color">
                    <img src="{{ asset('/img/arrow-left.svg') }}" alt="">
                    @lang('Back to log in')
                </button>
            </form>
        </div>
    </div>
@endsection
