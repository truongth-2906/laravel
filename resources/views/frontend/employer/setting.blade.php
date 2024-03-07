@extends('frontend.layouts.app')

@section('title', __('Settings'))

@section('content')
    <div class="wrapper">
        <div class="form-header d-flex justify-content-between align-items-center">
            <div class="text-header primary-color font-weight-500">@lang('Settings')</div>
        </div>
        <div class="form-sub-title border-bottom d-flex">
            <div class="font-size-14 primary-color font-weight-600 sub-title">@lang('Your profile details')</div>
        </div>
        <form class="form-setting-details color-F9FAFB" id="form-setting-details"
            action="{{ route('frontend.employer.updateDetails', $employer->id) }}" method="post"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="employer_id" value="{{ $employer->id }}">

            <div
                class="form-group-function pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                <div class="form-group-button-title">
                    <p class="font-weight-600 font-size-18 text-color">@lang('Your company info')</p>
                    <div class="font-weight-400 font-size-14 description-color">@lang('Update your company photo and details here.')</div>
                </div>
                <div class="form-group-button d-flex" id="group-button-top">
                    <button type="button" class="button-base hover-button" id="button-cancel"
                        onclick="window.location='{{ route('frontend.employer.setting') }}'">@lang('Cancel')</button>
                    <button type="button" class="button-base hover-button" id="button-save">@lang('Save')</button>
                </div>
            </div>

            <div class="form-sub-title tab-setting d-flex border-bottom pb-4">
                <a href="{{ route('frontend.employer.setting') }}" class="btn tab-base active">
                    @lang('My details')
                </a>
                <a href="{{ route('frontend.user.setting.settingPassword') }}" class="btn tab-base">
                    @lang('Password')
                </a>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="form-select-company"
                    class="font-weight-500 font-size-14 text-color form-title">@lang('Your company name')</label>
                <div class="form-content text-color font-size-16 form-select2-container form-select-company-container">
                    <div class="d-flex justify-content-center align-content-center ">
                        <select name="company_id" id="form-select-company" class="js-states form-control">
                            <option value="">@lang('Please choose one')</option>
                            @foreach (getListCompany() as $company)
                                <option {{ $company->id == old('company_id', $employer->company_id) ? 'selected' : '' }}
                                    value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="flex-center btn btn-general btn-add-company mt-2 ml-2"
                            data-toggle="modal" data-target="#add-company-modal">
                            <img src="{{ asset('/img/add_icon.svg') }}" alt="">
                        </button>
                    </div>
                    @error('company_id')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
                @include('backend.employer.add-modal')
            </div>

            <div class="form-group-name border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title"
                    for="form-input-first-name">@lang('Company representative full name')</label>
                <div class="form-content form-group-input d-flex justify-content-between flex-wrap">
                    <div class="form-group-base half-width">
                        <input type="text" name="firstname" id="form-input-first-name"
                            value="{{ old('firstname', $employer->firstname) }}"
                            class="form-input-name text-color font-size-16" placeholder="">
                        @error('firstname')
                            <div class="text-danger font-14">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group-base half-width">
                        <input type="text" name="lastname" id="form-input-last-name"
                            value="{{ old('lastname', $employer->lastname) }}"
                            class="form-input-name text-color font-size-16" placeholder="">
                        @error('lastname')
                            <div class="text-danger font-14">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-email">@lang("Email
                                                    address")
                    <div class="font-weight-400 font-size-14 description-color pr-4">@lang("Your email address is only
                                                                    visible to the administrators and shared with the public.")
                    </div>
                </label>
                <div class="form-content form-group-base">
                    <input type="text" name="email" id="form-input-email" value="{{ old('email', $employer->email) }}"
                        class="form-input-email text-color font-size-16" placeholder="">
                    @error('email')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title"
                    for="form-input-email">@lang('Phone Number')</label>
                <div class="form-content form-group-base">
                    @include('backend.includes.partials.input-phone-number', [
                        'phone_number' => $employer->phone_number,
                        'calling_code' => $employer->calling_code_id,
                    ])
                    @error('calling_code')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                    @error('phone_number')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <div class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang('Company Logo')</div>
                    <div class="font-weight-400 font-size-14 description-color">@lang('This will be displayed on your profile.')
                    </div>
                </div>
                <div class="form-content form-group-photo d-flex justify-content-between flex-wrap">
                    <div class="photo d-flex justify-content-center align-items-center test">
                        <img id="image-dropzone-photo"
                            src="{{ asset(optional($employer->company)->logo ? optional($employer->company)->avatar : '/img/avatar_default.svg') }}"
                            alt="@lang('Logo company')">
                    </div>
                    <div class="dropzone-content-photo">
                        <div class="profile-image-dropzone form-group-base dropzone-content d-flex flex-column justify-content-between align-items-center w-100"
                            ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
                            <div class="dropzone-content-img d-flex justify-content-center align-items-center">
                                <img src="{{ asset('img/upload-icon.svg') }}" alt="">
                            </div>
                            <div class="description-color font-size-14 font-weight-400">
                                <span class="primary-color font-weight-600 click-text-upload">@lang('Click to upload')</span>
                                @lang('or drag and drop')
                            </div>
                            <div class="description-color font-size-14 font-weight-400">
                                @lang('SVG, PNG, JPG or GIF (max. 800x400px)') </div>
                        </div>
                        <div class="text-danger font-14 d-none error-file-invalid error-dropzone-photo">
                            @lang('File upload format is not correct.')
                        </div>
                        <div class="text-danger font-14 d-none error-size error-dropzone-photo">
                            @lang('Upload file size must not exceed (800x400px).')
                        </div>
                        @error('avatar')
                            <div class="text-danger font-14 error-dropzone-photo">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <input id="dropzone-photo" class="d-none" type="file" name="logo"
                    onchange="changeHandler(this)" />
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="form-select-sector"
                    class="font-weight-500 font-size-14 text-color form-title">@lang('Business sector')</label>
                <div class="form-content text-color font-size-16 form-select2-container form-select-sector-container">
                    <select name="sector_id" id="form-select-sector" class="js-states form-control">
                        <option value="">@lang('Please choose one')</option>
                        @foreach (getListSector() as $sector)
                            <option {{ $sector->id == old('sector_id', $employer->sector_id) ? 'selected' : '' }}
                                value="{{ $sector->id }}">{{ $sector->name }}</option>
                        @endforeach
                    </select>
                    @error('sector_id')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="form-select-country"
                    class="font-weight-500 font-size-14 text-color form-title">@lang('Country')</label>
                <div class="form-content text-color font-size-16 form-select2-container form-select-country-container">
                    <select id="form-select-country" name="country_id" class="js-states form-control">
                        <option value="" class="placeholder-option">@lang('Please choose one')</option>
                        @foreach ($countries as $country)
                            <option data-path="{{ asset('/img/country/' . $country->code . '.png') }}"
                                data-calling-code="{{ $country->calling_code }}"
                                {{ old('country_id', $employer->country_id) == $country->id ? 'selected' : '' }}
                                value="{{ $country->id }}">
                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="form-select-timezone"
                    class="font-weight-500 font-size-14 text-color form-title">@lang('Timezone')</label>
                <div class="form-content text-color font-size-16 form-select2-container form-select-timezone-container">
                    <select id="form-select-timezone" name="timezone_id" class="js-states form-control">
                        <option value="">@lang('Please choose one')</option>
                        @foreach (getListTimezone() as $time)
                            <option {{ $time->id == old('timezone_id', $employer->timezone_id) ? 'selected' : '' }}
                                value="{{ $time->id }}" data-path="{{ asset('img/clock-regular.svg') }}">
                                {{ $time->name }}</option>
                        @endforeach
                    </select>
                    @error('timezone_id')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="editor1" class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang('Bio')</div>
                    <div class="font-weight-400 font-size-14 description-color pr-4">@lang('Tell us about your company.')
                    </div>
                </label>
                <div class="form-content form-group-photo d-flex flex-column">
                    <textarea name="bio" class="full-width form-input-group resize-none" id="editor1" rows="10"
                        cols="80" maxlength="{{ $lengthBio }}">{{ old('bio', $employer->bio) }}</textarea>

                    <div class="count-character font-weight-400 font-size-14 description-color">
                        <div class="number d-inline">
                            {{ old('bio') ? getLengthBio($lengthBio, strlen(old('bio'))) : $lengthBio - strlen(old('bio', $employer->bio)) }}
                        </div> @lang('characters left')
                    </div>

                    <div class="text-danger font-14 d-none error-max-bio">
                        @lang('Enter the limit allowed.')
                    </div>
                    @error('bio')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <div class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang('Company documents')</div>
                    <div class="font-weight-400 font-size-14 description-color pr-4">@lang("These are documents which
                                                                    can be added to your company profile for freelancers information.")
                    </div>
                </div>
                @include('backend.includes.dropzone_file', ['file_uploaded' => $employer->portfolios])
            </div>

            <div class="form-group-button d-flex justify-content-end mt-3">
                <button type="button" class="button-base hover-button" id="button-cancel"
                        onclick="window.location='{{ route('frontend.employer.index') }}'">@lang("Cancel")</button>
                <button type="button" class="button-base hover-button" id="button-save">@lang("Save")</button>
            </div>
        </form>
    </div>
@endsection
