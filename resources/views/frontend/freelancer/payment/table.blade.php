<div class="table-transactions-freelancer">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
                <tr>
                    <th class="column-sm">
                        <div class="custom-checkbox">
                            <div class="has-checked-checkbox">
                                <input type="checkbox" class="ipt-has-check">
                                <label class="d-flex align-items-center"></label>
                            </div>
                        </div>
                    </th>
                    <th class="color-475467 font-12 column-lg">@lang('Employer')</th>
                    <th class="color-475467 font-12 column-xl">@lang('Job Description')</th>
                    <th class="column-md">
                        <a href="{{ route(FREELANCER_PAYMENT_INDEX, ['orderBy' => request()->query('orderBy') == 'DESC' ? 'ASC' : 'DESC']) }}" class="d-flex justify-content-start align-items-center cursor-pointer text-decoration-none">
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
                    <th class="column-md"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    @php
                        $isJobExists = !is_null($transaction->job);
                    @endphp
                    <tr>
                        <td class="custom-checkbox column-sm">
                            <input type="checkbox" id="{{ $transaction['id'] }}" value="{{ $transaction['id'] }}"
                                class="ipt-check-account">
                            <label for="{{ $transaction['id'] }}" class="d-flex align-items-center"></label>
                        </td>
                        <td class="column-lg">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="avatar mr-2">
                                    <img src="{{ asset(optional($transaction->sender->company)->logo ? optional($transaction->sender->company)->avatar : '/img/avatar_default.svg') }}"
                                        alt="Logo" class="rounded-circle h-100 w-100">
                                </div>
                                <div class="d-flex flex-column align-items-start name-and-mail">
                                    <div class="font-14 color-000000 long-text">
                                        {{ $transaction->sender->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="column-xl">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <div class="font-14 {{ $isJobExists ? 'color-000000' : 'color-999999' }}">
                                        {{ $isJobExists ? optional($transaction->job)->description ?? '--' : 'Has been deleted' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="column-md">
                            <div>
                                {{ $isJobExists ? formatDate($transaction->job->due_date) : '' }}
                            </div>
                        </td>
                        <td class="column-md">
                            <div class="d-flex justify-content-start align-items-center">
                                @if ($transaction->isCancel())
                                    <div
                                        class="status-category cancel-status d-flex justify-content-center align-items-center mr-2">
                                        <div class="color-344054">@lang('Cancel')</div>
                                    </div>
                                @elseif($transaction->isReceived())
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
                                @if ($transaction->amount_receiver)
                                    <span class="text-uppercase">{{ $transaction->currency }}</span>
                                    <span>{{ symbolUnitMoney($transaction->currency) }}</span>
                                    <span>{{ $transaction->amount_sender }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="column-md">
                            <div class="d-flex justify-content-end align-items-center">
                                <a href="{{ $isJobExists ? route('frontend.freelancer.jobs.view_status', $transaction->job_id) : 'javascript:;' }}"
                                    class="btn btn-general-action d-flex justify-content-center align-items-center import-freelancer {{ $isJobExists ? 'hover-button' : "disabled" }}">
                                    <div class="color-2200A5 font-14 font-weight-bold">@lang('VIEW STATUS')</div>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="6" class="text-danger">@lang('No data')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($transactions->hasPages())
        <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-center py-3">
            {{ $transactions->withQueryString()->onEachSide(1)->links() }}
        </div>
    @endif
</div>
