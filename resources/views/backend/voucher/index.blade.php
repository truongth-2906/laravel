@extends('backend.layouts.app')

@section('title', __('Manage Vouchers'))

@section('content')
    <div class="container-fluid container-freelancer pl-0 pr-0 mb-5 admin-freelancers">
        <div class="w-100 header-freelancer">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Manage Vouchers')</div>
                    <div id="total-voucher">
                        {{ $vouchers->total() }} @lang(' Vouchers')
                    </div>
                </div>
                <div class="color-000000 font-14">@lang('Keep track of and manage vouchers.')</div>
            </div>
            <div class="d-flex justify-content-center align-items-center child-second">
                <a href="{{ route('admin.vouchers.create') }}"
                    class="btn btn-general-action d-flex justify-content-center align-items-center hover-button">
                    <img src="{{ asset('/img/add_icon.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('ADD VOUCHER')</div>
                </a>
            </div>
        </div>

        <div class="w-100 area-search">
            <form action="" class="w-100 d-flex justify-content-end" id="search-voucher-form">
                <input type="text" name="search" class="ipt-search-freelancer w-50 mr-3 font-16 color-000000"
                    value="{{ request()->query('search') }}" placeholder="{{ __('Search') }}">
                @if (request()->query('order_by_field') && request()->query('order_by_type'))
                    <input type="text" name="order_by_field" value="{{ request()->query('order_by_field') }}" hidden>
                    <input type="text" name="order_by_type" value="{{ request()->query('order_by_type') }}" hidden>
                @endif
            </form>
        </div>

        <div class="w-100 list-wrapper" id="show-voucher-table">
            @include('backend.voucher.table')
        </div>
    </div>
    @include('backend.voucher.detail-used-modal')
    @include('backend.voucher.confirm-disable-modal')
@endsection
