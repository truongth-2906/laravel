@php
    $message = $message['messageFile'] ?? $message['message'];
@endphp
<a href="{{ route(USER_CHAT_MESSAGE_ROUTE, $message->sender_id) }}" class="w-100 user-chat-message" data-id="{{ $message->sender_id }}">
    <div
        class="d-flex flex-column align-items-start cursor-pointer messages w-100 {{ $message->isRead() ? '' : 'un-read' }} @if(request()->id == $message->sender_id) active @endif">
        <div class="d-flex justify-content-between align-items-center w-100 mb-2">
            <div class="d-flex justify-content-start align-items-center">
                <div class="mr-2 position-relative avatar-chat">
                    @if($message->sender->isFreelancer())
                        <img src="{{ asset($message->sender->avatar ? $message->sender->logo : '/img/avatar_default.svg') }}" alt=""
                             class="avatar-chat">
                    @else
                        <img
                            src="{{ asset(optional($message->sender->company)->logo ? optional($message->sender->company)->avatar : '/img/avatar_default.svg') }}"
                            alt="" class="avatar-chat">
                    @endif
                    <img src="{{ asset($isOnline ? '/img/status_online_icon.svg' : '/img/status_offline_icon.svg') }}" alt="" class="status-user">
                </div>
                <div class="d-flex flex-column align-items-start">
                    <div class="font-14 font-weight-600 color-000000 long-text user-name"
                         title="{{ $message->sender->name }}">{{ $message->sender->name }}</div>
                    <div class="font-14 font-weight-400 color-475467 long-text user-name"
                         title="{{ $message->sender->email }}">{{ $message->sender->email }}</div>
                </div>
            </div>
            <div class="font-14 font-weight-400 color-475467">{{ $message->created_at->diffForHumans() }}</div>
        </div>
        <div class="font-weight-400 font-14 color-475467 content-message">
            @if($message->type == TYPE_MESSAGE_TEXT)
                {{ $message->message }}
            @else
                <span class="font-14 font-weight-500 color-2200A5">@lang($message->sender->name . ' sent an attachment.')</span>
            @endif
        </div>
    </div>
</a>
