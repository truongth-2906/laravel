@extends('mails.app')

@section('content')
    <div class="color-344054 mt-5">
        <p>@lang('Hi '){{ $user->name . ' ,' }}</p>
        <p>@lang('This is your verification code:')</p>
    </div>
    <div class="text-center mt-3">
        <div class="digit-group">
            <input type="text" value="{{ substr($token, 0, 1) }}" readonly />
            <input type="text" value="{{ substr($token, 1, 1) }}" readonly />
            <input type="text" value="{{ substr($token, 2, 1) }}" readonly />
            <input type="text" value="{{ substr($token, 3, 1) }}" readonly />
        </div>
    </div>
    <div class="color-344054 mt-3">
        <p>@lang('This code will only be valid for the next 5 minutes.</br> If the code does not work, you can use this login verification link:')</p>
    </div>
    <div class="mt-5">
        <a href="{{ $url }}" class="btn btn-general-action hover-button color-2200A5 font-size-16">
            <span>@lang('VERIFY EMAIL')</span>
        </a>
    </div>
@endsection
