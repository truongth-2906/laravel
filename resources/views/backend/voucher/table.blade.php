<div class="voucher-table-wrapper">
    <div class="scroll-table">
        <table class="table">
            <thead>
                <tr>
                    <th class="column-sm">@lang('No')</th>
                    <th class="column-xl">
                        @include('backend.voucher.column-sort', [
                            'columnName' => __('Name'),
                            'field' => 'name',
                        ])
                    </th>
                    <th class="column-lg">@lang('Code')</th>
                    <th class="column-md">
                        @include('backend.voucher.column-sort', [
                            'columnName' => __('Discount'),
                            'field' => 'discount',
                        ])
                    </th>
                    <th class="column-md">
                        @include('backend.voucher.column-sort', [
                            'columnName' => __('Type'),
                            'field' => 'type',
                        ])
                    </th>
                    <th class="column-md">
                        @include('backend.voucher.column-sort', [
                            'columnName' => __('Quantity'),
                            'field' => 'count',
                        ])
                    </th>
                    <th class="column-md">
                        @include('backend.voucher.column-sort', [
                            'columnName' => __('Quantity used'),
                            'field' => 'count_used',
                        ])
                    </th>
                    <th class="column-md">
                        @include('backend.voucher.column-sort', [
                            'columnName' => __('Expired Date'),
                            'field' => 'expired_date',
                        ])
                    </th>
                    <th class="column-md"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vouchers as $voucher)
                    @if ($loop->first)
                        @php
                            $index = 1;
                            if ($vouchers->currentPage() > 1) {
                                $index = ($vouchers->currentPage() - 1) * config('paging.quantity') + 1;
                            }
                        @endphp
                    @endif
                    <tr>
                        <td class="column-sm">{{ $index++ }}</td>
                        <td class="column-xl" title="{{ $voucher->name ?? '' }}">{{ $voucher->name ?? '' }}</td>
                        <td class="column-lg">
                            <div class="flex-center">
                                <span class="flex-grow-1"
                                    title="{{ $voucher->code ?? '' }}">{{ $voucher->code ?? '' }}</span>
                                <a href="javascript:;" class="text-decoration-none pl-2 btn-copy-code"
                                    data-content="{{ $voucher->code ?? '' }}" data-toggle="tooltip" data-placement="top"
                                    title="Click to copy!">
                                    <img src="{{ asset('img/content-copy.svg') }}" alt="">
                                </a>
                            </div>
                        </td>
                        <td class="column-md">{{ $voucher->discount ?? '' }}</td>
                        <td class="column-md">{{ $voucher->type_name ?? '' }}</td>
                        <td class="column-md">{{ $voucher->count ?? __('Unlimited') }}</td>
                        <td class="column-md">
                            <div class="flex-center">
                                <span class="flex-grow-1"
                                    title="{{ $voucher->count_used ?? 0 }}">{{ $voucher->count_used ?? 0 }}</span>
                                <a href="javascript:;" class="text-decoration-none pl-2 btn-detail-used"
                                    data-id="{{ $voucher->id }}" data-toggle="tooltip" data-toggle="modal"
                                    data-target="#detail-used-modal" data-placement="top" title="Click to details!">
                                    <img src="{{ asset('img/more_vert.svg') }}" alt="">
                                </a>
                            </div>
                        </td>
                        <td class="column-md">{{ $voucher->expired_date ?? '' }}</td>
                        <td class="column-md">
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="javascript:;" data-id="{{ $voucher->id }}"
                                    class="btn btn-general d-flex justify-content-center align-items-center hover-button-list {{ $voucher->status ? 'btn-disable-voucher' : 'btn-availability-voucher' }}"
                                    title="{{ $voucher->status ? 'Click to disable.' : 'Click to availability.' }}" data-toggle="tooltip" data-placement="top">
                                    @if ($voucher->status)
                                        <img src="{{ asset('/img/icon-eye.svg') }}" alt=""
                                            class="cursor-pointer">
                                    @else
                                        <img src="{{ asset('/img/icon-eye-off.svg') }}" alt=""
                                            class="cursor-pointer">
                                    @endif
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center text-danger" colspan="9">@lang('No data.')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($vouchers->hasPages())
        <div class="d-flex justify-content-center align-items-center my-3">
            <div class="next-back-pagination d-flex justify-content-center align-items-center">
                {{ $vouchers->withQueryString()->onEachSide(1)->links() }}
            </div>
        </div>
    @endif
</div>
