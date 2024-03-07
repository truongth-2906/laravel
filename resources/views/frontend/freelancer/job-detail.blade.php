@extends('frontend.layouts.app')

@section('title', __('Job Detail'))

@section('page-header', __('Your Dashboard, :NAME', ['name' => $logged_in_user->name]))

@section('content')
    <div class="p-32 pt-0">
        <div class="general-card preview_applying_job">
            <div class="w-100 general-card__header d-flex flex-wrap">
                <div class="d-flex flex-column align-items-start flex-grow-1">
                    <div class="d-flex flex-column">
                        <div class="color-000000 font-18 font-weight-600 mr-2">@lang('Application Introduction')</div>
                        <div class="color-000000 font-14">@lang('Manage and keep track of all job listings.')</div>
                    </div>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <a href="{{ route('frontend.freelancer.index') }}" class="text-decoration-none">
                        <button
                            class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                            <div class="color-2200A5 font-14 font-weight-bold">@lang('BACK')</div>
                        </button>
                    </a>
                    <button
                        class="btn btn-apply-now btn-general-action d-flex justify-content-center align-items-center hover-button">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('APPLY NOW')</div>
                    </button>
                </div>
            </div>
            <div class="w-100 general-card__body">
                <div class="preview-wrapper border-0">
                    <div class="preview__header d-flex flex-wrap">
                        <div class="flex-center">
                            <div class="flex-center">
                                <img
                                    src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                                    alt="Logo" class="rounded-circle photo">
                            </div>
                            <div class="flex-center flex-column align-items-start">
                                <p class="font-30 color-2200A5 mb-0 text-uppercase font-weight-500">
                                    {{ optional($job->company)->name ?? '' }}</p>
                                <p class="font-16 color-475467 mb-0">
                                    @lang('Job posted by') {{ optional($job->user)->name ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 preview__body">
                        <div class="d-flex flex-column part">
                            <div class="color-000000 font-16 font-weight-500 part__title">@lang('Job Title')</div>
                            <div class="color-475467 font-16 font-weight-500 text-break">{{ $job->name }}</div>
                        </div>
                    </div>
                </div>
                <div class="preview-wrapper border-top-0">
                    <div class="preview__header d-flex flex-wrap">
                        <div class="flex-center">
                            <div class="flex-center">
                                <img
                                    src="{{ asset($logged_in_user->avatar ? $logged_in_user->logo : '/img/avatar_default.svg') }}"
                                    alt="Logo" class="rounded-circle photo">
                            </div>
                            <div class="flex-center flex-column align-items-start">
                                <p class="font-30 color-2200A5 mb-0 text-uppercase font-weight-500">
                                    {{ $logged_in_user->name ?? '' }}</p>
                                <p class="font-16 color-475467 mb-0">@lang('Freelancer')</p>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 preview__body">
                        <div class="d-flex flex-column part">
                            <div class="color-000000 font-16 font-weight-500 part__title">@lang('Introduction')</div>
                            <div
                                class="color-475467 font-16 font-weight-500 text-break">{!! nl2br(e($logged_in_user->bio)) !!}</div>
                        </div>
                    </div>
                </div>

                <div
                    class="w-100 pagination-wrapper d-flex align-items-center justify-content-end justify-content-md-between">
                    <a href="{{ route(USER_CHAT_MESSAGE_ROUTE, $job->user_id) }}"
                       class="text-decoration-none">
                        <button
                            class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                            <div class="color-2200A5 font-14 font-weight-bold">@lang('SEND A MESSAGE')</div>
                        </button>
                    </a>
                    <button
                        class="btn btn-apply-now btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('APPLY NOW')</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('frontend.freelancer.modal_apply_success')
@include('frontend.freelancer.modal_confirm_apply')

