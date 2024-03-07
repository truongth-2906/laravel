<div class="message-table-wrapper">
    <div class="scroll-table">
        <table class="table">
            <thead>
                <tr>
                    <th class="column-md">@lang('No')</th>
                    <th class="column-xl">@lang('Members')</th>
                    <th class="column-md">
                        <div class="d-flex justify-content-start align-items-center cursor-pointer btn-sort-date" data-order-by="{{ request()->order_by != TYPE_SORT_ASC ? TYPE_SORT_ASC : TYPE_SORT_DESC }}">
                            <div class="color-475467 font-12 mr-2">@lang('Last Texting Time')</div>
                            @if (request()->order_by == TYPE_SORT_ASC)
                            <img class="arrow-up" src="{{ asset('img/arrow-up-active.svg') }}" alt="arrow-up-active">
                            @else
                            <img class="arrow-up" src="{{ asset('img/arrow-down-active.svg') }}" alt="arrow-up-active">
                            @endif
                        </div>
                    </th>
                    <th class="column-sm"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messageGroups as $group)
                    @if ($loop->first)
                        @php
                            $index = 1;
                            if ($messageGroups->currentPage() > 1) {
                                $index = ($messageGroups->currentPage() - 1) * config('paging.quantity') + 1;
                            }
                        @endphp
                    @endif
                    <tr>
                        <td class="column-md">{{ $index++ }}</td>
                        <td class="column-xl">
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-start align-items-center mb-1">
                                    <div class="avatar mr-2">
                                        @if ($group->sender->isFreelancer())
                                            <img src="{{ asset($group->sender->avatar ? $group->sender->logo : '/img/avatar_default.svg') }}" alt=""
                                                class="w-100 rounded-56">
                                        @else
                                            <img src="{{ asset(optional($group->sender->company)->logo ? optional($group->sender->company)->avatar : '/img/avatar_default.svg') }}"
                                                alt="" class="w-100 rounded-56">
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column align-items-start">
                                        <a href="{{ route('admin.'. $group->sender->type .'.edit', ['id' => $group->sender->id]) }}">
                                            <div
                                                class="d-flex align-items-center font-14 color-000000 font-weight-bold">
                                                <span class="member-info" title="{{ $group->sender->name ?? '' }}">{{ $group->sender->name ?? '' }}</span>
                                                @if ($group->sender->active == IS_ACTIVE)
                                                    <span class="ml-1">
                                                        <img width="15px" height="15px"
                                                            src="{{ asset('/img/verified-tick.svg') }}"
                                                            alt="verified-tick">
                                                    </span>
                                                @endif
                                            </div>
                                        </a>
                                        <div class="font-14 color-000000 member-info"
                                            title="{{ $group->sender->tag_line ?? '' }}">
                                            {{ $group->sender->tag_line ?? '' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center mb-1">
                                    <div class="avatar mr-2">
                                        @if ($group->receiver->isFreelancer())
                                            <img src="{{ asset($group->receiver->avatar ? $group->receiver->logo : '/img/avatar_default.svg') }}" alt=""
                                                class="w-100 rounded-56">
                                        @else
                                            <img src="{{ asset(optional($group->receiver->company)->logo ? optional($group->receiver->company)->avatar : '/img/avatar_default.svg') }}"
                                                alt="" class="w-100 rounded-56">
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column align-items-start">
                                        <a href="{{ route('admin.'. $group->receiver->type .'.edit', ['id' => $group->receiver->id]) }}">
                                            <div
                                                class="d-flex align-items-center font-14 color-000000 font-weight-bold">
                                                <span class="member-info" title="{{ $group->receiver->name ?? '' }}">{{ $group->receiver->name ?? '' }}</span>
                                                @if ($group->receiver->active == IS_ACTIVE)
                                                    <span class="ml-1">
                                                        <img width="15px" height="15px"
                                                            src="{{ asset('/img/verified-tick.svg') }}"
                                                            alt="verified-tick">
                                                    </span>
                                                @endif
                                            </div>
                                        </a>
                                        <div class="font-14 color-000000 member-info"
                                            title="{{ $group->receiver->tag_line ?? '' }}">
                                            {{ $group->receiver->tag_line ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="column-md">{{ $group->last_texting_time->format('H:i:s d M Y') ?? '' }}</td>
                        <td class="column-sm">
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.messages.show', ['sender' => $group->sender->id, 'receiver' => $group->receiver->id]) }}"
                                    class="btn btn-general d-flex justify-content-center align-items-center hover-button-list">
                                    <img src="{{ asset('/img/icon-eye.svg') }}" alt="" class="cursor-pointer">
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                <tr class="text-center">
                    <td colspan="4" class="text-danger">@lang('No data')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($messageGroups->hasPages())
        <div class="d-flex justify-content-center align-items-center mb-3">
            <div class="next-back-pagination d-flex justify-content-center align-items-center">
                {{ $messageGroups->withQueryString()->onEachSide(1)->links() }}
            </div>
        </div>
    @endif
</div>
