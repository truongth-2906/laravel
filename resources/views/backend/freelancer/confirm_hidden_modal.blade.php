<div class="modal fade" id="confirm-hidden-modal" tabindex="-1" aria-labelledby="confirmHiddenModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="row no-gutters p-4">
                <div class="col-md-2 col-sm-12 text-center">
                    <div class="icon-delete-bolder">
                        <img class="icon-delete" src="{{ asset('/img/icon-eye-off.svg') }}" alt="">
                    </div>
                    <button type="button" class="close close-mobile mobile-device position-relative"
                        data-dismiss="modal" aria-label="Close">
                        <span class="icon-close" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="col-md-10 content-confirm">
                    <div>
                        <h4 class="modal-title modal-text-logout title-center-logout pr-4" id="confirmHiddenModal">
                            @lang('Are you sure you want to hidden freelancer?')</h4>
                        <button type="button" class="close close-mobile pc-device" data-dismiss="modal"
                            aria-label="Close">
                            <span class="icon-close" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="pt-2 text-color">
                            @lang('By clicking ok below you will hide the selected freelancer. Incomplete, undisbursed transactions will be canceled and employers will no longer see and interact with such freelancer,..')
                        </div>
                    </form>
                    <div class="modal-footer no-gutters border-0 pt-5">
                        <div class="col-md-5">
                            <div class="form-check">
                                <input class="form-check-input" id="dont-show-again" type="checkbox"
                                    name="dont_show_again" value="">
                                <label class="form-check-label text-color font-weight-600" for="dont-show-again">
                                    @lang('Don’t show again')
                                </label>
                            </div>
                        </div>
                        <input type="hidden" id="hidden-freelancer-id" name="hidden_freelancer">
                        <div class="col-md-7 col-sm-12 d-flex btn-group-logout">
                            <button type="button" class="btn-cancel font-weight-600"
                                data-dismiss="modal">@lang('CANCEL')</button>
                            <button type="button" data-dismiss="modal"
                                class="btn-delete btn-margin-top font-weight-600 "
                                id="btn-confirmed-hidden-freelancer">@lang('OK')
                                <img src="{{ asset('img/icon-sideways.svg') }}" class="pl-2" alt="">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
