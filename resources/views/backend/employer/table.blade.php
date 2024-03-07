<div class="table-employers table-datas admin-employer-table">
    <div class="scroll-table">
        <table class="table w-100">
            <thead>
            <tr>
                <th class="column-sm">
                    <div class="custom-checkbox">
                        <div class="has-checked-checkbox">
                            <input type="checkbox" id="check-all-{{ \App\Domains\Auth\Models\User::DEVICE_PC }}"
                                   class="ipt-has-check">
                            <label for="check-all-{{ \App\Domains\Auth\Models\User::DEVICE_PC }}"
                                   class="d-flex align-items-center"></label>
                        </div>
                    </div>
                </th>
                <th class="column-lg">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-name"
                         data-type="{{ $orderByField == user()->getFieldAllowSort('name') ? $orderByType : TYPE_SORT_DESC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Freelancer')</div>
                        @if ($orderByField == user()->getFieldAllowSort('name'))
                            @if($orderByType == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}" alt="rrow-up-active">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}"
                                     alt="arrow-down-active">
                            @endif
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="arrow-down">
                        @endif
                    </div>
                </th>
                <th class="color-475467 font-12 column-lg">@lang('Onboarding Progress')</th>
                <th class="column-md">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-last-login-at"
                         data-type="{{ $orderByField == user()->getFieldAllowSort('last_login_at') ? $orderByType : TYPE_SORT_DESC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Last Logged In')</div>
                        @if ($orderByField == user()->getFieldAllowSort('last_login_at'))
                            @if($orderByType == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}"
                                     alt="arrow-up-active">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}"
                                     alt="arrow-down-active">
                            @endif
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="arrow-down">
                        @endif
                    </div>
                </th>
                <th class="column-md">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-is-online"
                         data-type="{{ $orderByField == user()->getFieldAllowSort('is_online') ? $orderByType : TYPE_SORT_DESC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Online Status')</div>
                        @if ($orderByField == user()->getFieldAllowSort('is_online'))
                            @if($orderByType == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}"
                                     alt="arrow-up-active">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}"
                                     alt="arrow-down-active">
                            @endif
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="arrow-down">
                        @endif
                    </div>
                </th>
                <th class="column-xl">
                    <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-sector"
                         data-type="{{ $orderByField == user()->getFieldAllowSort('sector_name') ? $orderByType : TYPE_SORT_DESC }}">
                        <div class="color-475467 font-12 mr-2">@lang('Business sector')</div>
                        @if ($orderByField == user()->getFieldAllowSort('sector_name'))
                            @if($orderByType == TYPE_SORT_ASC)
                                <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}"
                                     alt="arrow-up-active">
                            @else
                                <img class="arrow-down" src="{{ asset('img/arrow-down-active.svg') }}"
                                     alt="arrow-down-active">
                            @endif
                        @else
                            <img class="arrow-down" src="{{ asset('img/arrow-down.svg') }}" alt="arrow-down">
                        @endif
                    </div>
                </th>
                <th class="column-md"></th>
            </tr>
            </thead>
            <tbody>
            <form id="form-export-employers" action="{{ route('admin.employer.export') }}" method="GET">
                @forelse($employers as $employer)
                    <tr>
                        <td class="custom-checkbox column-sm">
                            <input type="checkbox" form="form-export-employers" id="check-box-10-{{ $employer->id }}"
                                   class="ipt-check-account" name="checkBoxIds[]" value="{{ $employer->id }}">
                            <label for="check-box-10-{{ $employer->id }}"
                                   class="d-flex align-items-center"></label>
                        </td>
                        <td class="column-lg">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="avatar mr-2">
                                    <img
                                        src="{{ asset(optional($employer->company)->logo ? optional($employer->company)->avatar : '/img/avatar_default.svg') }}"
                                        alt="Logo" class="rounded-circle h-100 w-100">
                                </div>

                                <div class="d-flex flex-column align-items-start position-relative">
                                    <div class="d-flex align-items-center font-14 color-000000 font-weight-bold">
                                        <span class="employer-info" title="{{ optional($employer->company)->name }}">{{ optional($employer->company)->name }}</span>
                                    </div>
                                    <a href="{{ route('admin.employer.edit', $employer->id) }}">
                                        <div class="d-flex align-items-center font-14 color-000000">
                                            <span class="employer-info" title="{{ $employer->name }}">{{ $employer->name }}</span>
                                            @if($employer->active == IS_ACTIVE)
                                                <span class="ml-1">
                                                <img width="15px" height="15px"
                                                     src="{{ asset('/img/verified-tick.svg') }}" alt="verified-tick">
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="column-lg">
                            <div class="d-flex justify-content-start align-items-center">
                                <input type="range" max="{{ $employer->getFullProcess() }}"
                                       value="{{ $employer->getProcess() }}" class="mr-2 w-75 custom-input-range"
                                       disabled>
                                <div>{{ percentUser($employer->getProcess(), $employer->getFullProcess()) }}</div>
                            </div>
                        </td>
                        <td class="column-md">{{ optional($employer->last_login_at)->format('d M Y') }}</td>
                        <td class="column-md">
                            <div class="d-flex justify-content-start align-items-center">
                                @if ($employer->isOnline())
                                    <div
                                        class="status-category status-open-job d-flex justify-content-center align-items-center mr-2">
                                        <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
                                        <div class="color-496300">@lang('Online')</div>
                                    </div>
                                @else
                                    <div
                                        class="status-category inactive-status d-flex justify-content-center align-items-center mr-2">
                                        <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
                                        <div class="color-344054">@lang('Offline')</div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="column-xl">
                            <div class="d-flex justify-content-start align-items-center flex-wrap">
                                @if($employer->sector_id)
                                    <div class="status-category mr-2 job-requirement-tag mb-1 mt-1 blue-prism-type">
                                        {{ optional($employer->sector)->name }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="column-md">
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="javascript:;"
                                   class="btn btn-general d-flex justify-content-center align-items-center hover-button-list btn-confirm-delete-employer"
                                   data-id="{{ $employer->id }}">
                                    <img src="{{ asset('/img/delete_icon.svg') }}" alt=""
                                         class="cursor-pointer">
                                </a>
                                <a href="{{ route('admin.employer.edit', $employer->id) }}"
                                   class="btn btn-general d-flex justify-content-center align-items-center hover-button-list">
                                    <img src="{{ asset('/img/edit_icon.svg') }}" alt="" class="cursor-pointer">
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="6" class="text-danger">@lang('No data')</td>
                    </tr>
                @endforelse
            </form>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center align-items-center mb-3">
        <div class="next-back-pagination d-flex justify-content-center align-items-center">
            {{ $employers->withQueryString()->onEachSide(1)->links() }}
        </div>
    </div>
</div>
