import { loading } from "../backend/common";
import { renderHtmlTemplate, createBlankPage } from './common';

const payment = (function ($) {
    let idCancel = null;
    const SUFFIXES_ERROR = '_input_error_message';

    function listener() {
        $(document).on('click', '.btn-cancel-transaction', showModalConfirmCancelTransaction);

        $('#modal-confirm-cancel-transaction').on(
            'click',
            '.btn-confirmed-cancel',
            cancelTransaction
        );

        $('#modal-confirm-cancel-transaction').on(
            'click',
            '.btn-cancel, .close',
            hideModalConfirmCancelTransaction
        );

        $(document).on('click', '.btn-pay-now-transaction', payNow);

        $(document).on('click', '.btn-funding-transaction', funding);
    }

    function showModalConfirmCancelTransaction() {
        idCancel = $(this).closest('tr').data('id');
        $('#modal-confirm-cancel-transaction').modal('show');
    }

    function hideModalConfirmCancelTransaction() {
        idCancel = null;
        $('#modal-confirm-cancel-transaction #cancel-message').val('');
        $(`.errors`).text('').hide();
        $('#modal-confirm-cancel-transaction').modal('hide');
    }

    function cancelTransaction() {
        if (idCancel) {
            loading(true);
            $(`.errors`).text('').hide();
            $.ajax({
                type: 'POST',
                url: `/employer/payments/${idCancel}`,
                data: {
                    message: $('#modal-confirm-cancel-transaction #cancel-message').val()
                },
                dataType: 'json',
                success: function (response) {
                    const tr = $(`.table-transactions-employer table tr[data-id="${idCancel}"]`);
                    tr.find('.transaction-status').html(renderHtmlTemplate('cancel-status-template'));
                    tr.find('.btn-cancel-transaction, .btn-funding-transaction').remove();
                    idCancel = null;
                    toastr.success(response.message);
                    hideModalConfirmCancelTransaction();
                },
                error: function (response) {
                    if ('status' in response && response.status == 422) {
                        $.each(response.responseJSON.errors, function (key, message) {
                            $(`.${key}${SUFFIXES_ERROR}`).text(message).show();
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                complete: function () {
                    loading(false);
                }
            });
        }
    }

    function payNow() {
        const id = $(this).attr('data-transaction-id');
        if (id) {
            $.ajax({
                url: `/employer/payments/${id}/pay-now`,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method: 'POST',
                dataType: 'json',
                beforeSend: () => {
                    loading(true);
                }
            }).done((response) => {
                const parent = $(this).parent();
                $(this).remove();
                parent.prepend(renderHtmlTemplate('paid-status-template'));
                const tr = $(`.table-transactions-employer table tr[data-id="${id}"]`);
                tr.find('.transaction-status').html(renderHtmlTemplate('complete-status-template'));
                toastr.success(response.message);
            }).fail(() => {
                toastr.error('Pay now failed.');
            }).always(() => {
                loading(false);
            });
        }
    }

    function funding() {
        const id = $(this).closest('tr').data('id');
        const element = $(this);
        let fundingPage = createBlankPage();

        if (id) {
            element.attr('disabled', true);
            $.ajax({
                url: `/employer/payments/${id}/funding`,
                method: 'GET',
                dataType: 'json',
                beforeSend: () => {
                    loading(true);
                }
            }).done((response) => {
                if (response.redirect_url || null) {
                    fundingPage.location.href = response.redirect_url;
                    element.removeClass('btn-funding-transaction hover-button');
                }
            }).fail(() => {
                fundingPage.close();
                toastr.error('An error has occurred.');
                element.removeAttr('disabled');
            }).always(() => {
                loading(false);
            });
        }
    }

    return { listener };
})(jQuery);

$(function () {
    payment.listener();
});
