<div class="table-freelancers">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
            <th class="color-475467 font-12">@lang('Freelancer')</th>
            <th class="color-475467 font-12">@lang('Skills & Experience')</th>
            <th class="w-10">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-475467 font-12 mr-2">@lang('Job Availability')</div>
                    <img class="arrow-up" src="{{ asset('img/arrow-down.svg') }}" alt="">
                </div>
            </th>
            <th></th>
            </thead>
            <tbody>
            <tr>
                <td class="align-middle">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="avatar mr-2 disabled">
                            <img src="{{ asset($freelancer->avatar ? $freelancer->logo : '/img/avatar_default.svg') }}"
                                 alt="Logo" class="rounded-circle h-100 w-100">
                        </div>
                        <div class="d-flex flex-column align-items-start name-and-mail">
                            <div class="font-14 color-000000 font-weight-bold long-text disabled"
                                 title="{{ $freelancer->name ?? '' }}">{{ $freelancer->name ?? '' }}
                            </div>
                            <div class="font-14 color-000000 long-text disabled"
                                 title="{{ $freelancer->tag_line ?? '' }}">
                                {{ $freelancer->tag_line ?? '' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="align-middle">
                    <div class="d-flex justify-content-start align-items-center flex-wrap">
                        @foreach ($freelancer->categories as $category)
                            <div
                                class="status-category mr-2 job-requirement-tag mb-1 mt-1 disabled {{ $category->class ?? '' }}">
                                {{ $category->name ?? '' }}</div>
                        @endforeach
                        @if (!is_null($freelancer->experience))
                            <div class="status-category mr-2 job-requirement-tag mb-1 mt-1 disabled">
                                {{ $freelancer->experience->name ?? '' }}</div>
                        @endif
                        @if (!is_null($freelancer->country))
                            <div
                                class="status-category mr-2 job-requirement-tag mb-1 mt-1 customer-type flex-center color-000000 disabled pt-0">
                                <img src="{{ asset('img/country/' . $freelancer->country->code . '.png') }}"
                                     alt="country" class="w-100 h-80 mr-2">
                                {{ $freelancer->country->name ?? '' }}
                            </div>
                        @endif
                    </div>
                </td>
                <td class="align-middle">
                    <div class="d-flex justify-content-start align-items-center">
                        @if ($freelancer->available == \App\Domains\Auth\Models\User::AVAILABLE)
                            <div
                                class="status-category disabled status-open-job d-flex justify-content-center align-items-center mr-2">
                                <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                <div class="color-496300">@lang('Available')</div>
                            </div>
                        @else
                            <div
                                class="status-category disabled status-close-job d-flex justify-content-center align-items-center mr-2">
                                <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                                <div class="color-344054">@lang('Unavailable')</div>
                            </div>
                        @endif
                    </div>
                </td>
                <td class="align-middle">
                    <div class="flex-center justify-content-start">
                        @php
                            $saveFreelancerIds = Auth::user()->freelancersSaved->pluck('saved_id');
                        @endphp
                        <div class="flex-center cursor-pointer btn-icon-func mr-4 ">
                            <img src="{{ $saveFreelancerIds->contains($freelancer->id) ? asset('/img/icon-red-heart.svg') : asset('/img/icon-heart.svg') }}" alt="" class="icon-heart-preview">
                        </div>
                        <div class="flex-center cursor-pointer btn-icon-func btn-active back-to-page btn-active"
                             data-id="{{ $freelancer->id }}">
                            <img src="{{ asset('/img/icon-eye.svg') }}" alt="">
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="flex-center flex-column w-100 job-detail freelancer-expand-wrapper">
    <div class="w-100 header-freelancer px-4">
        <div class="w-100 d-flex flex-wrap pb-3">
            <div class="d-flex flex-column align-items-start flex-grow-1">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="flex-center">
                        <img src="{{ asset($freelancer->avatar ? $freelancer->logo : '/img/avatar_default.svg') }}"
                             alt="Logo" class="rounded-circle photo-preview">
                    </div>
                    <div class="flex-center flex-column align-items-start ml-2">
                        <p class="font-30 color-2200A5 mb-0 text-uppercase font-weight-500"
                           title="{{ $freelancer->name ?? '' }}">
                            {{ $freelancer->name ?? '' }}</p>
                        @if (!is_null($freelancer->country))
                            <p class="font-16 color-475467 mb-0">@lang('Freelancer based in ')
                                {{ optional($freelancer->country)->name ?? '' }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center child-second pt-md-0 pt-3">
                <a href="{{ route(USER_CHAT_MESSAGE_ROUTE, $freelancer->id) }}" class="text-decoration-none">
                    <div
                        class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                        <div class="color-2200A5 font-14 font-weight-600">@lang('SEND MESSAGE')</div>
                    </div>
                </a>
                @php
                    $saveFreelancerIds = Auth::user()->freelancersSaved->pluck('saved_id')->toArray();
                @endphp
                <div class="btn btn-general-action d-flex justify-content-center align-items-center hover-button color-2200A5 btn-save-freelancer" data-freelancer-id="{{ $freelancer->id }}">
                        {{ in_array($freelancer->id, $saveFreelancerIds) ? __('UN SAVE FREELANCER') : __('SAVE FREELANCER') }}
                </div>
            </div>
        </div>
    </div>
    <div class="flex-center w-100 job-content">
        <div class="flex-center flex-column w-100 over-view pl-4 pr-4">
            <div class="flex-center justify-content-start w-100 tabs flex-wrap">
                <div class="flex-center tab tab-active color-667085 cursor-pointer switch-tab" data-tab="1">
                    @lang('Overview')</div>
                <div class="flex-center tab color-667085 cursor-pointer switch-tab" data-tab="2">@lang('Description')
                </div>
                <div class="flex-center tab color-667085 cursor-pointer switch-tab"
                     data-tab="3">@lang('About the freelancer')
                </div>
                <div class="flex-center tab color-667085 cursor-pointer switch-tab" data-tab="4">@lang('Reviews')
                </div>
            </div>
            <div class="flex-center flex-column w-100 layout-switch-tab tab-overview ">
                <div class="flex-center flex-column w-100 align-items-start py-4 job-requirement">
                    <p class="mb-2 font-weight-500 font-16 color-000000">@lang('Skills & Experience')</p>
                    <p class="mb-2 font-16 color-475467">{!! nl2br($freelancer->bio) !!}</p>
                    <div class="flex-center justify-content-start flex-wrap">
                        @foreach ($freelancer->categories as $category)
                            <div
                                class="status-category mr-2 job-requirement-tag mb-1 mt-1 {{ $category->class ?? '' }}">
                                {{ $category->name ?? '' }}</div>
                        @endforeach
                        @if (!is_null($freelancer->experience))
                            <div class="status-category mr-2 job-requirement-tag mb-1 mt-1 role-financial">
                                {{ $freelancer->experience->name ?? '' }}</div>
                        @endif
                        @if (!is_null($freelancer->country))
                            <div
                                class="status-category mr-2 job-requirement-tag mb-1 mt-1 role-financial flex-center pt-0">
                                <img src="{{ asset('img/country/' . $freelancer->country->code . '.png') }}"
                                     alt="country" class="w-100 h-80 mr-2">
                                {{ $freelancer->country->name ?? '' }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-wrap flex-column align-items-start w-100 py-4 job-requirement">
                    <p class="font-weight-500 font-16 color-000000 mb-2">@lang('Availability')</p>
                    <div class="font-16 color-475467">
                        @if (!is_null($freelancer->hours))
                            @lang('About :time hours per project.', ['time' => $freelancer->hours])
                        @endif
                    </div>
                </div>
                <div class="flex-wrap flex-column align-items-start w-100 py-4 job-requirement">
                    <p class="font-weight-500 font-16 color-000000 mb-2">@lang('Rate per hours')</p>
                    <div class="font-16 color-475467">
                        @if (!is_null($freelancer->rate_per_hours))
                        @lang('For $:amount per hours.', ['amount' => $freelancer->rate_per_hours ? number_format($freelancer->rate_per_hours) : 0])
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex-center flex-column w-100 layout-switch-tab tab-description d-none"></div>
            <div class="flex-center flex-column w-100 layout-switch-tab tab-about-employer d-none"></div>
            <div class="flex-center flex-column w-100 layout-switch-tab tab-review d-none">
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
                                        <div class="star-review d-flex justify-content-center align-items-center mr-2">
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
                            <textarea class="enter-comment w-100 font-16" name="description" id="" cols="30" rows="10"
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
    <div class="flex-center job-footer w-100">
        <div class="flex-center justify-content-between w-100 p-3">
            <button
                class="flex-center btn-next-back mr-2 @if ($previousFreelancer) previous-preview-freelancer @endif"
                @if (!$previousFreelancer) disabled @endif data-id="{{ $previousFreelancer ?? null }}">
                <img src="{{ asset('/img/backend/round-left.svg') }}" alt="">
                <p class="mb-0 color-2200A5 font-14 ml-2 text-uppercase">@lang('Previous')</p>
            </button>
            <button class="flex-center btn-next-back @if ($nextFreelancer) next-preview-freelancer @endif"
                    @if (!$nextFreelancer) disabled @endif data-id="{{ $nextFreelancer ?? null }}">
                <p class="mb-0 color-2200A5 font-14 mr-2 text-uppercase">@lang('Next')</p>
                <img src="{{ asset('/img/backend/round-right.svg') }}" alt="">
            </button>
        </div>
    </div>
</div>
