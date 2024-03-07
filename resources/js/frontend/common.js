import { raauUrl } from '../global.js';
import { loading } from '../backend/common';

function formatResultCategory(state) {
    if (!state.id) return state.text;
    return $(
        `<div class="font-weight-500 font-size-16 color-inherit" data-select2-manual-id=${state.id} data-select2-color=${state.color} data-select2-border-color=${state.borderColor} data-select2-background-color=${state.backgroundColor}>` +
            state.text +
            "</div>"
    );
}

export const replaceState = function (paramSearch) {
    let queryString = '';

    $.each(paramSearch, function (key, item) {
        if (typeof item === 'object' || Array.isArray(item)) {
            $.each(item, function (_i, value) {
                queryString += `${encodeURIComponent(
                    `${key}[]`
                )}=${encodeURIComponent(value)}&`;
            });
        } else {
            queryString += `${encodeURIComponent(
                `${key}`
            )}=${encodeURIComponent(item)}&`;
        }
    });

    window.history.replaceState(null, null, `?${queryString}`);
};

export const getFilterParams = function (searchParams) {
    const params = {};

    if (searchParams instanceof URLSearchParams) {
        if (searchParams.get('hot_search')) {
            params.keyword = searchParams.get('hot_search');
        } else {
            if (searchParams.get('keyword')) {
                params.keyword = searchParams.get('keyword');
            }

            if (searchParams.get('sector_id')) {
                params.sector_id = searchParams.get('sector_id');
            }

            if (searchParams.get('experience_id')) {
                params.experience_id = searchParams.get('experience_id');
            }

            if (searchParams.get('country_id')) {
                params.country_id = searchParams.get('country_id');
            }

            if (searchParams.get('key_search')) {
                params.key_search = searchParams.get('key_search');
            }

            searchParams.forEach((value, key) => {
                if (key.includes('category_id') && value) {
                    params.category_id = {
                        ...params.category_id,
                        [Object.values(params.category_id || {}).length]: value,
                    };
                }
            });
        }
    }

    return params;
};

export const renderHtmlTemplate = function (templateName, props = {}) {
    function handle() {
        const template = $(`script[data-template="${templateName}"]`)
            .text()
            .split(/\$\{(.+?)\}/g);

        return template
            .map(
                renderProps(props)
            )
            .join('');
    }

    function renderProps(props) {
        return function (e, i) {
            return i % 2 ? props[e] : e;
        };
    }

    return handle();
};

$(document).ready(function () {
    let reached = false;

    let strHtml = $('.show-read-more').html();

    let dataSaveJob = {
        job_id: '',
    };

    $(window).resize(function () {
        if ($(document).width() < 740) {
            if (!reached) {
                showReadMore();
                reached = true;
                clickReadMore();
            }
        } else {
            $('.show-read-more').each(function () {
                $(this).empty().html(strHtml);
            });
            reached = false;
        }
    });

    showReadMore();
    clickReadMore();

    function clickReadMore() {
        $('.read-more').click(function () {
            $(this).parent().html(strHtml);
            $(this).remove();
        });
    }

    function showReadMore() {
        let maxLength = 200;
        $('.show-read-more').each(function () {
            if ($(document).width() < 740) {
                let myStr = $(this).text();
                if (myStr.length > maxLength) {
                    let newStr = myStr.substring(0, maxLength);
                    let removedStr = myStr.substring(maxLength, myStr.length);
                    $(this).empty().html(newStr);
                    $(this).append(
                        ' <div id="read-more" class="read-more mt-5 d-none sp-device">\n' +
                            "                    Read more\n" +
                            "                </div>"
                    );
                    $(this).append(
                        '<span class="more-text">' + removedStr + "</span>"
                    );
                }
            }
        });
    }

    $(document).on('click', '.icon-heart-job, .btn-save-job', function () {
        dataSaveJob.job_id = $(this).attr('data-job-id');
        saveJobByUser(dataSaveJob, $(this), window.location.href);
    });

    function saveJobByUser(dataSaveJob, element, href) {
        $.ajax({
            url: '/saved/job',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content"),
            },
            method: 'POST',
            dataType: 'json',
            data: dataSaveJob,
            beforeSend: () => {
                loading(true);
            }
        }).done(() => {
        if ((href.includes(raauUrl.employer_saved_job) || href.includes(raauUrl.freelancer_saved_job)) && !element.hasClass('no-reload')) {
            location.reload();
        } else if (element.prop('tagName').toLowerCase() === 'img') {
            element.attr('src', element.attr('src').includes('/img/icon-heart.svg') ? '/img/icon-red-heart.svg' : '/img/icon-heart.svg')
        } else {
            const imgPreview = $('img.icon-heart-preview');
            if (imgPreview.length > 0) {
                imgPreview.attr('src', imgPreview.attr('src').includes('/img/icon-heart.svg') ? '/img/icon-red-heart.svg' : '/img/icon-heart.svg');
            }

            element.text(element.text().trim() === 'SAVE JOB' ? 'UN SAVE JOB' : 'SAVE JOB')
        }
        }).fail(() => {
            toastr.error('Save job fail!')
        }).always(() => {
            loading(false);
        })
    }
    $(document).on('hidden.bs.modal', '#alert-account-hidden-modal', function () {
        localStorage.setItem('alertAccountHidden', 'close');
        if (localStorage.getItem('kyc') !== 'close' && localStorage.getItem('modalSupport') !== 'open') {
            $('#kyc-modal').modal('show');
        }
    });

    if (localStorage.getItem('alertAccountHidden') !== 'close') {
        $('#alert-account-hidden-modal').modal('show');
    }

    $(document).on('hidden.bs.modal', '#kyc-modal', function () {
        localStorage.setItem('kyc', 'close');
    });

    if ((localStorage.getItem('alertAccountHidden') == 'close' || $('#alert-account-hidden-modal').length <= 0) && localStorage.getItem('kyc') !== 'close') {
        $('#kyc-modal').modal('show');
    }

    $('.btn-contact-support').on('click', function () {
        localStorage.setItem('modalSupport', 'open');
        $('#alert-account-hidden-modal').modal('hide');
        $('#modal-support').modal('show');
    });

    $(document).on('hidden.bs.modal', '#modal-support', function () {
        if (localStorage.getItem('kyc') !== 'close') {
            $('#kyc-modal').modal('show');
        }
    });
});

export const escapeHtml = (unsafe) => {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

export const createBlankPage = (message = '', pageTitle = '') => {
    let newPage = window.open("about:blank", '_blank');
    message = message ? message : 'Processing may take time, please wait while you are being redirected...';
    pageTitle = pageTitle ? pageTitle : 'Redirect to payment screen';

    newPage.document.title = $('meta[name="app-name"]').attr('content') + ' | ' + pageTitle;
    $(newPage.document.head).append(`<link rel="icon" type="image/x-icon" href="${window.location.origin}/img/brand/favicon_32x32.png">`);
    $(newPage.document.body).html(`<p style="text-align: center; margin-left: auto; margin-right: auto; margin-top: 20vh;">${message}</p>`);

    return newPage;
}
