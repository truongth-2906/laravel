import {removeHtmlRemainDevice} from "./common";
import {TYPE_SORT_ASC, TYPE_SORT_DESC, ACTIVE, IN_ACTIVE} from '../global';

const ORDER_BY_NAME_FIELD = 'name';
const ORDER_BY_IS_ONLINE_FIELD = 'is_online';
const ORDER_BY_LAST_LOGIN_AT_FIELD = 'last_login_at';
const HIDDEN = 'hidden';
const UNHIDDEN = 'unhidden';

const freelancer = {
    numberConvertByte: 1024,
    typeImage: ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'],

    init: () => {
        $(document).on('click', '.filter-status button', function () {
            $('.filter-status button').removeClass('filter-tag-active color-1D2939').addClass('color-344054');
            $(this).removeClass('color-344054').addClass('filter-tag-active color-1D2939');
        });

        $(document).on('click', '.btn-confirm-delete-freelancer', function () {
            let dontShowAgain = sessionStorage.getItem('dont-show-again-freelancer');
            if (dontShowAgain) {
                $('#delete-freelancer-id').val($(this).data('id'));
                freelancer.deleteFreelancer();
            } else {
                freelancer.showModalConfirm(this);
            }
        });

        $(document).on('click', '.btn-delete-freelancer', function () {
            let isChecked = $('#show-again')[0].checked;
            if (isChecked) {
                sessionStorage.setItem('dont-show-again-freelancer', isChecked);
            } else {
                sessionStorage.removeItem('dont-show-again-freelancer');
            }
            freelancer.deleteFreelancer();
        });
    },

    search: async (paramSearch) => {
        $.ajax({
            url: '/admin/freelancer',
            method: 'GET',
            dataType: 'json',
            data: paramSearch,
        }).done((response) => {
            if (response.html) {
                $('.table-freelancers').html(response.html);
                $('.total-freelancer').text(response.total + ' Freelancer');
                $('#form-export-freelancers').append(`
                <input type="hidden" form="form-export-freelancers" name="keyword" value="${paramSearch.keyword}" />
                <input type="hidden" form="form-export-freelancers" name="orderByType" value="${paramSearch.orderByType}" />
                <input type="hidden" form="form-export-freelancers" name="orderByField" value="${paramSearch.orderByField}" />
                <input type="hidden" form="form-export-freelancers" name="countryId" value="${paramSearch.countryId}" />
                <input type="hidden" form="form-export-freelancers" name="experienceId" value="${paramSearch.experienceId}" />
                <input type="hidden" form="form-export-freelancers" name="is_active" value="${paramSearch.is_active}" />`);
                if (paramSearch.categoryIds) {
                    paramSearch.categoryIds.map((val) => {
                        $('.form-export-freelancers').append(`<input type="hidden" name="categoryIds[]" value="${val}" />`)
                    })
                }
                setCurrentURL(paramSearch);
            }
            removeHtmlRemainDevice();
        }).fail(() => {
            toastr.error('Search freelancer fail!');
        })
    },

    showModalConfirm: function (obj) {
        $('#show-again').prop("checked", false);
        let id = $(obj).data('id');
        $('#delete-freelancer-id').val(id);
        $('#modal-confirm-delete-freelancer').modal('show');
    },

    deleteFreelancer: function () {
        let id = $('#delete-freelancer-id').val();
        $('#modal-confirm-delete-freelancer').modal('hide');
        if (id) {
            $.ajax({
                type: 'GET',
                url: '/admin/freelancer/delete/' + id,
                dataType: 'json',
                success: function (response) {
                    window.location = response.url;
                },
                error: function () {
                    toastr.error('Delete failed');
                }
            });
        }
    },

    clearDataModal: function () {
        $(':input').val('');
        $("#form-select-categories").val(null).trigger("change");
        $("#form-select-rpa-experience").val(null).trigger("change");
        $("#form-select-country").val(null).trigger("change");
    },

    updateStatusHidden: function (element, type, id) {
        if (id) {
            const imgIcon = type == HIDDEN ? `<img src="/img/icon-eye-off.svg" alt="" class="cursor-pointer">` : `<img src="/img/icon-eye.svg" alt="" class="cursor-pointer">`;
            const newClass = type == HIDDEN ? 'btn-unhidden-freelancer' : 'btn-hidden-freelancer';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: `/admin/freelancer/${id}/${type}`,
                dataType: "json",
                beforeSend: function () {
                    $(element).addClass('is-processing');
                },
                success: function () {
                    $(element).removeClass('btn-unhidden-freelancer btn-hidden-freelancer').addClass(newClass);
                    $(element).html(imgIcon);
                    toastr.success(`${type} success.`.toCapitalize());
                },
                error: function () {
                    toastr.error(`${type} failed.`.toCapitalize());
                },
                complete: function () {
                    $(element).removeClass('is-processing');
                }
            });
        }
    }
}

