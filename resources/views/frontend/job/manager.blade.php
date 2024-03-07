@extends('frontend.layouts.app')

@section('title', __('Available Jobs'))

@section('content')
    <div class="p-32">
        <div class="general-card">
            <div class="w-100 general-card__header d-flex flex-wrap">
                <div class="d-flex flex-column align-items-start flex-grow-1">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="color-000000 font-18 font-weight-600 mr-2">@lang('All Jobs')</div>
                        <div class="color-2200A5 font-12 d-flex justify-content-center align-items-center font-weight-500 tag-pill tag-pill-primary">
                            {{ $jobs->total() }} @lang('Jobs')
                        </div>
                    </div>
                    <div class="color-000000 font-14">@lang('Manage and keep track of all job listings.')</div>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="button" data-toggle="modal" data-target="#employer-filter-freelancer-modal"
                        class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                        <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-600 text-uppercase">@lang('Download CSV')</div>
                    </button>
                    <a href="{{ route(EMPLOYER_CREATE_JOB) }}"
                        class="btn btn-general-action d-flex justify-content-center align-items-center hover-button">
                        <img src="{{ asset('/img/add_icon.svg') }}" alt="" class="mr-2">
                        <div class="color-2200A5 font-14 font-weight-600 text-uppercase">@lang('ADD JOB')</div>
                    </a>
                </div>
            </div>
            <div class="w-100 general-card__body" id="table-wrapper">
                @include('frontend.job.manager-table')
            </div>
        </div>
    </div>

    @include('backend.includes.modal_confirm_delete', ['type' => TYPE_JOB])

    <form action="#" method="post" id="form-delete" hidden>
        @csrf
        @method('DELETE')
    </form>
@endsection
