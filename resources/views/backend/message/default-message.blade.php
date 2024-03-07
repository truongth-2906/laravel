<div data-index="{{ $message->id }}"
    class="message-item {{ $message->sender_id == request()->sender ? 'message-left' : 'message-right' }}">
    <div class="user-avatar">
        @if ($message->sender->isFreelancer())
            <img src="{{ asset($message->sender->avatar ? $message->sender->logo : '/img/avatar_default.svg') }}" alt="user-avatar rounded-56"
                width="40px" height="40px">
        @else
            <img src="{{ asset(optional($message->sender->company)->logo ? optional($message->sender->company)->avatar : '/img/avatar_default.svg') }}"
                alt="user-avatar rounded-56" width="40px" height="40px">
        @endif
    </div>
    <div class="message-details">
        <div class="user-name-and-time">
            <span class="user-name" title="{{ $message->sender->name ?? '' }}">{{ $message->sender->name ?? '' }}</span>
            <span class="time">{{ formatHourMinute($message->created_at) }}</span>
        </div>
        <div class="message-content">{{ nl2br(e($message->message ?? '')) }}</div>
        <div class="reacts-wrapper">
            <div class="reacts-selected" style="display: none;">
                @foreach ($message->reactions as $react)
                    <span class="react" data-content="{{ $react->emoji_content }}"
                        title="{{ $react->title ?? ($react->count ?? 1) . 'user' }} reacted." data-toggle="tooltip"
                        data-placement="top">
                        <span class="count" data-count="{{ $react->count ?? 1 }}">{{ $react->count ?? 1 }}</span>
                    </span>
                @endforeach
            </div>
        </div>
        <span class="watched-status">{{ $message->isRead() ? 'Seen' : 'Not Seen' }}</span>
    </div>
</div>
