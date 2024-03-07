import { handleUserStatusOnline } from './message';
import { ONLINE_STATUS, OFFLINE_STATUS } from '../global';

export const authFrontend = (function ($, RECEIVER_ID, SESSION_LIFETIME) {
    const PRESENCE_ONLINE = 'online';
    const BROADCAST_LEAVE_CHANNEL_NAME = '.push.leave_channels';
    const leaveWaiting = {};

    function init() {
        registerPresenceOnline();
        disconnectWhenSessionExpired();
    }

    function registerPresenceOnline() {
        if (RECEIVER_ID) {
            Echo.join(PRESENCE_ONLINE)
                .listen(BROADCAST_LEAVE_CHANNEL_NAME, (e) => {
                    const token = e.token || null;
                    if (token == $('meta[name="echo-token"]').attr('content')) {
                        Echo.disconnect();
                    }
                })
                .joining((user) => {
                    if (`${user.id}` in leaveWaiting) {
                        clearTimeout(leaveWaiting[user.id]);
                        delete leaveWaiting[user.id];
                    }
                    return handleUserStatusOnline(user, ONLINE_STATUS);
                })
                .leaving((user) => {
                    if (!(`${user.id}` in leaveWaiting)) {
                        leaveWaiting[user.id] = setTimeout(() => {
                            return handleUserStatusOnline(user, OFFLINE_STATUS);
                        }, 10000);
                    }
                });
        }
    }

    function disconnectWhenSessionExpired() {
        let timeout = Number(SESSION_LIFETIME);
        if (!isNaN(timeout)) {
            timeout = timeout * 60000;   //minutes to milliseconds
        } else {
            timeout = 120 * 60000;   //minutes to milliseconds
        }

        setTimeout(() => {
            Echo.disconnect();
        }, timeout);
    }

    return { init };
})(
    jQuery,
    typeof RECEIVER_TYPE !== 'undefined' ? RECEIVER_TYPE : false,
    typeof SESSION_LIFETIME !== 'undefined' ? SESSION_LIFETIME : 120
);

$(function () {
    authFrontend.init();
});
