import {loading} from "../backend/common";

const support = (function ($) {

    function init() {
        $(document).on('click', '.modal-support .btn-send-support', function () {
            let formData = new FormData($('#send-support-modal')[0]);
            send(formData);
        });
    }

    function send(formData) {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/support",
            dataType: "json",
            contentType: false,
            processData: false,
            data: formData,
            beforeSend: () => {
                loading(true);
            },
        }).done(() => {
            $('#modal-support .ipt-email,#modal-support .ipt-full_name, #modal-support .ipt-message').val('');
            $('#modal-support .validation-errors').text('');
            $('#modal-support').modal('hide');
            toastr.success("Send support success!");
        }).fail((response) => {
            toastr.error("Send support fail!");
            $('#modal-support .validation-errors').text('');
            let errors = response.responseJSON.errors;
            for (let err in errors) {
                $(`#modal-support .validation-errors.${err}`).text(errors[err][0]);
            }
        }).always(() => {
            loading(false);
        });
    }

    return {init};
})(jQuery);

$(function () {
    support.init();
});
