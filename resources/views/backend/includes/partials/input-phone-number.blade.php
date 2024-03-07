@php
    $countries = isset($countries) ? $countries : [];
@endphp

<div class="input-phone-number-wrapper {{ $parent_class ?? '' }}">
    <div class="form-select-calling-code-container">
        <select name="calling_code" id="form-select-calling-code">
            @forelse ($countries as $country)
                <option data-path="{{ asset('/img/country/' . $country->code . '.png') }}" title="{{ $country->name }}"
                    {{ old('calling_code', $calling_code ?? '') == $country->id ||
                    (!old() && !isset($calling_code) && $country->code == config('base.default_country_select'))
                        ? 'selected'
                        : '' }}
                    value="{{ $country->id }}">
                    {{ $country->calling_code ? '+' . $country->calling_code : '' }}</option>
            @empty
                <option value="">--</option>
            @endforelse
        </select>
    </div>
    <input type="text" name="phone_number" id="form-input-phone-number"
        value="{{ old('phone_number', $phone_number ?? '') }}" class="form-input-name text-color font-size-16"
        placeholder="{{ $placeholder ?? '' }}">
</div>
