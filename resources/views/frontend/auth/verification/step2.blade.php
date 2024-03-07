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
        <div class="text-center mt-3">
            <form action="{{ route('frontend.auth.email.verification.step3') }}" data-group-name="digits"
                  data-autosubmit="false" autocomplete="off" method="post">
                @csrf
                <div class="digit-group">
                    <input type="text" id="digit-1" name="digit-1" data-next="digit-2" placeholder="-"/>
                    <input type="text" id="digit-2" name="digit-2" data-next="digit-3" data-previous="digit-1"
                           placeholder="-"/>
                    <input type="text" id="digit-3" name="digit-3" data-next="digit-4" data-previous="digit-2"
                           placeholder="-"/>
                    <input type="text" id="digit-4" name="digit-4" data-next="digit-5" data-previous="digit-3"
                           placeholder="-"/>
                </div>
                <div class="mt-4">
                    <button type="submit"
                            class="btn btn-general-action hover-button color-2200A5 font-size-16 pl-5 pr-5 w-100">
                        <span>@lang('VERIFY EMAIL')</span>
                    </button>
                </div>
            </form>
        </div>
        <div class="d-flex justify-content-center align-items-center mt-4">
            <span>@lang('Didn’t receive the email?')</span>
            <form action="{{ route('frontend.auth.email.verification.resend') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-link font-size-14 color-2200A5">
                    @lang('Click to resend')
                </button>
            </form>
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
