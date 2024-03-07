<div class="table-transactions-employer">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
                <tr>
                    <th class="column-sm">
                        <div class="custom-checkbox">
                            <div class="has-checked-checkbox">
                                <input type="checkbox" class="ipt-has-check" id="check-all-transaction">
                                <label for="check-all-transaction" class="d-flex align-items-center"></label>
                            </div>
                        </div>
                    </th>
                    <th class="color-475467 font-12 column-lg">@lang('Freelancer')</th>
                    <th class="column-xl">
                        <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-name">
                            <div class="color-475467 font-12 mr-2">@lang('Job Description')</div>

                        </div>
                    </th>
                    <th class="column-md">
                        <a href="{{ route(EMPLOYER_PAYMENT_INDEX, ['orderBy' => request()->query('orderBy') == 'DESC' ? 'ASC' : 'DESC']) }}" class="d-flex justify-content-start align-items-center cursor-pointer text-decoration-none">
                            <div class="color-475467 font-12 mr-2">@lang('Due date')</div>
                            @if(request()->query('orderBy') == 'ASC')
                                <img class="arrow-up" src="{{ asset('img/arrow-up.svg') }}" alt="">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="">
                            @endif
                        </a>
                    </th>
                    <th class="color-475467 font-12 column-md">@lang('Status')</th>
                    <th class="color-475467 font-12 column-md">@lang('Amount')</th>
                    <th class="column-lg"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($escrowTransaction as $key => $transaction)
                    @php
                        $isJobExists = !is_null($transaction->job);
                    @endphp
                    <tr data-id="{{ $transaction->id }}">
                        <td class="custom-checkbox column-sm">
                            <input type="checkbox" id="{{ $key }}"
                                value="{{ $transaction->escrow_transaction_id }}" class="ipt-check-account">
                            <label for="{{ $key }}" class="d-flex align-items-center"></label>
                        </td>
                        <td class="column-lg">
                            @if ($transaction->receiver->is_hidden)
                                <div class="color-999999">@lang('Freelancer Unknown')</div>
                            @else
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar mr-2">
                                        <img src="{{ asset($transaction->receiver->avatar ? $transaction->receiver->logo : '/img/avatar_default.svg') }}"
                                            alt="Logo" class="rounded-circle h-100 w-100">
                                    </div>
                                    <div class="d-flex flex-column align-items-start name-and-mail">
                                        <div class="font-14 color-000000 long-text">
                                            {{ $transaction->receiver->name }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="column-xl">
                            <div class="{{ $isJobExists ? 'color-000000' : 'color-999999' }}">
                                {{  $isJobExists ? optional($transaction->job)->description ?? '--' : 'Has been deleted' }}
                            </div>
                        </td>
                        <td class="column-md">
                            <div>
                                {{ $isJobExists ? formatDate(optional($transaction->job)->due_date) : '' }}
                            </div>
                        </td>
                        <td class="column-md">
                            <div class="d-flex justify-content-start align-items-center transaction-status">
                                @if ($transaction->isCancel())
                                    <div
                                        class="status-category cancel-status d-flex justify-content-center align-items-center mr-2">
                                        <div class="color-344054">@lang('Cancel')</div>
                                    </div>
                                @elseif($transaction->isComplete())
                                    <div
                                        class="status-category completed-status d-flex justify-content-center align-items-center mr-2">
                                        <div class="color-496300">@lang('Completed')</div>
                                    </div>
                                @else
                                    <div
                                        class="status-category pending-status d-flex justify-content-center align-items-center mr-2">
                                        <div class="color-B42318">@lang('Processing')</div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="column-md">
                            <div>
                                @if ($transaction->amount_sender)
                                    <span class="text-uppercase">{{ $transaction->currency }}</span>
                                    <span>{{ symbolUnitMoney($transaction->currency) }}</span>
                                    <span>{{ $transaction->amount_sender }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="column-lg">
                            <div class="d-flex justify-content-end align-items-center">
                                @if ($isJobExists && !$transaction->job->isMarkDone() && $transaction->isCreated())
                                    <button
                                        class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button btn-funding-transaction"
                                        style="margin: 5px">
                                        <div class="color-2200A5 font-14 font-weight-bold text-nowrap">@lang('FUNDING')
                                        </div>
                                    </button>
                                @endif
                                @if ($isJobExists && !$transaction->job->isMarkDone() && $transaction->isHasCancel())
                                    <button
                                        class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button btn-cancel-transaction"
                                        style="margin: 5px">
                                        <div class="color-2200A5 font-14 font-weight-bold text-nowrap">@lang('CANCEL')
                                        </div>
                                    </button>
                                @endif
                                @if ($isJobExists && $transaction->job->isMarkDone() && $transaction->isPayNow())
                                    <button data-transaction-id="{{ $transaction->id }}"
                                        class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button btn-pay-now-transaction"
                                        style="margin: 5px">
                                        <div class="color-2200A5 font-14 font-weight-bold text-nowrap">
                                            @lang('PAY NOW')
                                        </div>
                                    </button>
                                @elseif($transaction->isComplete())
                                    <div class="btn btn-complete-pay d-flex justify-content-center align-items-center import-freelancer"
                                        style="margin: 5px">
                                        <div class="color-496300 font-14 font-weight-bold text-nowrap">
                                            @lang('PAID')</div>
                                    </div>
                                @elseif ($isJobExists)
                                    <a href="{{ route('frontend.employer.jobs.applications', ['job' => $transaction->job_id]) }}"
                                        class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button text-decoration-none"
                                        style="margin: 5px">
                                        <div class="color-2200A5 font-14 font-weight-bold text-nowrap">
                                            @lang('CHECK STATUS')</div>
                                    </a>
                                @endif
                                @if (!$transaction->receiver->is_hidden)
                                    <a href="{{ route(USER_CHAT_MESSAGE_ROUTE, $transaction->receiver->id) }}"
                                        class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer hover-button text-decoration-none"
                                        style="margin: 5px">
                                        <div class="color-2200A5 font-14 font-weight-bold text-nowrap">
                                            @lang('MESSAGE')</div>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center font-14 font-weight-500 color-475467">@lang('No data')
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($escrowTransaction->hasPages())
        <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-center py-3">
            {{ $escrowTransaction->withQueryString()->onEachSide(1)->links() }}
        </div>
    @endif
</div>

<script type="text/template" data-template="cancel-status-template">
    <div class="status-category cancel-status d-flex justify-content-center align-items-center mr-2">
        <div class="color-344054">@lang('Cancel')</div>
    </div>
</script>

<script type="text/template" data-template="complete-status-template">
    <div
        class="status-category completed-status d-flex justify-content-center align-items-center mr-2">
        <div class="color-496300">@lang('Completed')</div>
    </div>
</script>

<script type="text/template" data-template="paid-status-template">
    <div
        class="btn btn-complete-pay d-flex justify-content-center align-items-center import-freelancer"
        style="margin: 5px">
    <div class="color-496300 font-14 font-weight-bold text-nowrap">
        @lang('PAID')</div>
    </div>
</script>
