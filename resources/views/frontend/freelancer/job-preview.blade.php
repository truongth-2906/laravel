<div class="table-freelancers">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
                <tr>
                    <th class="color-475467 font-12">@lang('Listing as')</th>
                    <th class="color-475467 font-12">@lang('Job Title')</th>
                    <th class="color-475467 font-12">@lang('Job Requirements')</th>
                    <th class="w-10">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="color-475467 font-12 mr-2">@lang('Job Status')</div>
                            <img class="arrow-up" src="{{ asset('img/arrow-down.svg') }}" alt="">
                        </div>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="align-middle">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="avatar mr-2 disabled">
                                <img src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                                    alt="Logo" class="rounded-circle h-100 w-100">
                            </div>
                            <div class="d-flex flex-column align-items-start name-and-mail">
                                <div class="font-14 color-000000 font-weight-bold long-text disabled"
                                    title="{{ optional($job->company)->name }}">{{ optional($job->company)->name }}
                                </div>
                                <div class="font-14 color-000000 long-text disabled"
                                    title="{{ optional($job->user)->name }}">{{ optional($job->user)->name }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle ">
                        <p class="color-000000 mb-0 disabled">{{ $job->name }}</p>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex justify-content-start align-items-center flex-wrap">
                            @foreach ($job->categories as $category)
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1 disabled {{ $category->class }}">
                                    {{ $category->name }}</div>
                            @endforeach
                            <div class="status-category mr-2 job-requirement-tag mb-1 mt-1 disabled">
                                {{ $job->experience->name }}</div>
                            @if ($job->country_id)
                                <div
                                    class="status-category mr-2 job-requirement-tag mb-1 mt-1 customer-type flex-center color-000000 disabled">
                                    <img src="{{ asset('img/country/' . $job->country->code . '.png') }}"
                                        alt="country" class="w-100 h-80 mr-2">
                                    {{ $job->timezone->diff_from_gtm }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex justify-content-start align-items-center">
                            @if($job->mark_done == \App\Domains\Job\Models\Job::MARK_DONE)
                                <div
                                    class="status-category disabled status-open-job d-flex justify-content-center align-items-center mr-2 disabled">
                                    <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                    <div class="color-496300 disabled">@lang('Done')</div>
                                </div>
                            @else
                                @if ($job->status == \App\Domains\Job\Models\Job::STATUS_OPEN)
                                    <div
                                        class="status-category disabled status-open-job d-flex justify-content-center align-items-center mr-2 disabled">
                                        <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                        <div class="color-496300 disabled">@lang('Open')</div>
                                    </div>
                                @else
                                    <div
                                        class="status-category disabled status-close-job d-flex justify-content-center align-items-center mr-2">
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
                                $saveJobIds = Auth::user()
                                    ->jobsSaved->pluck('saved_id')
                                    ->toArray();
                            @endphp
                            <div class="flex-center cursor-pointer mr-3 btn-action hover-button disabled">
                                <img class="icon-heart-job no-reload" data-job-id="{{ $job->id }}"
                                    src="{{ in_array($job->id, $saveJobIds) ? asset('/img/icon-red-heart.svg') : asset('/img/icon-heart.svg') }}"
                                    alt="">
                            </div>
                            <div class="flex-center cursor-pointer btn-action hover-button active redirect-page ml-1"
                                data-id="{{ $job->id }}">
                                <button
                                    class="btn-general-action d-flex justify-content-start align-items-center hover-button">
                                    <div class="color-2200A5 font-14 font-weight-bold ml-2 mr-2">@lang('BACK')</div>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="flex-center flex-column w-100 job-detail">
    <div class="w-100 header-freelancer flex-wrap unset-border-bottom">
        <div class="d-flex flex-column align-items-start">
            <div class="d-flex justify-content-start align-items-center">
                <div class="flex-center avatar">
                    <img src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                        alt="Logo" class="rounded-circle h-100 w-100">
                </div>
                <div class="flex-center flex-column align-items-start ml-2">
                    <p class="font-30 color-2200A5 mb-0" title="{{ optional($job->company)->name }}">
                        {{ optional($job->company)->name }}</p>
                    <p class="font-16 color-475467 mb-0" title="{{ $job->user->name }}">@lang('Job posted by')
                        {{ $job->user->name }}</p>
                </div>
            </div>
        </div>
        @if (isset($isFreelancer))
            <div class="d-flex justify-content-center align-items-center child-second">
                @if ($user->available == \App\Domains\Auth\Models\User::AVAILABLE)
                    @if(in_array($job->id, $jobApplied) || $job->mark_done == \App\Domains\Job\Models\Job::MARK_DONE)
                        <button class="btn-general-action d-flex justify-content-start align-items-center mr-3 btn-applied-disable" disabled>
                            <div class="color-2200A5 font-14 font-weight-bold">@lang('APPLY NOW')</div>
                        </button>
                    @else
                        <a href="{{ route(FREELANCER_JOB_DETAIL, ['id' => $job->id]) }}"
                           class="text-decoration-none">
                            <button
                                class="btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
                                <div class="color-2200A5 font-14 font-weight-bold">@lang('APPLY NOW')</div>
                            </button>
                        </a>
                    @endif
                @else
                    @if(in_array($job->id, $jobApplied) || $job->mark_done == \App\Domains\Job\Models\Job::MARK_DONE)
                        <button class="btn-general-action d-flex justify-content-start align-items-center mr-3 btn-applied-disable" disabled>
                            <div class="color-2200A5 font-14 font-weight-bold">@lang('APPLY NOW')</div>
                        </button>
                    @else
                        <button class="btn-general-action d-flex justify-content-start align-items-center mr-3" disabled>
                            <div class="color-2200A5 font-14 font-weight-bold">@lang('APPLY NOW')</div>
                        </button>
                    @endif
                @endif
                @php
                    $saveJobIds = Auth::user()
                        ->jobsSaved->pluck('saved_id')
                        ->toArray();
                @endphp
                <div class="btn btn-general-action d-flex font-weight-bold justify-content-center align-items-center hover-button color-2200A5 btn-save-job"
                    data-job-id="{{ $job->id }}">
                    {{ in_array($job->id, $saveJobIds) ? __('UN SAVE JOB') : __('SAVE JOB') }}
                </div>
            </div>
        @else
            @if (isset($markDone) && $markDone)
                <button class="mt-2 btn-general-action color-2200A5 font-weight-600 btn-mark-done hover-button mark-done-job-approve {{ Route::is('frontend.employer.jobs.applications') ? 'd-none' : '' }}">
                    @lang('MARK DONE')
                </button>
            @else
                <button class="mt-2 btn-general-action color-2200A5 font-weight-600 btn-mark-done {{ Route::is('frontend.employer.jobs.applications') ? 'd-none' : '' }}" disabled>
                    @lang('MARK DONE')
                </button>
            @endif
        @endif
    </div>
    <div class="flex-center w-100 job-content">
        <div class="flex-center flex-column w-100 over-view pl-4 pr-4">
            <div class="w-100 mb-3 border-bottom-EAECF0"></div>
            <div class="flex-center justify-content-start w-100 tabs flex-wrap">
                <div class="flex-center tab  color-667085 font-weight-600 cursor-pointer switch-tab {{ !Route::is('frontend.employer.jobs.applications') ? 'tab-active' : '' }}"
                    data-tab="1">@lang('Overview')</div>
                @if (!isset($isFreelancer))
                    <div class="flex-center tab color-667085 font-weight-600 cursor-pointer switch-tab {{ Route::is('frontend.employer.jobs.applications') ? 'tab-active' : '' }}" data-tab="5">
                        @lang('Job Applications')
                        <span
                            class="quantity-background color-2200A5 ml-2 font-12 font-weight-500 d-flex justify-content-center align-items-center">
                            {{ count($job->applicants) }}
                        </span>
                    </div>
                @endif
                <div class="flex-center tab color-667085 font-weight-600 cursor-pointer switch-tab" data-tab="3">
                    @lang('About the employer')</div>
                <div class="flex-center tab color-667085 font-weight-600 cursor-pointer switch-tab" data-tab="4">
                    @lang('Reviews')</div>
            </div>
            <div class="flex-center flex-column w-100 layout-switch-tab tab-overview {{ Route::is('frontend.employer.jobs.applications') ? 'd-none' : '' }} }}">
                <div class="flex-center flex-column w-100 align-items-start py-4 job-requirement">
                    <p class="mb-2 font-weight-bold font-16 color-000000">@lang('Job Skills Requirement')</p>
                    <p class="mb-2 font-weight-bold font-16 color-475467">@lang('The employer is looking for a freelance who have the skills in the categories selected.')</p>
                    <div class="flex-center justify-content-start flex-wrap">
                        @foreach ($job->categories as $category)
                            <div class="status-category mr-2 job-requirement-tag mb-1 mt-1 {{ $category->class }}">
                                {{ $category->name }}</div>
                        @endforeach
                        <div class="status-category mr-2 job-requirement-tag mb-1 mt-1 role-financial">
                            {{ $job->experience->name }}</div>
                        @if ($job->country_id)
                            <div class="status-category mr-2 job-requirement-tag mb-1 mt-1 role-financial flex-center">
                                <img src="{{ asset('img/country/' . $job->country->code . '.png') }}" alt="country"
                                    class="w-100 h-80 mr-2">
                                {{ $job->country->name }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-wrap flex-column align-items-start w-100 py-4 job-requirement">
                    <p class="font-weight-bold font-16 color-000000 mb-2">@lang('Amount')</p>
                    <div class="font-16 color-475467">
                        {{ $job->wage ? number_format($job->wage, 2) . ' $USD' : __('The job has not set wages yet')}}
                    </div>
                </div>
                <div class="flex-wrap flex-column align-items-start w-100 py-4 job-requirement">
                    <p class="font-weight-bold font-16 color-000000 mb-2">@lang('Job Title')</p>
                    <div class="font-16 color-475467 text-break">
                        {{ $job->name }}
                    </div>
                </div>
                <div class="flex-wrap flex-column align-items-start w-100 py-4 job-requirement">
                    <p class="font-weight-bold font-16 color-000000 mb-2">@lang('Description')</p>
                    <div class="font-16 color-475467 text-break">
                        {!! nl2br(e($job->description ?? '')) !!}
                    </div>
                </div>
            </div>
            @if (!isset($isFreelancer))
                <div class="flex-center flex-column w-100 layout-switch-tab tab-job-application {{ Route::is('frontend.employer.jobs.applications') ? '' : 'd-none' }}">
                    @include('frontend.employer.table_job_application')
                </div>
            @endif
            <div class="flex-center flex-column w-100 layout-switch-tab tab-about-employer d-none"></div>
            <div class="flex-center flex-column w-100 layout-switch-tab tab-review d-none">

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
                                <span class="total-job-review">{{ count($jobReviews) }}</span> {{ $job->user->name }}
                                @lang('review(s)')
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-start content-review w-100">
                            @forelse($jobReviews as $jobReview)
                                <div class="d-flex flex-column align-items-start mb-4 detail-job-review">
                                    <div class="name-user-review font-16 color-000000 mb-1">
                                        {{ $jobReview->userReview->name }}</div>
                                    <div class="rate-review mb-1 d-flex justify-content-start align-items-center">
                                        <div class="star-review d-flex justify-content-center align-items-center mr-2">
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
                        <div class="text-danger mb-2">@lang('No reviews yet.')</div>
                    @endif
                </div>
                @if (!$myReview && auth()->id() != $job->user_id)
                    <form class="w-100" id="review-job" action="{{ route('frontend.freelancer.add-review-job') }}"
                        method="post">
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
            <input type="hidden" class="ipt-job-id" value="{{ $job->id }}">
            <input type="hidden" class="user-name-create-job" value="{{ $job->user->name }}">
        </div>
    </div>
    <div class="flex-center job-footer w-100">
        <div class="flex-center justify-content-between w-100 p-3">
            <button
                class="btn-next-page btn-next-back mr-md-0 mr-3 @if ($previousJob) previous-preview-job @endif"
                @if (!$previousJob) disabled @endif data-id="{{ $previousJob ?? null }}">
                <img src="{{ asset('/img/backend/round-left.svg') }}" alt="">
                <p class="mb-0 color-2200A5 font-14 ml-2">@lang('PREVIOUS')</p>
            </button>
            <button class="btn-next-page btn-next-back @if ($nextJob) next-preview-job @endif"
                @if (!$nextJob) disabled @endif data-id="{{ $nextJob ?? null }}">
                <p class="mb-0 color-2200A5 font-14 mr-2">@lang('NEXT')</p>
                <img src="{{ asset('/img/backend/round-right.svg') }}" alt="">
            </button>
        </div>
    </div>
</div>

<div class="detail-freelancer-apply-template"></div>
