@extends('backend.layouts.app')

@section('title', __('Manage Jobs'))

@section('content')
    <div class="container-fluid container-job pl-0 pr-0 mb-5 pc-device">
        <div class="w-100 header-job">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-000000 font-18 font-weight-bold mr-2">@lang('All Jobs')</div>
                    <div
                        class="color-2200A5 font-12 total-job d-flex justify-content-center align-items-center">{{ $jobs->total() }}@lang(' Jobs')</div>
                </div>
                <div class="color-000000 font-14">@lang('Keep track of and manage available jobs.')</div>
            </div>
            <div class="d-flex justify-content-center align-items-center child-second">
                <button type="submit" form="form-export-jobs"
                        class="btn btn-general d-flex justify-content-start align-items-center mr-3 hover-button">
                    <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('Download CSV')</div>
                </button>
                <a href="{{ route('admin.job.create') }}"
                   class="btn btn-general d-flex justify-content-center align-items-center hover-button">
                    <img src="{{ asset('/img/add_icon.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('ADD JOB')</div>
                </a>
            </div>
        </div>
        <div class="w-100 list-wrapper">
            @include('backend.job.table')
        </div>
    </div>

    <div class="container-fluid container-job mobile-device">
        <div class="d-flex flex-column align-items-start">
            <div class="d-flex justify-content-start align-items-center">
                <div class="color-000000 font-18 font-weight-bold mr-2">@lang('All Jobs')</div>
                <div
                    class="color-2200A5 font-12 total-job d-flex justify-content-center align-items-center">{{ $jobs->total() }}@lang(' Jobs')</div>
            </div>
            <div class="color-000000 font-14">@lang('Keep track of and manage available jobs.')</div>
        </div>
        <div class="d-flex align-items-center child-second mt-3 mb-3">
            <button type="submit" form="form-export-jobs"
                    class="btn btn-general d-flex justify-content-start align-items-center mr-3">
                <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                <div class="color-2200A5 font-14 font-weight-bold">@lang('Download CSV')</div>
            </button>
            <a href="{{ route('admin.job.create') }}"
               class="btn btn-general d-flex justify-content-center align-items-center">
                <img src="{{ asset('/img/add_icon.svg') }}" alt="" class="mr-2">
                <div class="color-2200A5 font-14 font-weight-bold">@lang('ADD JOB')</div>
            </a>
        </div>
        <div class="w-100 list-wrapper">
            @include('backend.job.table')
        </div>
        <form action="#" method="post" id="delete-job-form" hidden>
            @csrf
            @method('DELETE')
        </form>
    </div>

    @include('backend.includes.modal_confirm_delete', ['type' => TYPE_JOB])
@endsection
