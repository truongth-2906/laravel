<div class="modal fade" id="modal-apply-success" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <a href="{{ route(FREELANCER_JOB_APPLICATIONS) }}" class="flex-center text-decoration-none">
                    <button type="button" class="close btn-close position-absolute" >
                            <span aria-hidden="true">&times;</span>
                    </button>
                </a>
            </div>
            <div class="modal-body">
                <div class="flex-center flex-column w-100">
                    <div class="flex-center">
                        <img
                            src="{{ asset($logged_in_user->avatar ? $logged_in_user->logo : '/img/avatar_default.svg') }}"
                            alt="Logo" class="rounded-circle" width="64px" height="64px">
                    </div>
                    <div class="font-18 font-weight-600 color-101828">@lang('Your introduction has been sent.')</div>
                    <div
                        class="font-14 color-475467 text-center font-weight-400">@lang('Thank you for applying for the job. The employer has been notified about your application.')</div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route(FREELANCER_JOB_APPLICATIONS) }}"
                   class="flex-center p-2 mb-2 w-100 text-decoration-none">
                    <button type="button" class="btn btn-search btn-search-filter color-2200A5 hover-button w-100">
                        @lang('FINISH APPLICATION')
                    </button>
                </a>

            </div>
        </div>
    </div>
</div>

