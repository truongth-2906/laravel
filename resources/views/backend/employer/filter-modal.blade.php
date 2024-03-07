<div class="modal fade admin-employers" id="filter-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100 flex-center justify-content-start align-items-start">
                    <div class="flex-center icon-filter mr-2">
                        <img src="{{ asset('/img/filter_gray_icon.svg') }}" alt="" class="">
                    </div>
                    <div class="flex-center flex-column align-items-start">
                        <p class="mb-0 font-20 color-2200A5">@lang('Search')</p>
                        <p class="mb-0 font-14">@lang('Filter the results based on categories,</br> industry or skill requirements.')</p>
                    </div>
                </div>
                <button type="button" class="close btn-close position-absolute" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body flex-center flex-column justify-content-start align-items-start pt-0 pb-0">
                <div class="flex-center flex-column align-items-start w-100 mb-3">
                    <p class="mb-0 font-14 color-000000">@lang('Filter by search')</p>
                    <input type="text" class="ipt-search-employer font-16 color-667085 w-100 ipt-search-filter-employer"
                           placeholder="{{ __('Type to search') }}">
                </div>
                <div class="flex-center flex-column align-items-start w-100 mb-3">
                    <p class="mb-0 font-14 color-000000">@lang('Filter by company')</p>
                    <div
                        class="form-select form-select-lg w-100 form-select2-container form-select-company-container">
                        <select name="company_id" id="form-select-company" class="js-states form-control">
                            <option value="">@lang("Open this select company")</option>
                            @foreach(getListCompany() as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex-center flex-column align-items-start w-100 mb-3">
                    <p class="mb-0 font-14 color-000000">@lang('Filter by country')</p>
                    <div
                        class="form-select form-select-lg w-100 form-select2-container form-select-country-container">
                        <select name="country_id" id="form-select-country" class="js-states form-control">
                            <option value="">@lang("Open this select country")</option>
                            @foreach (getListCountry() as $country)
                                <option data-path="{{ asset('img/country/' . $country->code . '.png') }}"
                                        {{ $country->id == old('country_id') ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-3 flex-center justify-content-end">
                <button type="button" class="btn btn-cancel color-2200A5 mr-2 mt-0"
                        data-dismiss="modal">@lang('Cancel')</button>
                <button type="button" class="btn btn-search btn-search-filter color-2200A5">
                    @lang('Search')
                    <img src="{{ asset('/img/backend/round-right.svg') }}">
                </button>
            </div>
        </div>
    </div>
</div>
