<div class="table-freelancers">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
            <tr>
                <th class="color-475467 font-12">@lang('Listing as')</th>
                <th class="color-475467 font-12">@lang('Job Title')</th>
                <th class="color-475467 font-12">@lang('Job Requirements')</th>
                <th class="w-10">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-status"
                         data-type="{{ $orderBy == TYPE_SORT_ASC ? TYPE_SORT_DESC : TYPE_SORT_ASC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Job Status')</div>
                        @if($orderBy == TYPE_SORT_ASC)
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
            @forelse($jobs as $key => $job)
                <tr>
                    <td class="align-middle">
                        <div class="d-flex justify-content-start align-items-center">
                            <a href="{{ route('frontend.employer.profile', $job->user->id) }}">
                                <div class="avatar mr-2">
                                    <img alt="Logo" class="rounded-circle h-100 w-100 cursor-pointer"
                                         src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}">
                                </div>
                            </a>
                            <div class="d-flex flex-column align-items-start name-and-mail">
                                <div class="font-14 color-000000 font-weight-bold long-text"
                                     title="{{ optional($job->company)->name }}">{{ optional($job->company)->name }}
                                </div>
                                <a href="{{ route('frontend.employer.profile', $job->user->id) }}">
                                    <div class="font-14 color-000000 long-text"
                                         title="{{ optional($job->user)->name }}">{{ optional($job->user)->name }}
                                    </div>
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">
                        <p class="color-000000 mb-0 cursor-pointer btn-eye btn-name"
                           data-id="{{ $job->id }}" data-toggle="collapse" data-target="collapse-{{ $key }}">
                            {{ $job->name }}
                        </p>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex justify-content-start align-items-center flex-wrap">
                            @foreach($job->categories as $category)
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1 {{ $category->class }}">{{ $category->name }}</div>
                            @endforeach
                            <div
                                class="status-category mr-2 job-requirement-tag mb-1 mt-1">{{ $job->experience->name }}</div>
                            @if($job->country_id)
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1 customer-type flex-center color-000000">
                                    <img src="{{ asset('img/country/' . $job->country->code . '.png') }}" alt="country"
                                         class="w-100 h-80 mr-2">
                                    {{ $job->timezone->diff_from_gtm }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex justify-content-start align-items-center">
                            @if($job->mark_done == \App\Domains\Job\Models\Job::MARK_DONE)
                                <div
                                    class="status-category status-open-job d-flex justify-content-center align-items-center mr-2">
                                    <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-496300">@lang('Done')</div>
                                </div>
                            @else
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
                            @endif
                        </div>
                    </td>
                    <td class="align-middle">
                        <div class="flex-center justify-content-start">
                            @php
                                $saveJobIds = Auth::user()->jobsSaved->pluck('saved_id')->toArray();
                            @endphp
                            <div class="flex-center cursor-pointer btn-action hover-button mr-3">
                                <img class="icon-heart-job" data-job-id="{{ $job->id }}"
                                     src="{{ in_array($job->id, $saveJobIds) ? asset('/img/icon-red-heart.svg') : asset('/img/icon-heart.svg') }}"
                                     alt="">
                            </div>

                            @if($job->status == \App\Domains\Job\Models\Job::STATUS_OPEN)
                                <div class="flex-center cursor-pointer btn-eye accordion-toggle {{ !auth()->user()->is_hidden ? 'btn-action' : ''}} "
                                    @if (!auth()->user()->is_hidden)
                                        data-id="{{ $job->id }}" data-toggle="collapse" data-target="collapse-{{ $key }}"
                                    @endif
                                    >
                                    <button
                                        class="btn-general-action d-flex justify-content-start align-items-center hover-button" {{ auth()->user()->is_hidden ? 'disabled' : '' }}>
                                        <div class="color-2200A5 font-14 font-weight-bold ml-1 mr-1">@lang('APPLY')</div>
                                    </button>
                                </div>
                            @else
                                <div class="flex-center btn-action ml-1">
                                    <img src="{{ asset('/img/icon-eye-off.svg') }}" alt="">
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="text-center">
                    <td colspan="5" class="text-danger">@lang('No data')</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center align-items-center mb-3">
        <div class="next-back-pagination d-flex justify-content-center align-items-center">
            {{ $jobs->onEachSide(1)->links() }}
        </div>
    </div>
</div>
