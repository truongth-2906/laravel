@extends('frontend.layouts.app')

@section('title', __('Find Freelancers'))

@section('page-header', __('Dashboard for :NAME', ['name' => optional($logged_in_user->company)->name]))

@section('content')
    <div id="list-find-freelancer" class="container-fluid container-freelancer front-end pl-0 pr-0 w-96">
        <div class="w-100 header-freelancer">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Freelancers')</div>
                    <div class="color-2200A5 font-12 total-freelancer d-flex justify-content-center align-items-center total-freelancer">
                        {{ $freelancers->total() }} @lang(' Freelancers')
                    </div>
                </div>
                <div class="color-000000 font-14">@lang('Freelancers matching your search result')</div>
            </div>
        </div>

        <div class="w-100 area-search">
            <div class="d-flex justify-content-end align-items-center child-second area-find-freelancer">
                <form class="d-flex align-items-center w-100 child-second" action="" method="GET"
                      id="search">
                    <input type="text" class="ipt-employer-find-freelancer ipt-employer-search-freelancer w-100 mr-3 font-16 color-000000"
                           placeholder="{{ __('Search') }}">
                    <button type="button" data-toggle="modal" data-target="#employer-filter-freelancer-modal"
                            class="btn btn-filter btn-general-action d-flex justify-content-start align-items-center import-freelancer position-relative hover-button">
                        <img src="{{ asset('/img/filter_icon.svg') }}" alt="">
                        <div class="color-2200A5 font-14 font-weight-bold ml-2 mobile-filter-employer">@lang('FILTERS')</div>
                    </button>
                </form>
            </div>
        </div>

        <div class="w-100 list-wrapper preview-freelancer">
            @include('frontend.employer.find.table')
        </div>
    </div>
    @include('frontend.employer.find.filter-modal')
@endsection
