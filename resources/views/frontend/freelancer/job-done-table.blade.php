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
                            <div class="color-475467 font-12 mr-2">@lang('Feedback')</div>
                        </div>
                    </th>
                    <th class="column-sm"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobDone as $job)
                    <tr>
                        <td class="column-md">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="avatar mr-2">
                                    <img src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                                        alt="Logo" class="rounded-circle h-100 w-100">
                                </div>
                                <div class="d-flex flex-column align-items-start name-and-mail">
                                    <div class="font-14 color-101828 font-weight-bold long-text"
                                        title="{{ optional($job->company)->name }}">
                                        {{ optional($job->company)->name }}</div>
                                    <a href="{{ route('frontend.employer.profile', $job->user->id) }}">
                                        <div class="font-14 color-475467 long-text" title="{{ $job->user->name }}">
                                            {{ $job->user->name }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="column-md" title="">
                            <div class="font-14 color-101828 long-text cursor-pointer btn-preview" data-id="{{ $job->id }}" title="{{ $job->name }}">
                                {{ $job->name }}
                            </div>
                        </td>
                        <td class="column-lg">
                            <div class="d-flex flex-wrap">
                                @foreach ($job->categories as $category)
                                    <div
                                        class="status-category mr-2 job-requirement-tag mb-1 mt-1 {{ $category->class }}">
                                        {{ $category->name }}</div>
                                @endforeach
                                <div class="status-category mr-2 job-requirement-tag mb-1 mt-1">
                                    {{ $job->experience->name }}</div>
                                @if ($job->country_id)
                                    <div
                                        class="status-category mr-2 job-requirement-tag mb-1 mt-1 customer-type flex-center color-000000">
                                        <img src="{{ asset('img/country/' . $job->country->code . '.png') }}"
                                            alt="country" class="w-100 h-80 mr-2">
                                        {{ $job->timezone->diff_from_gtm }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="column-sm">
                            <button type="button"
                            @if (!auth()->user()->is_hidden)
                                id="review-job-done" data-id-job-done="{{ $job->id }}" data-toggle="modal" data-target="#modal-review-job-done"
                            @else
                            disabled
                            @endif
                                class="btn-action-review color-2200A5 font-14 font-weight-600 mr-2 hover-button">@lang('REVIEW')</button>
                        </td>
                        @include('frontend.freelancer.modal-job-done-review')
                        <td class="column-sm">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="flex-center cursor-pointer mr-1 btn-action hover-button">
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
                    <tr>
                        <td colspan="5" class="text-center">@lang('No data')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($jobDone->hasPages())
    <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-center">
        {{ $jobDone->withQueryString()->onEachSide(1)->links() }}
    </div>
@endif
