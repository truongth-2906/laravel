<div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-voucher"
    data-field="{{ $field }}"
    data-type="{{ request()->query('order_by_field') == $field && request()->query('order_by_type') != TYPE_SORT_ASC ? TYPE_SORT_ASC : TYPE_SORT_DESC }}">
    <div class="color-475467 font-12 mr-2">{{ $columnName }}</div>
    @if (request()->query('order_by_field') == $field)
        @if (request()->query('order_by_type') == TYPE_SORT_ASC)
            <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}" alt="arrow-up-active">
        @else
            <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}" alt="arrow-down-active">
        @endif
    @else
        <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="arrow-down">
    @endif
</div>
