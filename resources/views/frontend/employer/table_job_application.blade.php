<div class="scroll-table w-100">
    <table class="table w-100 job-application-freelancer">
        <thead>
        <tr>
            <th class="color-475467 font-12 w-15">@lang('Freelancer')</th>
            <th class="color-475467 font-12 w-25">@lang('Job Description')</th>
            <th class="color-475467 font-12 w-15">
                <div class="d-flex justify-content-start align-items-center cursor-pointer sort-apply-job" data-type="DESC">
                    <div class="color-475467 font-12 mr-2">@lang('Date Applied')</div>
                    <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                    <img class="arrow-up d-none" src="{{ asset('img/arrow-up.svg') }}" alt="">
                </div>
            </th>
            <th class="color-475467 font-12 w-15">@lang('Application Status')</th>
            <th class="action-application-area"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($job->applicants as $application)
            <tr>
                <td>
                    <div class="d-flex justify-content-start align-items-center">
                        <img alt="" src="
                        {{ asset($application->avatar ? $application->logo : '/img/avatar_default.svg') }}"
                             class="avatar-freelancer-job mr-2">
                        <div class="font-14 color-344054 font-weight-600">{{ $application['name'] }}</div>
                    </div>
                </td>
                <td class="align-middle">
                    <div class="color-101828 font-14 font-weight-500 description-table" title="{{ $application->application->pivotParent->description }}">
                        {{ $application->application->pivotParent->description }}
                    </div>
                </td>
                <td class="align-middle">{{ formatDate($application->application->created_at) }}</td>
                <td>
                    <div class="d-flex justify-content-start align-items-center">
                    @if($application->application->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_APPROVE)
                        <div class="d-flex align-items-center application-status application-status-approved">@lang('Approved')</div>
                    @elseif($application->application->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_REJECT)
                        <div class="d-flex align-items-center application-status application-status-reject">@lang('Rejected')</div>
                    @elseif($application->application->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_DONE)
                        <div class="d-flex align-items-center application-status application-status-done">@lang('Done')</div>
                    @elseif($application->application->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_ESCROW_HANDLING)
                        <div class="d-flex align-items-center application-status application-status-pending text-nowrap">@lang('Escrow handling')</div>
                    @else
                        <div class="d-flex align-items-center application-status application-status-pending">@lang('Pending')</div>
                    @endif
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-end align-items-center">
                        <button class="button-general color-2200A5 font-weight-600 hover-button mr-2 detail-freelancer-apply" data-id="{{ $application->id }}">@lang('VIEW APPLICATION')</button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td class="text-center align-middle" colspan="5">@lang('No data')</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
