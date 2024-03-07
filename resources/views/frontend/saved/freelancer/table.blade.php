<div class="w-100 table-wrapper">
    <table id="freelancers-table">
        <thead>
            <tr>
                <th class="column-md font-12 font-weight-500">@lang('Freelancer')</th>
                <th class="column-lg font-12 font-weight-500">@lang('Skills & Experience')</th>
                <th class="column-sm">
                    <div
                        class="d-flex justify-content-start align-items-center cursor-pointer font-weight-500 btn-sort-status">
                        <div class="color-475467 font-12 mr-2">@lang('Job Availability')</div>
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
            @forelse ($freelancers as $freelancer)
                <tr>
                    <td class="column-md">
                        <div class="d-flex justify-content-start align-items-center">
                            <a class="avatar mr-2" href="{{ route('frontend.freelancer.profile', $freelancer->id) }}">
                                <img src="{{ asset($freelancer->avatar ? $freelancer->logo : '/img/avatar_default.svg') }}"
                                    alt="Logo" class="rounded-circle" width="40px" height="40px">
                            </a>
                            <div class="d-flex flex-column align-items-start flex-grow-1 name-and-mail">
                                <div class="d-flex" title="Heather">
                                    <a class="font-14 color-000000 font-weight-bold long-text freelancer-name" href="{{ route('frontend.freelancer.profile', $freelancer->id) }}"
                                        title="{{ $freelancer->name ?? '' }}">
                                        {{ $freelancer->name ?? '' }}
                                    </a>
                                    @if ($freelancer->isVerified())
                                        <img src="{{ asset('img/verified-tick.svg') }}" alt=""
                                            class="ml-2 mb-1">
                                    @endif
                                </div>
                                <div class="font-14 color-000000 long-text" title="{{ $freelancer->tag_line ?? '' }}">
                                    {{ $freelancer->tag_line ?? '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="column-lg">
                        <div class="d-flex justify-content-start align-items-center flex-wrap">
                            @foreach ($freelancer->categories as $category)
                                <div class="tag tag__pill mr-2 my-1 {{ $category->class }}" title="{{ $category->name ?? '' }}">
                                    {{ $category->name ?? '' }}</div>
                            @endforeach
                            @if (!is_null($freelancer->experience))
                                <div class="tag tag__pill mr-2 my-1" title="{{ $freelancer->experience->name ?? '' }}">
                                    {{ $freelancer->experience->name ?? '' }}
                                </div>
                            @endif
                            @if (!is_null($freelancer->country))
                                <div class="tag tag__pill mr-2 my-1" title="{{ $freelancer->country->name ?? '' }}">
                                    <img src="{{ asset('img/country/' . $freelancer->country->code . '.png') }}"
                                        alt="logo" class="mr-2">
                                    {{ $freelancer->country->name ?? '' }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="column-sm">
                        @if ($freelancer->isAvailable())
                            <div class="tag tag__sm tag__pill tag__success tag__open">
                                <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                <div class="color-496300 font-14">@lang('Available')</div>
                            </div>
                        @else
                            <div class="tag tag__sm tag__pill tag__secondary tag__unavailable">
                                <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                                <div class="color-344054 font-14">@lang('Unavailable')</div>
                            </div>
                        @endif
                    </td>
                    <td class="column-sm">
                        <div class="d-flex justify-content-center">
                            @php
                                $saveFreelancerIds = Auth::user()->freelancersSaved->pluck('saved_id');
                            @endphp
                            <div class="flex-center cursor-pointer btn-action mr-1 hover-button" title="@lang('Saved')">
                                <img class="icon-heart-freelancer" data-freelancer-id="{{ $freelancer->id }}"
                                     src="{{ $saveFreelancerIds->contains($freelancer->id) ? asset('/img/icon-red-heart.svg') : asset('/img/icon-heart.svg') }}"
                                     alt="">
                            </div>
                            @if ($freelancer->isAvailable())
                                <div class="flex-center cursor-pointer btn-action ml-1 btn-preview hover-button"
                                    data-id="{{ $freelancer->id }}">
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
                <td class="text-center" colspan="4">@lang('No data.')</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($freelancers->hasPages())
    <div class="w-100 pagination-wrapper">
        <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-center">
            {{ $freelancers->withQueryString()->onEachSide(1)->links() }}
        </div>
    </div>
@endif
