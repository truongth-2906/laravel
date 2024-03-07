@foreach($freelancers as $freelancer)
    <tr>
        <td>
            <div class="d-flex justify-content-start align-items-center">
                <img alt="" src="
                {{ asset($freelancer['avatar'] ? config('azure.storage_url') . config('azure.storage_container') . '/public/users/'.$freelancer['avatar'] :'/img/avatar_default.svg') }}"
                     class="avatar-freelancer-job mr-2">
                <div class="font-14 color-344054 font-weight-600">{{ $freelancer->name }}</div>
            </div>
        </td>
        <td class="align-middle">
            <div class="color-101828 font-14 font-weight-500 description-table" title="{{ $freelancer->description }}">
                {{ $freelancer->description }}
            </div>
        </td>
        <td class="align-middle">{{ formatDate($freelancer->date_apply) }}</td>
        <td>
            <div class="d-flex justify-content-start align-items-center">
            @if($freelancer->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_APPROVE)
                <div class="d-flex align-items-center application-status application-status-approved">@lang('Approved')</div>
            @elseif($freelancer->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_REJECT)
                <div class="d-flex align-items-center application-status application-status-reject">@lang('Rejected')</div>
            @elseif($freelancer->status == \App\Domains\JobApplication\Models\JobApplication::STATUS_DONE)
                <div class="d-flex align-items-center application-status application-status-done">@lang('Done')</div>
            @else
                <div class="d-flex align-items-center application-status application-status-pending">@lang('Pending')</div>
            @endif
            </div>
        </td>
        <td>
            <div class="d-flex justify-content-end align-items-center">
                <button class="button-general color-2200A5 font-weight-600 hover-button detail-freelancer-apply mr-2" data-id="{{ $freelancer->user_id }}">@lang('VIEW APPLICATION')</button>
            </div>
        </td>
    </tr>
@endforeach

