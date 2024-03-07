<div>
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
                        <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                    </div>
                </th>
                <th class="column-sm"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="column-md disabled">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="avatar mr-2">
                            <img src="{{ asset($freelancer->avatar ? $freelancer->logo : '/img/avatar_default.svg') }}"
                                 alt="Logo" class="rounded-circle" width="40px" height="40px">
                        </div>
                        <div class="d-flex flex-column align-items-start flex-grow-1 name-and-mail">
                            <div class="d-flex" title="Heather">
                                <div class="font-14 color-000000 font-weight-500 long-text"
                                     title="{{ $freelancer->name ?? '' }}">
                                    {{ $freelancer->name ?? '' }}
                                </div>
                                @if ($freelancer->isVerified())
                                    <img src="{{ asset('img/verified-tick.svg') }}" alt=""
                                         class="ml-2 mb-1 disabled">
                                @endif
                            </div>
                            <div class="font-14 color-000000 long-text" title="{{ $freelancer->tag_line ?? '' }}">
                                {{ $freelancer->tag_line ?? '' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="column-lg disabled">
                    <div class="d-flex justify-content-start align-items-center flex-wrap">
                        @foreach ($freelancer->categories as $category)
                            <div class="tag tag__pill mr-2 my-1 disabled" title="{{ $category->name ?? '' }}">
                                {{ $category->name ?? '' }}</div>
                        @endforeach
                        @if (!is_null($freelancer->experience))
                            <div class="tag tag__pill mr-2 my-1 disabled"
                                 title="{{ $freelancer->experience->name ?? '' }}">
                                {{ $freelancer->experience->name ?? '' }}
                            </div>
                        @endif
                        @if (!is_null($freelancer->country))
                            <div class="tag tag__pill mr-2 my-1 disabled"
                                 title="{{ $freelancer->country->name ?? '' }}">
                                <img src="{{ asset('img/country/' . $freelancer->country->code . '.png') }}"
                                     alt="logo" class="mr-2">
                                {{ $freelancer->country->name ?? '' }}
                            </div>
                        @endif
                    </div>
                </td>
                <td class="column-sm disabled">
                    @if ($freelancer->isAvailable())
                        <div class="tag tag__sm tag__pill tag__success tag__open disabled">
                            <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                            <div class="font-14">@lang('Available')</div>
                        </div>
                    @else
                        <div class="tag tag__sm tag__pill tag__secondary tag__unavailable disabled">
                            <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                            <div class="font-14">@lang('Unavailable')</div>
                        </div>
                    @endif
                </td>
                <td class="column-sm">
                    <div class="d-flex justify-content-center">
                        <div class="flex-center cursor-pointer btn-action mr-1 hover-button disabled">
                            <img src="{{ asset('/img/icon-red-heart.svg') }}" alt="">
                        </div>
                        <div class="flex-center cursor-pointer btn-action ml-1 active btn-close-preview "
                             data-id="{{ $freelancer->id }}">
                            <img src="{{ asset('/img/icon-eye.svg') }}" alt="">
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="preview-wrapper">
        <div class="preview__header d-flex flex-wrap">
            <div class="d-flex flex-grow-1">
                <div class="flex-center">
                    <img src="{{ asset($freelancer->avatar ? $freelancer->logo : '/img/avatar_default.svg') }}"
                         alt="Logo" class="rounded-circle photo">
                </div>
                <div class="flex-center flex-column align-items-start">
                    <p class="font-30 color-2200A5 mb-0 text-uppercase font-weight-500">
                        {{ $freelancer->name ?? '' }}</p>
                    @if (!is_null($freelancer->country))
                        <p class="font-16 color-475467 mb-0">@lang('Freelancer based in ')
                            {{ optional($freelancer->country)->name ?? '' }}</p>
                    @endif
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center child-second">
                <a href="{{ route(USER_CHAT_MESSAGE_ROUTE, $freelancer->id) }}" class="text-decoration-none">
                    <div
                        class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                        <div class="color-2200A5 font-14 font-weight-600">@lang('SEND MESSAGE')</div>
                    </div>
                </a>
                @php
                    $saveFreelancerIds = Auth::user()->freelancersSaved->pluck('saved_id')->toArray();
                @endphp
                <div class="btn btn-general-action d-flex justify-content-center align-items-center hover-button">
                    <div class="color-2200A5 font-14 font-weight-600 btn-save-freelancer no-reload"
                         data-freelancer-id="{{ $freelancer->id }}">{{ in_array($freelancer->id, $saveFreelancerIds) ? __('UN SAVE FREELANCER') : __('SAVE FREELANCER') }}</div>
                </div>
            </div>
        </div>
        <div class="w-100 preview__body">
            <div class="flex-center flex-column w-100">
                <div class="tabs">
                    <div class="flex-center tab tab-active switch-tab" data-tab="1">@lang('Overview')</div>
                    <div class="flex-center tab switch-tab" data-tab="3">@lang('About the freelancer')</div>
                    <div class="flex-center tab switch-tab" data-tab="4">@lang('Reviews')</div>
                </div>
                <div class=" w-100 tab-content layout-switch-tab tab-overview">
                    <div class="d-flex flex-column part">
                        <p class="part__title">@lang('Skills & Experience')</p>
                        <p class="color-475467">{!! nl2br(e($freelancer->bio)) !!}</p>
                        <div class="d-flex flex-wrap">
                            @foreach ($freelancer->categories as $category)
                                <div class="tag tag__rectangle mr-2 my-1 {{ $category->class ?? '' }}">
                                    {{ $category->name ?? '' }}</div>
                            @endforeach
                            @if (!is_null($freelancer->experience))
                                <div class="tag tag__rectangle tag__experience mr-2 my-1">
                                    {{ $freelancer->experience->name ?? '' }}
                                </div>
                            @endif
                            @if (!is_null($freelancer->country))
                                <div class="tag tag__rectangle tag__country mr-2 my-1">
                                    <img src="{{ asset('img/country/' . $freelancer->country->code . '.png') }}"
                                         alt="country" class="w-100 h-80 mr-2">
                                    {{ $freelancer->country->name ?? '' }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="part">
                        <p class="part__title">@lang('Availability')</p>
                        <div class="color-475467 text-break">
                            @if (!is_null($freelancer->hours))
                                @lang('About :time hours per project.', ['time' => $freelancer->hours])
                            @endif
                        </div>
                    </div>
                    <div class="part">
                        <p class="part__title">@lang('Rate per hours')</p>
                        <div class="color-475467 text-break">
                            @if (!is_null($freelancer->rate_per_hours))
                            @lang('For $:amount per hours.', ['amount' => $freelancer->rate_per_hours ? number_format($freelancer->rate_per_hours) : 0])
                            @endif
                        </div>
                    </div>
                </div>
                <div class="w-100 layout-switch-tab tab-about-employer d-none"></div>
                <div class="w-100 layout-switch-tab tab-review d-none">
                    <div class="flex-center flex-column w-100 align-items-start pt-4 pb-4requirement">
                        @if (count($freelancerReviews) > 0)
                            <p class="mb-2 font-weight-bold font-16 color-000000">@lang('All Reviews')</p>
                            <div class="d-flex justify-content-start align-items-center mb-4">
                                <div class="avg-rate-point mr-2 color-475467 font-14">
                                    {{ renderRatePointAvg(array_sum($freelancerReviews->pluck('star')->toArray()) / count($freelancerReviews)) }}
                                </div>
                                <div class="avg-rate-star d-flex justify-content-center align-items-center mr-2">
                                    @foreach (renderStarReview(array_sum($freelancerReviews->pluck('star')->toArray()) / count($freelancerReviews)) as $star)
                                        <div
                                            class="rate-star mr-1 @if ($star == ACTIVE_STAR) img-star-yellow @elseif($star == HALF_PART_STAR) img-half-star @else img-star-white @endif">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="total-employer-review color-2200A5 font-14">
                                    <span class="total-job-review">{{ count($freelancerReviews) }}</span>
                                    @lang('review(s)') {{ $freelancer->name }}
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-start content-review w-100">
                                @forelse($freelancerReviews as $freelancerReview)
                                    <div class="d-flex flex-column align-items-start mb-4 detail-job-review">
                                        <div class="name-user-review font-16 color-000000 mb-1">
                                            {{ $freelancerReview->userReview->name }}</div>
                                        <div class="rate-review mb-1 d-flex justify-content-start align-items-center">
                                            <div
                                                class="star-review d-flex justify-content-center align-items-center mr-2">
                                                @foreach (renderStarReview($freelancerReview->star) as $star)
                                                    <div
                                                        class="rate-star mr-1 @if ($star == ACTIVE_STAR) img-star-yellow @else img-star-white @endif">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="time-review font-14 color-475467">
                                                {{ $freelancerReview->created_at->diffForHumans() }}</div>
                                        </div>
                                        <div class="comment-review font-16 color-475467">
                                            {!! nl2br($freelancerReview->description) !!}</div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        @else
                            <div class="text-danger">@lang('No reviews yet.')</div>
                        @endif
                    </div>
                    @if (!$myReview)
                        <form class="w-100" id="review-freelancer"
                              action="{{ route('frontend.employer.add-review-freelancer') }}" method="post">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $freelancer->id }}">
                            <div class="flex-wrap flex-column justify-content-start align-items-start w-100 pt-4">
                                <p class="font-weight-bold font-16 color-000000 mb-2">@lang('Write a review')</p>
                                <div class="star-rate-job d-flex justify-content-start align-items-center mr-2 mb-1">
                                    <div class="rate-star img-star-white cursor-pointer mr-1"></div>
                                    <div class="rate-star img-star-white cursor-pointer mr-1"></div>
                                    <div class="rate-star img-star-white cursor-pointer mr-1"></div>
                                    <div class="rate-star img-star-white cursor-pointer mr-1"></div>
                                    <div class="rate-star img-star-white cursor-pointer"></div>
                                </div>
                                <p class="text-danger error-star" data-error="star"></p>
                                <input type="hidden" name="star" value="0">
                                <p class="font-16 color-475467 mb-2">@lang('Have you worked with ' . $freelancer->name . ' before? Describe how it is to work with them below.')</p>
                                <textarea class="enter-comment w-100 font-16" name="description" id="" cols="30"
                                          rows="10"
                                          placeholder="@lang('Enter a description...')"></textarea>
                                <p class="text-danger error-comment" data-error="description"></p>
                            </div>
                            <div class="d-flex justify-content-end align-items-center w-100 pr-4 mt-2 mb-4">
                                <button type="button"
                                        class="btn-action-review hover-button btn-cancel-review-job color-2200A5 font-14 font-weight-600 mr-2">@lang('CANCEL')</button>
                                <button type="button"
                                        class="btn-action-review hover-button btn-submit-review-freelancer color-2200A5 font-14 font-weight-600">@lang('SAVE')</button>
                            </div>
                        </form>
                    @endif
                </div>
                <input type="hidden" class="user-name-create-job" value="{{ $freelancer->name }}">
            </div>
        </div>
    </div>

    <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-end justify-content-md-between">
        <button class="btn-next-page @if ($previousFreelancer) previous-preview-job @endif mr-md-0 mr-3"
                @if (!$previousFreelancer) disabled @endif data-id="{{ $previousFreelancer ?? null }}">
            <img src="{{ asset('/img/backend/round-left.svg') }}" alt="">
            <p class="mb-0 ml-2">@lang('PREVIOUS')</p>
        </button>
        <button class="btn-next-page @if ($nextFreelancer) next-preview-job @endif"
                @if (!$nextFreelancer) disabled @endif data-id="{{ $nextFreelancer ?? null }}">
            <p class="mb-0 mr-2">@lang('NEXT')</p>
            <img src="{{ asset('/img/backend/round-right.svg') }}" alt="">
        </button>
    </div>
</div>
