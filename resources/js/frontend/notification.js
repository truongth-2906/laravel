import { renderHtmlTemplate } from './common';

const notification = (function ($, window, RECEIVER_ID, IS_NOTIFICATIONS_SCREEN, RECEIVER_TYPE) {
    const NOTIFY_TOAST_TEMPLATE = 'notify-toast-template';
    const NOTIFY_ACTIONS_TEMPLATE = 'notify-actions-template';
    const ID_TOAST_PREFIX = 'notify-toast-';
    const SUGGESTION_HEIGHT = 40;
    const DELAY_TOAST = 3000;
    const BROADCAST_NAME = '.push.notification';

    function init() {
        listener();
        registerEcho();
    }

    function listener() {
        $(document).on('click', '.btn-confirm-delete-notification', function () {
            const dontShowAgain = sessionStorage.getItem(
                'dont-show-again-notification'
            );
            $('#form-delete').attr('action', $(this).data('action'));
            if (dontShowAgain) {
                submitDelete();
            } else {
                showModalConfirmDelete();
            }
        });

        $(document).on('click', '.btn-delete-notification', function () {
            let isChecked = $('#show-again')[0].checked;
            if (isChecked) {
                sessionStorage.setItem('dont-show-again-notification', isChecked);
            } else {
                sessionStorage.removeItem('dont-show-again-notification');
            }
            submitDelete();
        });

        $('#modal-confirm-delete-notification').on(
            'click',
            '.btn-cancel, .close',
            function () {
                $('#form-delete').attr('action', '#');
            }
        );

        $(document).on('hidden.bs.toast', '.notify-toast', function () {
            $(this).remove();
        });

        $('.notify-toast-wrapper').on('scroll', handleScrollNotifications);

        $(document).on('click', '.notify-toast', handleReadNotification);

        if (RECEIVER_ID) {
            resizeObserver.observe(document.querySelector(".notify-toast-wrapper"));
        }
    }

    function showModalConfirmDelete() {
        $('#show-again').prop('checked', false);
        $('#modal-confirm-delete-notification').modal('show');
    }

    function submitDelete() {
        $('#form-delete').trigger('submit');
        $('#modal-confirm-delete-notification').modal('hide');
    }

    function registerEcho() {
        if (RECEIVER_ID) {
            Echo.private(`notification.${RECEIVER_ID}`).listen(BROADCAST_NAME, (e) => {
                if (!IS_NOTIFICATIONS_SCREEN) {
                    showPopupNotification(e.notification);
                    handleSuggestionBottom.call($('.notify-toast-wrapper').get(0), $('.notify-toast-wrapper').scrollTop(), 0);
                }
                increaseNumberUnread();
            });
        }
    }

    function showPopupNotification(notification) {
        const props = {
            id: notification.id,
            title: notification.title,
            content: notification.content,
            icon: notification.icon,
            delay: DELAY_TOAST,
            actions: renderHtmlTemplate(NOTIFY_ACTIONS_TEMPLATE, {
                title: notification.actions.title || 'Link',
                route: notification.actions.route || '#',
            })
        };

        const html = renderHtmlTemplate(NOTIFY_TOAST_TEMPLATE, props);
        $('#notify-toast-container').prepend(html);
        $(`#${ID_TOAST_PREFIX}${notification.id}`).toast('show');
    }

    function increaseNumberUnread() {
        let number = Number($('#compact-number-unread-notification').data('number'));
        number = isNaN(number) ? 1 : number + 1;
        $('#compact-number-unread-notification').text(number > 100 ? '100+' : number).data('number', number).show();
        $('#total-number-unread-notification').text(number);
    }

    function decreaseNumberUnread() {
        let number = Number($('#compact-number-unread-notification').data('number'));
        number = isNaN(number) && number <= 0 ? 0 : number - 1;
        $('#compact-number-unread-notification').text(number > 100 ? '100+' : number).data('number', number);
        $('#total-number-unread-notification').text(number);
    }

    const resizeObserver = new ResizeObserver((entries) => {
        const element = $(entries[0].target);
        const maxHeight = Math.round($(window).height() * 80 / 100);
        const elementHeight = Math.round(element.height());

        if (elementHeight >= maxHeight) {
            element.addClass('has-scroll');
        } else {
            element.removeClass('has-scroll');
        }
    });

    function handleScrollNotifications() {
        const positionScrollTop = $(this).scrollTop();
        handleSuggestionTop.call(this, positionScrollTop);
        handleSuggestionBottom.call(this, positionScrollTop);
    }

    function handleSuggestionTop(positionScrollTop, offset = SUGGESTION_HEIGHT) {
        if (positionScrollTop >= offset) {
            $(this).addClass('show-suggestion-top');
        } else {
            $(this).removeClass('show-suggestion-top');
        }
    }

    function handleSuggestionBottom(positionScrollTop, offset = SUGGESTION_HEIGHT) {
        if (positionScrollTop + $(this).innerHeight() <= $(this)[0].scrollHeight - offset) {
            $(this).addClass('show-suggestion-bottom');
        } else {
            $(this).removeClass('show-suggestion-bottom');
        }
    }

    function handleReadNotification(event) {
        const element = $(event.target);
        if (
            !element.hasClass('except reading trigger') &&
            !element.parent().hasClass('except reading trigger')
        ) {
            read($(this).data('id'));
            $(this).toast('hide');
        }
    }

    function read(notificationId) {
        if (notificationId && RECEIVER_TYPE) {
            $.ajax({
                type: "GET",
                url: `/${RECEIVER_TYPE}/notifications/${notificationId}`,
                dataType: "json",
                success: function () {
                    decreaseNumberUnread();
                }
            });
        }
    }

    return { init };
})(
    jQuery,
    window,
    typeof RECEIVER_ID !== 'undefined' ? RECEIVER_ID : null,
    typeof IS_NOTIFICATIONS_SCREEN !== 'undefined' ? IS_NOTIFICATIONS_SCREEN : false,
    typeof RECEIVER_TYPE !== 'undefined' ? RECEIVER_TYPE : false,
);

$(function () {
    notification.init();
});
