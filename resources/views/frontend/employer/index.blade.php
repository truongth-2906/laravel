@extends('frontend.layouts.app')

@section('title', __('Home'))

@section('page-header', __('Dashboard for :NAME', ['name' => optional($logged_in_user->company)->name]))

@section('content')
    @if($jobs->total() == 0 )
        <div class="content-dashboard h-898">
            <div class="pt-300">
                <img class="pc-device" src="{{ asset('/img/features-icon-search.svg') }}" alt="">
                <div
                    class="font-size-16 font-weight-600 font-content text-color-content mt-3">@lang("Post a job and it will appear here.")</div>
                <div
                    class="font-size-14 font-weight-400 color-101828">@lang("Find people fast by creating your first job, below.")</div>
                <div class="d-flex justify-content-center align-items-center mt-3">
                    <a href="{{ route(FIND_FREELANCER) }}"
                            class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                        <img src="{{ asset('/img/search-icon.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('BROWSE FREELANCERS')</div>
                    </a>
                    <a href="{{ route('frontend.employer.jobs.create') }}"
                       class="btn btn-general-action d-flex justify-content-center align-items-center hover-button">
                        <img src="{{ asset('/img/add_icon.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('ADD JOB')</div>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div id="wrapper-employer" class="container-fluid container-freelancer front-end pl-0 pr-0 mb-5 w-96">
            <div class="w-100 header-freelancer flex-wrap">
                <div class="d-flex flex-column align-items-start">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Your Current Listed Jobs')</div>
                        <div
                            class="color-2200A5 font-12 total-freelancer d-flex justify-content-center align-items-center">
                            {{ $totalJobs }} @lang('Jobs')
                        </div>
                    </div>
                    <div
                        class="color-000000 font-14">@lang('Add or view your current active/inactive jobs')</div>
                </div>
                <div class="d-flex justify-content-center align-items-center child-second group-btn">
                    <button type="button" data-toggle="modal" data-target="#employer-filter-freelancer-modal"
                            class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                        <img src="{{ asset('/img/search_icon_2.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('FIND FREELANCERS')</div>
                    </button>
                    <a href="{{ route('frontend.employer.jobs.create') }}"
                       class="btn btn-general-action d-flex justify-content-center align-items-center hover-button">
                        <img src="{{ asset('/img/add_icon.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('ADD JOB')</div>
                    </a>
                </div>
            </div>
            <div class="w-100 list-wrapper">
                @include('frontend.employer.table')
            </div>
        </div>
        <input type="hidden" name="is_sorted" class="">
        <input type="hidden" value="{{ $orderBy == TYPE_SORT_ASC ? TYPE_SORT_DESC : TYPE_SORT_ASC }}"
               class="sort-list-job">
    @endif

    @include('frontend.employer.find.filter-modal')
    @include('backend.includes.modal_confirm_delete', ['type' => TYPE_JOB])
    @include('backend.includes.modal_approve')
    @include('backend.includes.modal_reject')
    @include('backend.includes.modal_mark_done')

    <form action="#" method="post" id="form-delete" hidden>
        @csrf
        @method('DELETE')
    </form>
@endsection
