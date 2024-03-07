import { TYPE_SORT_ASC, TYPE_SORT_DESC, SAVED_JOB } from '../global';
import { getFilterParams, replaceState } from "./common";

const saved = (function ($) {
    let parentSelector = '.freelancer-saved';
    let routeIndex = '/employer/saved/freelancers';
    let routePreview = '/employer/saved/freelancers/';
    let isInitial = false;

    function init(typeSaved) {
        if (isInitial === false) {
            if (typeSaved === SAVED_JOB) {
                parentSelector = '.job-saved';
                routeIndex = '/employer/saved/jobs';
                routePreview = '/employer/saved/jobs/';
            }
            if (typeSaved) {
                listener();
            }
            isInitial = true;
        }
    }

    function listener() {
        $(document).on(
            'click',
            `${parentSelector} .btn-sort-status`,
            handleSort
        );

        $(document).on(
            'click',
            `${parentSelector} a.page-link`,
            handleNextPage
        );

        $(document).on(
            'click',
            `${parentSelector} .btn-preview, ${parentSelector} .previous-preview-job, ${parentSelector} .next-preview-job`,
            preview
        );

        $(document).on(
            'click',
            `${parentSelector} .btn-close-preview`,
            reloadPage
        );

        $(document).on(
            'keyup paste',
            `${parentSelector} input[name="hot_search"]`,
            hotSearch
        );

        $(document).on(
            'submit',
            `${parentSelector} form#search-form`,
            filter
        );
    }

    async function handleSort() {
        const searchParams = new URLSearchParams(location.search);
        const params = {
            page: 1,
            orderBy:
                searchParams.get('orderBy') != TYPE_SORT_ASC
                    ? TYPE_SORT_ASC
                    : TYPE_SORT_DESC,
            ...getFilterParams(searchParams),
        };

        await get(params);
    }

    async function handleNextPage(e) {
        e.preventDefault();
        try {
            const url = new URL($(this).attr('href'));
            const params = {
                page: url.searchParams.get('page') || 1,
                orderBy:
                    url.searchParams.get('orderBy') != TYPE_SORT_ASC
                        ? TYPE_SORT_DESC
                        : TYPE_SORT_ASC,
                ...getFilterParams(url.searchParams),
            };

            await get(params);
        } catch (error) {
            toastr.error('An error has occurred.');
        }
    }

    async function reloadPage(e) {
        e.preventDefault();
        const searchParams = new URLSearchParams(location.search);
        const params = {
            page: searchParams.get('page') || 1,
            orderBy:
                searchParams.get('orderBy') != TYPE_SORT_ASC
                    ? TYPE_SORT_DESC
                    : TYPE_SORT_ASC,
            ...getFilterParams(searchParams),
        };

        await get(params);
    }

    async function hotSearch() {
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

            await get(params, () => {
                element.data('old-value', params.hot_search);
                clearFilterModal();
            });
        }
    }

    async function filter(e) {
        e.preventDefault();
        $(`${parentSelector} .filter-modal`).modal('hide');
        try {
            const rawParams = {
                keyword: $(`${parentSelector} input[name="keyword"]`).val() || '',
                experience_id: $(`${parentSelector} select[name="experience_id"]`).val() || '',
                country_id: $(`${parentSelector} select[name="country_id"]`).val() || '',
                sector_id: $(`${parentSelector} select[name="sector_id"]`).val() || '',
            };

            $.each($(`${parentSelector} select[name="category_id[]"]`).val() || [], function (i, value) {
                rawParams[`category_id[${i}]`] = value;
            });

            const searchParams = getFilterParams(new URLSearchParams(rawParams));

            await get(searchParams, () => {
                $(`${parentSelector} input[name="hot_search"]`).val('').data('old-value', '');
                clearFilterModal();
            });
        } catch (error) {
            toastr.error('An error has occurred.');
        }
    }

    function get(params, callBackSuccess = null) {
        return $.ajax({
            type: 'GET',
            url: routeIndex,
            data: params,
            dataType: 'json',
            success: function (response) {
                if (response.html) {
                    $('#table-wrapper').html(response.html);
                    if ('current_page' in response && response.current_page) {
                        params.page = response.current_page;
                    }
                    replaceState(params);
                    if (typeof callBackSuccess === 'function') {
                        callBackSuccess();
                    }
                }
            },
            error: function () {
                toastr.error('An error has occurred.');
            },
        });
    }

    function preview() {
        const id = $(this).data('id');
        const params = {
            ...getFilterParams(new URLSearchParams(location.search)),
        };
        if (id) {
            $.ajax({
                type: 'GET',
                url: `${routePreview}${id}`,
                data: params,
                dataType: 'json',
                success: function (response) {
                    if (response.html) {
                        $('#table-wrapper').html(response.html);
                    }
                },
                error: function () {
                    toastr.error('An error has occurred.');
                },
            });
        }
    }

    function clearFilterModal() {
        $(`${parentSelector} .filter-modal`).find('input, select').val('').trigger("change");
    }

    return { init };
})(jQuery);

$(function () {
    saved.init(typeof TYPE_SAVED == 'undefined' ? null : TYPE_SAVED);
});
