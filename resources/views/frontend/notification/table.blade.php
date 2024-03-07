<div class="notification-table">
    <div class="scroll-table">
        <div class="notification__rows">

            @forelse ($notifications as $notification)
                <div class="notification__row {{ !$notification->isRead() ? 'unread' : '' }}">
                    <div class="column-sm column-icon">
                        <img src="{{ asset($notification->icon) }}" alt="">
                    </div>
                    <div class="column-sm column-action mobile-device">
                        <button class="btn-action cursor-pointer hover-button btn-confirm-delete-notification"
                            data-action="{{ notificationRoute('destroy', $notification->id) }}">
                            <img src="{{ asset('img/icon-times.svg') }}" alt="">
                        </button>
                    </div>
                    <div class="column-xl notify-summary-info">
                        <div class="notify-summary-info__title">
                            {{ $notification->title ?? '' }}
                        </div>
                        <div class="notify-summary-info__content">
                            @lang('Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid pariatur, ipsum similique veniam.')
                        </div>
                        <div class="notify-summary-info__action">
                            @if ($notification->actions->get('title', null))
                            <a href="{{ $notification->actions->get('route', '#') }}" class="d-flex">
                                <span>
                                    {{ $notification->actions->get('title') }}
                                </span>
                                <img src="{{ asset('img/icon-arrow-right.svg') }}" alt="" class="ml-2">
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="column-sm column-action pc-device">
                        <button class="btn-action cursor-pointer hover-button btn-confirm-delete-notification"
                            data-action="{{ notificationRoute('destroy', $notification->id) }}">
                            <img src="{{ asset('img/icon-times.svg') }}" alt="">
                        </button>
                    </div>
                </div>
            @empty
                <div class="notification__row">
                    <div class="w-100 text-center py-5">@lang('No data.')</div>
                </div>
            @endforelse
        </div>
    </div>

    @if (isset($notifications) && $notifications->hasPages())
        <div class="w-100 pagination-wrapper d-flex align-items-center justify-content-center">
            {{ $notifications->withQueryString()->onEachSide(1)->links() }}
        </div>
    @endif
</div>
