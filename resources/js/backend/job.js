import { removeHtmlRemainDevice } from "./common";
import { TYPE_SORT_ASC, TYPE_SORT_DESC } from '../global';

const ORDER_BY_STATUS_FIELD = 'status';
const ORDER_BY_NAME_FIELD = 'name';
const ORDER_BY_EMPLOYER_NAME_FIELD = 'employer_name';

const job = {
    PARENT_SELECTOR: '#admin-job-manager-table',

    init: () => {
        let paramSearch = {
            orderByType: '',
            orderByField: '',
            page: ''
        };
        job.getUserByCompanyId();

        $(document).on('click', '.admin-btn-delete-job', function () {
            let dontShowAgain = sessionStorage.getItem('dont-show-again-job');
            $('#delete-job-form').attr('action', $(this).data('action'));
            if (dontShowAgain) {
                job.deleteJob();
            } else {
                job.showModalConfirm(this);
            }
        });

        $(document).on('click', '.btn-delete-job', function () {
            let isChecked = $('#show-again')[0].checked;
            if (isChecked) {
                sessionStorage.setItem('dont-show-again-job', isChecked);
            } else {
                sessionStorage.removeItem('dont-show-again-job');
            }
            job.deleteJob();
        });

        $(document).on('click', `${job.PARENT_SELECTOR} .btn-sort-status`, function () {
            paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
            paramSearch.orderByField = ORDER_BY_STATUS_FIELD;
            job.search(paramSearch);
        });

        $(document).on('click', `${job.PARENT_SELECTOR} .btn-sort-name`, function () {
            paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
            paramSearch.orderByField = ORDER_BY_NAME_FIELD;
            job.search(paramSearch);
        });

        $(document).on('click', `${job.PARENT_SELECTOR} .btn-sort-employer-name`, function () {
            paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
            paramSearch.orderByField = ORDER_BY_EMPLOYER_NAME_FIELD;
            job.search(paramSearch);
        });

        $(document).on('click', `${job.PARENT_SELECTOR} .table-jobs .pagination a`, function (e) {
            e.preventDefault();
            paramSearch.page = $(this).attr('href').split('page=')[1];
            job.search(paramSearch);
        });

        $(document).on('change', '#form-select-company', (e) => {
            job.getUserByCompanyId();
        });

        $(document).on('change', '#form-select-company-representative', (e) => {
            localStorage.setItem('user_id', $('#form-select-company-representative option:selected').val());
        });

        $(document).on('click', '.btn-status-job', function () {
            let val = $(this).attr('aria-pressed');
            $('input[name="status"]').attr('value', val === 'true' ? 1 : 0);
        });

        $('#modal-confirm-delete-job').on(
            'click',
            '.btn-cancel, .close',
            function () {
                $('#delete-job-form').attr('action', '#');
            }
        );
    },

    showModalConfirm: function (obj) {
        $('#show-again').prop("checked", false);
        $('#modal-confirm-delete-job').modal('show');
    },

    deleteJob: function () {
        $('#delete-job-form').trigger('submit');
        $('#modal-confirm-delete-job').modal('hide');
    },

    search: (paramSearch) => {
        $.ajax({
            url: '/admin/job',
            method: 'GET',
            dataType: 'json',
            data: paramSearch,
        }).done((response) => {
            if (response.html) {
                $('.table-jobs').html(response.html)
                $('#form-export-jobs').append(`<input type="hidden" form="form-export-jobs" name="orderByType" value="${paramSearch.orderByType}"/>`);
                $('#form-export-jobs').append(`<input type="hidden" form="form-export-jobs" name="orderByField" value="${paramSearch.orderByField}"/>`);
                setCurrentURL(paramSearch);
            }
            removeHtmlRemainDevice();
        }).fail(() => {
            toastr.error(msgError);
        })
    },

    getUserByCompanyId: () => {
        const companyId = parseInt($('#form-select-company').val());
        const formSelectUser = $('#form-select-company-representative');
        const path = $('#form-select-company option:selected').data('path');
        $('.icon-company img').attr('src', path);
        formSelectUser.empty();
        if (isNaN(companyId)) {
            $('#form-select-company-representative').append(`<option value="">Please choose user</option>`);
            return;
        } else {
            let userId = parseInt(localStorage.getItem('user_id'));
            $.ajax({
                url: '/admin/job/create',
                method: 'GET',
                dataType: 'json',
                data: {company_id: companyId}
            }).done(function (response) {
                let optionHtml = '';
                let arrUserId = [];
                response.map(val => {
                    optionHtml += `<option value="${val.id}">${val.name}</option>`
                    arrUserId.push(val.id)
                });
                formSelectUser.append(optionHtml);
                if (userId && arrUserId.includes(userId)) formSelectUser.val(userId).trigger('change');
                localStorage.setItem('user_id', $('#form-select-company-representative option:selected').val());
            }).fail(function (response) {
                toastr.error(msgError);
            })
        }
    },
}

function processOrderByTypes(currentValue) {
    return currentValue != TYPE_SORT_ASC ? TYPE_SORT_ASC : TYPE_SORT_DESC;
}

function setCurrentURL(paramSearch) {
    const params = {
        page: 1,
        orderByField: '',
        orderByType: '',
        ...paramSearch
    };

    window.history.replaceState(
        null,
        null,
        `?page=${params.page}&orderByField=${params.orderByField}&orderByType=${params.orderByType}`
    );
}

$(document).ready(() => {
    job.init();
});
