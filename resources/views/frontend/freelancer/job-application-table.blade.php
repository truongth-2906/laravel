<div class="table-datas">
    <div class="scroll-table">
        <table class="table">
            <thead>
                <tr>
                    <th class="color-475467 font-12 column-md">@lang('Listing as')</th>
                    <th class="color-475467 font-12 column-md">@lang('Job Title')</th>
                    <th class="color-475467 font-12 column-lg">@lang('Job Requirements')</th>
                    <th class="column-sm">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="color-475467 font-12 mr-2">@lang('Application Status')</div>
                            <div class="btn-sort-application-status cursor-pointer"
                                data-type="{{ $orderBy == TYPE_SORT_DESC ? TYPE_SORT_DESC : TYPE_SORT_ASC }}">
                                @if ($orderBy == TYPE_SORT_ASC)
                                    <img class="arrow-up" src="{{ asset('img/arrow-up.svg') }}" alt="">
                                @else
                                    <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                                @endif
                            </div>
                        </div>
                    </th>
                    <th class="column-sm"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobs as $key => $job)
                    <tr>
                        <td class="column-md">
                            <div class="d-flex justify-content-start align-items-center object-info">
                                <div class="avatar mr-2">
                                    <img src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                                        alt="Logo" class="rounded-circle" width="40px" height="40px">
                                </div>
                                <div class="d-flex flex-column align-items-start flex-grow-1">
                                    <div class="font-14 color-101828 data-info font-weight-500"
                                        title="{{ optional($job->company)->name ?? '' }}">
                                        {{ optional($job->company)->name ?? '' }}
                                    </div>
                                    <a href="{{ route('frontend.employer.profile', $job->user->id) }}">
                                        <div class="font-14 color-475467 data-info"
                                            title="{{ optional($job->user)->name ?? '' }}">
                                            {{ optional($job->user)->name ?? '' }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="column-md color-475467 cursor-pointer btn-preview" data-id="{{ $job->id }}" title="{{ $job->name ?? '' }}">
                            {{ $job->name ?? '' }}</td>
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
                                            {{ $job->timezone->diff_from_gtm }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="column-sm text-center">
                            <span class="tag tag__sm tag_pill tag__success tag__open">@lang('Applied')</span>
                        </td>
                        <td class="column-sm">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="flex-center cursor-pointer btn-action hover-button mr-1">
                                    <img class="icon-heart-job" data-job-id="{{ $job->id }}"
                                        src="{{ $job->isSaved() ? asset('/img/icon-red-heart.svg') : asset('/img/icon-heart.svg') }}"
                                        alt="">
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
                    <tr class="text-center">
                        <td colspan="5" class="text-center">@lang('No data')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if ($jobs->hasPages())
    <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-center">
        {{ $jobs->withQueryString()->onEachSide(1)->links() }}
    </div>
@endif
