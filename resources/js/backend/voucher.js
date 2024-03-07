import { formatResultCategory, loading } from '../backend/common';
import { TYPE_SORT_ASC, TYPE_SORT_DESC } from '../global';
import { replaceState } from '../frontend/common';

$(function () {
    Voucher.init();
});

const Voucher = (function ($, columnSortAllow) {
    let isInitial = false;
    const searchNameAllow = ['order_by_field', 'order_by_type', 'page', 'search'];
    const orderByTypeAllow = [TYPE_SORT_ASC, TYPE_SORT_DESC];
    let searchTimeout = null;
    let detailSavedVar = {
        voucherId: null,
        currentPage: 1,
        isLoading: false,
        hasNextPage: true,
    };
    const DISABLED_STATUS = 'disabled';
    const AVAILABILITY_STATUS = 'availability';

    function init() {
        if (isInitial === false) {
            setup();
            listener();

            isInitial = true;
        }
    }

    function listener() {
        $('input[name="scope"]').on('change', handleChangeScope);

        $(document).on('click', '.btn-sort-voucher', sort);

        $(document).on('click', '.btn-copy-code', copyCode);

        $(document).on('mouseout', '.btn-copy-code', function () {
            $(this).tooltip('hide');
        });

        $(document).on('keyup change paste', '#search-voucher-form input[name="search"]', search);

        $(document).on('click', '.btn-detail-used', detailUsed);

        $('#detail-used-modal .modal-body').on('scroll', loadMoreDetail);

        $(document).on('click', '.btn-availability-voucher:not(.is-processing)', function () {
            updateStatus(this, AVAILABILITY_STATUS, $(this).data('id'));
        });

        $(document).on('click', '.btn-disable-voucher:not(.is-processing)', function () {
            let dontShowAgain = sessionStorage.getItem('dont-show-again-confirm-disable-voucher');
            if (dontShowAgain) {
                updateStatus(this, DISABLED_STATUS, $(this).data('id'));
            } else {
                $('#disable-voucher-id').val($(this).data('id'));
                $('#confirm-disable-voucher-modal').modal('show');
            }
        });

        $(document).on('click', '#btn-confirmed-disable-voucher', function () {
            let isChecked = $('#confirm-disable-voucher-modal #dont-show-again').first().is(':checked');
            if (isChecked) {
                sessionStorage.setItem('dont-show-again-confirm-disable-voucher', isChecked);
            } else {
                sessionStorage.removeItem('dont-show-again-confirm-disable-voucher');
            }
            updateStatus($(`.btn-disable-voucher[data-id="${$('#disable-voucher-id').val()}"]`).get(0), DISABLED_STATUS, $('#disable-voucher-id').val());
        });
    }

    function setup() {
        setupDatepicker();
        setupSelect2();
    }

    function setupDatepicker() {
        if ($('.datetimepicker')) {
            window.flatpickr($('.datetimepicker'), {
                dateFormat: "d-m-Y",
                disableMobile: "true",
                minDate: new Date().fp_incr(1)
            });
        }
    }

    function setupSelect2() {
        if ($("#form-select-users")) {
            $("#form-select-users").select2({
                templateSelection: formatResultCategory,
                dropdownParent: $('.form-select-users-container'),
                selectionCssClass: 'form-select-timezone',
                dropdownAutoWidth: true,
                containerCss: 'form-select-users-container',
                placeholder: "Select users",
                ajax: {
                    type: 'GET',
                    url: '/admin/users/for-select2',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            name: params.term,
                            page: params.page || 1,
                        }

                        return query;
                    },
                    processResults: function (response) {
                        return {
                            results: response.payload.data,
                            pagination: {
                                more: !!response.payload.next_page_url
                            }
                        };
                    }
                }
            });
        }
    }

    function handleChangeScope() {
        if ($(this).is(':checked')) {
            $('.form-select-users-container').show();
            setupSelect2();
        } else {
            $('.form-select-users-container').hide();
        }
    }

    function sort() {
        const sortField = $(this).data('field');
        const sortType = $(this).data('type');
        if (validateSortField(sortField, sortType)) {
            const params = getParamsOnUrl({
                order_by_field: sortField,
                order_by_type: sortType,
            });

            return get(params);
        }
    }

    function search() {
        const params = getParamsOnUrl({ search: $(this).val() });
        return get(params);
    }

    function getParamsOnUrl(appends = {}) {
        const urlSearchParams = new URLSearchParams(window.location.search);
        let params = {};

        urlSearchParams.forEach(function (value, key) {
            if (validateParamName(key) && value != '') {
                params[key] = value;
            }
        });
        params = {
            ...params,
            ...appends,
        };

        return params;
    }

    function validateParamName(name) {
        return searchNameAllow.includes(name);
    }

    function validateSortField(name, value) {
        return name && value && (columnSortAllow.length <= 0 || columnSortAllow.includes(name)) && orderByTypeAllow.includes(value);
    }

    function get(params) {
        if (searchTimeout !== null) {
            clearTimeout(searchTimeout);
        }
        searchTimeout = setTimeout(() => {
            $.ajax({
                type: 'GET',
                url: '/admin/vouchers',
                data: params,
                dataType: 'json',
                success: function (response) {
                    if (response.html) {
                        $('#show-voucher-table').html(response.html);
                    }
                    $('#total-voucher').text(`${response.count || 0} Vouchers`);
                    replaceState(params);
                    if (params.order_by_field && params.order_by_type) {
                        $('#search-voucher-form').remove('input[name="order_by_field"], input[name="order_by_type"]').append(`
                            <input type="text" name="order_by_field" value="${params.order_by_field}" hidden>
                            <input type="text" name="order_by_type" value="${params.order_by_type}" hidden>
                        `);
                    }
                },
                error: function () {
                    toastr.error('An error has occurred.');
                }
            });
        }, 500);
    }

    function copyCode() {
        const element = this;
        const text = $(element).data('content');
        const updateTitle = function (title) {
            $(element).attr('data-original-title', title).tooltip('update');

            if ($(element).is(':hover')) {
                $(element).tooltip('show');
            }
        }

        if (text) {
            navigator.clipboard.writeText(text);
            updateTitle('Copied!');
            setTimeout(() => {
                updateTitle('Click to copy!');
            }, 2000);
        }
    }

    function detailUsed() {
        const voucherId = $(this).data('id');
        if (voucherId) {
            if (detailSavedVar.voucherId != voucherId) {
               return getDetailSaved(voucherId, 1);
            } else {
                $('#detail-used-modal').modal('show');
            }
        }
    }

    function loadMoreDetail() {
        const isBottom = $(this).scrollTop() + $(this).innerHeight() + 30 >= $(this)[0].scrollHeight;
        if (isBottom && detailSavedVar.voucherId && detailSavedVar.hasNextPage) {
            return getDetailSaved(detailSavedVar.voucherId, detailSavedVar.currentPage + 1, false);
        }
    }

    function getDetailSaved(voucherId, page, isReload = true) {
        if (detailSavedVar.isLoading === false) {
            $.ajax({
                type: 'GET',
                url: `/admin/vouchers/${voucherId}/saved`,
                data: { page },
                dataType: 'json',
                beforeSend: function () {
                    detailSavedVar.isLoading = true;
                    loading(true);
                },
                success: function (response) {
                    showDataSaved(response.html, isReload);
                    detailSavedVar = {
                        currentPage: page,
                        voucherId: voucherId,
                        isLoading: false,
                        hasNextPage: Boolean(response.has_next_page)
                    };
                    $('#detail-used-modal').modal('show');
                },
                error: function () {
                    toastr.error('An error has occurred.');
                },
                complete: function () {
                    loading(false);
                }
            });
        }
    }

    function showDataSaved(html, idClear = true) {
        if (idClear) {
            $('#detail-used-table > .tbody').html(html);
        } else {
            $('#detail-used-table > .tbody').append(html);
        }
    }

    function updateStatus(element, type, id) {
        if (id) {
            let imgIcon = `<img src="/img/icon-eye-off.svg" alt="" class="cursor-pointer">`;
            let newClass = 'btn-availability-voucher';
            let title = 'Click to availability.';
            if (type == AVAILABILITY_STATUS) {
                imgIcon = `<img src="/img/icon-eye.svg" alt="" class="cursor-pointer">`;
                newClass = 'btn-disable-voucher';
                title = 'Click to disable.';
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: `/admin/vouchers/${id}/update-status`,
                data: {
                    status: type
                },
                dataType: "json",
                beforeSend: function () {
                    $(element).addClass('is-processing');
                    $(element).tooltip('hide');
                },
                success: function () {
                    $(element).removeClass('btn-availability-voucher btn-disable-voucher').addClass(newClass);
                    $(element).html(imgIcon);
                    toastr.success(`${type} success.`.toCapitalize());
                    $(element).attr('data-original-title', title).attr('title', title).tooltip('update');
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

    return { init };
})(jQuery, typeof columnSortAllow !== 'undefined' ? columnSortAllow : []);