function processOrderByTypes(currentValue) {
    return currentValue !== TYPE_SORT_ASC ? TYPE_SORT_ASC : TYPE_SORT_DESC;
}

function setCurrentURL(paramSearch) {
    const params = {
        page: 1,
        keyword: '',
        orderByField: '',
        orderByType: '',
        countryId: '',
        categoryIds: '',
        experienceId: '',
        is_active: '',
        ...paramSearch
    };

    window.history.replaceState(
        null,
        null,
        `?page=${params.page}&keyword=${params.keyword}&orderByField=${params.orderByField}&orderByType=${params.orderByType}&countryId=${params.countryId}&categoryIds=${params.categoryIds}&experienceId=${params.experienceId}&is_active=${params.is_active}`
    );
}

$(function () {
    freelancer.init();

    let paramSearch = {
        page: '',
        keyword: '',
        orderByField: '',
        orderByType: '',
        countryId: '',
        categoryIds: '',
        experienceId: '',
        is_active: '',
    };

    let searchCooldown;

    $(document).on('input', '.admin-freelancers input[name="hot_search"]', async function () {
        paramSearch.page = '';
        paramSearch.keyword = $(this).val().trim();
        await freelancer.search(paramSearch);
        clearTimeout(searchCooldown);
        searchCooldown = setTimeout(() => {
            freelancer.search(paramSearch);
        }, 800);
        $('.ipt-search-filter').val('');
    });

    $(document).on('click', '.admin-freelancers .btn-sort-name', function () {
        paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
        paramSearch.orderByField = ORDER_BY_NAME_FIELD;
        freelancer.search(paramSearch);
    });

    $(document).on('click', '.admin-freelancers .btn-sort-is-online', function () {
        paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
        paramSearch.orderByField = ORDER_BY_IS_ONLINE_FIELD;
        freelancer.search(paramSearch);
    });

    $(document).on('click', '.admin-freelancers .btn-sort-last-login-at', function () {
        paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
        paramSearch.orderByField = ORDER_BY_LAST_LOGIN_AT_FIELD;
        freelancer.search(paramSearch);
    });

    $(document).on('click', '.admin-freelancers .filter-all', function () {
        paramSearch.is_active = '';
        freelancer.search(paramSearch);
    });

    $(document).on('click', '.admin-freelancers .btn-sort-active', function () {
        paramSearch.is_active = ACTIVE;
        freelancer.search(paramSearch);
    });

    $(document).on('click', '.admin-freelancers .btn-sort-in-active', function () {
        paramSearch.is_active = IN_ACTIVE;
        freelancer.search(paramSearch);
    });

    $(document).on('click', '.admin-freelancers .btn-search-filter', async function () {
        paramSearch.page = '';
        paramSearch.keyword = $(document).find('.ipt-search-filter').val();
        paramSearch.experienceId = $('select[name=experience_id] option').filter(':selected').val();
        paramSearch.countryId = $('select[name=country_id] option').filter(':selected').val()
        paramSearch.categoryIds = $('select[name=category_ids]').val();
        await freelancer.search(paramSearch);
        $('#filter-modal').modal('hide');
        $('.ipt-search').val('');
        freelancer.clearDataModal();
    });

    $(document).on('click', '.admin-freelancers [data-dismiss="modal"]', function () {
        freelancer.clearDataModal();
    })

    $(document).on('click', '.admin-freelancers .table-freelancers .pagination a', function (e) {
        e.preventDefault();
        paramSearch.page = $(this).attr('href').split('page=')[1];
        freelancer.search(paramSearch);
    });

    $(document).on('click', '.btn-unhidden-freelancer:not(.is-processing)', function () {
        freelancer.updateStatusHidden(this, UNHIDDEN, $(this).data('id'));
    });

    $(document).on('click', '.btn-hidden-freelancer:not(.is-processing)', function () {
        let dontShowAgain = sessionStorage.getItem('dont-show-again-confirm-hidden');
        if (dontShowAgain) {
            freelancer.updateStatusHidden(this, HIDDEN, $(this).data('id'));
        } else {
            $('#hidden-freelancer-id').val($(this).data('id'));
            $('#confirm-hidden-modal').modal('show');
        }
    });

    $(document).on('click', '#btn-confirmed-hidden-freelancer', function () {
        let isChecked = $('#confirm-hidden-modal #dont-show-again').first().is(':checked');
        if (isChecked) {
            sessionStorage.setItem('dont-show-again-confirm-hidden', isChecked);
        } else {
            sessionStorage.removeItem('dont-show-again-confirm-hidden');
        }
        freelancer.updateStatusHidden($(`.btn-hidden-freelancer[data-id="${$('#hidden-freelancer-id').val()}"]`).get(0), HIDDEN, $('#hidden-freelancer-id').val());
    });
});
