<div class="table-jobs table-datas admin-job-table" id="admin-job-manager-table">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
            <tr>
                <th class="column-sm">
                    <div class="custom-checkbox">
                        <div class="has-checked-checkbox">
                            <input type="checkbox" id="check-all-{{ \App\Domains\Auth\Models\User::DEVICE_PC }}"
                                   class="ipt-has-check">
                            <label for="check-all-{{ \App\Domains\Auth\Models\User::DEVICE_PC }}"
                                   class="d-flex align-items-center"></label>
                        </div>
                    </div>
                </th>
                <th class="column-lg">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-employer-name"
                         data-type="{{ $orderByField == job()->getFieldAllowSort('employer_name') ? $orderByType : TYPE_SORT_DESC }}">
                         <div class="color-475467 font-12 mr-2">@lang('Listing as')</div>
                         @if ($orderByField == job()->getFieldAllowSort('employer_name'))
                             @if($orderByType == TYPE_SORT_ASC)
                                 <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}" alt="">
                             @else
                                 <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}" alt="">
                             @endif
                         @else
                             <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                         @endif
                    </div>
                </th>
                <th class="column-lg">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-name"
                         data-type="{{ $orderByField == job()->getFieldAllowSort('name') ? $orderByType : TYPE_SORT_DESC }}">
                         <div class="color-475467 font-12 mr-2">@lang('Job Title')</div>
                         @if ($orderByField == job()->getFieldAllowSort('name'))
                             @if($orderByType == TYPE_SORT_ASC)
                                 <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}" alt="">
                             @else
                                 <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}" alt="">
                             @endif
                         @else
                             <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                         @endif
                    </div>
                </th>
                <th class="color-475467 font-12 column-xl">@lang('Job Requirements')</th>
                <th class="column-md">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-status"
                         data-type="{{ $orderByField == job()->getFieldAllowSort('status') ? $orderByType : TYPE_SORT_DESC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Status')</div>
                        @if ($orderByField == job()->getFieldAllowSort('status'))
                            @if($orderByType == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}" alt="">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}" alt="">
                            @endif
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                        @endif
                    </div>
                </th>
                <th class="column-md"></th>
            </tr>
            </thead>
            <tbody>
            <form id="form-export-jobs" action="{{ route('admin.job.export') }}" method="GET">
                @forelse($jobs as $job)
                    <tr>
                        <td class="custom-checkbox column-sm">
                            <input type="checkbox" id="check-box-{{ $job->id }}" class="ipt-check-account"
                                   data-id="{{ $job->id }}" form="form-export-jobs" name="checkBoxIds[]" value="{{ $job->id }}">
                            <label for="check-box-{{ $job->id }}" class="d-flex align-items-center"></label>
                        </td>
                        <td class="column-lg">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="avatar mr-2">
                                    <img
                                        src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                                        alt="Logo" class="rounded-circle h-100 w-100">
                                </div>
                                <div class="d-flex flex-column align-items-start name-and-mail">
                                    <div class="font-14 color-000000 font-weight-bold long-text employer-info"
                                         title="{{ optional($job->company)->name }}">{{ optional($job->company)->name }}
                                    </div>
                                    <a href="{{ route('admin.job.edit', $job->id) }}">
                                        <div class="font-14 color-000000 long-text employer-info"
                                             title="{{ optional($job->user)->name }}">{{ optional($job->user)->name }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="column-lg">{{ $job->name }}</td>
                        <td class="column-xl">
                            <div class="d-flex justify-content-start align-items-center flex-wrap">
                                @foreach($job->categories as $category)
                                    <div
                                        class="status-category mr-2 job-requirement-tag mb-1 mt-1 {{ $category->class }}">{{ $category->name }}</div>
                                @endforeach
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1">{{ $job->experience->name }}</div>
                            </div>
                        </td>
                        <td class="column-md">
                            <div class="d-flex justify-content-start align-items-center">
                                @if($job->status == \App\Domains\Job\Models\Job::STATUS_OPEN)
                                    <div
                                        class="status-category status-open-job d-flex justify-content-center align-items-center mr-2">
                                        <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                        <div class="color-496300">@lang('Open')</div>
                                    </div>
                                @else
                                    <div
                                        class="status-category status-close-job d-flex justify-content-center align-items-center mr-2">
                                        <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                                        <div class="color-344054">@lang('Close')</div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="column-md">
                            <div class="d-flex justify-content-center align-items-center">
                                <button type="button" class="btn d-flex justify-content-center align-items-center hover-button-list admin-btn-delete-job" data-action="{{ route('admin.job.delete', $job->id) }}">
                                    <img src="{{ asset('/img/delete_icon.svg') }}" alt=""
                                         class="cursor-pointer icon-delete-job" data-id="{{ $job->id }}">
                                </button>
                                <a href="{{ route('admin.job.edit', $job->id) }}"
                                   class="btn d-flex justify-content-center align-items-center hover-button-list">
                                    <img src="{{ asset('/img/edit_icon.svg') }}" alt="" class="cursor-pointer">
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="6" class="text-danger">@lang('No data')</td>
                    </tr>
                @endforelse
            </form>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center align-items-center mb-3">
        <div class="next-back-pagination d-flex justify-content-center align-items-center">
            {{ $jobs->withQueryString()->onEachSide(1)->links() }}
        </div>
    </div>
</div>
