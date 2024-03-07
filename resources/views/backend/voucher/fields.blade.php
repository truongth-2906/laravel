<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label class="font-weight-500 font-size-14 text-color form-title" for="name">@lang('Voucher Name')</label>
    <div class="form-content">
        <input type="text" class="form-input-name text-color font-size-16 form-group-base" name="name" placeholder=""
            id="name" value="{{ old('name') }}">
        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <div class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang('Description')</div>
        <div class="font-weight-400 font-size-14 description-color">@lang('Write a short intro and detailed description.')</div>
    </div>
    <div class="form-content form-group-photo d-flex flex-column">
        <textarea name="description" class="full-width p-2 description-max-length" id="description" rows="10"
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
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label for="form-select-discount-type" class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang('Discount Type')</div>
        <div class="font-weight-400 font-size-14 description-color pr-4">@lang('Specify the discount type: subtract by percentage or subtract by specific amount.')
        </div>
    </label>
    <div class="form-content text-color font-size-16 form-select2-container form-select-discount-type-container">
        <select id="form-select-discount-type" class="form-control" name="discount_type">
            <option value="">@lang('Please choose one')</option>
            @foreach ($types as $type => $name)
                <option {{ $type == old('discount_type') ? 'selected' : '' }} value="{{ $type }}">
                    {{ $name }}</option>
            @endforeach
        </select>
        @error('discount_type')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label class="font-weight-500 font-size-14 text-color form-title" for="discount">@lang('Discount')</label>
    <div class="form-content">
        <input type="number" class="form-input-name text-color font-size-16 form-group-base" name="discount"
            placeholder="" id="discount" value="{{ old('discount') }}" min="0" step="0.01">
        @error('discount')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label class="font-weight-500 font-size-14 text-color form-title" for="max-discount">@lang('Max Discount')</label>
    <div class="form-content">
        <input type="number" class="form-input-name text-color font-size-16 form-group-base" name="max_discount"
            placeholder="" id="max-discount" value="{{ old('max_discount') }}" min="0" step="0.01">
        @error('max_discount')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label for="count" class="form-group-button-title form-title">
        <div class="font-weight-500 font-size-14 text-color">@lang('Number of vouchers')</div>
        <div class="font-weight-400 font-size-14 description-color pr-4">@lang('If not entered, this voucher will not be limited in quantity.')
        </div>
    </label>
    <div class="form-content">
        <input type="number" class="form-input-name text-color font-size-16 form-group-base" name="count"
            placeholder="" id="count" value="{{ old('count') }}" min="0" step="1">
        @error('count')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label class="font-weight-500 font-size-14 text-color form-title">@lang('Voucher status')</label>
    <div class="form-content text-color font-size-16">
        <div class="switch-input-wrapper">
            <div class="option-name">@lang('DISABLED')</div>
            <input type="checkbox" id="status" class="switch-input" name="status" {{ !old() || old('status') ? 'checked' : '' }} value="1"/>
            <label for="status" class="switch"></label>
            <div class="option-name">@lang('AVAILABILITY')</div>
        </div>
        @error('status')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label class="font-weight-500 font-size-14 text-color form-title">@lang('Number of uses per person')</label>
    <div class="form-content text-color font-size-16">
        <div class="switch-input-wrapper">
            <div class="option-name">@lang('TIMES')</div>
            <input type="checkbox" id="number-times-used-type" class="switch-input" name="number_times_used_type" value="1" {{ old('number_times_used_type') ? 'checked' : '' }}/>
            <label for="number-times-used-type" class="switch orange-switch"></label>
            <div class="option-name">@lang('DAYS')</div>
        </div>
        <div class="form-content w-100 mt-4">
            <input type="number" class="form-input-name text-color font-size-16 form-group-base" name="number_times_used_value"
                placeholder="" id="number-times-used-value" value="{{ old('number_times_used_value', 1) }}" min="1" step="1">
        </div>
        @error('number_times_used_type')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        @error('number_times_used_value')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label class="font-weight-500 font-size-14 text-color form-title" for="expired-date">@lang('Expired Date')</label>
    <div class="form-content">
        <input type="text" class="form-input-name text-color font-size-16 form-group-base datetimepicker" name="expired_date"
            placeholder="" id="expired-date" value="{{ old('expired_date') }}">
        @error('expired_date')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
    <label class="font-weight-500 font-size-14 text-color form-title">@lang('Scope')</label>
    <div class="form-content text-color font-size-16">
        <div class="switch-input-wrapper">
            <div class="option-name">@lang('ALL')</div>
            <input type="checkbox" id="scope" class="switch-input" name="scope" value="1" {{ old('scope') ? 'checked' : '' }}/>
            <label for="scope" class="switch orange-switch"></label>
            <div class="option-name">@lang('SPECIFY')</div>
        </div>
        <div class="form-content text-color font-size-16 form-select2-container form-select-users-container mt-4 w-100" style="{{ !old('scope') ? 'display: none;' : '' }}">
            <select id="form-select-users" class="form-control" name="users_specify[]" multiple>
                <option value="">@lang('Please choose one')</option>
                @foreach (session('users_selected', []) as $id => $text)
                    <option selected value="{{ $id }}">
                        {{ $text }}</option>
                @endforeach
            </select>
            @error('users_specify')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        @error('scope')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>
