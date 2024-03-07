<div class="flex-center flex-column w-100 align-items-start pt-4 pb-4requirement">
    <p class="mb-2 font-weight-bold font-16 color-000000">@lang('All Reviews')</p>
    <div class="d-flex justify-content-start align-items-center mb-4">
        <div class="avg-rate-point mr-2 color-475467 font-14">{{ renderRatePointAvg(array_sum($reviews->pluck('star')->toArray()) / count($reviews)) }}</div>
        <div class="avg-rate-star d-flex justify-content-center align-items-center mr-2">
            @foreach(renderStarReview(array_sum($reviews->pluck('star')->toArray()) / count($reviews)) as $star)
                <div class="rate-star mr-1 @if($star == ACTIVE_STAR) img-star-yellow @elseif($star == HALF_PART_STAR) img-half-star @else img-star-white @endif"></div>
            @endforeach
        </div>
        <div class="total-employer-review color-2200A5 font-14">
            <span class="total-job-review">{{ count($reviews) }}</span>
            <span class="user-job-create"></span> @lang('review(s)')
        </div>
    </div>
    <div class="d-flex flex-column align-items-start content-review w-100">
        @foreach($reviews as $review)
            <div class="d-flex flex-column align-items-start mb-4">
                <div class="name-user-review font-16 color-000000 mb-1">{{ $review->userReview->name }}</div>
                <div class="rate-review mb-1 d-flex justify-content-start align-items-center">
                    <div class="star-review d-flex justify-content-center align-items-center mr-2">
                        @foreach(renderStarReview($review->star) as $star)
                            <div class="rate-star mr-1 @if($star == ACTIVE_STAR) img-star-yellow @else img-star-white @endif"></div>
                        @endforeach
                    </div>
                    <div class="time-review font-14 color-475467">{{ $review->created_at->diffForHumans() }}</div>
                </div>
                <div class="comment-review font-16 color-475467">{!! nl2br($review->description) !!}</div>
            </div>
        @endforeach
    </div>
</div>
