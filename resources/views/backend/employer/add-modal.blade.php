<div class="modal fade" id="add-company-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
     data-keyboard="false">
    <div id="filter-modal" class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <meta name="csrf-token" content="{{ csrf_token() }}" />
            <div class="modal-header">
                <div class="w-100 flex-center justify-content-center align-items-center">
                    <div class="flex-center flex-column align-items-center">
                        <p class="mb-0 font-20 color-2200A5">@lang('Add Company')</p>
                    </div>
                </div>
                <button type="button" class="close btn-close position-absolute btn-close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body flex-center flex-column justify-content-start align-items-start pt-0 pb-0">
                <div class="flex-center flex-column align-items-start w-100 mb-3">
                    <p class="mb-0 font-18 color-000000 mb-2">@lang('Name company')</p>
                    <input type="text" class="ipt-add-company font-16 color-667085 w-100"
                           name="name" placeholder="{{ __('Enter my company name') }}">
                    <div id="validation-errors" class="text-danger"></div>
                </div>
            </div>
            <div class="modal-footer p-3 flex-center justify-content-end">
                <button type="button" class="btn btn-cancel color-2200A5 mr-2 btn-close-modal mt-0"
                        data-dismiss="modal">@lang('Cancel')</button>
                <button type="button" class="btn btn-search btn-save-company color-2200A5">
                    @lang('Save')
                    <img src="{{ asset('/img/backend/round-right.svg') }}">
                </button>
            </div>
        </div>
    </div>
</div>
