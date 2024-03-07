@extends('backend.layouts.app')

@section('title', __('Create Employer'))

@section('content')
    <div class="wrapper">
        <form class="form-freelancer" id="form-freelancer" method="post" enctype="multipart/form-data"
              action="{{ route('admin.employer.store') }}">
            @csrf
            <input type="text" class="d-none" name="type" value="{{ $type }}">

            <div class="form-header d-flex justify-content-between align-items-center">
                <div class="text-header primary-color font-weight-500">@lang("CREATE EMPLOYER")</div>
            </div>
            <div class="form-sub-title border-bottom d-flex">
                <div class="font-size-14 primary-color font-weight-600 sub-title">@lang("Employer Details")</div>
            </div>

            <div
                class="form-group-function border-bottom pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                <div class="form-group-button-title">
                    <p class="font-weight-600 font-size-18 text-color">@lang("Employer company info")</p>
                    <div class="font-weight-400 font-size-14 description-color">@lang("Update employer company photo and
                    details here.")
                    </div>
                </div>
                <div class="form-group-button d-flex">
                    <button type="button" class="button-base btn-general-action hover-button" id="button-cancel"
                            onclick="window.location='{{ route('admin.employer.index') }}'">
                        @lang("Cancel")
                    </button>
                    <button type="button" class="button-base btn-general-action hover-button" id="button-save">@lang("Save")</button>
                </div>
            </div>
            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="form-select-company"
                       class="font-weight-500 font-size-14 text-color form-title">@lang("Business name")</label>
                <div
                    class="form-content text-color font-size-16 form-select2-container form-select-company-container">
                    <div class="d-flex justify-content-center align-content-center ">
                        <select name="company_id" id="form-select-company" class="js-states form-control">
                            <option value="">@lang("Please choose one")</option>
                            @foreach(getListCompany() as $company)
                                <option
                                    {{ $company->id == old('company_id') ? 'selected' : '' }} value="{{ $company->id }}">{{ $company->name }}</option>
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
                <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-first-name">
                    @lang("Company representative full name")
                </label>
                <div class="form-content form-group-input d-flex justify-content-between flex-wrap">
                    <div class="form-group-base half-width">
                        <input type="text" name="firstname" id="form-input-first-name" value="{{ old('firstname') }}"
                               class="form-input-name text-color font-size-16" placeholder="">
                        @error('firstname')
                        <div class="text-danger font-14">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group-base half-width">
                        <input type="text" name="lastname" id="form-input-last-name" value="{{ old('lastname') }}"
                               class="form-input-name text-color font-size-16" placeholder="">
                        @error('lastname')
                        <div class="text-danger font-14">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-email">@lang("Email
                Address")</label>
                <div class="form-content form-group-base">
                    <input type="text" name="email" id="form-input-email" value="{{ old('email') }}"
                           class="form-input-email text-color font-size-16"
                           placeholder="">
                    @error('email')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-phone-number">@lang("Phone Number")</label>
                <div class="form-content form-group-base">
                    @include('backend.includes.partials.input-phone-number')
                    @error('calling_code')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                    @error('phone_number')
                        <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title"
                       for="form-input-password">@lang("Password")</label>
                <div class="form-content form-group-base">
                    <input type="password" name="password" id="form-input-password"
                           class="form-input-name text-color font-size-16" placeholder="">
                    @error('password')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <div class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang("Company Logo")</div>
                    <div
                        class="font-weight-400 font-size-14 description-color">@lang("This will be displayed on their profile.")
                    </div>
                </div>
                <div class="form-content form-group-photo d-flex justify-content-between flex-wrap">
                    <div class="photo d-flex justify-content-center align-items-center test">
                        <img id="image-dropzone-photo" src="{{ asset('img/logo-default.svg') }}"
                             alt="@lang('Logo company')">
                    </div>
                    <div class="dropzone-content-photo">
                        <div
                            class="profile-image-dropzone form-group-base dropzone-content d-flex flex-column justify-content-between align-items-center w-100"
                            ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
                            <div class="dropzone-content-img d-flex justify-content-center align-items-center">
                                <img src="{{asset('img/upload-icon.svg')}}" alt="">
                            </div>
                            <div class="description-color font-size-14 font-weight-400">
                                <span class="primary-color font-weight-600 click-text-upload">Click to upload</span>
                                or drag and drop
                            </div>
                            <div class="description-color font-size-14 font-weight-400">
                                SVG, PNG, JPG or GIF (max. 800x400px)
                            </div>
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
                <input id="dropzone-photo" class="d-none" type="file" name="logo" onchange="changeHandler(this)"/>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="form-select-sector"
                       class="font-weight-500 font-size-14 text-color form-title">@lang("Business sector")</label>
                <div class="form-content text-color font-size-16 form-select2-container form-select-sector-container">
                    <select name="sector_id" id="form-select-sector" class="js-states form-control">
                        <option value="">@lang("Please choose one")</option>
                        @foreach(getListSector() as $sector)
                            <option {{ $sector->id == old('sector_id') ? 'selected' : '' }} value="{{ $sector->id }}">{{ $sector->name }}</option>
                        @endforeach
                    </select>
                    @error('sector_id')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="form-select-country"
                       class="font-weight-500 font-size-14 text-color form-title">@lang("Country")</label>
                <div class="form-content text-color font-size-16 form-select2-container form-select-country-container">
                    <select id="form-select-country" name="country_id" class="js-states form-control">
                        <option value="" class="placeholder-option">@lang("Please choose one")</option>
                        @foreach ($countries as $country)
                            <option data-path="{{ asset('/img/country/' . $country->code . '.png') }}"
                                data-calling-code="{{ $country->calling_code }}"
                                {{ old('country_id') == $country->id ? 'selected' : '' }}
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
                       class="font-weight-500 font-size-14 text-color form-title">@lang("Timezone")</label>
                <div class="form-content text-color font-size-16 form-select2-container form-select-timezone-container">
                    <select id="form-select-timezone" name="timezone_id" class="js-states form-control">
                        <option value="">@lang("Please choose one")</option>
                        @foreach(getListTimezone() as $time)
                            <option {{ $time->id == old('timezone_id') ? 'selected' : '' }} value="{{ $time->id }}" data-path="{{ asset('img/clock-regular.svg') }}">
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
                    <div class="font-weight-500 font-size-14 text-color">@lang("Bio")</div>
                    <div
                        class="font-weight-400 font-size-14 description-color">@lang("Write a short introduction.")</div>
                </label>
                <div class="form-content form-group-photo d-flex flex-column">
                    <textarea name="bio" class="full-width form-input-group resize-none" id="editor1" rows="10"
                              cols="80" maxlength="{{ $lengthBio }}">{{ old('bio') }}</textarea>

                    <div class="count-character font-weight-400 font-size-14 description-color">
                        <div class="number d-inline">
                            {{ old('bio') ? getLengthBio($lengthBio, strlen(old('bio'))) : $lengthBio }}
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
                    <div class="font-weight-500 font-size-14 text-color">@lang("Company documents")</div>
                    <div
                        class="font-weight-400 font-size-14 description-color pr-4">@lang("These are documents the company can save on their profile which might be required by freelancers.")
                    </div>
                </div>
                @include('backend.includes.dropzone_file')
            </div>
            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <div class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang("Profile Verification")</div>
                    <div class="font-weight-400 font-size-14 description-color pr-4">@lang("Is the information provided checked and
                    the freelancer identity verified?")
                    </div>
                </div>
                <div class="form-content d-flex justify-content-start align-items-start">
                    <div class="form-check-box-group position-relative">
                        <input class="form-check-box d-none" type="checkbox" name="active" value="{{ IS_ACTIVE }}"
                               id="profile-checkbox" {{ old('active') == IS_ACTIVE ? 'checked' : '' }}>
                        <label id="label-for-profile-check-box" for="profile-checkbox"></label>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
