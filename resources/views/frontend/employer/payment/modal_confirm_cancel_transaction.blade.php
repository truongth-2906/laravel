<div class="modal fade" id="modal-confirm-cancel-transaction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="row no-gutters p-4">
                <div class="col-md-2 col-sm-12 text-center">
                    <div class="icon-delete-bolder">
                        <img class="icon-delete" src="{{ asset('/img/document_icon.svg') }}" alt="">
                    </div>
                    <button type="button" class="close close-mobile mobile-device position-relative"
                        data-dismiss="modal" aria-label="Close">
                        <span class="icon-close" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="col-md-10 content-confirm">
                    <div>
                        <h4 class="modal-title modal-text-logout title-center-logout" id="myModalLabel">
                            @lang('Are you sure you want to cancel?')</h4>
                        <button type="button" class="close close-mobile pc-device" data-dismiss="modal"
                            aria-label="Close">
                            <span class="icon-close" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="pt-2 text-color">
                            @lang('By clicking ok below, you will cancel the transaction. Please leave a reason why you canceled this transaction.')
                        </div>
                        <div class="form-group mt-2">
                            <textarea name="message" class="full-width p-2" id="cancel-message" rows="5" cols="80" style="resize: none;">{{ old('message') }}</textarea>
                            <div class="text-danger errors message_input_error_message" style="display: none;">
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer no-gutters border-0 pt-5">
                        <div class="col-md-7 col-sm-12 d-flex btn-group-logout">
                            <button type="button" class="btn-cancel font-weight-600"
                                data-dismiss="modal">@lang('CANCEL')</button>
                            <button type="button"
                                class="btn-delete btn-confirmed-cancel btn-margin-top font-weight-600">@lang('OK')
                                <img src="{{ asset('img/icon-sideways.svg') }}" class="pl-2" alt="">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
