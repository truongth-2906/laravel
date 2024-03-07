@extends('frontend.layouts.app')

@section('title', __('Job Applications'))

@section('content')
    <div class="p-32"  id="job-application-wrapper">
        <div class="container-fluid container-freelancer front-end pl-0 pr-0 job-applications-wrapper">
            <div class="w-100 flex-center border-bottom-EAECF0 p-3">
                @include('frontend.freelancer.job-redirect')
            </div>
            <div class="w-100 header-freelancer justify-content-center">
                <form class="flex-center w-100" action="" method="GET"
                      id="search">
                    <input type="text" class="w-100 m-w-400 ipt-search-freelancer ipt-search mr-3 font-16 color-000000"
                           placeholder="{{ __('Search') }} " name="hot_search" data-old-value="{{ request()->query('hot_search', '') }}">
                    <button type="button" data-toggle="modal" data-target="#modal-filter-job-application"
                            class="btn btn-filter btn-general-action d-flex justify-content-start align-items-center mr-3 import-freelancer position-relative hover-button">
                            <img src="{{ asset('/img/filter_icon.svg') }}" alt="">
                        <p class="mb-0 color-2200A5 font-14 font-weight-bold pc-device ml-2">@lang('FILTERS')</p>
                    </button>
                </form>
            </div>
            <div class="w-100 list-wrapper table-job-applications">
                @include('frontend.freelancer.job-application-table')
            </div>
        </div>
    </div>
    @include('frontend.includes.modal_filter_job', ['id' => 'filter-job-application'])
@endsection
