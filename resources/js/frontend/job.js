import {loading, scrollToError} from '../backend/common';
import {TYPE_SORT_ASC, TYPE_SORT_DESC} from '../global';

const job = (function ($) {
    const modules = {};
    let isSubmit = false;
    const SUFFIXES_ERROR = '_input_error_message';
    const CATEGORY_ID = 'category_id';

    modules.init = function () {

        $(document).on('click', '.btn-confirm-delete-job', function () {
            if (isSubmit === false) {
                const dontShowAgain = sessionStorage.getItem(
                    'dont-show-again-job'
                );
                $('#form-delete').attr('action', $(this).data('action'));
                if (dontShowAgain) {
                    submitDelete();
                } else {
                    showModalConfirmDelete();
                }
            }
        });

        $(document).on('click', '.btn-delete-job', function () {
            let isChecked = $('#show-again')[0].checked;
            if (isChecked) {
                sessionStorage.setItem('dont-show-again-job', isChecked);
            } else {
                sessionStorage.removeItem('dont-show-again-job');
            }
            submitDelete();
        });

        $('#modal-confirm-delete-job').on(
            'click',
            '.btn-cancel, .close',
            function () {
                $('#form-delete').attr('action', '#');
            }
        );

        $('#form-create-job').on('submit', modules.onSubmit);
    };

    function showModalConfirmDelete() {
        $('#show-again').prop('checked', false);
        $('#modal-confirm-delete-job').modal('show');
    }

    function submitDelete() {
        isSubmit = true;
        $('#form-delete').trigger('submit');
        $('#modal-confirm-delete-job').modal('hide');
    }

    modules.onSubmit = function (e) {
        e.preventDefault();
        if (isSubmit === false) {
            beforeSaveJob();
            $.ajax({
                type: 'POST',
                url: '/employer/jobs',
                data: getFormData(),
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (response) {
                    location.href = response.redirect || location.url;
                },
                error: function (response) {
                    return afterSaveJob(response);
                },
            });
        }
    };

    function beforeSaveJob() {
        loading(true);
        $('.errors').text('').hide();
        isSubmit = true;
    }

    function afterSaveJob(response) {
        $('#form-create-job button').removeAttr('disabled');
        showError(response);
        isSubmit = false;
        setTimeout(() => {
            loading(false);
        }, 1000);
    }

    function showError(response) {
        if (response.status == 422) {
            $.each(response.responseJSON.errors, function (key, message) {
                key = key.includes(CATEGORY_ID)
                    ? CATEGORY_ID
                    : key.replace('/./g', '_');
                $(`.${key}${SUFFIXES_ERROR}`).text(message).show();
            });
            scrollToError();
        } else {
            toastr.error('Job added failed.');
        }
    }

    function getFormData() {
        const formData = new FormData();
        const inputsName = ['name', 'country_id', 'status', 'timezone_id', 'description', 'wage', 'category_id[]', 'experience_id'];
        $.each(inputsName, function (_i, name) {
            const value = $(`#form-create-job [name="${name}"]`).val();
            const isInputArray = name.search(/\[\]/) != -1;
            name = name.replace('/[|]/g', '');

            if (isInputArray) {
                $.each(value, function (_j, item) {
                    formData.append(`${name}`, item);
                });
            } else {
                formData.append(name, value);
            }
        });

        $.each(
            $('#form-create-job [name="file_upload[]"]').get(0).files,
            function (i, file) {
                const index = $($('.file-uploaded').get(i)).data('index') || i;
                formData.append(`file_upload[${index}]`, file);
            }
        );

        $('.project-info input.name-file').map(function () {
            formData.append('file_name[]', $(this).val());
        });

        return formData;
    }

    return modules;
})(jQuery);

const jobManager = (function ($) {
    let modules = {};
    let isOldCheckedAll = false;
    const PARENT_SELECTOR = '.manager-job-table';

    modules.init = function () {
        $(document).on('click', `${PARENT_SELECTOR} #check-all-job`, checkedAll);

        $(document).on('click', `${PARENT_SELECTOR} .check-all`, unChecked);

        $(document).on('click', `${PARENT_SELECTOR} .btn-sort-status`, handleSort);

        $(document).on('click', `${PARENT_SELECTOR} a.page-link`, handleNextPage);

        $(document).on('click', `${PARENT_SELECTOR} .btn-preview, ${PARENT_SELECTOR} .previous-preview-job, ${PARENT_SELECTOR} .next-preview-job`, preview);

        $(document).on('click', `${PARENT_SELECTOR} .btn-close-preview`, reloadPage);
    };

    async function handleSort() {
        const params = {
            page: 1,
            orderBy:
                new URLSearchParams(location.search).get('orderBy') !=
                TYPE_SORT_ASC
                    ? TYPE_SORT_ASC
                    : TYPE_SORT_DESC,
            isCheckAll: $('#check-all-job').is(':checked') ? 1 : 0,
        };

        await get(params);
    }

    async function handleNextPage(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'));
        const params = {
            page: url.searchParams.get('page') || 1,
            orderBy:
                url.searchParams.get('orderBy') !=
                TYPE_SORT_ASC
                    ? TYPE_SORT_DESC
                    : TYPE_SORT_ASC,
            isCheckAll: $('#check-all-job').is(':checked') ? 1 : 0,
        };

        await get(params);
    }

    async function reloadPage(e) {
        e.preventDefault();
        const params = {
            page: new URLSearchParams(location.search).get('page') || 1,
            orderBy:
                new URLSearchParams(location.search).get('orderBy') !=
                TYPE_SORT_ASC
                    ? TYPE_SORT_DESC
                    : TYPE_SORT_ASC,
            isCheckAll: isOldCheckedAll ? 1 : 0,
        };

        await get(params);
    }

    function get(params) {
        return $.ajax({
            type: 'GET',
            url: '/employer/jobs',
            data: params,
            dataType: 'json',
            success: function (response) {
                if (response.html) {
                    $('#table-wrapper').html(response.html);
                    window.history.replaceState(
                        null,
                        null,
                        `?page=${params.page || 1}&orderBy=${params.orderBy}`
                    );
                }
            },
            error: function () {
                toastr.error('An error has occurred.');
            },
        });
    }

    function checkedAll() {
        if ($(this).is(':checked')) {
            $('.check-all:not(:checked)').prop('checked', true);
        } else {
            $('.check-all:checked').prop('checked', false);
        }
    }

    function unChecked() {
        if (!$(this).is(':checked')) {
            $('#check-all-job').prop('checked', false);
        }
    }

    function preview() {
        const id = $(this).data('id');
        if (id) {
            $.ajax({
                type: 'GET',
                url: `/employer/jobs/${id}/preview`,
                data: {},
                dataType: 'json',
                success: function (response) {
                    if (response.html) {
                        isOldCheckedAll = $('#check-all-job').is(':checked');
                        $('#table-wrapper').html(response.html);
                    }
                },
                error: function () {
                    toastr.error('An error has occurred.');
                }
            });
        }
    }

    return modules;
})(jQuery);

$(function () {
    job.init();
    jobManager.init();
});
