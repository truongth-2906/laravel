<div class="modal fade" id="modal-confirm-apply" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-close position-absolute" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="flex-center flex-column w-100">
                    <div class="flex-center">
                        <img src="{{ asset($logged_in_user->avatar ? $logged_in_user->logo : '/img/avatar_default.svg') }}"
                            alt="Logo" class="rounded-circle" width="56px" height="56px">
                    </div>
                    <div class="modal-body__title">@lang('Application Details')</div>
                    <div class="modal-body__notice">@lang('Please find the job application details below. Once you are ready you can click on apply now to proceed with your application.')</div>
                    <div class="job-amount-and-fees">
                        <div class="item">
                            <label>@lang('Job Title'):</label>
                            <div class="item__content">{{ $job->name ?? '' }}</div>
                        </div>
                        <div class="item">
                            <label>@lang('Job Description'):</label>
                            <div class="item__content">{!! nl2br(e($job->description ?? '')) !!}</div>
                        </div>
                        <div class="item item-breakdown">
                            @lang('Payment Breakdown')
                        </div>
                        <div class="item">
                            <label>@lang('Job Budget'):</label>
                            <div class="item__content">@lang('$') {{ $job->wage ?? '' }}</div>
                        </div>
                        <div class="item">
                            <label>@lang('Service Fee'):</label>
                            <div class="item__content">@lang('$') {{ $job->freelance_service_fee_pay ?? '' }}</div>
                        </div>
                        <div class="item">
                            <label>@lang('Escrow Fee'):</label>
                            <div class="item__content">@lang('$') {{ $job->escrow_fee ?? '' }}</div>
                        </div>
                        <div class="item">
                            <label>@lang('Total Received After Job'):</label>
                            <div class="item__content">@lang('$') {{ $job->total_received_after ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button
                    class="button-base btn-general-action hover-button text-decoration-none" data-dismiss="modal">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('Go Back')</div>
                </button>
                <button
                    class="button-base btn-general-action hover-button text-decoration-none btn-confirmed-apply">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('APPLY NOW')</div>
                </button>
            </div>
        </div>
    </div>
</div>
