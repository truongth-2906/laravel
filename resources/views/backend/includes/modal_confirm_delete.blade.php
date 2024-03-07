<div class="modal fade" id="modal-confirm-delete-{{ $type }}" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="row no-gutters p-4">
                <div class="col-md-2 col-sm-12 text-center">
                    <div class="icon-delete-bolder">
                        <img class="icon-delete" src="{{ asset('/img/delete_icon.svg') }}" alt="">
                    </div>
                    <button type="button" class="close close-mobile mobile-device position-relative" data-dismiss="modal" aria-label="Close">
                        <span class="icon-close" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="col-md-10 content-confirm">
                    <div>
                        <h4 class="modal-title modal-text-logout title-center-logout"
                            id="myModalLabel">@lang('Are you sure you want to delete?')</h4>
                        <button type="button" class="close close-mobile pc-device" data-dismiss="modal" aria-label="Close">
                            <span class="icon-close" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="pt-2 text-color">
                            @lang('By clicking delete below, you will remove the selected item and won’t be able to retrieve it.Uncompleted, undisbursed transactions will be cancelled.')
                        </div>
                    </form>
                    <div class="modal-footer no-gutters border-0 pt-5">
                        <div class="col-md-5">
                            <div class="form-check">
                                <input class="form-check-input" id="show-again" type="checkbox" name="dont_show_again"
                                       value="">
                                <label class="form-check-label text-color font-weight-600" for="show-again">
                                    @lang('Don’t show again')
                                </label>
                            </div>
                        </div>
                        <input type="hidden" id="delete-{{ $type }}-id" name="delete_{{ $type }}">
                        <div class="col-md-7 col-sm-12 d-flex btn-group-logout">
                            <button type="button" class="btn-cancel font-weight-600"
                                    data-dismiss="modal">@lang('CANCEL')</button>
                            <button type="button"
                                    class="btn-delete btn-delete-{{ $type }} btn-margin-top font-weight-600">@lang('DELETE')
                                <img src="{{asset('img/icon-sideways.svg')}}" class="pl-2" alt="">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

