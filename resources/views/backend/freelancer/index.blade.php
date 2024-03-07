@extends('backend.layouts.app')

@section('title', __('Manage Freelancers'))

@section('content')
    <div class="container-fluid container-freelancer pl-0 pr-0 mb-5 pc-device admin-freelancers">
        <div class="w-100 header-freelancer">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Manage Freelancers')</div>
                    <div
                        class="color-2200A5 font-12 total-freelancer d-flex justify-content-center align-items-center total-freelancer">
                        {{ $freelancers->total() }} @lang(' Freelancers')
                    </div>
                </div>
                <div class="color-000000 font-14">@lang('Keep track of freelancers and their activity.')</div>
            </div>
            <div class="d-flex justify-content-center align-items-center child-second">
                <a href="{{ route('admin.freelancer.create') }}"
                   class="btn btn-general-action d-flex justify-content-center align-items-center hover-button">
                    <img src="{{ asset('/img/add_icon.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('ADD FREELANCER')</div>
                </a>
            </div>
        </div>

        <div class="w-100 area-search">
            <div class="d-flex justify-content-center align-items-center filter-status">
                <button type="button"
                        class="font-weight-bold filter-tag color-1D2939 filter-tag-active font-14 filter-all">
                    @lang('View all')
                </button>
                <button type="button"
                        class="font-weight-bold filter-tag color-344054 font-14 filter-active btn-sort-active">
                    @lang('Active')
                </button>
                <button type="button"
                        class="font-weight-bold filter-tag color-344054 font-14 filter-inactive btn-sort-in-active">
                    @lang('Inactive')
                </button>
            </div>
            <div class="d-flex justify-content-end align-items-center w-60 child-second">
                <div class="d-flex justify-content-end align-items-center w-100 child-second" id="search">
                    <input type="text" name="hot_search" class="ipt-search-freelancer ipt-search w-60 mr-3 font-16 color-000000"
                           placeholder="{{ __('Search') }}">
                    <button type="button" data-toggle="modal" data-target="#filter-modal"
                            class="btn btn-filter btn-general-action d-flex justify-content-start align-items-center mr-3 import-freelancer position-relative hover-button">
                        <img src="{{ asset('/img/filter_icon.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('FILTER')</div>
                    </button>
                </div>
                <button type="submit" form="form-export-freelancers"
                        class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button btn-export-freelancers">
                    <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('EXPORT')</div>
                </button>
            </div>
        </div>

        <div class="w-100 list-wrapper">
            @include('backend.freelancer.table')
        </div>
    </div>

    <div class="container-fluid container-freelancer mobile-device admin-freelancers">
        <div class="w-100 d-flex justify-content-between align-items-start">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Manage Freelancers')</div>
                    <div
                        class="color-2200A5 font-12 total-freelancer d-flex justify-content-center align-items-center">
                        {{ $freelancers->total() }} @lang(' Freelancers')
                    </div>
                </div>
                <div class="color-000000 font-14">@lang('Keep track of freelancers and their activity.')</div>
            </div>
        </div>
        <div class="d-flex justify-content-start align-items-center mt-3 mb-3">
            <button type="submit" form="form-export-freelancers"
                    class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button btn-export-freelancers">
                <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                <div class="color-2200A5 font-14 font-weight-bold">@lang('EXPORT')</div>
            </button>
            <a class="btn btn-general-action d-flex justify-content-center align-items-center hover-button"
               href="{{ route('admin.freelancer.create') }}">
                <img src="{{ asset('/img/add_icon.svg') }}" alt="" class="mr-2">
                <div class="color-2200A5 font-14 font-weight-bold">@lang('ADD FREELANCER')</div>
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-1 mb-3">
            <div class="d-flex justify-content-between align-items-center w-100" id="search">
                <input type="text" name="hot_search" class="ipt-search-freelancer ipt-search w-60 mr-3 font-16 color-000000 w-90"
                       placeholder="{{ __('Search') }} ">
                <button type="button" data-toggle="modal" data-target="#filter-modal"
                        class="d-flex btn-filter justify-content-center align-items-center filter-mobile-icon position-relative">
                    <img src="{{ asset('/img/filter_gray_icon.svg') }}" alt="" class="">
                </button>
            </div>
        </div>
        <div class="d-flex justify-content-start align-items-center filter-status mb-4">
            <button type="button" class="font-weight-bold filter-tag color-1D2939 filter-tag-active font-14 filter-all">
                @lang('View all')
            </button>
            <button type="button"
                    class="font-weight-bold filter-tag color-344054 font-14 filter-active btn-sort-active">
                @lang('Active')
            </button>
            <button type="button"
                    class="font-weight-bold filter-tag color-344054 font-14 filter-inactive btn-sort-in-active">
                @lang('Inactive')
            </button>
        </div>
        <div class="w-100 list-wrapper">
            @include('backend.freelancer.table')
        </div>
    </div>

    @include('backend.freelancer.confirm_hidden_modal')
    @include('backend.freelancer.filter-modal')
    @include('backend.includes.modal_confirm_delete', ['type' => TYPE_FREELANCER])
@endsection
