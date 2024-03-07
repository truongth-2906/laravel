@extends('frontend.index')

@section('title', __('Password reset successful'))

@section('content')
    <div class="reset-password-success">
        <div class="d-flex justify-content-center align-items-center flex-column">
            <img src="{{ asset('/img/icon-tick-success.svg') }}" alt="icon-featured">
            <div class="font-28 font-weight-600 text-uppercase mt-3">
                @lang('Change password successfully')
            </div>

            <span class="font-16 font-weight-400 text-center color-475467 mt-3">
                @lang('Your password has been changed successfully.')
            </span>

            <span class="font-16 font-weight-400 text-center color-475467 mt-1">
                @lang('Please log back into the system using the new password.')
            </span>

            <div class="w-100 mt-3">
                <a href="{{ route('frontend.auth.login') }}">
                    <button class="btn w-100 mt-3 btn-screen-reset-password" type="submit">
                        @lang('Back to login')
                    </button>
                </a>
            </div>
        </div>
    </div>
@endsection
