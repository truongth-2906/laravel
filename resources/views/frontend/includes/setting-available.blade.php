@extends('frontend.layouts.app')

@section('title', __('Settings'))

@section('content')
    <div class="wrapper h-1080">
        <div class="form-header d-flex justify-content-between align-items-center">
            <div class="d-flex text-header primary-color font-weight-500">
                @lang("Settings")
                @if($logged_in_user->isDeclined() || ($logged_in_user->isPending() && is_null($logged_in_user->identity_passbase)))
                    <a href="{{ route('frontend.auth.passbase.index') }}" target="_blank" rel="noopener"
                       class="btn btn-modal-logout btn-margin-top btn-logout-background ml-5"> @lang('Verify now')
                        <img src="{{asset('img/icon-sideways.svg')}}" class="ml-2" alt="">
                    </a>
                @endif
            </div>
        </div>
        <div class="text-danger mt-2 font-14 font-weight-600">
            {{ $logged_in_user->checkIdentity() }}
        </div>
        <div class="form-sub-title border-bottom d-flex">
            <div class="font-size-14 primary-color font-weight-600 sub-title">@lang("Your profile details")</div>
        </div>
        <form class="form-setting-available color-F9FAFB" id="form-setting-available"
              action="{{ route('frontend.freelancer.setting-available') }}" method="post">
            @csrf
            <input type="hidden" name="id" value="{{ $freelancer->id }}">
            <div
                class="form-group-function pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                <div class="form-group-button-title">
                    <p class="font-weight-600 font-size-18 text-color">@lang("Personal info")</p>
                    <div
                        class="font-weight-400 font-size-14 description-color">@lang("Complete your profile and details here.")</div>
                </div>
            </div>

            <div class="form-sub-title tab-setting d-flex border-bottom pb-4">
                <a href="{{ route('frontend.freelancer.setting') }}" class="btn tab-base">
                    @lang("My details")
                </a>
                <a href="{{ route('frontend.user.setting.settingPassword') }}" class="btn tab-base">
                    @lang("Password")
                </a>
                <a href="{{ route('frontend.freelancer.available') }}" class="btn tab-base active">
                    @lang("Availability")
                </a>
            </div>

            <div class=" d-flex pb-20 justify-content-start align-items-center flex-wrap">
                <label for="editor1" class="form-group-button-title form-title m-md-0 m-sm-2">
                    <div class="font-weight-500 font-size-14 text-color">@lang("Available for work?")</div>
                </label>
                <div class="form-content text-color font-size-16 ">
                    <button type="button"
                            class="btn btn-secondary btn-toggle btn-available {{ old('available',$freelancer->available) == 1 ? 'active' : '' }}"
                            data-toggle="button"
                            aria-pressed="{{ old('available',$freelancer->available) == 1 ? 'true' : 'false' }}"
                            autocomplete="off">
                        <div class="handle"></div>
                    </button>
                    <input type="hidden" name="available" value="{{ old('available',$freelancer->available) }}">
                </div>
            </div>

            <div class="d-flex pb-20 justify-content-start align-items-center flex-wrap">
                <label for="editor1" class="form-group-button-title form-title m-md-0 m-sm-2">
                    <div class="font-weight-500 font-size-14 text-color">@lang("Hours per project")</div>
                </label>
                <div class="d-flex form-content form-group-input flex-wrap">
                    <div class="d-flex form-group-base justify-content-start align-items-center w-100">
                        <input type="number" name="hours" id="form-input-first-name"
                               value="{{ old('hours', $freelancer->hours) }}"
                               class="form-input-name text-color font-size-16 w-100" placeholder="Input total hours"
                               min="1"
                               max="168">
                        <span class="d-flex color-000000 w-50 ml-3">@lang('per week')</span>
                    </div>
                    @error('hours')
                    <div class="text-danger font-14 mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex pb-20 border-bottom justify-content-start align-items-center flex-wrap">
                <label for="form-input-rate_per_hours" class="form-group-button-title form-title m-md-0 m-sm-2">
                    <div class="font-weight-500 font-size-14 text-color">@lang("Rate per hours")</div>
                </label>
                <div class="d-flex form-content form-group-input flex-wrap">
                    <div class="d-flex form-group-base justify-content-start align-items-center w-100">
                        <input type="number" name="rate_per_hours" id="form-input-rate_per_hours"
                               value="{{ old('rate_per_hours', $freelancer->rate_per_hours) }}"
                               class="form-input-name text-color font-size-16 w-100" placeholder="$ Input rate eg. 15"
                               min="15">
                        <span class="d-flex color-000000 w-50 ml-3">@lang('min of $15 per hours')</span>
                    </div>
                    @error('rate_per_hours')
                    <div class="text-danger font-14 mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="button-form-available d-flex justify-content-end mt-3">
                <button type="button" class="button-base hover-button"
                        onclick="window.location='{{ route('frontend.freelancer.available') }}'"
                        id="button-cancel">@lang("Cancel")</button>
                <button type="submit" class="button-base hover-button" id="button-save">@lang("Save")</button>
            </div>
        </form>
    </div>
@endsection
