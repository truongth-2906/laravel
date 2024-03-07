<div class="modal fade" id="modal-reject-job-application" tabindex="-1" role="dialog"
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
                            id="myModalLabel">@lang('Do You Want To Reject The Application?')</h4>
                        <button type="button" class="close close-mobile pc-device" data-dismiss="modal" aria-label="Close">
                            <span class="icon-close" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="pt-2 text-color">
                            @lang('By rejecting this job application, a notifications will be sent to the freelancers that their application for this job was unsuccessfull.')
                        </div>
                    </form>
                    <div class="modal-footer no-gutters border-0 pt-5 modal-footer-custom">
                        <input type="hidden">
                        <div class="area-btn-footer-modal">
                            <button type="button" class="btn-cancel font-weight-600 group-btn-modal"
                                    data-dismiss="modal">@lang('CANCEL')</button>
                            <div class="d-flex align-items-center group-btn-modal group-btn-submit cursor-pointer btn-agree-reject">
                                <div class="font-weight-600 color-2200A5 text-uppercase mr-2">@lang('Yes, Proceed')</div>
                                <img src="{{asset('img/icon-sideways.svg')}}" class="pl-2" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

