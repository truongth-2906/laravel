import {raauUrl, TYPE_SORT_ASC, TYPE_SORT_DESC} from '../global.js'
import {loading, removeHtmlRemainDevice} from '../backend/common.js';
import {createBlankPage} from "./common";

const MSG_ERROR_FREELANCER = 'There was a problem this freelancer. Please try again.';
const MSG_ERROR_JOB = 'There was a problem this job. Please try again.';
const IS_EMPLOYER = true;
const REJECT_JOB_APPLICATION = 0;
const APPROVE_JOB_APPLICATION = 2;
const DONE_JOB_APPLICATION = 3;
const ORDER_BY_NAME_FIELD = 'name';
const ORDER_BY_IS_ONLINE_FIELD = 'is_online';
const ORDER_BY_AVAILABLE_FIELD = 'available';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let paramFreelancerApply = {};

const employer = {
    PARENT_SELECTOR: '#my-jobs-table',

    init: () => {
        let dataCompany = {
            name: ''
        };

        let paramSearch = {
            orderBy: '',
            page: ''
        };

        let paramSearchFreelancer = {
            keyword: '',
            orderByAvailable: '',
            categoryIds: [],
            experienceId: '',
            countryId: '',
            page: ''
        };

        let dataSaveFreelancer = {
            freelancer_id: ''
        };

        let searchLiveValue = $('.ipt-employer-search-freelancer').val();
        if (!searchLiveValue) {
            paramSearchFreelancer.keyword = new URLSearchParams(location.search).get('keyword') || '';
            paramSearchFreelancer.categoryIds = new URLSearchParams(location.search).getAll('categoryIds[]') || [];
            paramSearchFreelancer.experienceId = new URLSearchParams(location.search).get('experienceId') || '';
            paramSearchFreelancer.countryId = new URLSearchParams(location.search).get('countryId') || '';
        } else {
            paramSearchFreelancer.keyword = searchLiveValue;
        }

        if (localStorage.getItem('warning_message') && $('.warning_message').length) {
            toastr.warning(localStorage.getItem('warning_message'));
            localStorage.removeItem('warning_message');
        }

        $(document).on('click', `${employer.PARENT_SELECTOR} .btn-sort-status`, function () {
            paramSearch.orderBy = $(this).attr('data-type');
            $('input[name="is_sorted"]').addClass('sorted');
            $('.sort-list-job').val($(this).attr('data-type'));
            employer.searchJob(paramSearch);
        });

        $(document).on('click', `.table-jobs ${employer.PARENT_SELECTOR} .pagination a`, function (e) {
            e.preventDefault();
            paramSearch.page = $(this).attr('href').split('page=')[1];
            const orderBy = new URLSearchParams(location.search).get('orderBy') || '';
            if (orderBy && !$('input[name="is_sorted"]').hasClass('sorted')) {
                paramSearch.orderBy = orderBy
            }
            employer.searchJob(paramSearch);
        });

        $(document).on('click', '.btn-save-company', function () {
            dataCompany.name = $(document).find('.ipt-add-company').val();
            employer.saveCompany(dataCompany);
        });

        $(document).on('click', '.btn-status-job', function () {
            let val = $(this).attr('aria-pressed');
            $('input[name="status"]').attr('value', val === 'true' ? 1 : 0);
        });

        $(document).on('click', '.btn-preview-freelancer, .previous-preview-freelancer, .next-preview-freelancer', async function () {
            const searchLiveValue = $('.ipt-employer-search-freelancer').val().replace(/^[\s\uFEFF\xA0　]+|[\s\uFEFF\xA0　]+$/g, "");
            let data = {
                id: $(this).data('id'),
                keyword: searchLiveValue,
            };
            if (!searchLiveValue) {
                data = {
                    ...data,
                    keyword: new URLSearchParams(location.search).get('keyword') || '',
                    categoryIds: new URLSearchParams(location.search).getAll('categoryIds[]') || [],
                    experienceId: new URLSearchParams(location.search).get('experienceId') || '',
                    countryId: new URLSearchParams(location.search).get('countryId') || '',
                }
            }
            const previewFreelancer = await employer.previewFreelancer(data);
            $('.preview-freelancer').html(previewFreelancer.html);
        });

        $(document).on('click', '.btn-submit-review-freelancer', function () {
            const formData = new FormData($('#review-freelancer')[0]);
            $.ajax({
                url: '/employer/add-review-freelancer',
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
            }).fail((err) => {
                if (err.responseJSON.errors) {
                    const errors = err.responseJSON.errors;
                    for (let error in errors) {
                        $(`#review-freelancer p.text-danger[data-error="${error}"]`).text(errors[error]);
                    }
                }
            }).always(() => {
                loading(false);
            });
        });

        $(document).on('click', '.back-to-page', async function () {
            let url = '/employer/find-freelancer?';
            let queries = {};
            if (!searchLiveValue) {
                queries = {
                    keyword: paramSearchFreelancer.keyword,
                    categoryIds: paramSearchFreelancer.categoryIds,
                    experienceId: paramSearchFreelancer.experienceId,
                    countryId: paramSearchFreelancer.countryId,
                    page: paramSearchFreelancer.page,
                };
            } else {
                queries = {
                    keyword: paramSearchFreelancer.keyword,
                    orderByAvailable: paramSearchFreelancer.orderByAvailable,
                    page: paramSearchFreelancer.page,
                };
            }

            $.each(queries, function (key, value) {
                if (key == 'categoryIds') {
                    $.each(value, function (i, item) {
                        url += `${encodeURIComponent('categoryIds[]')}=${encodeURIComponent(item)}&`;
                    });
                } else {
                    url += `${encodeURIComponent(key)}=${encodeURIComponent(value)}&`;
                }
            });

            window.location.href = url;
        });

        $(document).on('keyup', '.ipt-employer-search-freelancer', async function () {
            paramSearchFreelancer.keyword = $(this).val();
            paramSearchFreelancer.page = '';
            paramSearchFreelancer.categoryIds = '';
            paramSearchFreelancer.experienceId = '';
            paramSearchFreelancer.countryId = '';
            paramSearchFreelancer.orderByAvailable = 'DESC';
            await employer.searchFreelancer(paramSearchFreelancer);
        });

        $(document).on('click', '.employer-table-freelancers .btn-sort-name', function () {
            paramSearchFreelancer.orderByType = employer.processOrderByTypes($(this).attr('data-type'));
            paramSearchFreelancer.orderByField = ORDER_BY_NAME_FIELD;
            employer.searchFreelancer(paramSearchFreelancer);
        });

        $(document).on('click', '.employer-table-freelancers .btn-sort-is-online', function () {
            paramSearchFreelancer.orderByType = employer.processOrderByTypes($(this).attr('data-type'));
            paramSearchFreelancer.orderByField = ORDER_BY_IS_ONLINE_FIELD;
            employer.searchFreelancer(paramSearchFreelancer);
        });

        $(document).on('click', '.employer-table-freelancers .btn-sort-available-freelancer', function () {
            paramSearchFreelancer.orderByType = employer.processOrderByTypes($(this).attr('data-type'));
            paramSearchFreelancer.orderByField = ORDER_BY_AVAILABLE_FIELD;
            employer.searchFreelancer(paramSearchFreelancer);
        });

        $(document).on('click', '.employer-table-freelancers .pagination a', function (e) {
            e.preventDefault();
            paramSearchFreelancer.orderByAvailable = $('.sort-available-freelancer').attr('data-type') || 'DESC';
            paramSearchFreelancer.page = $(this).attr('href').split('page=')[1];
            employer.searchFreelancer(paramSearchFreelancer);
        });

        $(document).on('click', '.view-job-employer, #wrapper-employer .previous-preview-job, #wrapper-employer .next-preview-job', async function () {
            const jobId = $(this).data('id');
            let orderBy =
                $('input[name="is_sorted"]').hasClass('sorted') ||
                location.href.split('orderBy=')[1]
                    ? employer.getTypeSort($('.sort-list-job').val()) : null;
            let params = {
                id: jobId,
                orderBy,
                isEmployer: IS_EMPLOYER
            }
            const detailJob = await employer.previewJobOfEmployer(params);
            if (detailJob.html) {
                $('#wrapper-employer .w-100.list-wrapper').html(detailJob.html);
            } else {
                toastr.error(MSG_ERROR_JOB);
            }
        });

        $(document).on('click', '#wrapper-employer .redirect-page', async function () {
            paramSearch.id = $(this).data('id');
            const page = await employer.getCurrentPageJob(paramSearch);
            let url = '/employer';
            let character = '?';
            if (paramSearch.orderBy) {
                url += `?orderBy=${paramSearch.orderBy}`;
                character = '&';
            }
            if (page.page !== 1) {
                url = url + `${character}page=${page.page}`;
            }
            window.location.href = url;
        });

        $(document).on('click', '#wrapper-employer .sort-apply-job', async function () {
            const originType = $(this).attr('data-type');
            const type = originType === 'ASC' ? 'DESC' : 'ASC';
            $('.sort-apply-job').attr('data-type', type);
            $(this).find('img.arrow-down').toggleClass('d-none');
            $(this).find('img.arrow-up').toggleClass('d-none');
            paramFreelancerApply.id = $('input.ipt-job-id').val();
            paramFreelancerApply.orderBy = employer.getTypeSort(type);
            const listFreelancer = await employer.listFreelancerOfJob(paramFreelancerApply);
            $('table.job-application-freelancer tbody').html(listFreelancer.html);
        });

        $(document).on('click', '#wrapper-employer .detail-freelancer-apply', async function () {
            const params = {
                id: $(this).data('id'),
                jobId: $('input.ipt-job-id').val()
            }
            const detailFreelancer = await employer.detailFreelancerApply(params);
            $('#wrapper-employer .tab-job-application').addClass('d-none');
            $('#wrapper-employer .job-footer').addClass('d-none');
            $('#wrapper-employer .detail-freelancer-apply-template').html(detailFreelancer.html);
        });

        $(document).on('click', '#wrapper-employer .switch-tab.tab-active', function () {
            const dataTab = $(this).data('tab');
            if (dataTab === 5) {
                $('.detail-freelancer-apply-template').html('');
                $('#wrapper-employer .job-footer').removeClass('d-none');
            }
        });

        $(document).on('click', '.show-modal-approve', function () {
            $('#modal-approve-job-application').modal('show');
        });

        $(document).on('click', '.show-modal-reject', function () {
            $('#modal-reject-job-application').modal('show');
        });

        $(document).on('click',
            '#modal-approve-job-application .btn-agree-approve,' +
            '#modal-reject-job-application .btn-agree-reject,' +
            '#modal-mark-done-job-application .btn-agree-mark-done', function () {
                const params = {
                    userId: $('input.job-application-user').val(),
                    jobId: $('input.ipt-job-id').val()
                };
                let isMarkDone = false;
                if ($(this).parents('#modal-approve-job-application').length > 0) {
                    params.status = APPROVE_JOB_APPLICATION;
                    isMarkDone = APPROVE_JOB_APPLICATION;
                } else if ($(this).parents('#modal-mark-done-job-application').length > 0) {
                    params.status = DONE_JOB_APPLICATION;
                    isMarkDone = DONE_JOB_APPLICATION;
                } else {
                    params.status = REJECT_JOB_APPLICATION;
                }
                employer.updateStatusJobApplication(params, isMarkDone);
            });

        $(document).on('click', '.icon-heart-freelancer, .btn-save-freelancer', function () {
            dataSaveFreelancer.freelancer_id = $(this).attr('data-freelancer-id');
            employer.saveFreelancer(dataSaveFreelancer, $(this), window.location.href);
        });

        $(document).on('click', '.mark-done-job-approve', function () {
            $('#modal-mark-done-job-application').modal('show');
        });
    },

    processOrderByTypes: (currentValue) => {
        return currentValue !== TYPE_SORT_ASC ? TYPE_SORT_ASC : TYPE_SORT_DESC;
    },

    setCurrentURL: (paramSearch) => {
        const params = {
            page: 1,
            keyword: '',
            orderByField: '',
            orderByType: '',
            countryId: '',
            categoryIds: '',
            experienceId: '',
            ...paramSearch
        };

        window.history.replaceState(
            null,
            null,
            `?page=${params.page}&keyword=${params.keyword}&orderByField=${params.orderByField}&orderByType=${params.orderByType}&countryId=${params.countryId}&categoryIds=${params.categoryIds}&experienceId=${params.experienceId}`
        );
    },

    saveFreelancer: (dataSaveFreelancer, element, href) => {
        $.ajax({
            url: '/employer/saved/freelancer',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: 'POST',
            dataType: 'json',
            data: dataSaveFreelancer,
            beforeSend: () => {
                loading(true);
            }
        }).done(() => {
            if (href.includes(raauUrl.employer_saved_freelancer) && !element.hasClass('no-reload')) {
                location.reload();
            } else if (element.prop('tagName').toLowerCase() === 'img') {
                element.attr('src', element.attr('src').includes('/img/icon-heart.svg') ? '/img/icon-red-heart.svg' : '/img/icon-heart.svg');
            } else {
                const imgPreview = $('img.icon-heart-preview');
                if (imgPreview.length > 0) {
                    imgPreview.attr('src', imgPreview.attr('src').includes('/img/icon-heart.svg') ? '/img/icon-red-heart.svg' : '/img/icon-heart.svg');
                }

                element.text(element.text().trim() === 'SAVE FREELANCER' ? 'UN SAVE FREELANCER' : 'SAVE FREELANCER');
            }
        }).fail(() => {
            toastr.error('Save freelancer fail!')
        }).always(() => {
            loading(false);
        });
    },

    updateStatusJobApplication: (params, isMarkDone = false) => {
        loading(true);
        let fundingPage = null;
        if (params.status == APPROVE_JOB_APPLICATION) {
            fundingPage = createBlankPage();
        }

        return $.ajax({
            url: '/employer/update-status-job-application',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'POST',
            dataType: 'json',
            data: params
        }).done((response) => {
            if (response.status === 301) {
                localStorage.setItem('warning_message', response.message);
                window.location.href = response.url;
            } else if (response.status === 200) {
                if (params.status == APPROVE_JOB_APPLICATION && response.redirect_url) {
                    $('.freelancer-apply-area').find('.show-modal-approve, .show-modal-reject').attr('disabled', true).removeClass('show-modal-approve show-modal-reject hover-button');
                    fundingPage.location.href = response.redirect_url;
                } else {
                    toastr.success(response.update);
                    if (isMarkDone === APPROVE_JOB_APPLICATION) {
                        $('.btn-mark-done').addClass('hover-button mark-done-job-approve').prop('disabled', false);
                    }
                    if (isMarkDone === DONE_JOB_APPLICATION) {
                        $('.btn-mark-done').removeClass('hover-button mark-done-job-approve').prop('disabled', true);
                        return;
                    }
                    employer.backToListFreelancerApply();
                }
            } else {
                toastr.error(response.update);
                if (fundingPage) {
                    fundingPage.close();
                }
            }
        }).fail(() => {
            toastr.error(MSG_ERROR_JOB);
            fundingPage.close();
        }).always(() => {
            loading(false);
            $('#modal-approve-job-application, #modal-reject-job-application, #modal-mark-done-job-application').modal('hide');
        });
    },

    backToListFreelancerApply: async () => {
        $('.detail-freelancer-apply-template').html('');
        paramFreelancerApply.id = $('input.ipt-job-id').val();
        const listFreelancer = await employer.listFreelancerOfJob(paramFreelancerApply);
        $('table.job-application-freelancer tbody').html(listFreelancer.html);
        $('#wrapper-employer .tab-job-application').removeClass('d-none');
        $('#wrapper-employer .job-footer').removeClass('d-none');
    },

    detailFreelancerApply: (params) => {
        loading(true);
        return $.ajax({
            url: '/employer/detail-freelancer-apply',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: params,
            beforeSend: () => {
                loading(true);
            }
        }).done((response) => {
            return response;
        }).fail(() => {
            toastr.error(MSG_ERROR_FREELANCER);
        }).always(() => {
            loading(false);
        });
    },

    listFreelancerOfJob: (params) => {
        loading(true);
        return $.ajax({
            url: '/employer/list-freelancer-job',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: params,
            beforeSend: () => {
                loading(true);
            }
        }).done((response) => {
            return response;
        }).fail(() => {
            toastr.error(MSG_ERROR_JOB);
        }).always(() => {
            loading(false);
        });
    },

    getCurrentPageJob: (params) => {
        loading(true);
        return $.ajax({
            url: '/employer/current-page-job',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: params,
            beforeSend: () => {
                loading(true);
            }
        }).done((response) => {
            return response;
        }).fail(() => {
            toastr.error(MSG_ERROR_JOB);
        }).always(() => {
            loading(false);
        });
    },

    previewJobOfEmployer: (params) => {
        loading(true);
        return $.ajax({
            url: '/employer/job-preview',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: params,
            beforeSend: () => {
                loading(true);
            }
        }).done((response) => {
            return response;
        }).fail(() => {
            toastr.error(MSG_ERROR_JOB);
        }).always(() => {
            loading(false);
        });
    },

    saveCompany: (dataCompany) => {
        $.ajax({
            url: '/company/store',
            method: 'POST',
            dataType: 'json',
            data: dataCompany,
        }).done((response) => {
            $("#form-select-company").append('<option value="' + response.id + '" selected>' + response.name + '</option>');
            $('#add-company-modal').modal('hide');
            $('.ipt-add-company').val('');
            $('#validation-errors').text('');
        }).fail((response) => {
            $("#validation-errors").text(response.responseJSON.errors.name);
        })
    },

    searchFreelancer: async (paramFreelancer) => {
        $.ajax({
            url: '/employer/find-freelancer',
            method: 'GET',
            dataType: 'json',
            data: paramFreelancer,
        }).done((response) => {
            if (response.html) {
                $('.employer-table-freelancers').html(response.html);
                $('.total-freelancer').text(response.total + ' Freelancer');
                employer.setCurrentURL(paramFreelancer);
            }
        }).fail(() => {
            toastr.options =
                {
                    "closeButton": true,
                    "progressBar": true
                }
            toastr.error(MSG_ERROR_FREELANCER);
        })

    },

    searchJob: (paramSearch) => {
        $.ajax({
            url: raauUrl.user_dashboard,
            method: 'GET',
            dataType: 'json',
            data: paramSearch,
        }).done((response) => {
            if (response.html) {
                $('.table-jobs').html(response.html)
            }
            removeHtmlRemainDevice();
        }).fail(() => {
            toastr.error('error');
        })
    },

    previewFreelancer: (queries) => {
        return $.ajax({
            url: '/employer/preview-freelancer',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: {...queries},
            beforeSend: () => {
                loading(true);
            }
        }).done((response) => {
            return response;
        }).fail(() => {
            toastr.error(MSG_ERROR_FREELANCER);
        }).always(() => {
            loading(false);
        });
    },

    getCurrentPage: (params) => {
        return $.ajax({
            url: '/employer/current-page-freelancer',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            method: 'GET',
            dataType: 'json',
            data: params,
        }).done((response) => {
            return response;
        });
    },

    getTypeSort: (type) => {
        return type === 'ASC' ? 'DESC' : 'ASC';
    },
}

$(function () {
    employer.init();
});
