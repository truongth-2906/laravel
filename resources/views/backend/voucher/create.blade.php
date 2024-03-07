@extends('backend.layouts.app')

@section('title', __('Create Voucher'))

@section('content')
    <div class="wrapper">
        <form id="create-voucher-form" autocomplete="off" action="{{ route('admin.vouchers.store') }}" method="POST">
            @csrf
            <div class="form-header d-flex justify-content-between align-items-center">
                <div class="text-header primary-color font-weight-500">@lang("CREATE VOUCHER")</div>
            </div>
            <div class="form-sub-title border-bottom d-flex">
                <div class="font-size-14 primary-color font-weight-600 sub-title">@lang("Voucher Details")</div>
            </div>

            <div
                class="form-group-function border-bottom pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                <div class="form-group-button-title">
                    <p class="font-weight-600 font-size-18 text-color">@lang("Voucher info")</p>
                    <div
                        class="font-weight-400 font-size-14 description-color">@lang("Update voucher details here.")
                    </div>
                </div>
                <div class="form-group-button d-flex">
                    <button type="button" class="button-base btn-general-action hover-button" id="button-cancel"
                            onclick="window.location='{{ route('admin.vouchers.index') }}'">@lang("Cancel")</button>
                    <button type="button" class="button-base btn-general-action hover-button" id="button-save">@lang("Save")</button>
                </div>
            </div>

            @include('backend.voucher.fields')
        </form>
    </div>
@endsection
