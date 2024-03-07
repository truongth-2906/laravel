@extends('frontend.layouts.app')

@section('title', __('Settings'))

@section('content')
    <div class="wrapper">
        <div class="form-header d-flex justify-content-between align-items-center">
            <div class="d-flex text-header primary-color font-weight-500">
                @lang("Settings")
                @if($logged_in_user->isDeclined() || ($logged_in_user->isPending() && is_null($logged_in_user->identity_passbase)))
                    <a href="{{ route('frontend.auth.passbase.index') }}" target="_blank"
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
        <form class="form-setting-details color-F9FAFB" id="form-setting-details"
              action="{{ route('frontend.freelancer.update', $freelancer->id) }}" method="post"
              enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="freelancer_id" value="{{ $freelancer->id }}">

            <div
                class="form-group-function pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                <div class="form-group-button-title">
                    <p class="font-weight-600 font-size-18 text-color">@lang("Personal info")</p>
                    <div class="font-weight-400 font-size-14 description-color">@lang("Complete your profile and details
                    here.")</div>
                </div>
                <div class="form-group-button d-flex" id="group-button-top">
                    <button type="button" class="button-base hover-button" id="button-cancel"
                            onclick="window.location='{{ route('frontend.freelancer.index') }}'">@lang("Cancel")</button>
                    <button type="button" class="button-base hover-button" id="button-save">@lang("Save")</button>
                </div>
            </div>
            <div class="form-sub-title tab-setting d-flex border-bottom pb-4">
                <a href="{{ route('frontend.freelancer.setting') }}" class="btn tab-base active">
                    @lang("My details")
                </a>
                <a href="{{ route('frontend.user.setting.settingPassword') }}" class="btn tab-base">
                    @lang("Password")
                </a>
                <a href="{{ route('frontend.freelancer.available') }}" class="btn tab-base">
                    @lang("Availability")
                </a>
            </div>
            <div class="form-group-name border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-first-name">@lang("Full
                    Name")</label>
                <div class="form-content form-group-input d-flex justify-content-between flex-wrap">
                    <div class="form-group-base half-width">
                        <input type="text" name="firstname" id="form-input-first-name"
                               value="{{ old('firstname', $freelancer->firstname) }}"
                               class="form-input-name text-color font-size-16" placeholder="">
                        @error('firstname')
                        <div class="text-danger font-14">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group-base half-width">
                        <input type="text" name="lastname" id="form-input-last-name"
                               value="{{ old('lastname', $freelancer->lastname) }}"
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
                    <div
                        class="font-weight-400 font-size-14 description-color pr-4">@lang("Your email address is only
                            visible to the administrators and shared with the public.")
                    </div>
                </label>
                <div class="form-content form-group-base">
                    <input type="text" name="email" id="form-input-email"
                           class="form-input-email text-color font-size-16"
                           placeholder="" value="{{ old('email', $freelancer->email) }}">
                    @error('email')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-tag-line">@lang("Tag line")
                </label>
                <div class="form-content form-group-base">
                    <input type="text" name="tag_line" id="form-input-tag-line"
                           class="form-input-name text-color font-size-16"
                           placeholder="" value="{{ old('tag_line', $freelancer->tag_line) }}">
                    @error('tag_line')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title"
                       for="form-input-email">@lang("Phone Number")</label>
                <div class="form-content form-group-base">
                    @include('backend.includes.partials.input-phone-number', [
                        'phone_number' => $freelancer->phone_number,
                        'calling_code' => $freelancer->calling_code_id,
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
                    <div class="font-weight-500 font-size-14 text-color">@lang("My profile image")</div>
                    <div
                        class="font-weight-400 font-size-14 description-color">@lang("Upload a professional photo.")
                    </div>
                </div>
                <div class="form-content form-group-photo d-flex justify-content-between flex-wrap">
                    <div class="photo d-flex justify-content-center align-items-center test">
                        <img id="image-dropzone-photo"
                             src="{{ asset($freelancer->avatar ? $freelancer->logo : '/img/avatar_default.svg') }}"
                             alt="@lang('Avatar')">
                    </div>
                    <div class="dropzone-content-photo">
                        <div
                            class="profile-image-dropzone form-group-base dropzone-content dropzone-content-photo d-flex flex-column justify-content-between align-items-center"
                            ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
                            <div class="dropzone-content-img d-flex justify-content-center align-items-center">
                                <img src="{{asset('img/upload-icon.svg')}}" alt="">
                            </div>
                            <div class="description-color font-size-14 font-weight-400">
                                <span
                                    class="primary-color font-weight-600 click-text-upload">@lang("Click to upload")</span>
                                @lang("or drag and drop")
                            </div>
                            <div class="description-color font-size-14 font-weight-400">
                                @lang("SVG, PNG, JPG or GIF (max. 800x400px)")                                </div>
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
                <input id="dropzone-photo" class="d-none" type="file" name="avatar" onchange="changeHandler(this)"/>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="form-select-country"
                       class="font-weight-500 font-size-14 text-color form-title">@lang("Country")</label>
                <div
                    class="form-content text-color font-size-16 form-select2-container form-select-country-container">
                    <select id="form-select-country" name="country_id" class="js-states form-control">
                        <option class="placeholder-option">@lang("Please choose one")</option>
                        @foreach ($countries as $country)
                            <option data-path="{{ asset('/img/country/' . $country->code . '.png') }}"
                                    data-calling-code="{{ $country->calling_code }}"
                                    {{ old('country_id', $freelancer->country_id) == $country->id ? 'selected' : '' }} value="{{ $country->id }}">
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
                <div
                    class="form-content text-color font-size-16 form-select2-container form-select-timezone-container">
                    <select id="form-select-timezone" name="timezone_id" class="js-states form-control">
                        <option>@lang("Please choose one")</option>
                        @foreach(getListTimezone() as $time)
                            <option
                                {{ $time->id == old('timezone_id', $freelancer->timezone_id) ? 'selected' : '' }}
                                value="{{ $time->id }}"
                                data-path="{{ asset('img/clock-regular.svg') }}">{{ $time->name }}</option>
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
                    <div class="font-weight-400 font-size-14 description-color">
                        @lang("Write a short introduction about yourself to let potential employers know about you.")
                    </div>
                </label>
                <div class="form-content form-group-photo d-flex flex-column">
                    <textarea name="bio" class="full-width form-input-group resize-none" id="editor1" rows="10"
                              cols="80" maxlength="{{ $lengthBio }}">{{ old('bio', $freelancer->bio) }}</textarea>

                    <div class="count-character font-weight-400 font-size-14 description-color">
                        <div class="number d-inline">
                            {{ old('bio') ? getLengthBio($lengthBio, strlen(old('bio'))) : ($lengthBio - strlen($freelancer->bio)) }}
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
                <label for="form-select-categories" class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang("RPA Software")</div>
                    <div class="font-weight-400 font-size-14 description-color pr-4">@lang("Type and select which RPA Software
                        you use and have experience using.")
                    </div>
                </label>
                <div
                    class="form-content text-color font-size-16 form-select2-container form-select-categories-container">
                    <select name="categories[]" class="js-states form-control" id="form-select-categories"
                            multiple="multiple">
                        @php
                            $categoryIds = $freelancer->categories->pluck('id')->toArray();
                        @endphp
                        @foreach(getListCategory() as $category)
                            <option value="{{ $category->id }}"
                            @if ($errors->first('categories'))
                                >
                                @else
                                    {{ in_array($category->id, old('categories', $categoryIds)) ? 'selected' : '' }}>
                                @endif
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('categories')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="editor1" class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang("RPA Experience")</div>
                    <div class="font-weight-400 font-size-14 description-color">
                        @lang("What is your current RPA experience?")
                    </div>
                </label>
                <div
                    class="form-content text-color font-size-16 form-select2-container form-select-rpa-experience-container">
                    <select class="js-states form-control" name="experience_id" id="form-select-rpa-experience">
                        <option value="">@lang("Please choose one")</option>
                        @foreach(getListExperience() as $experience)
                            <option
                                {{ $experience->id == old('experience_id', $freelancer->experience_id) ? 'selected' : '' }} value="{{ $experience->id }}">
                                {{ $experience->name }}</option>
                        @endforeach
                    </select>
                    @error('experience_id')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="border-bottom pb-20 d-flex flex-wrap justify-content-between">
                <div class="justify-content-start">
                    <div class="font-weight-500 font-size-14 text-color">
                        @lang("Structuring Your CV")
                    </div>
                    <div class="font-weight-400 font-size-14 description-color">
                        @lang("Before you upload your CV, please use this template to make sure that your CV is optimised.
                               </br>This will help you increase your success rate on our platform.")
                    </div>
                </div>
                <div class="justify-content-end pt-2">
                    <button type="button" id="download-example-cv"
                            data-url-cv="{{ asset('/file/Automatorr_CV_Template_for_Freelancers.docx') }}"
                            class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button">
                        <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('DOWNLOAD EXAMPLE CV')</div>
                    </button>
                </div>
            </div>
            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <div class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang("Professional Summary")</div>
                    <div
                        class="font-weight-400 font-size-14 description-color pr-4">@lang("These are snippets of work you have completed for clients over time. You can add or remove media from here.")
                    </div>
                </div>
                @include('backend.includes.dropzone_file', ['file_uploaded' => $freelancer->portfolios])
            </div>

            <div class="form-group-button d-flex justify-content-end mt-3">
                <button type="button" class="button-base hover-button" id="button-cancel"
                        onclick="window.location='{{ route('frontend.freelancer.index') }}'">@lang("Cancel")</button>
                <button type="button" class="button-base hover-button" id="button-save">@lang("Save")</button>
            </div>
        </form>
    </div>
@endsection
