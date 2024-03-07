import { raauUrl } from './global';

const auth = (function ($) {

    function init() {
        $(document).on('click', '.logout-form', logout);
        $(document).on('click', '#sign-up-form #agree-terms-of-use', handleAgreeToTheTermsOfUse);
    }

    function logout(e) {
        e.preventDefault();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST",
            url: "/logout",
            dataType: "json",
            success: function () {
                loggedOut();
            },
            error: function (response) {
                if ('status' in response && response.status == '419') {
                    loggedOut();
                } else {
                    toastr.error(msgError);
                }
            }
        });
    }

    function loggedOut() {
        localStorage.clear();
        window.location.href = raauUrl.login;
    }

    function handleAgreeToTheTermsOfUse() {
        if ($(this).is(':checked')) {
            $('#sign-up-form #btn-sign-up').removeAttr('disabled');
        } else {
            $('#sign-up-form #btn-sign-up').attr('disabled', true);
        }
    }

    return { init };
})(jQuery);

$(function () {
    auth.init();
});
