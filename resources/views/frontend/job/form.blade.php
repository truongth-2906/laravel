<div class="form-header d-flex justify-content-between align-items-center">
    <div class="text-header primary-color font-weight-500">@lang('CREATE JOB')</div>
</div>
<div class="form-sub-title border-bottom d-flex">
    <div class="font-size-14 primary-color font-weight-600 sub-title">@lang('Job Details')</div>
</div>

<div
    class="form-group-function border-bottom pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
    <div class="form-group-button-title">
        <p class="font-weight-600 font-size-18 text-color">@lang('Job company info')</p>
        <div class="font-weight-400 font-size-14 description-color">@lang('Update employer company photo and details here.')
        </div>
    </div>
    <div class="form-group-button d-flex">
        <a class="button-base btn-general-action hover-button text-decoration-none" href="{{ $previousUrl ?? route('frontend.employer.index') }}" >@lang('Cancel')</a>
        <button type="submit" class="button-base btn-general-action hover-button"
            id="button-save">@lang('Save')</button>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label class="font-weight-500 font-size-14 text-color form-title" for="form-input-email">@lang('Job title')</label>
    <div class="form-content">
        <input type="text" class="form-input-name text-color font-size-16 form-group-base" name="name"
            placeholder="Enter name job" value="{{ old('name') }}">
        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="text-danger errors name_input_error_message" style="display: none;"></div>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label for="form-select-country"
        class="font-weight-500 font-size-14 text-color form-title">@lang('Country')</label>
    <div class="form-content text-color font-size-16 form-select2-container form-select-country-container">
        <select id="form-select-country" class="js-states form-control" name="country_id">
            <option value="" class="placeholder-option">@lang('Please choose one')</option>
            @foreach ($countries as $country)
                <option data-path="{{ asset('/img/country/' . $country->code . '.png') }}"
                    data-calling-code="{{ $country->calling_code }}"
                    {{ old('country_id') == $country->id ? 'selected' : '' }} value="{{ $country->id }}">
                    {{ $country->name }}</option>
            @endforeach
        </select>
        @error('country_id')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="text-danger errors country_id_input_error_message" style="display: none;"></div>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label for="editor1" class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang("Job status")</div>
    </label>
    <div class="form-content text-color font-size-16 employer-toggle">
        <button type="button" class="btn btn-secondary btn-toggle btn-status-job" data-toggle="button"
                aria-pressed="{{ old('status') == 1 ? 'true' : 'false' }}" autocomplete="off">
            <div class="handle"></div>
        </button>
        <input type="hidden" name="status" value="{{ old('status', 0) }}">
        @error('status')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="text-danger errors status_input_error_message" style="display: none;"></div>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label for="form-select-timezone"
        class="font-weight-500 font-size-14 text-color form-title">@lang('Timezone for working')</label>
    <div class="form-content text-color font-size-16 form-select2-container form-select-timezone-container">
        <select id="form-select-timezone" class="js-states form-control" name="timezone_id">
            <option value="">@lang('Please choose one')</option>
            @foreach (getListTimezone() as $timezone)
                <option value="{{ $timezone->id }}" data-path="{{ asset('img/clock-regular.svg') }}"
                    {{ $timezone->id == old('timezone_id') ? 'selected' : '' }}>{{ $timezone->name }}</option>
            @endforeach
        </select>
        @error('timezone_id')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="text-danger errors timezone_id_input_error_message" style="display: none;"></div>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <div class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang('Job Description')</div>
        <div class="font-weight-400 font-size-14 description-color">@lang('Write a short introduction and describe in detail what the freelancers would require to qualify for your job.')</div>
    </div>
    <div class="form-content form-group-photo d-flex flex-column">
        <textarea name="description" class="full-width p-2" id="job-description" rows="10"
            maxlength="{{ $maxDescription }}" cols="80">{{ old('description') }}</textarea>

        <div class="font-weight-400 font-size-14 description-color">
            <span
                class="count-character">{{ old('description') ? getLengthBio($maxDescription, strlen(old('description'))) : $maxDescription }}</span>
            @lang(' characters left')
        </div>
        <div class="text-danger font-14 d-none error-max-des">
            @lang('Enter the limit allowed.')
        </div>
        @error('description')
            <div class="text-danger font-14">{{ $message }}</div>
        @enderror
        <div class="text-danger errors description_input_error_message" style="display: none;"></div>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <div class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang('Amount (USD)')</div>
        <div class="font-weight-400 font-size-14 description-color">@lang('Enter the total amount you\'d like to pay for this job/project.')</div>
    </div>

    <div class="form-content">
        <input id="form-input-wage" type="number" class="form-input-name text-color font-size-16 form-group-base" name="wage"
               placeholder="Can enter with 2 decimal number" value="{{ old('wage') }}" min="1">
        @error('wage')
        <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="text-danger errors wage_input_error_message" style="display: none;"></div>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label for="form-select-categories" class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang('RPA Software')</div>
        <div class="font-weight-400 font-size-14 description-color pr-4">@lang('What are the RPA software requirements for this job?')
        </div>
    </label>
    <div class="form-content text-color font-size-16 form-select2-container form-select-categories-container">
        <select class="js-states form-control" id="form-select-categories" multiple="multiple" name="category_id[]">
            @foreach (getListCategory() as $category)
                <option value="{{ $category->id }}"
                    {{ is_array(old('category_id')) && in_array($category->id, old('category_id')) ? 'selected' : '' }}>
                    {{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="text-danger errors category_id_input_error_message" style="display: none;"></div>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label for="form-select-experience" class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang('RPA Experience')</div>
        <div class="font-weight-400 font-size-14 description-color pr-4">@lang('What are the RPA experience requirements for this job?')
        </div>
    </label>
    <div class="form-content text-color font-size-16 form-select2-container form-select-rpa-experience-container">
        <select id="form-select-rpa-experience" class="js-states form-control" name="experience_id">
            <option value="">@lang('Please choose one')</option>
            @foreach (getListExperience() as $experience)
                <option {{ $experience->id == old('experience_id') ? 'selected' : '' }}
                    value="{{ $experience->id }}">{{ $experience->name }}</option>
            @endforeach
        </select>
        @error('experience_id')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="text-danger errors experience_id_input_error_message" style="display: none;"></div>
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <div class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang('Company documents')</div>
        <div class="font-weight-400 font-size-14 description-color pr-4">@lang('These are documents the company can save on their profile which might be required by freelancers.')
        </div>
    </div>
    @include('backend.includes.dropzone_file')
</div>
