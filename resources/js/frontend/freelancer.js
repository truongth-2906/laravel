import {loading} from '../backend/common.js'
import {TYPE_SORT_ASC, TYPE_SORT_DESC} from "../global";
import {getFilterParams, replaceState} from "./common";

const MSG_ERROR_JOB = 'There was a problem this job. Please try again.';
const FREELANCER_ID = '#wrapper-freelancer';
const TB_FREELANCER_SAVED_JOB = '#table-freelancer-saved-job';
const JOB_DONE_PATHNAME = '/freelancer/job-done';

const freelancer = {
    init: () => {
        let paramSearch = {
            page: new URLSearchParams(location.search).get('page') || 1,
            orderBy: new URLSearchParams(location.search).get('orderBy') || '',
            text: new URLSearchParams(location.search).get('text') || '',
            sector_id: new URLSearchParams(location.search).get('sector_id') || '',
            category_id: new URLSearchParams(location.search).get('category_id') || '',
            experience_id: new URLSearchParams(location.search).get('experience_id') || '',
            country_id: new URLSearchParams(location.search).get('country_id') || '',
        };

        $(document).on('click', `${FREELANCER_ID} .btn-action.btn-eye,` +
            `${FREELANCER_ID} .btn-name.btn-eye,` +
            `${FREELANCER_ID} .previous-preview-job,` +
            `${FREELANCER_ID} .next-preview-job`,
            async function () {
                const jobId = $(this).data('id');
                let orderBy =
                    $('input[name="is_sorted"]').hasClass('sorted') ||
                    location.href.split('orderBy=')[1]
                        ? $('input.sort-list-job').val() : null;
                let params = {
                    id: jobId,
                    ...paramSearch
                }
                const detailJob = await freelancer.getDetailJob(params);
                $('#wrapper-freelancer .w-100.list-wrapper').html(detailJob.html);
            });

        $(document).on(
            'click',
            `${FREELANCER_ID} .redirect-page`,
            async function () {
                let orderBy =
                    $('input[name="is_sorted"]').hasClass('sorted') ||
                    new URLSearchParams(location.search).get('orderBy')
                        ? $('input.sort-list-job').val() : null;
                if (orderBy) {
                    paramSearch.orderBy = freelancer.getTypeSort(orderBy);
                }
                let params = {
                    ...paramSearch,
                    id: $(this).data('id'),
                };
                const page = await freelancer.getCurrentPage(params);
                if (page.page !== 1) {
                    paramSearch.page = page.page;
                    freelancer.setCurrentURL(paramSearch);
                }

                window.location.href = `/freelancer${location.search || ''}`;
            }
        );

        $(document).on(
            'click',
            `${FREELANCER_ID} .btn-sort-status`,
            async function () {
                $('input[name="is_sorted"]').addClass('sorted');
                $('input.sort-list-job').val(freelancer.getTypeSort($(this).data('type')));
                paramSearch.orderBy = $(this).data('type')
                await freelancer.searchJobs(paramSearch);
                let params = new URLSearchParams(location.search);
                params.set('orderBy', paramSearch.orderBy);
                window.history.replaceState(null, null, location.pathname + '?' + params.toString());
            }
        );

        $(document).on(
            'click',
            `${FREELANCER_ID} .table-freelancers .next-back-pagination a`,
            function (e) {
                e.preventDefault();
                paramSearch.page = $(this).attr('href').split('page=')[1];

                window.history.replaceState(
                    null,
                    null,
                    `?page=${paramSearch.page}&orderBy=${paramSearch.orderBy}&text=${paramSearch.text}&sector_id=${paramSearch.sector_id}&category_id=${paramSearch.category_id}&experience_id=${paramSearch.experience_id}&country_id=${paramSearch.country_id}`
                );
                window.location.href = $(location).attr('href');
            }
        );

        $(document).on('click', '.btn.btn-apply-now', function () {
            $('#modal-confirm-apply').modal('show');
        });

        $(document).on('click', '.btn-confirmed-apply', function () {
            $('.btn.btn-apply-now').attr('disabled', true);
            $('#modal-confirm-apply').modal('hide');
            let data = {
                job_id: location.pathname.split('/').at(-1),
            };
            $.ajax({
                url: '/freelancer/apply',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content'
                    ),
                },
                data: data,
            })
                .done(() => {
                    $('#modal-apply-success').modal('show');
                })
                .fail(() => {
                    $('.btn.btn-apply-now').removeAttr('disabled');
                    toastr.error('Apply failed.');
                });
        });

        $(document).on('click', '.btn-sort-application-status', function () {
            const urlSearchParams = new URLSearchParams(location.search);
            const params = {
                page: 1,
                orderBy:
                    urlSearchParams.get('orderBy') != TYPE_SORT_ASC
                        ? TYPE_SORT_ASC
                        : TYPE_SORT_DESC,
                ...getFilterParams(urlSearchParams),
            };
            freelancer.search(params);
        });

        $(document).on('click', '.btn-available', function () {
            let val = $(this).attr('aria-pressed');
            $('input[name="available"]').attr('value', val === 'true' ? 1 : 0);
        });

        $(document).on('click', '.switch-tab', function () {
            $('.switch-tab').removeClass('tab-active');
            $(this).addClass('tab-active');
            $('.layout-switch-tab').addClass('d-none');
            freelancer.switchTabReview($(this).data('tab'));
        });

        $(document).on('click', '.star-rate-job .rate-star', function () {
            const element = $('.star-rate-job .rate-star');
            element.removeClass('img-star-yellow').addClass('img-star-white');
            const indexChoose = element.index($(this));
            $('input[name="star"]').val(indexChoose + 1);
            element.map(function (index) {
                if (index <= indexChoose) {
                    element.eq(index).addClass('img-star-yellow').removeClass('img-star-white');
                }
            });
        });

        $(document).on('click', '.btn-cancel-review-job', () => {
            $('.star-rate-job .rate-star').removeClass('img-star-yellow').addClass('img-star-white');
            $('input[name="rate_star"]').val(0);
            $('textarea.enter-comment').val('');
        });

        $(document).on('click', '#review-job .btn-submit-review-job', () => {
            const formData = new FormData($('#review-job')[0]);
            $.ajax({
                url: '/freelancer/add-review-job',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: () => {
                    loading(true);
                },
            }).done((response) => {
                $('.layout-switch-tab.tab-review').html(response.html);
                $('.layout-switch-tab.tab-review .user-job-create').text($('.over-view .user-name-create-job').val());
                $('#data-review-job-done').html(response.html);
            }).fail((err) => {
                if (err.responseJSON.errors) {
                    const errors = err.responseJSON.errors;
                    for (let error in errors) {
                        $(`#review-job p.text-danger[data-error="${error}"]`).text(errors[error]);
                    }
                }
            }).always(() => {
                loading(false);
            });
        });

        $(document).on('show.bs.modal', '#filter-modal-job', function () {
            freelancer.getValueSearch($(this), paramSearch);
        });

        $(document).on('hide.bs.modal', '#filter-modal-job', function () {
            freelancer.clearDataSearch($(this));
        });

        $(document).on('submit', '#filter-modal-job #filter-modal-job-form', (e) => {
            e.preventDefault();
            let form = $('#filter-modal-job #filter-modal-job-form');
            paramSearch.page = 1;
            paramSearch.text = form.find('input[type="text"]').val();
            paramSearch.sector_id = form.find('select[name="sector_id"]').val();
            paramSearch.category_id = form.find('#form-select-categories').val();
            paramSearch.experience_id = form.find('select[name="experience_id"]').val();
            paramSearch.country_id = form.find('select[name="country_id"]').val();

            window.location.href = `/freelancer?page=${paramSearch.page}&orderBy=${paramSearch.orderBy}&text=${paramSearch.text}&sector_id=${paramSearch.sector_id}&category_id=${paramSearch.category_id}&experience_id=${paramSearch.experience_id}&country_id=${paramSearch.country_id}`;
        });

        $(document).on('click', `${TB_FREELANCER_SAVED_JOB} .btn-sort-status`, function (_e) {
            const searchParams = new URLSearchParams(location.search);
            const params = {
                page: 1,
                orderBy:
                    searchParams.get('orderBy') != TYPE_SORT_ASC
                        ? TYPE_SORT_ASC
                        : TYPE_SORT_DESC,
                ...getFilterParams(searchParams),
            };

            freelancer.searchSavedJob(params);

            window.history.replaceState(null, null, location.pathname + '?' + params.toString());
        });

        $(document).on('keyup paste', `.list-saved-job input[name="hot_search"]`, async function () {
            const value = $(this)
                .val()
                .replace(/^[\s　]+|[\s　]+$/g, '');
            const oldValue = $(this).data('old-value');
            if (value !== oldValue && value != '') {
                paramSearch = {
                    page: 1,
                    orderBy: TYPE_SORT_DESC,
                    hot_search: value,
                }
            }
            await freelancer.searchSavedJob(paramSearch);
        });

        $(document).on('click', '.show-form-filter-job-done', function () {
            $('#modal-filter-job-done').modal('show');
        });

        $(document).on('keyup', '.search-job-done', function () {
            const key_search = $(this).val().trim();
            freelancer.searchJobDone({ key_search });
        });

        $(document).on('click', '#modal-filter-job-done .btn-search-filter', function () {
            $('#form-filter-job-done').attr('action', '/freelancer/job-done').submit();
        });

        $(document).on('hidden.bs.modal', '#modal-apply-success', function () {
            location.href = "/freelancer/job-applications";
        });

        $(document).on('keyup paste', '.job-applications-wrapper input[name="hot_search"]', freelancer.hotSearch);

        $(document).on('keyup paste', '.job-applications-wrapper input[name="hot_search"]', freelancer.hotSearch);

        $(document).on('click', '#modal-filter-job-application .btn-search-filter', function () {
            $(this).closest('form').trigger('submit');
        });

        $(document).on(
            'click',
            `.table-job-applications .btn-preview, .table-job-applications .btn-next-page`,
            async function (e) {
                e.stopPropagation();
                const urlSearchParams = new URLSearchParams(location.search);
                await freelancer.jobApplicationDetail($(this).data('id'), getFilterParams(urlSearchParams));
            }
        );

        $(document).on(
            'click',
            `.list-saved-job .btn-preview, .list-saved-job .btn-next-page`,
            freelancer.jobSavedPreview
        );

        $(document).on(
            'click',
            `.list-saved-job .btn-close-preview, .list-jobs-done .btn-close-preview`,
            function () {
                    location.reload();
            }
        );

        $(document).on(
            'click',
            `.list-jobs-done .btn-preview, .list-jobs-done .btn-next-page`,
            freelancer.jobDonePreview
        );

        $(document).on('click', '#review-job-done', function () {
            freelancer.getReviewJobDone($(this).attr('data-id-job-done'));
        });
    },

    searchJobDone: (params) => {
        return $.ajax({
            url: '/freelancer/job-done',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: params,
        }).done((response) => {
            $('.list-jobs-done #table-wrapper').html(response.html);
            replaceState(params);
        }).fail(() => {
            toastr.error(MSG_ERROR_JOB);
        });
    },

    searchJobs: (paramSearch) => {
        return $.ajax({
            url: '/freelancer',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: paramSearch,
        }).done((response) => {
            $(`${FREELANCER_ID} .w-100.list-wrapper`).html(response.html);
        }).fail(() => {
            toastr.error(MSG_ERROR_JOB);
        });
    },


    searchSavedJob: (paramSearch) => {
        return $.ajax({
            url: '/freelancer/job-saved',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: paramSearch,
        }).done((response) => {
            if (response.html) {
                replaceState(paramSearch);
                $(`${TB_FREELANCER_SAVED_JOB} .scroll-table`).html(response.html);
                if ('current_page' in response && response.current_page) {
                    paramSearch.page = response.current_page;
                }
            }

        }).fail(() => {
            toastr.error(MSG_ERROR_JOB);
        });
    },

    getDetailJob: (params) => {
        loading(true);
        return $.ajax({
            url: '/freelancer/job-preview',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: params,
            beforeSend: () => {
                loading(true);
            }
        })
            .done((response) => {
                return response;
            })
            .fail(() => {
                toastr.error(MSG_ERROR_JOB);
            })
            .always(() => {
                loading(false);
            });
    },

    getCurrentPage: (params) => {
        return $.ajax({
            url: '/freelancer/current-page-job',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: params,
        }).done((response) => {
            return response;
        }).fail(() => {
            toastr.error(MSG_ERROR_JOB);
        });
    },

    search: async (paramSearch, callBackSuccess = null) => {
        $.ajax({
            url: '/freelancer/job-applications',
            method: 'GET',
            dataType: 'json',
            data: paramSearch,
        }).done((response) => {
            if (response.html) {
                $('.table-job-applications').html(response.html);
                replaceState(paramSearch);
                if (typeof callBackSuccess === 'function') {
                    callBackSuccess();
                }
            }
        }).fail(() => {
            toastr.error('Sort failed.');
        });
    },

    jobApplicationDetail: async (jobId, params) => {
        if (!jobId) return;
        $.ajax({
            url: `/freelancer/job-applications/${jobId}`,
            method: 'GET',
            data: params,
            dataType: 'json',
        }).done((response) => {
            if (response.html) {
                $('#job-application-wrapper .table-job-applications').html(
                    response.html
                );
            }
        }).fail(() => {
            toastr.error('Sort failed.');
        });
    },

    getTypeSort: (type) => {
        return type === 'ASC' ? 'DESC' : 'ASC';
    },

    switchTabReview: (dataTab) => {
        const elementBtnMark = $('#wrapper-employer .btn-mark-done');
        $('.detail-freelancer-apply-template').html('');
        $('#wrapper-employer .job-footer').removeClass('d-none');
        elementBtnMark.addClass('d-none');
        switch (dataTab) {
            case 1:
                $('.tab-overview').removeClass('d-none');
                elementBtnMark.removeClass('d-none');
                break;
            case 2:
                $('.tab-description').removeClass('d-none');
                break;
            case 3:
                $('.tab-about-employer').removeClass('d-none');
                break;
            case 4:
                $('.tab-review').removeClass('d-none');
                break;
            case 5:
                $('.tab-job-application').removeClass('d-none');
                break;
            default:
                return false;
        }
    },

    getValueSearch: (modal, paramSearch) => {
        if (paramSearch.text) { modal.find('input[type="text"]').val(paramSearch.text).trigger('change'); }
        if (paramSearch.sector_id) { modal.find('select[name="sector_id"]').val(paramSearch.sector_id).trigger('change'); }
        if (paramSearch.category_id) { modal.find('#form-select-categories').val(paramSearch.category_id.split(',')).trigger('change'); }
        if (paramSearch.experience_id) { modal.find('select[name="experience_id"]').val(paramSearch.experience_id).trigger('change'); }
        if (paramSearch.country_id) { modal.find('select[name="country_id"]').val(paramSearch.experience_id).trigger('change'); }
    },

    clearDataSearch: (modal) => {
        modal.find('input[type="text"]').val('');
        modal.find('select[name="sector_id"]').val(null).trigger("change");
        modal.find('#form-select-categories').val([]).trigger("change");
        modal.find('select[name="experience_id"]').val(null).trigger("change");
        modal.find('select[name="country_id"]').val(null).trigger("change");
    },

    setCurrentURL: (paramSearch) => {
        window.history.replaceState(
            null,
            null,
            `?page=${paramSearch.page}&orderBy=${paramSearch.orderBy}&text=${paramSearch.text}&sector_id=${paramSearch.sector_id}&category_id=${paramSearch.category_id}&experience_id=${paramSearch.experience_id}&country_id=${paramSearch.country_id}`
        );
    },

    hotSearch: async function () {
        const element = $(this);
        const value = element
            .val()
            .replace(/^[\s\u3000]+|[\s\u3000]+$/g, '');
        const oldValue = element.data('old-value');
        if (value !== oldValue) {
            const params = {
                page: 1,
                orderBy: TYPE_SORT_DESC,
                hot_search: value,
            };

            await freelancer.search(params, () => {
                element.data('old-value', params.hot_search);
                $(`#modal-filter-job-application`).find('input, select').val('').trigger("change");
            });
        }
    },

    jobSavedPreview: function () {
        const id = $(this).data('id');
        const urlSearchParams = new URLSearchParams(location.search);
        const params = getFilterParams(urlSearchParams);
        if (id) {
            $.ajax({
                type: 'GET',
                url: `/freelancer/jobs/${id}/saved`,
                data: params,
                dataType: 'json',
                success: function (response) {
                    $('.list-saved-job .list-wrapper').html(
                        response.html
                    );
                },
                error: function () {
                    toastr.error('An error has occurred.');
                },
            });
        }
    },

    jobDonePreview: function () {
        const id = $(this).data('id');
        const urlSearchParams = new URLSearchParams(location.search);
        const params = getFilterParams(urlSearchParams);
        if (id) {
            $.ajax({
                type: 'GET',
                url: `/freelancer/jobs/${id}/done`,
                data: params,
                dataType: 'json',
                success: function (response) {
                    $('.list-jobs-done #table-wrapper').html(response.html);
                },
                error: function () {
                    toastr.error('An error has occurred.');
                },
            });
        }
    },

    getReviewJobDone: function (id) {
        $.ajax({
            type: 'GET',
            url: `/freelancer/get-review-job-done/${id}`,
            dataType: 'json',
            success: function (response) {
                $('#data-review-job-done').html(response.html);
            },
            error: function () {
                toastr.error('An error has occurred.');
            },
        });
    }
};

$(document).ready(function () {
    freelancer.init();
});
