<div id="table-freelancer-saved-job" class="table-datas">
    <div class="scroll-table">
        <table class="table">
            <thead>
                <tr>
                    <th class="color-475467 font-12 column-md">@lang('Listing as')</th>
                    <th class="color-475467 font-12 column-md">@lang('Job Title')</th>
                    <th class="color-475467 font-12 column-lg">@lang('Job Requirements')</th>
                    <th class="column-sm">
                        <div class="d-flex justify-content-start align-items-center cursor-pointer font-weight-500 btn-sort-status"
                            data-type="{{ $orderBy == TYPE_SORT_DESC ? TYPE_SORT_ASC : TYPE_SORT_DESC }}">
                            <div class="color-475467 font-12 mr-2">@lang('Status')</div>
                            @if ($orderBy == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up.svg') }}" alt="">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                            @endif
                        </div>
                    </th>
                    <th class="column-sm"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobSaved as $job)
                    <tr>
                        <td class="column-md">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="avatar mr-2">
                                    <img src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                                        alt="Logo" class="rounded-circle" width="40px" height="40px">
                                </div>
                                <div class="d-flex flex-column align-items-start flex-grow-1 name-and-mail">
                                    <div class="font-14 color-000000 font-weight-500 long-text"
                                        title="{{ optional($job->company)->name ?? '' }}">
                                        {{ optional($job->company)->name ?? '' }}
                                    </div>
                                    <a href="{{ route('frontend.employer.profile', $job->user->id) }}">
                                        <div class="font-14 color-000000 long-text"
                                            title="{{ optional($job->user)->name ?? '' }}">
                                            {{ optional($job->user)->name ?? '' }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="column-md font-14 color-000000 cursor-pointer btn-preview" data-id="{{ $job->id }}" title="{{ $job->name ?? '' }}">
                            {{ $job->name ?? '' }}
                        </td>
                        <td class="column-lg">
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
                        <td class="column-sm">
                            <div class="d-flex justify-content-center padding-top-10">
                                @if ($job->isOpen())
                                    <div class="flex-center tag tag__sm tag__pill tag__success tag__open">
                                        <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                        <div class="color-496300 font-14">@lang('Open')</div>
                                    </div>
                                @else
                                    <div class="flex-center tag tag__sm tag__pill tag__secondary tag__close">
                                        <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                                        <div class="color-344054 font-14">@lang('Close')</div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="column-sm">
                            <div class="d-flex justify-content-center">
                                <div class="flex-center cursor-pointer mr-1 btn-action  hover-button">
                                    <img class="icon-heart-job" data-job-id="{{ $job->id }}"
                                        src="{{ asset('/img/icon-red-heart.svg') }}" alt="">
                                </div>
                                @if ($job->isOpen())
                                    <div class="flex-center cursor-pointer btn-action ml-1 btn-preview hover-button"
                                        data-id="{{ $job->id }}">
                                        <img src="{{ asset('/img/icon-eye.svg') }}" alt="">
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
                    <tr>
                        <td colspan="5" class="text-center">@lang('No data.')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($jobSaved->hasPages())
        <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-center">
            {{ $jobSaved->withQueryString()->onEachSide(1)->links() }}
        </div>
    @endif
</div>
