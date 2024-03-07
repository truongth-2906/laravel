<div class="manager-job-table">
    <div class="scroll-table">
        <table class="table">
            <thead>
                <tr>
                    <th class="column-sm">
                        <div class="custom-checkbox">
                            <div class="has-checked-checkbox">
                                <input type="checkbox" id="check-all-job" class="ipt-has-check"
                                    {{ $isCheckAll ? 'checked' : '' }}>
                                <label for="check-all-job" class="d-flex align-items-center"></label>
                            </div>
                        </div>
                    </th>
                    <th class="column-lg">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="color-475467 font-12 mr-2">@lang('Listing as')</div>
                        </div>
                    </th>
                    <th class="color-475467 font-12 column-lg">@lang('Job Title')</th>
                    <th class="color-475467 font-12 column-xl">@lang('Job Requirements')</th>
                    <th class="column-md">
                        <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-status">
                            <div class="color-475467 font-12 mr-2">@lang('Job Status')</div>
                            @if ($orderBy == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up.svg') }}" alt="">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                            @endif
                        </div>
                    </th>
                    <th class="column-md"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobs as $job)
                    <tr>
                        <td class="align-middle column-sm">
                            <div class="has-checked-checkbox custom-checkbox">
                                <input type="checkbox" id="check-job-{{ $job->id }}"
                                    class="ipt-has-check check-all" {{ $isCheckAll ? 'checked' : '' }}>
                                <label for="check-job-{{ $job->id }}" class="d-flex align-items-center"></label>
                            </div>
                        </td>
                        <td class="align-middle column-lg">
                            <div class="d-flex justify-content-start align-items-center object-info">
                                <a href="{{ route('frontend.employer.profile', $job->user->id) }}">
                                    <div class="avatar mr-2">
                                        <img alt="Logo" class="rounded-circle h-100 w-100 cursor-pointer"
                                            src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}">
                                    </div>
                                </a>
                                <div class="d-flex flex-column align-items-start flex-grow-1">
                                    <div class="font-14 color-101828 data-info font-weight-500"
                                        title="{{ optional($job->company)->name ?? '' }}">
                                        {{ optional($job->company)->name ?? '' }}
                                    </div>
                                    @if (!is_null($job->user) && !$job->isAuthor())
                                        <a class="font-14 color-475467 data-info"
                                            href="{{ !is_null($job->user) && !$job->isAuthor() ? route('frontend.employer.profile', $job->user->id) : '#' }}"
                                            title="{{ optional($job->user)->name ?? '' }}">
                                            {{ optional($job->user)->name ?? '' }}
                                        </a>
                                    @else
                                        <div class="font-14 color-475467 data-info"
                                            title="{{ optional($job->user)->name ?? '' }}">
                                            {{ optional($job->user)->name ?? '' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="align-middle color-101828 font-14 column-lg" title="{{ $job->name ?? '' }}">
                            {{ $job->name ?? '' }}</td>
                        <td class="align-middle column-xl">
                            <div class="d-flex justify-content-start align-items-center flex-wrap">
                                @foreach ($job->categories as $category)
                                    <div class="tag tag__pill mr-2 my-1 {{ $category->class }}"
                                        title="{{ $category->name ?? '' }}">
                                        {{ $category->name ?? '' }}</div>
                                @endforeach
                                @if (!is_null($job->experience))
                                    <div class="tag tag__pill mr-2 my-1" title="{{ $job->experience->name ?? '' }}">
                                        {{ $job->experience->name ?? '' }}
                                    </div>
                                @endif
                                @if (!is_null($job->country))
                                    <div class="tag tag__pill mr-2 my-1" title="{{ $job->country->name ?? '' }}">
                                        <img src="{{ asset('img/country/' . $job->country->code . '.png') }}"
                                            alt="logo" class="mr-2">
                                        {{ $job->country->name ?? '' }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="align-middle column-md">
                            @if ($job->isOpen())
                                <div class="tag tag__sm tag__pill tag__success tag__open">
                                    <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-496300 font-14">@lang('Open')</div>
                                </div>
                            @else
                                <div class="tag tag__sm tag__pill tag__secondary tag__close">
                                    <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-344054 font-14">@lang('Close')</div>
                                </div>
                            @endif
                        </td>
                        <td class="align-middle column-md">
                            <div class="d-flex justify-content-center">
                                @if ($job->isAuthor())
                                    <button
                                        class="flex-center mr-1 btn-action {{ $job->isMarkDone() ? '' : 'cursor-pointer hover-button-list btn-confirm-delete-job' }}"
                                        title="@lang('Delete')"
                                        {{ $job->isMarkDone() ? 'disabled' : '' }}
                                        data-action="{{ route('frontend.employer.jobs.destroy', $job->id) }}">
                                        <img src="{{ asset('/img/delete_icon.svg') }}" alt=""
                                            class="icon-delete-job">
                                    </button>
                                    <a href="{{ route('frontend.employer.jobs.edit', $job->id) }}"
                                        title="@lang('Edit')"
                                        class="flex-center cursor-pointer mr-1 btn-action hover-button">
                                        <img src="{{ asset('/img/edit_icon.svg') }}" alt="">
                                    </a>
                                @else
                                    <div class="flex-center cursor-pointer mr-1 btn-action hover-button"
                                        title="@lang('Saved')">
                                        <img class="icon-heart-job" data-job-id="{{ $job->id }}"
                                            src="{{ $job->isSaved() ? asset('/img/icon-red-heart.svg') : asset('/img/icon-heart.svg') }}"
                                            alt="">
                                    </div>
                                    @if ($job->isOpen())
                                        <div class="flex-center cursor-pointer ml-1 btn-action btn-preview  hover-button"
                                            title="@lang('Preview')" data-id="{{ $job->id }}">
                                            <img src="{{ asset('/img/icon-eye.svg') }}" alt="">
                                        </div>
                                    @else
                                        <div class="flex-center ml-1 btn-action">
                                            <img src="{{ asset('/img/icon-eye-off.svg') }}" alt="">
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">@lang('No data')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($jobs->hasPages())
        <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-center">
            {{ $jobs->withQueryString()->onEachSide(1)->links() }}
        </div>
    @endif
</div>
