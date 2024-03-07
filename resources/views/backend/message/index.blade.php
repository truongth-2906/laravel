@extends('backend.layouts.app')

@section('title', __('Manage Messages'))

@section('content')
    <div class="container-fluid container-freelancer pl-0 pr-0 mb-5 admin-freelancers">
        <div class="w-100 header-freelancer">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Manage Messages')</div>
                    <div
                        class="total-message">
                        {{ $messageGroups->total() }} @lang(' Message Group')
                    </div>
                </div>
                <div class="color-000000 font-14">@lang('Keep track of and manage messages.')</div>
            </div>
        </div>

        <div class="w-100 area-search">
            <form action="" class="w-100 d-flex justify-content-end" id="search-message-form">
                <input type="text" name="search" class="ipt-search-freelancer w-50 mr-3 font-16 color-000000"
                           placeholder="{{ __('Search') }}">
            </form>
        </div>

        <div class="w-100 list-wrapper">
            @include('backend.message.table')
        </div>
    </div>
@endsection
