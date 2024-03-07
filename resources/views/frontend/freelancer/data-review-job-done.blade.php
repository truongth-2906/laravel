@if (count($reviews) > 0)
    <div class="flex-center flex-column w-100 align-items-start pt-2 pb-4 requirement">
        <p class="mb-2 font-weight-bold font-16 color-000000">@lang('All Reviews')</p>
        <div class="d-flex justify-content-start align-items-center mb-2">
            <div
                class="avg-rate-point mr-2 color-475467 font-14">{{ renderRatePointAvg(array_sum($reviews->pluck('star')->toArray()) / count($reviews)) }}</div>
            <div class="avg-rate-star d-flex justify-content-center align-items-center mr-2">
                @foreach(renderStarReview(array_sum($reviews->pluck('star')->toArray()) / count($reviews)) as $star)
                    <div
                        class="rate-star mr-1 @if($star == ACTIVE_STAR) img-star-yellow @elseif($star == HALF_PART_STAR) img-half-star @else img-star-white @endif"></div>
                @endforeach
            </div>
            <div class="total-employer-review color-2200A5 font-14">
                <span class="total-job-review">{{ count($reviews) }}</span>
                <span class="user-job-create"></span> {{ $job->user->name }} @lang('review(s)')
            </div>
        </div>
        <div
            class="d-flex flex-column align-items-start  w-100 @if(!$myReview)content-review-job-done @else custom-content-review-job-done @endif">
            @foreach($reviews as $review)
                <div class="d-flex flex-column align-items-start mb-4">
                    <div class="name-user-review font-16 color-000000 mb-1">{{ $review->userReview->name }}</div>
                    <div class="rate-review mb-1 d-flex justify-content-start align-items-center">
                        <div class="star-review d-flex justify-content-center align-items-center mr-2">
                            @foreach(renderStarReview($review->star) as $star)
                                <div
                                    class="rate-star mr-1 @if($star == ACTIVE_STAR) img-star-yellow @else img-star-white @endif"></div>
                            @endforeach
                        </div>
                        <div class="time-review font-14 color-475467">{{ $review->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="comment-review font-16 color-475467">{!! nl2br($review->description) !!}</div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="flex-center flex-column w-100 align-items-start pt-2 pb-2 requirement">
        <p class="mb-2 font-weight-bold font-16 color-000000">@lang('All Reviews')</p>
        <div class="d-flex justify-content-start align-items-center mb-4">
            <div class="text-danger">@lang('No reviews yet.')</div>
        </div>
    </div>
@endif
@if (!$myReview)
    <form class="w-100" id="review-job" action="{{ route('frontend.freelancer.add-review-job') }}"
          method="post">
        @csrf
        <input type="hidden" name="job_id" value="{{ $job->id }}">
        <div class="flex-wrap flex-column justify-content-start align-items-start w-100 pt-2">
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
            <button type="button" data-dismiss="modal"
                    class="btn-action-review hover-button btn-cancel-review-job color-2200A5 font-14 font-weight-600 mr-2">@lang('CANCEL')</button>
            <button type="button"
                    class="btn-action-review hover-button btn-submit-review-job color-2200A5 font-14 font-weight-600">@lang('SAVE')</button>
        </div>
    </form>
@endif
