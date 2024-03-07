<div class="modal fade modal-support" id="modal-support" tabindex="-1" role="dialog" aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-support" role="document">
        <div class="modal-content">
            <form id="send-support-modal">
                <div class="modal-header">
                    <button type="button" class="close btn-close position-absolute" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="d-flex">
                    <div class="w-75 ml-3 mt-md-5">
                        <div class="flex-center flex-column align-items-start">
                            <p class="font-18 color-2200A5 font-weight-600">@lang('The team at Automatorr are experts in the industry.')</p>
                            <p class="font-14 color-667085">@lang('To become a member of the team, or to see how advanced automation can transform your organization’s processes, skyrocket your team’s productivity and maximize profits, contact us today.')</p>
                            <p class="font-14 color-667085">@lang('Fill out the form and a member of our team will respond within 24 hours.')</p>
                        </div>
                    </div>
                    <div>
                        <div
                            class="modal-body flex-center flex-column justify-content-start align-items-start pt-0 pb-0">
                            <div class="flex-center flex-column align-items-start w-100 mb-2">
                                <p class="mb-0 font-16 color-000000">@lang('Subject')</p>
                                <input type="text" class="font-16 w-100" value="Web App Support"
                                       placeholder="{{ __('Enter Subject') }}" disabled>
                            </div>
                        </div>
                        <div
                            class="modal-body flex-center flex-column justify-content-start align-items-start pt-0 pb-0">
                            <div class="flex-center flex-column align-items-start w-100 mb-2">
                                <p class="mb-0 font-16 color-000000">@lang('Email')</p>
                                <input type="text" name="email" class="font-16 w-100 ipt-email" placeholder="{{ __('Email *') }}">
                                <p class="text-danger validation-errors email"></p>
                            </div>
                        </div>
                        <div
                            class="modal-body flex-center flex-column justify-content-start align-items-start pt-0 pb-0">
                            <div class="flex-center flex-column align-items-start w-100 mb-2">
                                <p class="mb-0 font-16 color-000000">@lang('Full Name')</p>
                                <input type="text" name="full_name" class="font-16 w-100 ipt-full_name"
                                       placeholder="{{ __('Full Name *') }}">
                                <p class="text-danger validation-errors full_name"></p>
                            </div>
                        </div>

                        <div
                            class="modal-body flex-center flex-column justify-content-start align-items-start pt-0 pb-0">
                            <div class="flex-center flex-column align-items-start w-100 mb-2">
                                <p class="mb-0 font-16 color-000000">@lang('Message')</p>
                                <textarea name="message" class="full-width form-input-group resize-none ipt-message" id="editor1"
                                          rows="10"
                                          cols="80" maxlength="1000" placeholder="@lang('How can we help?')"></textarea>
                                <p class="text-danger validation-errors message"></p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-footer p-3 flex-center justify-content-end">
                    <button type="button" class="btn btn-send-support hover-button color-2200A5 font-weight-600">
                        @lang('SEND')
                        <img src="{{ asset('/img/backend/round-right.svg') }}" alt="round-right">
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
