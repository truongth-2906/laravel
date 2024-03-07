@extends('frontend.layouts.app')

@section('title', __('Job Saved'))

@section('content')
    <div id="saved-wrapper" class="list-saved-job p-32">
        <div class="saved-wrapper-child">
            <div class="w-100 flex-center border-bottom-EAECF0 p-3">
                @include('frontend.freelancer.job-redirect')
            </div>
            <div class="w-100 wrapper-body">
                <div class="w-100 d-flex justify-content-center align-items-center search-wrapper">
                    <form action="" method="GET" class="d-flex justify-content-center align-items-center"
                        id="search-form">
                        <input type="text" name="hot_search" placeholder="@lang('Search')" form="hot-search-form"
                            data-old-value="{{ request()->query('hot_search', '') }}">
                        <button type="button" data-toggle="modal" data-target="#filter-job-modal"
                            class="btn btn-filter btn-general-action d-flex justify-content-start align-items-center import-freelancer position-relative hover-button">
                            <img src="{{ asset('/img/filter_icon.svg') }}" alt="">
                            <p class="mb-0 color-2200A5 font-14 font-weight-bold pc-device ml-2">@lang('FILTERS')</p>
                        </button>
                    </form>
                </div>
                <div class="w-100 list-wrapper" id="table-wrapper">
                    @include('frontend.freelancer.job-saved-table')
                </div>
            </div>
        </div>
    </div>
    @include('frontend.saved.job.filter-modal')
@endsection
