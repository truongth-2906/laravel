<div class="modal fade modal-review-job" id="modal-review-job-done" tabindex="-1" role="dialog"
     aria-hidden="true" data-backdrop="static" data-keyboard="false" style="border-radius: 5px">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content b-r-15">
            <div class="modal-header border-bottom">
                <div class="w-100 flex-center justify-content-start align-items-start">
                    <div class="flex-center logo-company mr-2">
                        <img
                            src="{{ asset(optional($job->company)->logo ? optional($job->company)->avatar : '/img/avatar_default.svg') }}"
                            alt="Logo" class="rounded-circle h-100 w-100">
                    </div>
                    <div class="flex-center flex-column align-items-start">
                        <p class="mb-0 font-20 color-2200A5 font-weight-600">{{ optional($job->company)->name }}</p>
                        <p class="mb-0 font-14">@lang('Job posted by :NAME', [ 'name' => $job->user->name])</p>
                    </div>
                </div>
                <button type="button" class="close btn-close position-absolute" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body flex-center flex-column justify-content-start align-items-start pt-0 pb-0" id="data-review-job-done">
            </div>
        </div>
    </div>
</div>
