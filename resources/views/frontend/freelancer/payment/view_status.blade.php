@extends('frontend.layouts.app')

@section('title', __('Home'))

@section('page-header', __('Your Dashboard, :NAME', ['name' => $logged_in_user->name]))

@section('content')
    <div id="wrapper-freelancer" class="container-fluid container-freelancer front-end pl-0 pr-0 mb-5 w-96">
        <div class="w-100 header-freelancer flex-wrap">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Recommended Jobs')</div>
                    <div class="color-2200A5 font-12 total-freelancer d-flex justify-content-center align-items-center">
                        {{ $count }} @lang('Jobs')
                    </div>
                </div>
                <div class="color-000000 font-14">@lang('Add or view current jobs that are within your skillset.')</div>
            </div>
            <div class="d-flex justify-content-center align-items-center child-second">
                <button class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button"
                    data-toggle="modal" data-target="#filter-modal-job">
                    <img src="{{ asset('/img/search_icon_2.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('FIND JOBS')</div>
                </button>
                <a href="{{ route('frontend.freelancer.setting') }}"
                    class="btn btn-general-action d-flex justify-content-center align-items-center hover-button">
                    <img src="{{ asset('/img/backend/sidebar/setting_active.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('EDIT PROFILE')</div>
                </a>
            </div>
        </div>
        <div class="w-100 list-wrapper">
            @include('frontend.freelancer.job-preview')
            <div class="template-preview-job"></div>
        </div>
        <input type="hidden" name="is_sorted" class="">
        <input type="hidden" value="{{ $orderBy == TYPE_SORT_ASC ? TYPE_SORT_DESC : TYPE_SORT_ASC }}"
            class="sort-list-job">
    </div>

    @include('frontend.freelancer.modal')
@endsection
