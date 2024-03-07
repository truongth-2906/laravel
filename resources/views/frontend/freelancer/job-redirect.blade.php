<div class="flex-center justify-content-around w-50">
    <a href="{{ route(FREELANCER_JOB_SAVED) }}">
        <button type="button"
                class="btn-action-review color-2200A5 font-14 font-weight-600 mr-2 {{ Route::is(FREELANCER_JOB_SAVED) ? 'active-button': 'hover-button' }}">@lang('SAVED')</button>
    </a>
    <a href="{{ route(FREELANCER_JOB_APPLICATIONS) }}">
        <button type="button"
                class="btn-action-review color-2200A5 font-14 font-weight-600 mr-2 {{ Route::is(FREELANCER_JOB_APPLICATIONS) ? 'active-button': 'hover-button' }}">@lang('APPLICATIONS')</button>
    </a>
    <a href="{{ route(FREELANCER_JOB_DONE) }}">
        <button type="button"
                class="btn-action-review color-2200A5 font-14 font-weight-600 mr-2 {{ Route::is(FREELANCER_JOB_DONE) || Route::is(FREELANCER_JOB_DONE_PREVIEW) ? 'active-button': 'hover-button' }}">@lang('DONE')</button>
    </a>
</div>
