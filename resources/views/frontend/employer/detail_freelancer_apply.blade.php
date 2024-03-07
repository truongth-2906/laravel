<div class="scroll-table w-100">
    <table class="table w-100 job-application-freelancer">
        <thead>
        <tr>
            <th class="color-475467 font-12 w-15">@lang('Freelancer')</th>
            <th class="color-475467 font-12 w-25">@lang('Job Description')</th>
            <th class="color-475467 font-12 w-15">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-475467 font-12 mr-2">@lang('Date Applied')</div>
                    <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                </div>
            </th>
            <th class="color-475467 font-12 w-15">@lang('Application Status')</th>
            <th class="action-application-area"></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <div class="d-flex justify-content-start align-items-center">
                    <img alt="" src="
                        {{ asset($freelancerDetail->avatar ? $freelancerDetail->logo : '/img/avatar_default.svg') }}"
                         class="avatar-freelancer-job mr-2 disabled">
                    <div class="font-14 color-344054 font-weight-600 disabled">{{ $freelancerDetail->user_name }}</div>
                </div>
            </td>
            <td class="align-middle">
                <div class="color-101828 font-14 font-weight-500 description-table disabled"
                     title="{{ $freelancerDetail->description }}">
                    {{ $freelancerDetail->description }}
                </div>
            </td>
            <td class="align-middle">
                <div class="color-101828 font-14 font-weight-500 disabled">
                    {{ formatDate($freelancerDetail->date_apply) }}
                </div>
            </td>
            <td>
                <div class="d-flex justify-content-start align-items-center">
                    @if($freelancerDetail->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_APPROVE)
                        <div
                            class="d-flex align-items-center application-status application-status-approved disabled">@lang('Approved')</div>
                    @elseif($freelancerDetail->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_REJECT)
                        <div
                            class="d-flex align-items-center application-status application-status-reject disabled">@lang('Rejected')</div>
                    @elseif($freelancerDetail->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_DONE)
                        <div
                            class="d-flex align-items-center application-status application-status-done disabled">@lang('Done')</div>
                    @elseif($freelancerDetail->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_ESCROW_HANDLING)
                        <div
                            class="d-flex align-items-center application-status  application-status-pending text-nowrap disabled">@lang('Escrow handling')</div>
                    @else
                        <div
                            class="d-flex align-items-center application-status application-status-pending disabled">@lang('Pending')</div>
                    @endif
                </div>
            </td>
            <td>
                <div class="d-flex justify-content-end align-items-center">
                    <button class="button-general color-2200A5 hover-button mr-2">@lang('VIEW APPLICATION')</button>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="w-100 d-flex flex-column freelancer-apply-area">
    <div class="w-100 header-detail-freelancer">
        <div class="d-flex justify-content-start align-items-center">
            <img alt=""
                 src="{{ asset($freelancerDetail->avatar ? $freelancerDetail->logo : '/img/avatar_default.svg') }}"
                 class="avatar-detail mr-3">
            <div class="d-flex flex-column align-items-start">
                <div
                    class="font-weight-500 font-30 color-2200A5 text-uppercase">{{ $freelancerDetail->user_name }}</div>
                <div class="font-weight-400 font-16 color-475467">@lang('Freelance applicant')</div>
            </div>
        </div>
        <div class="d-flex justify-content-start align-items-center area-download-and-view">
            <button class="button-general color-2200A5 font-weight-600 hover-button mr-2">@lang('DOWNLOAD CV')</button>
            <a href="{{ route('frontend.freelancer.profile', $freelancerDetail->user_id) }}">
                <button class="button-general color-2200A5 font-weight-600 hover-button">@lang('VIEW PROFILE')</button>
            </a>
        </div>
    </div>
    <div class=""></div>
    <div class="w-100 d-flex flex-column content-detail-freelancer">
        <div class="d-flex flex-column mb-3 parent-introduction">
            <p class="font-16 font-weight-500 color-000000">@lang('Introduction')</p>
            <div class="font-weight-500 font-16 color-475467 bio-freelancer">{{ $freelancerDetail->bio }}</div>
        </div>
        <div class="d-flex flex-column mb-3">
            <p class="font-16 font-weight-500 color-000000">@lang('Availability per week')</p>
            @if($freelancerDetail->hours)
                <div
                    class="font-16 font-weight-500 color-475467">@lang($freelancerDetail->user_name . ' is available for ' . $freelancerDetail->hours . ' to work on projects.')</div>
            @endif
        </div>
        <div class="d-flex flex-column mb-3">
            <p class="font-16 font-weight-500 color-000000">@lang('Rate per hour')</p>
            @if($freelancerDetail->rate_per_hours)
                <div
                    class="font-16 font-weight-500 color-475467">@lang($freelancerDetail->user_name . '’s rate is ' . $freelancerDetail->rate_per_hours . '.')</div>
            @endif
        </div>
    </div>
    <input type="hidden" class="job-application-user" value="{{ $freelancerDetail->user_id }}">
    <div class="w-100 footer-detail-freelancer">
        <a href="{{ route(USER_CHAT_MESSAGE_ROUTE, $freelancerDetail->user_id) }}" class="text-decoration-none">
            <button class="button-general color-2200A5 font-weight-600 hover-button">
                @lang('SEND A MESSAGE')
            </button>
        </a>
        <div class="area-approve-and-reject">
            @if($freelancerDetail->status != \App\Domains\JobApplication\Models\JobApplication::STATUS_PENDING)
                <button class="button-general color-2200A5 font-weight-600 text-uppercase mr-3"
                        disabled>@lang('Approve Application')</button>
                <button class="button-general color-2200A5 font-weight-600 text-uppercase"
                        disabled>@lang('Reject Application')</button>
            @else
                <button
                    class="button-general color-2200A5 font-weight-600 hover-button text-uppercase mr-3 show-modal-approve">@lang('Approve Application')</button>
                <button
                    class="button-general color-2200A5 font-weight-600 hover-button text-uppercase show-modal-reject">@lang('Reject Application')</button>
            @endif
        </div>
    </div>
</div>
