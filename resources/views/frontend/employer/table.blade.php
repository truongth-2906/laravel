<div class="table-jobs table-datas" id="my-jobs-table">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
            <tr>
                <th>
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="color-475467 font-12 mr-2">@lang('Listing as')</div>
                    </div>
                </th>
                <th class="color-475467 font-12">@lang('Job Title')</th>
                <th class="color-475467 font-12">@lang('Job Requirements')</th>
                <th>
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-status"
                         data-type="{{ $orderBy == 'ASC' ? 'DESC' : 'ASC' }}">
                        <div class="color-475467 font-12 mr-2">@lang('Job Status')</div>
                        @if($orderBy == 'ASC')
                            <img class="arrow-up" src="{{ asset('img/arrow-up.svg') }}" alt="">
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                        @endif
                    </div>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($jobs as $job)
                <tr>
                    <td>
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="avatar mr-2">
                                <img
                                    src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                                    alt="Logo" class="rounded-circle h-100 w-100">
                            </div>
                            <div class="d-flex flex-column align-items-start name-and-mail">
                                <div class="font-14 color-000000 font-weight-bold long-text"
                                     title="{{ optional($job->company)->name }}">{{ optional($job->company)->name }}
                                </div>
                                <div class="font-14 color-000000 long-text"
                                     title="{{ optional($job->user)->name }}">{{ optional($job->user)->name }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $job->name }}</td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center flex-wrap">
                            @foreach($job->categories as $category)
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1 {{ $category->class }}">{{ $category->name }}</div>
                            @endforeach
                            <div
                                class="status-category mr-2 job-requirement-tag mb-1 mt-1">{{ $job->experience->name }}</div>
                            <div class="status-category mr-2 job-requirement-tag mb-1 mt-1 padding-top-2">
                                <img src="{{ asset('img/country/' . $job->country->code . '.png') }}" alt="logo">
                                {{ $job->timezone->offset }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center">
                            @if($job->mark_done == \App\Domains\Job\Models\Job::MARK_DONE)
                                <div
                                    class="status-category status-open-job d-flex justify-content-center align-items-center mr-2">
                                    <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-496300">@lang('Done')</div>
                                </div>
                            @else
                                @if($job->status == STATUS_OPEN)
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
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center">
                            <button class="btn d-flex justify-content-center align-items-center {{ $job->isMarkDone() ? '' : 'cursor-pointer hover-button-list btn-confirm-delete-job' }}" data-action="{{ route('frontend.employer.jobs.destroy', $job->id) }}" {{ $job->isMarkDone() ? 'disabled' : '' }}>
                                <img src="{{ asset('/img/delete_icon.svg') }}" alt=""
                                     class=" icon-delete-job" data-id="">
                            </button>
                            <a href="{{ route('frontend.employer.jobs.edit', $job->id) }}"
                               class="btn d-flex justify-content-center align-items-center hover-button-list">
                                <img src="{{ asset('/img/edit_icon.svg') }}" alt="" class="cursor-pointer">
                            </a>

                            @if($job->status == \App\Domains\Job\Models\Job::STATUS_OPEN)
                                <div class="flex-center cursor-pointer btn-icon-func view-job-employer"
                                     data-id="{{ $job->id }}">
                                    <img src="{{ asset('/img/icon-eye.svg') }}" class="w-75" alt="">
                                </div>
                            @else
                                <div class="flex-center cursor-pointer btn-icon-func">
                                    <img src="{{ asset('/img/icon-eye-off.svg') }}" class="w-75" alt="">
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="text-center">
                    <td colspan="6" class="text-danger">@lang('No data')</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center align-items-center mb-3">
        <div class="next-back-pagination d-flex justify-content-center align-items-center">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
