@extends('backend.layouts.app')

@section('title', __('Create Freelancer'))

@section('content')
    <div class="wrapper">
        <form class="form-freelancer" id="form-freelancer" action="{{ route('admin.freelancer.store') }}" method="post"
              enctype="multipart/form-data">
            @csrf
            <input type="text" class="d-none" name="type" value="{{ $type }}">

            <div class="form-header d-flex justify-content-between align-items-center">
                <div class="text-header primary-color font-weight-500">@lang("CREATE FREELANCER")</div>
            </div>
            <div class="form-sub-title border-bottom d-flex">
                <div class="font-size-14 primary-color font-weight-600 sub-title">@lang("Freelancer Details")</div>
            </div>

            <div
                class="form-group-function border-bottom pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                <div class="form-group-button-title">
                    <p class="font-weight-600 font-size-18 text-color">@lang("Freelancer personal info")</p>
                    <div class="font-weight-400 font-size-14 description-color">@lang("Update freelancer profile photo and personal
                    details here.")
                    </div>
                </div>
                <div class="form-group-button d-flex">
                    <button type="button" class="button-base btn-general-action hover-button" id="button-cancel"
                            onclick="window.location='{{ route('admin.freelancer.index') }}'">
                        @lang("Cancel")
                    </button>
                    <button type="button" class="button-base btn-general-action hover-button submit-form-create" id="button-save">@lang("Save")</button>
                </div>
            </div>

            <div class="form-group-name border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-first-name">@lang("Full
                Name")</label>
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
                <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-tag-line">@lang("Tag line")
                </label>
                <div class="form-content form-group-base">
                    <input type="text" name="tag_line" id="form-input-tag-line"
                           class="form-input-name text-color font-size-16"
                           placeholder="" value="{{ old('tag_line') }}">
                    @error('tag_line')
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
                    <div class="font-weight-500 font-size-14 text-color">@lang("Freelancer Profile Photo")</div>
                    <div
                        class="font-weight-400 font-size-14 description-color">@lang("This will be displayed on their profile.")
                    </div>
                </div>
                <div class="form-content form-group-photo d-flex justify-content-between flex-wrap">
                    <div class="photo d-flex justify-content-center align-items-center test">
                        <img id="image-dropzone-photo" src="{{ asset('img/logo-default.svg') }}" alt="avatar">
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
                <input id="dropzone-photo" class="d-none" type="file" name="avatar" onchange="changeHandler(this)"/>
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
                            <option {{ $time->id == old('timezone_id') ? 'selected' : '' }} value="{{ $time->id }}"
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

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap align-items-center">
                <label for="form-select-categories" class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang("RPA Software")</div>
                    <div class="font-weight-400 font-size-14 description-color pr-4">
                        @lang("Type and select which RPA Software you use and have experience in.")
                    </div>
                </label>
                <div
                    class="form-content text-color font-size-16 form-select2-container form-select-categories-container">
                    <select name="categories[]" class="js-states form-control" id="form-select-categories"
                            multiple="multiple">
                        @foreach(getListCategory() as $category)
                            <option
                                {{ is_array(old('categories')) && in_array($category->id, old('categories')) ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('categories')
                    <div class="text-danger font-14">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label for="experience_id" class="form-group-button-title form-title">
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
                                {{ $experience->id == old('experience_id') ? 'selected' : '' }} value="{{ $experience->id }}">{{ $experience->name }}</option>
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
                    <button type="button" id="download-example-cv" data-url-cv="{{ asset('/file/Automatorr_CV_Template_for_Freelancers.docx') }}"
                            class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button">
                        <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('DOWNLOAD EXAMPLE CV')</div>
                    </button>
                </div>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <div class="form-group-button-title form-title">
                    <div class="font-weight-500 font-size-14 text-color">@lang("Portfolio projects")</div>
                    <div class="font-weight-400 font-size-14 description-color pr-4">@lang("These are snippets of work the
                    freelancer submitted. You can add or remove snippets from here.")
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
                        <input
                            class="form-check-box d-none"
                            type="checkbox" name="active"
                            {{ old('active') == IS_ACTIVE ? 'checked' : '' }}
                            value="{{ IS_ACTIVE }}" id="profile-checkbox">
                        <label id="label-for-profile-check-box" for="profile-checkbox"></label>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
