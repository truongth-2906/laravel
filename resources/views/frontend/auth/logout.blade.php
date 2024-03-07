<div class="modal fade show-modal-mobile" id="logout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="row no-gutters p-3">
                <div class="col-md-2 col-sm-6 text-center">
                    <img src="{{asset('img/icon.svg')}}" alt="">
                </div>
                <div class="col-md-10">
                    <div>
                        <h4 class="modal-title modal-text-logout title-center-logout"
                            id="myModalLabel">@lang('Are you sure you want to logout?')</h4>
                        <button type="button" class="close close-mobile" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <form>
                        <div class="content-center-logout color-000000">
                            Before you logout, make sure that you have saved everything you worked on. Logging out now
                            will
                            revert any unsaved changes you might have made.
                        </div>
                    </form>
                    <div class="modal-footer no-gutters border-0 pt-4">
                        <div class="col-md-8 col-sm-12 d-flex btn-group-logout">
                            <button type="button"
                                    class="logout-form btn-modal-logout mr-1 btn-margin-top btn-logout-background"> @lang('Log me out')
                                <img src="{{asset('img/icon-sideways.svg')}}" class="ml-2" alt="">
                            </button>
                            <button type="button" class="btn-modal-logout btn-cancel-background"
                                    data-dismiss="modal"> @lang('Cancel')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
