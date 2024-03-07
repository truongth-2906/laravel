<div class="modal fade show-modal-mobile m-0" id="kyc-modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="row no-gutters p-3">
                <div class="col-md-2 col-sm-6 text-center">
                    <img src="{{asset('img/icon.svg')}}" alt="">
                </div>
                <div class="col-md-10">
                    <div>
                        <h4 class="modal-title modal-text-logout title-center-logout"
                            id="myModalLabel">@lang('USER NOT VERIFIED')</h4>
                        <button type="button" class="close close-mobile btn-close-kyc-modal" data-dismiss="modal"
                                aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="content-center-logout color-000000">
                        @lang('You have not completed your verification process yet.  </br>By completing the verification process, you will be able to apply for jobs, and all limitations will be removed. </br>
                                Please click on the "Verify now" button below to verify your identity. </br>
                                If you want to verify your identity later, please go to the Identity Verification tab on the "Settings" page.')
                    </div>

                    <div class="modal-footer no-gutters border-0 pt-4">
                        <div class="col-md-12 col-sm-12 d-flex btn-group-logout">
                            <a href="{{ route('frontend.auth.passbase.index') }}" target="_blank"
                               class="btn btn-modal-logout mr-1 btn-margin-top btn-logout-background"> @lang('Verify now')
                                <img src="{{asset('img/icon-sideways.svg')}}" class="ml-2" alt="">
                            </a>
                            <button type="button" class="btn-modal-logout btn-cancel-background btn-close-kyc-modal"
                                    data-dismiss="modal"> @lang('Verify later')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
