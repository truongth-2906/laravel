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
                            <div class="cursor-pointer">
                                <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                            </div>
                        </div>
                    </th>
                    <th class="column-sm"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="column-md">
                        <div class="d-flex justify-content-start align-items-center object-info">
                            <div class="avatar mr-2 disabled">
                                <img src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                                    alt="Logo" class="rounded-circle" width="40px" height="40px">
                            </div>
                            <div class="d-flex flex-column align-items-start flex-grow-1">
                                <div class="font-14 color-101828 data-info font-weight-500"
                                    title="{{ optional($job->company)->name ?? '' }}">
                                    {{ optional($job->company)->name ?? '' }}
                                </div>
                                <div class="font-14 color-475467 data-info"
                                    title="{{ optional($job->user)->name ?? '' }}">
                                    {{ optional($job->user)->name ?? '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="column-md color-475467" title="{{ $job->name ?? '' }}">
                        {{ $job->name ?? '' }}</td>
                    <td class="column-lg">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex flex-wrap">
                                @foreach ($job->categories as $category)
                                    <div
                                        class="status-category mr-2 job-requirement-tag mb-1 mt-1 {{ $category->class ?? '' }} disabled">
                                        {{ $category->name ?? '' }}</div>
                                @endforeach
                                @if (!is_null($job->experience))
                                    <div class="mr-2 status-category job-requirement-tag tag-pill-blue-light mb-1 mt-1 disabled"
                                        title="{{ $job->experience->name ?? '' }}">
                                        {{ $job->experience->name ?? '' }}</div>
                                @endif
                                @if (!is_null($job->country))
                                    <div
                                        class="mr-2 status-category job-requirement-tag tag-pill-blue-light mb-1 mt-1 d-flex align-items-center pt-0 disabled">
                                        <img src="{{ asset('/img/country/' . $job->country->code . '.png') }}"
                                            alt="Flag" title="{{ $job->country->name ?? '' }}"
                                            class="rounded-circle my-auto mr-2" width="16px" height="16px">
                                        {{ optional($job->timezone)->offset ?? '' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="column-sm">
                        <span class="tag tag_pill tag__sm tag__success tag__open disabled">@lang('Applied')</span>
                    </td>
                    <td class="column-sm">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="flex-center cursor-pointer mr-1 btn-action hover-button disabled">
                                <img src="{{ asset($job->isSaved() ? '/img/icon-red-heart.svg' : '/img/icon-heart.svg') }}"
                                    alt="" class="icon-heart-job" data-job-id="{{ $job->id }}">
                            </div>
                            <a class="flex-center btn-action cursor-pointer btn-preview active" href="">
                                <img src="{{ asset('/img/icon-eye.svg') }}" alt="">
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <div class="preview-wrapper">
        <div class="preview__header d-flex flex-wrap">
            <div class="flex-center">
                <div class="flex-center">
                    <img src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                        alt="Logo" class="rounded-circle photo">
                </div>
                <div class="flex-center flex-column align-items-start">
                    <p class="font-30 color-2200A5 mb-0 text-uppercase font-weight-500">
                        {{ optional($job->company)->name ?? '' }}</p>
                    <p class="font-16 color-475467 mb-0">
                        @lang('Job posted by') {{ optional($job->user)->name ?? '' }}</p>
                </div>
            </div>
        </div>
        <div class="w-100 preview__body">
            <div class="flex-center flex-column w-100">
                <div class="tabs">
                    <div class="flex-center tab tab-active switch-tab" data-tab="1">@lang('Overview')</div>
                    <div class="flex-center tab switch-tab" data-tab="3">@lang('About the employer')</div>
                    <div class="flex-center tab switch-tab" data-tab="4">@lang('Reviews')</div>
                </div>
                <div class=" w-100 tab-content layout-switch-tab tab-overview">
                    <div class="d-flex flex-column part">
                        <p class="part__title">@lang('Job Skills Requirement')</p>
                        <p class="color-475467">@lang('The employer is looking for a freelance who have the skills in the categories selected.')</p>
                        <div class="d-flex flex-wrap">
                            @foreach ($job->categories as $category)
                                <div class="tag tag__rectangle mr-2 my-1 {{ $category->class ?? '' }}">
                                    {{ $category->name ?? '' }}</div>
                            @endforeach
                            @if (!is_null($job->experience))
                                <div class="tag tag__rectangle tag__experience mr-2 my-1">
                                    {{ $job->experience->name ?? '' }}
                                </div>
                            @endif
                            @if (!is_null($job->country))
                                <div class="tag tag__rectangle tag__country mr-2 my-1">
                                    <img src="{{ asset('img/country/' . $job->country->code . '.png') }}"
                                        alt="country" class="w-100 h-80 mr-2">
                                    {{ $job->country->name ?? '' }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="part">
                        <p class="part__title">@lang('Amount')</p>
                        <div class="color-475467 text-break">
                            {{ $job->wage ? number_format($job->wage, 2) . ' $USD' : __('The job has not set wages yet')}}
                        </div>
                    </div>
                    <div class="part">
                        <p class="part__title">@lang('Job Title')</p>
                        <div class="color-475467 text-break">
                            {{ $job->name ?? '' }}
                        </div>
                    </div>
                    <div class="part">
                        <p class="part__title">@lang('Description')</p>
                        <div class="color-475467 text-break">
                            {!! nl2br(e($job->description ?? '')) !!}
                        </div>
                    </div>
                </div>
                <div class="w-100 layout-switch-tab tab-about-employer d-none"></div>
                <div class="w-100 layout-switch-tab tab-review d-none">

                    <div class="flex-center flex-column w-100 align-items-start pt-4 pb-4requirement">
                        @if (count($jobReviews) > 0)
                                <p class="mb-2 font-weight-bold font-16 color-000000">@lang('All Reviews')</p>
                                <div class="d-flex justify-content-start align-items-center mb-4">
                                    <div class="avg-rate-point mr-2 color-475467 font-14">
                                        {{ renderRatePointAvg(array_sum($jobReviews->pluck('star')->toArray()) / count($jobReviews)) }}
                                    </div>
                                    <div class="avg-rate-star d-flex justify-content-center align-items-center mr-2">
                                        @foreach (renderStarReview(array_sum($jobReviews->pluck('star')->toArray()) / count($jobReviews)) as $star)
                                            <div
                                                class="rate-star mr-1 @if ($star == ACTIVE_STAR) img-star-yellow @elseif($star == HALF_PART_STAR) img-half-star @else img-star-white @endif">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="total-employer-review color-2200A5 font-14">
                                        <span class="total-job-review">{{ count($jobReviews) }}</span>
                                        {{ $job->user->name }} @lang('review(s)')
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-start content-review w-100">
                                    @forelse($jobReviews as $jobReview)
                                        <div class="d-flex flex-column align-items-start mb-4 detail-job-review">
                                            <div class="name-user-review font-16 color-000000 mb-1">
                                                {{ $jobReview->userReview->name }}</div>
                                            <div class="rate-review mb-1 d-flex justify-content-start align-items-center">
                                                <div
                                                    class="star-review d-flex justify-content-center align-items-center mr-2">
                                                    @foreach (renderStarReview($jobReview->star) as $star)
                                                        <div
                                                            class="rate-star mr-1 @if ($star == ACTIVE_STAR) img-star-yellow @else img-star-white @endif">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="time-review font-14 color-475467">
                                                    {{ $jobReview->created_at->diffForHumans() }}</div>
                                            </div>
                                            <div class="comment-review font-16 color-475467">{!! nl2br($jobReview->description) !!}</div>
                                        </div>
                                    @empty
                                    @endforelse
                                </div>
                            @else
                                <div class="text-danger">@lang('No reviews yet.')</div>
                            @endif
                        </div>
                        @if (!$myReview && !auth()->user()->is_hidden)
                            <form class="w-100" id="review-job"
                                action="{{ route('frontend.freelancer.add-review-job') }}" method="post">
                                @csrf
                                <input type="hidden" name="job_id" value="{{ $job->id }}">
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
                                    <p class="font-16 color-475467 mb-2">@lang('Have you worked with ' . $job->user->name . ' before? Describe how it is to work with them below.')</p>
                                    <textarea class="enter-comment w-100 font-16" name="description" id="" cols="30" rows="10"
                                        placeholder="@lang('Enter a description...')"></textarea>
                                    <p class="text-danger error-comment" data-error="description"></p>
                                </div>
                                <div class="d-flex justify-content-end align-items-center w-100 pr-4 mt-2 mb-4">
                                    <button type="button"
                                        class="btn-action-review hover-button btn-cancel-review-job color-2200A5 font-14 font-weight-600 mr-2">@lang('CANCEL')</button>
                                    <button type="button"
                                        class="btn-action-review hover-button btn-submit-review-job color-2200A5 font-14 font-weight-600">@lang('SAVE')</button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <input type="hidden" class="user-name-create-job" value="{{ $job->user->name }}">
                </div>
            </div>
        </div>
    </div>

    <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-end justify-content-md-between">
        <button class="btn-next-page @if ($previousJob) previous-preview-job @endif mr-md-0 mr-3"
            @if (!$previousJob) disabled @endif data-id="{{ $previousJob ?? null }}">
            <img src="{{ asset('/img/backend/round-left.svg') }}" alt="">
            <p class="mb-0 ml-2">@lang('PREVIOUS')</p>
        </button>
        <button class="btn-next-page @if ($nextJob) next-preview-job @endif"
            @if (!$nextJob) disabled @endif data-id="{{ $nextJob ?? null }}">
            <p class="mb-0 mr-2">@lang('NEXT')</p>
            <img src="{{ asset('/img/backend/round-right.svg') }}" alt="">
        </button>
    </div>
</div>
