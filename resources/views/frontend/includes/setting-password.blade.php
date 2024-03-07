@extends('frontend.layouts.app')

@section('title', __('Settings'))

@section('content')
    <div class="wrapper h-1080">
        <div class="form-header d-flex justify-content-between align-items-center">
            <div class="d-flex text-header primary-color font-weight-500">
                @lang("Settings")
                @if($logged_in_user->isFreelancer() && ($logged_in_user->isDeclined() || ($logged_in_user->isPending() && is_null($logged_in_user->identity_passbase))))
                    <a href="{{ route('frontend.auth.passbase.index') }}" target="_blank" rel="noopener"
                       class="btn btn-modal-logout btn-margin-top btn-logout-background ml-5"> @lang('Verify now')
                        <img src="{{asset('img/icon-sideways.svg')}}" class="ml-2" alt="">
                    </a>
                @endif
            </div>
        </div>
        @if($logged_in_user->isFreelancer())
            <div class="text-danger mt-2 font-14 font-weight-600">
                {{ $logged_in_user->checkIdentity() }}
            </div>
        @endif
        <div class="form-sub-title border-bottom d-flex">
            <div class="font-size-14 primary-color font-weight-600 sub-title">@lang("Your profile details")</div>
        </div>
        <form class="form-setting-password color-F9FAFB" id="form-setting-password"
              action="{{ route('frontend.user.setting.changePassword') }}" method="post" enctype="multipart/form-data">
            @csrf
            @if($logged_in_user->isEmployer())
                <div
                    class="form-group-function pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                    <div class="form-group-button-title">
                        <p class="font-weight-600 font-size-18 text-color">@lang("Your company info")</p>
                        <div
                            class="font-weight-400 font-size-14 description-color">@lang("Update your company photo and details here.")</div>
                    </div>
                </div>
            @else
                <div
                    class="form-group-function pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                    <div class="form-group-button-title">
                        <p class="font-weight-600 font-size-18 text-color">@lang("Personal info")</p>
                        <div
                            class="font-weight-400 font-size-14 description-color">@lang("Complete your profile and details here.")</div>
                    </div>
                </div>
            @endif
            <div class="form-sub-title tab-setting d-flex border-bottom pb-4">
                <a href="{{ $logged_in_user->isEmployer() ? route('frontend.employer.setting') : route('frontend.freelancer.setting') }}"
                   class="btn tab-base">
                    @lang("My details")
                </a>
                <a href="{{ route('frontend.user.setting.settingPassword') }}" class="btn tab-base active">
                    @lang("Password")
                </a>
                @if($logged_in_user->isFreelancer())
                    <a href="{{ route('frontend.freelancer.available') }}" class="btn tab-base">
                        @lang("Availability")
                    </a>
                @endif
            </div>
            <div class="mt-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title"
                       for="form-input-role">@lang("Current password")</label>
                <div class="form-content form-group-base">
                    <input type="password" name="current_password" id="current-password"
                           class="form-input-name text-color font-size-16"
                           placeholder="">
                    @error('current_password')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mt-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title"
                       for="form-input-role">@lang("New password")</label>
                <div class="form-content form-group-base">
                    <input type="password" name="password" id="new-password"
                           class="form-input-name text-color font-size-16"
                           placeholder="">
                    @error('password')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title"
                       for="form-input-role">@lang("Confirm password")</label>
                <div class="form-content form-group-base">
                    <input type="password" name="confirm_password" id="confirm-password"
                           class="form-input-name text-color font-size-16"
                           placeholder="">
                    @error('confirm_password')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="button-form-password d-flex">
                <button type="button" class="button-base hover-button"
                        onclick="window.location='{{ route('frontend.user.setting.settingPassword') }}'"
                        id="button-cancel">@lang("Cancel")</button>
                <button type="submit" class="button-base hover-button" id="button-save">@lang("Save")</button>
            </div>
        </form>
    </div>
@endsection
