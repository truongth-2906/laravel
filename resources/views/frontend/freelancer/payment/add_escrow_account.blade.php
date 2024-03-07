@extends('frontend.layouts.app')

@section('title', __('Add your Escrow account'))

@section('content')
    <div class="wrapper">
        <form autocomplete="off"
              action="{{ route('frontend.freelancer.payments.escrow_account.store',['job_id' => request()->query('job_id')]) }}"
              method="post">
            @csrf
            <div class="form-header d-flex justify-content-between align-items-center">
                <div
                    class="text-header primary-color font-weight-500 text-uppercase">@lang('Add your Escrow account')</div>
            </div>

            <div
                class="form-group-function border-bottom pb-20 d-flex justify-content-between align-items-center flex-wrap position-sticky sticky-bar">
                <div class="form-group-button-title">
                    <p class="font-weight-600 font-size-18 text-color">@lang('Escrow account info')</p>
                    <div
                        class="font-weight-400 font-size-14 description-color">@lang('Update your email here.')
                    </div>
                </div>
                <div class="form-group-button d-flex">
                    <a class="button-base btn-general-action hover-button text-decoration-none"
                       href="{{ route(FREELANCER_PAYMENT_INDEX) }}">@lang('Cancel')</a>
                    <button type="submit" class="button-base btn-general-action hover-button"
                            id="button-save">@lang('Save')</button>
                </div>
            </div>

            <div class="py-4">
                <span class="color-475467 font-size-14">
                    @lang('Add the email you registered account with at')
                    <a href="http://escrow.com/" target="_blank">@lang('Escrow.com')</a>
                    @lang("or if you don't have an account we will create a new Escrow account for you.")
                </span><br>
                <span>@lang('You will receive a referral email from Escrow.com')</span>
                <span class="color-475467 font-size-14">@lang('Make sure you add the correct email.')</span>
            </div>

            <div class="border-bottom pb-20 d-flex justify-content-start flex-wrap">
                <label class="font-weight-500 font-size-14 text-color form-title">@lang('Escrow email')</label>
                <div class="form-content">
                    <input type="email" class="form-input-name text-color font-size-16 form-group-base" name="email"
                           placeholder="" value="{{ old('email', $escrowEmail ?? '') }}">
                    @error('email')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </form>
    </div>
@endsection
