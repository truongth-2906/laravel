<div class="modal fade" id="modal-approve-job-application" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="row no-gutters p-4">
                <div class="col-md-2 col-sm-12 text-center">
                    <div class="icon-delete-bolder">
                        <img class="icon-delete" src="{{ asset('/img/document_icon.svg') }}" alt="">
                    </div>
                    <button type="button" class="close close-mobile mobile-device position-relative" data-dismiss="modal" aria-label="Close">
                        <span class="icon-close" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="col-md-10 content-confirm">
                    <div>
                        <h4 class="modal-title modal-text-logout title-center-logout"
                            id="myModalLabel">@lang('Approve Freelancer Application?')</h4>
                        <button type="button" class="close close-mobile pc-device" data-dismiss="modal" aria-label="Close">
                            <span class="icon-close" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="pt-2 text-color">
                            @lang('You will need to deposit the wages that will be paid to the freelancer and other fees. Please confirm below if you want to approve margin trading. Once the deposit is approved, freelancer will receive a notification of approval and their status will change to approved.')
                        </div>
                    </form>
                    <div class="modal-footer no-gutters border-0 pt-5 modal-footer-custom">
                        <input type="hidden">
                        <div class="area-btn-footer-modal">
                            <button type="button" class="btn-cancel font-weight-600 group-btn-modal"
                                    data-dismiss="modal">@lang('CANCEL')</button>
                            <a class="d-flex align-items-center group-btn-modal group-btn-submit cursor-pointer text-decoration-none btn-agree-approve">
                                <div class="font-weight-600 color-2200A5 text-uppercase mr-2">@lang('Yes, Proceed')</div>
                                <img src="{{asset('img/icon-sideways.svg')}}" class="pl-2" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

