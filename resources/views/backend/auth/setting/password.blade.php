@extends('backend.layouts.app')

@section('title', __('Setting Password'))

@section('content')
    <div class="setting-password">
        <div class="form-sub-title border-bottom d-flex">
            <div class="font-size-14 primary-color font-weight-600 sub-title">@lang("Setting your password")</div>
        </div>
        <div class="mt-3">
            <form class="form-setting-password color-F9FAFB"
                  action="{{ route('admin.setting.password.update') }}" method="post">
                @csrf
                <div class="d-flex justify-content-start flex-wrap">
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
                            onclick="window.location='{{ route('admin.dashboard') }}'"
                            id="button-cancel">@lang("Cancel")</button>
                    <button type="submit" class="button-base hover-button" id="button-save">@lang("Save")</button>
                </div>
            </form>
        </div>
    </div>
@endsection
