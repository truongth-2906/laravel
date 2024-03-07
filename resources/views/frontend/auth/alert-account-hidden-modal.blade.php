<div class="modal fade show-modal-mobile m-0" id="alert-account-hidden-modal" tabindex="1" role="dialog" aria-labelledby="alertAccountHiddenModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="row no-gutters p-3">
                <div class="col-md-2 col-sm-6 text-center">
                    <img src="{{asset('img/icon.svg')}}" alt="">
                </div>
                <div class="col-md-10">
                    <div>
                        <h4 class="modal-title modal-text-logout title-center-logout"
                            id="alertAccountHiddenModal">@lang('THE ACCOUNT HAS BEEN HIDDEN')</h4>
                        <button type="button" class="close close-mobile" data-dismiss="modal"
                                aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="content-center-logout color-000000">
                        @lang('Your account is on hold. Please contact the support team.')
                    </div>

                    <div class="modal-footer no-gutters border-0 pt-4">
                        <div class="col-md-12 col-sm-12 d-flex btn-group-logout">
                            <button type="button"
                               class="btn btn-modal-logout mr-1 btn-margin-top btn-logout-background btn-contact-support"> @lang('CONTACT SUPPORT')
                                <img src="{{asset('img/icon-sideways.svg')}}" class="ml-2" alt="">
                            </button>
                            <button type="button" class="btn-modal-logout btn-cancel-background"
                                    data-dismiss="modal"> @lang('CLOSE')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
