@forelse ($saves as $item)
    @if ($loop->first)
        @php
            $i = 1;
            if ($saves->currentPage() > 1) {
                $i = ($saves->currentPage() - 1) * config('paging.quantity') + 1;
            }
        @endphp
    @endif
    <div class="t-row">
        <div class="col column-sm">{{ $i++ }}</div>
        <div class="col column-md">{{ $item->user->name ?? '' }}</div>
        <div class="col column-md">{{ !is_null($item->historiesUsed) ? $item->historiesUsed->count() : 0 }}</div>
        <div class="col column-md">{{ $item->remaining_times ?? '' }}</div>
        <div class="col column-sm">
            <div class="d-flex justify-content-center align-items-center">
                <button type="button" data-toggle="collapse"
                    data-target="#collapseOne{{ $item->id }}" aria-expanded="false"
                    aria-controls="collapseOne{{ $item->id }}"
                    class="btn btn-general d-flex justify-content-center align-items-center hover-button-list">
                    <img src="{{ asset('/img/history_icon.svg') }}" alt="" class="cursor-pointer">
                </button>
            </div>
        </div>
    </div>
    <div id="collapseOne{{ $item->id }}" class="collapse px-3" aria-labelledby="headingOne"
        data-parent="#accordionExample">
        <div class="histories-table">
            <div class="thead">
                <div class="t-row">
                    <div class="col column-sm">@lang('No')</div>
                    <div class="col column-md">@lang('Escrow transaction id')</div>
                    <div class="col column-md">@lang('Date')</div>
                </div>
            </div>
            <div class="tbody">
                @forelse ($item->historiesUsed as $j => $history)
                    <div class="t-row">
                        <div class="col column-sm">{{ $j + 1 }}</div>
                        <div class="col column-md">{{ $history->transaction->escrow_transaction_id ?? '' }}</div>
                        <div class="col column-md">{{ $history->created_at ? $history->created_at->format('H:i d-m-Y') : '' }}</div>
                    </div>
                @empty
                    <div class="t-row">
                        <div class="col w-100 text-danger text-center">@lang('No data.')</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@empty
<div class="t-row">
    <div class="col w-100 text-danger text-center">@lang('No data.')</div>
</div>
@endforelse
