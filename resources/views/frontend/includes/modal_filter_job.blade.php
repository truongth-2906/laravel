<div class="modal fade filter-modal-fe color-initial" id="modal-{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="" id="form-{{ $id }}" class="w-100">
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
                    <div class="flex-center flex-column align-items-start w-100 mb-2">
                        <p class="mb-0 font-14 color-000000">@lang('Filter by search')</p>
                        <input type="text" name="key_search" class="ipt-search-freelancer font-16 w-100 ipt-search-filter"
                               placeholder="{{ __('Type to search') }}">
                    </div>

                    <div class="flex-center flex-column align-items-start w-100 mb-2">
                        <p class="mb-0 font-14 color-000000">@lang('Filter by business sector')</p>
                        <div class="form-content text-color font-size-16 w-100 form-select2-container form-select-sector-container">
                            <select name="sector_id" id="form-select-sector" class="js-states form-control">
                                <option value="">@lang("Please choose one")</option>
                                @foreach(getListSector() as $sector)
                                    <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex-center flex-column align-items-start w-100 mb-2">
                        <p class="mb-0 font-14 color-000000">@lang('Filter by RPA Software')</p>
                        <div class="form-content text-color font-weight-500 font-size-16 w-100 form-select2-container form-select-categories-container">
                            <select class="js-states form-control" id="form-select-categories" multiple="multiple" name="category_id[]">
                                @foreach(getListCategory() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex-center flex-column align-items-start w-100 mb-2">
                        <p class="mb-0 font-14 color-000000">@lang('Filter by experience ')</p>
                        <div class="form-content text-color font-size-16 w-100 form-select2-container form-select-rpa-experience-container">
                            <select class="js-states form-control" name="experience_id" id="form-select-rpa-experience">
                                <option value="">@lang("Please choose one")</option>
                                @foreach(getListExperience() as $experience)
                                    <option value="{{ $experience->id }}">{{ $experience->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex-center flex-column align-items-start w-100 mb-2">
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
                    <button type="button" class="btn-modal btn-cancel-modal color-2200A5 mr-2 mt-0"
                            data-dismiss="modal">@lang('Cancel')</button>
                    <button type="button" class="btn btn-search btn-search-filter color-2200A5">
                        @lang('Search')
                        <img alt="" src="{{ asset('/img/backend/round-right.svg') }}">
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
