@extends('frontend.index')

@section('title', __('Resend Password Reset Link'))

@section('content')
    <div class="resend-password">
        <div class="d-flex justify-content-center align-items-center flex-column">
            <img src="{{ asset('/img/icon-email-rs.svg') }}" alt="icon-email">
            <div class="font-30 font-weight-600 text-uppercase mt-3">
                @lang('CHECK YOUR MAIL')
            </div>
            <div class="text-center">
                <span class="text-center font-16 font-weight-400 color-475467 mt-3">
                    @lang('We have sent the link to create a new password to your email.')
                </span>
                <p class="font-16 font-weight-600">
                    {{ $email }}
                </p>
            </div>

            <div class="w-100 mt-3">
                <form action="{{ route('frontend.auth.password.email') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" id="email" class="form-control"
                           value="{{ $email }}" placeholder="{{ __('Enter email') }}"
                           maxlength="255" required autofocus autocomplete="email"/>
                    <div class="d-flex justify-content-center align-items-center">
                        <span class="font-14 font-weight-400 color-475467">
                            @lang('Not receiving email?')
                        </span>
                        <button type="submit" class="btn font-14 font-weight-600 color-0E65B0 btn-resend-mail">
                            @lang('Click here to resend.')
                        </button>
                    </div>
                    <div class="d-flex justify-content-center align-items-center font-14 font-weight-600 text-center">
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
