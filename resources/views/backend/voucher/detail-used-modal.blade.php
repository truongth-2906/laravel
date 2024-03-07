<div class="modal fade" id="detail-used-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="detailUsedModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center flex-grow-1" id="detailUsedModal">@lang('Detail used')</h5>
                <button type="button" class="close btn-close position-absolute" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="accordionExample">
                    <div class="detail-used-table" id="detail-used-table">
                        <div class="thead">
                            <div class="t-row">
                                <div class="col column-sm">@lang('No')</div>
                                <div class="col column-md">@lang('User')</div>
                                <div class="col column-md">@lang('Number of times used')</div>
                                <div class="col column-md">@lang('Remaining times ("Times/Used expired date")')</div>
                                <div class="col column-sm"></div>
                            </div>
                        </div>
                        <div class="tbody"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-3">
                <button class="button-base btn-general-action hover-button text-decoration-none" data-dismiss="modal">
                    <div class="color-2200A5 font-14 font-weight-bold">@lang('Close')</div>
                </button>
            </div>
        </div>
    </div>
</div>
