<div class="employer-table-freelancers">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
            <tr>
                <th class="column-lg">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-name"
                         data-type="{{ $orderByField == user()->getFieldAllowSort('name') ? $orderByType : TYPE_SORT_DESC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Freelancer')</div>
                        @if ($orderByField == user()->getFieldAllowSort('name'))
                            @if($orderByType == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}"
                                     alt="arrow-up-active">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}"
                                     alt="arrow-down-active">
                            @endif
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="arrow-down">
                        @endif
                    </div>
                </th>
                <th class="column-md">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-is-online"
                         data-type="{{ $orderByField == user()->getFieldAllowSort('is_online') ? $orderByType : TYPE_SORT_DESC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Online Status')</div>
                        @if ($orderByField == user()->getFieldAllowSort('is_online'))
                            @if($orderByType == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}"
                                     alt="arrow-up-active">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}"
                                     alt="arrow-down-active">
                            @endif
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="arrow-down">
                        @endif
                    </div>
                </th>
                <th class="color-475467 font-12 column-xl">@lang('Skills & Experience')</th>
                <th class="column-md">
                    <div
                        class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-available-freelancer"
                        data-type="{{ $orderByField == user()->getFieldAllowSort('available') ? $orderByType : TYPE_SORT_DESC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Job Availability')</div>
                        @if ($orderByField == user()->getFieldAllowSort('available'))
                            @if($orderByType == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}"
                                     alt="arrow-up-active">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}"
                                     alt="arrow-down-active">
                            @endif
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="arrow-down">
                        @endif
                    </div>
                </th>
                <th class="column-md"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($freelancers as $key => $freelancer)
                <tr>
                    <td class="align-middle column-lg">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="avatar mr-2">
                                <a href="{{ route('frontend.freelancer.profile', $freelancer->id) }}">
                                    <img
                                        src="{{ asset($freelancer->avatar ? $freelancer->logo : '/img/avatar_default.svg') }}"
                                        alt="Logo" class="rounded-circle h-100 w-100">
                                </a>
                            </div>
                            <div class="d-flex flex-column align-items-start">
                                <a href="{{ route('frontend.freelancer.profile', $freelancer->id) }}">
                                    <div class="d-flex align-items-center font-14 color-000000 font-weight-bold">
                                        <span class="freelancer-info"
                                              title="{{ $freelancer->name }}">{{ $freelancer->name }}</span>
                                        @if($freelancer->active == IS_ACTIVE)
                                            <span class="ml-1">
                                                <img width="15px" height="15px"
                                                     src="{{ asset('/img/verified-tick.svg') }}" alt="verified-tick">
                                                </span>
                                        @endif
                                    </div>
                                </a>
                                <div class="font-14 color-000000 freelancer-info"
                                     title="{{ $freelancer->tag_line ?? '' }}">
                                    {{ $freelancer->tag_line ?? '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="column-md">
                        <div class="d-flex justify-content-start align-items-center">
                            @if ($freelancer->isOnline())
                                <div
                                    class="status-category status-open-job d-flex justify-content-center align-items-center mr-2">
                                    <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-496300">@lang('Online')</div>
                                </div>
                            @else
                                <div
                                    class="status-category inactive-status d-flex justify-content-center align-items-center mr-2">
                                    <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-344054">@lang('Offline')</div>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="align-middle column-xl">
                        <div class="d-flex justify-content-start align-items-center flex-wrap">
                            @foreach($freelancer->categories as $category)
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1 {{ $category->class }}">{{ $category->name }}
                                </div>
                            @endforeach
                            @if( $freelancer->experience)
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1">{{ $freelancer->experience->name ?? ''}}
                                </div>
                            @endif
                            @if($freelancer->country_id)
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1 customer-type flex-center color-000000 pt-0">
                                    <img src="{{ asset('img/country/' . $freelancer->country->code . '.png') }}"
                                         alt="country"
                                         class="w-100 h-80 mr-2">
                                    {{ $freelancer->country->name }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="align-middle column-md">
                        <div class="d-flex justify-content-start align-items-center">
                            @if($freelancer->available == \App\Domains\Auth\Models\User::AVAILABLE)
                                <div
                                    class="status-category status-open-job d-flex justify-content-center align-items-center mr-2">
                                    <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-496300">@lang('Available')</div>
                                </div>
                            @else
                                <div
                                    class="status-category status-close-job d-flex justify-content-center align-items-center mr-2">
                                    <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-344054">@lang('Unavailable')</div>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="align-middle column-md">
                        <div class="flex-center justify-content-start">
                            @php
                                $saveFreelancerIds = Auth::user()->freelancersSaved->pluck('saved_id');
                            @endphp
                            <div class="flex-center cursor-pointer mr-1 btn-action" title="@lang('Saved')">
                                <img class="icon-heart-freelancer" data-freelancer-id="{{ $freelancer->id }}"
                                     src="{{ $saveFreelancerIds->contains($freelancer->id) ? asset('/img/icon-red-heart.svg') : asset('/img/icon-heart.svg') }}"
                                     alt="">
                            </div>
                            @if($freelancer->available == \App\Domains\Auth\Models\User::AVAILABLE)
                                <div class="flex-center cursor-pointer btn-icon-func btn-preview-freelancer"
                                     data-id="{{ $freelancer->id }}">
                                    <img src="{{ asset('/img/icon-eye.svg') }}" alt="">
                                </div>
                            @else
                                <div class="flex-center cursor-pointer btn-icon-func">
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
            {{ $freelancers->withQueryString()->onEachSide(1)->links() }}
        </div>
    </div>
</div>
