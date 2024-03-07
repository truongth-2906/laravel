import {raauUrl} from '../global.js'

const account = {
    init: () => {
        $(document).on('click', '.tab-details', function () {
            let screen = localStorage.getItem('screen');
            let url = raauUrl.setting_employer;
            if (screen === 'freelancer') {
                url = raauUrl.setting_freelancer;
            }
            window.location.href = url;
        });
    }
}

$(document).ready(function () {
    account.init();
});
