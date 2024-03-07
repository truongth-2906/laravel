@extends('frontend.layouts.app')

@section('title', __('Billing'))

@section('content')
    <div class="billing-and-payment">
        <div class="w-100 area-download flex-wrap border-bottom pb-3">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-2200A5 font-30 font-weight-600 text-uppercase mr-2">@lang('Billing')</div>
                </div>
                <div class="color-000000 font-14">@lang('Manage your billing and payment details.')</div>
            </div>
        </div>
        <div class="billing-column pt-4">
            {{-- <div class="container-fluid container-freelancer draw-height billing-width-method billing-border mb-sm-2 d-flex flex-column">
                <div class="header-freelancer flex-grow-1 flex-wrap">
                    <div class="d-flex flex-column align-items-start">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Balance')</div>
                        </div>
                        <div class="color-000000 font-14">@lang('Your total balance is')</div>
                    </div>
                    <div class="color-000000 font-size-48 font-weight-600 mr-2">@lang('$10')</div>
                </div>
                <div class="d-flex justify-content-end align-items-center child-second mt-3 mb-3">
                    <button
                        class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('PAY FREELANCERS NOW')</div>
                        <img src="{{ asset('/img/arrow-up-right.svg') }}" alt="" class="mr-2">
                    </button>
                </div>
            </div> --}}
            <div class="container-fluid container-freelancer draw-height billing-width-method billing-border mb-sm-2 d-flex flex-column">
                <div class="header-freelancer flex-wrap flex-grow-1">
                    <div class="d-flex flex-column align-items-start">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Billing methods')</div>
                        </div>
                        @if (!is_null($escrowEmail))
                            <div class="color-000000 font-14">@lang('You have set up an Escrow account.')</div>
                        @else
                            <div class="color-000000 font-14">@lang('You have not set up any billing methods yet.')</div>
                        @endif
                    </div>
                    @if (!is_null($escrowEmail))
                        <div class="col-12 col-sm-7 d-flex justify-content-start align-items-center escrow-account py-2">
                            <span
                                class="color-344054 font-14 escrow-account__email">{{ $escrowEmail ?? '' }}</span>
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-end align-items-center child-second mt-3 mb-3">
                    <a href="{{ route('frontend.employer.payments.escrow_account.create') }}"
                        class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button">
                        @if (!is_null($escrowEmail))
                            <div class="color-2200A5 font-14 font-weight-bold">@lang('UPDATE YOUR ESCROW ACCOUNT')</div>
                        @else
                            <div class="color-2200A5 font-14 font-weight-bold">@lang('ADD YOUR ESCROW ACCOUNT')</div>
                        @endif
                        <img src="{{ asset('/img/arrow-up-right.svg') }}" alt="" class="mr-2">
                    </a>
                </div>
            </div>
        </div>

        <div class="w-100 area-download flex-wrap pt-4">
            <div class="d-flex justify-content-center align-items-center filter-status mb-sm-2 mr-5">
                <div class="d-flex flex-column align-items-start">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Payment history')</div>
                    </div>
                    <div class="color-000000 font-14">@lang('Overview of all of your previous and outstanding payments.')</div>
                </div>
            </div>
            <div class="d-flex justify-content-end align-items-center child-second mobile">
                <button
                    class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer" disabled>
                    <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('DOWNLOAD ALL')</div>
                </button>
            </div>
            <div class="d-flex align-items-center w-60 child-second mt-2 desktop">
                <button
                    class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer" disabled>
                    <img src="{{ asset('/img/export_icon.svg') }}" alt="" class="mr-2">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('DOWNLOAD ALL')</div>
                </button>
            </div>
        </div>

        <div class="container-fluid container-freelancer front-end pl-0 pr-0 mt-4">
            <div class="w-100 list-wrapper">
                @include('frontend.employer.payment.table')
            </div>
        </div>
    </div>
    @include('frontend.employer.payment.modal_confirm_cancel_transaction')
@endsection
