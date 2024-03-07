import {removeHtmlRemainDevice} from "./common";
import {TYPE_SORT_ASC, TYPE_SORT_DESC, ACTIVE, IN_ACTIVE} from '../global';

const ORDER_BY_NAME_FIELD = 'name';
const ORDER_BY_IS_ONLINE_FIELD = 'is_online';
const ORDER_BY_LAST_LOGIN_AT_FIELD = 'last_login_at';
const ORDER_BY_SECTOR_NAME_AT_FIELD = 'sector_name';

const employer = {
    init: () => {
        $(document).on('click', '.btn-confirm-delete-employer', function () {
            let dontShowAgain = sessionStorage.getItem('dont-show-again-employer');
            if (dontShowAgain) {
                $('#delete-employer-id').val($(this).data('id'));
                employer.deleteEmployer();
            } else {
                employer.showModalConfirm(this);
            }
        });

        $(document).on('click', '.btn-delete-employer', function () {
            let isChecked = $('#show-again')[0].checked;
            if (isChecked) {
                sessionStorage.setItem('dont-show-again-employer', isChecked);
            } else {
                sessionStorage.removeItem('dont-show-again-employer');
            }
            employer.deleteEmployer();
        });
    },

    saveCompany: (dataCompany) => {
        $.ajax({
            url: '/admin/company/store',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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

    search: async (paramSearch) => {
        $.ajax({
            url: '/admin/employer',
            method: 'GET',
            dataType: 'json',
            data: paramSearch,
        }).done((response) => {
            if (response.html) {
                $('.table-employers').html(response.html);
                $('.total-employer').text(response.total + ' Employer');
                $('#form-export-employers').append(`
                <input type="hidden" form="form-export-employers" name="keyword" value="${paramSearch.keyword}" />
                <input type="hidden" form="form-export-employers" name="orderByType" value="${paramSearch.orderByType}" />
                <input type="hidden" form="form-export-employers" name="orderByField" value="${paramSearch.orderByField}" />
                <input type="hidden" form="form-export-employers" name="countryId" value="${paramSearch.countryId}" />
                <input type="hidden" form="form-export-employers" name="companyId" value="${paramSearch.companyId}" />
                <input type="hidden" form="form-export-employers" name="is_active" value="${paramSearch.is_active}" />`);
                setCurrentURL(paramSearch);
            }
            removeHtmlRemainDevice();
        }).fail(() => {
            toastr.error('Search employer fail!');
        })

    },

    showModalConfirm: function (obj) {
        let id = $(obj).data('id');
        $('#delete-employer-id').val(id);
        $('#modal-confirm-delete-employer').modal('show');
    },

    deleteEmployer: function () {
        let id = $('#delete-employer-id').val();
        $('#modal-confirm-delete-employer').modal('hide');
        if (id) {
            $.ajax({
                type: 'GET',
                url: '/admin/employer/delete/' + id,
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
}

function clearDataModal() {
    $(':input').val('');
    $("#form-select-company").val(null).trigger("change");
    $("#form-select-country").val(null).trigger("change");
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
        companyId: '',
        is_active: '',
        ...paramSearch
    };

    window.history.replaceState(
        null,
        null,
        `?page=${params.page}&keyword=${params.keyword}&orderByField=${params.orderByField}&orderByType=${params.orderByType}&countryId=${params.countryId}&companyId=${params.companyId}&is_active=${params.is_active}`
    );
}

$(document).ready(() => {

    employer.init();

    let paramSearch = {
        page: '',
        keyword: '',
        orderByField: '',
        orderByType: '',
        countryId: '',
        companyId: '',
        is_active: '',
    };

    let dataCompany = {
        name: ''
    };

    $(document).on('click', '.btn-save-company', function () {
        dataCompany.name = $(document).find('.ipt-add-company').val();
        employer.saveCompany(dataCompany);
    });

    $(document).on('click', '.btn-close-modal', function () {
        $('#validation-errors').text('');
        $('.ipt-add-company').val('');
    });


    $(document).on('keyup', '.admin-employers .ipt-search', async function () {
        paramSearch.keyword = $(this).val();
        paramSearch.page = '';
        await employer.search(paramSearch);
        $('.ipt-search-filter-employer').val('');
    });

    $(document).on('click', '.admin-employers .btn-sort-name', function () {
        paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
        paramSearch.orderByField = ORDER_BY_NAME_FIELD;
        employer.search(paramSearch);
    });

    $(document).on('click', '.admin-employers .btn-sort-is-online', function () {
        paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
        paramSearch.orderByField = ORDER_BY_IS_ONLINE_FIELD;
        employer.search(paramSearch);
    });

    $(document).on('click', '.admin-employers .btn-sort-last-login-at', function () {
        paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
        paramSearch.orderByField = ORDER_BY_LAST_LOGIN_AT_FIELD;
        employer.search(paramSearch);
    });

    $(document).on('click', '.admin-employers .btn-sort-sector', function () {
        paramSearch.orderByType = processOrderByTypes($(this).attr('data-type'));
        paramSearch.orderByField = ORDER_BY_SECTOR_NAME_AT_FIELD;
        employer.search(paramSearch);
    });


    $(document).on('click', '.admin-employers .filter-all', function () {
        paramSearch.is_active = '';
        employer.search(paramSearch);
    });

    $(document).on('click', '.admin-employers .btn-sort-active', function () {
        paramSearch.is_active = ACTIVE;
        employer.search(paramSearch);
    });

    $(document).on('click', '.admin-employers .btn-sort-in-active', function () {
        paramSearch.is_active = IN_ACTIVE;
        employer.search(paramSearch);
    });

    $(document).on('click', '.admin-employers .btn-search-filter', async function () {
        paramSearch.keyword = $(document).find('.ipt-search-filter-employer').val();
        paramSearch.countryId = $('select[name=country_id] option').filter(':selected').val()
        paramSearch.companyId = $('select[name=company_id] option').filter(':selected').val()
        paramSearch.page = '';
        await employer.search(paramSearch);
        $('#filter-modal').modal('hide');
        $('.ipt-search').val('');
        clearDataModal()
    });

    $(document).on('click', '.admin-employers [data-dismiss="modal"]', function () {
        clearDataModal();
    })

    $(document).on('click', '.admin-employers .table-employers .pagination a', function (e) {
        e.preventDefault();
        paramSearch.page = $(this).attr('href').split('page=')[1];
        employer.search(paramSearch);
    });
});
