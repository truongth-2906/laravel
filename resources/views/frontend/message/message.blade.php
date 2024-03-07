@php
    $line = null;
@endphp
@foreach($messages as $key => $message)
    @if($line != date('Ymd', strtotime($message->created_at)))
        <div class="w-100 border-bottom-EAECF0 text-center font-14 color-475467 font-weight-500 group-msg-by-day">
            {{ formatDateChat($message->created_at) }}
        </div>
        @php($line = date('Ymd', strtotime($message->created_at)))
    @endif

    @if($message->sender_id == auth()->id())
        @if($message->type == TYPE_MESSAGE_TEXT)
            <div class="message-send message-chat-box" id="{{ $key == 0 ? 'scroll-to-div' : '' }}"
                 data-id="{{ $message->id }}">
                <div class="d-flex flex-column justify-content-center align-items-end w-100">
                    <div class="d-flex flex-column align-items-end width-msg-send">
                        <div class="d-flex justify-content-between align-items-center msg-send-mw w-100 mb-2">
                            <div class="font-weight-500 font-size-14 color-344054 mr-3">@lang('You')</div>
                            <div
                                class="font-weight-400 font-12 color-475467">{{ formatHourMinute($message->created_at) }}</div>
                        </div>
                        <div class="font-16 font-weight-400 color-2200A5 message-general message-text msg-send-mw">
                            <div class="text-break">{!! nl2br(e($message->message ?? '')) !!}</div>
                        </div>
                    </div>
                    @include('frontend.includes.reaction-wrapper', [
                        'id' => $message->id,
                        'reactions' => $message->reactions ?? [],
                        'align' => 'right'
                    ])
                </div>
            </div>
        @elseif($message->type == TYPE_MESSAGE_FILE)
            <div class="message-send message-chat-box" id="{{ $key == 0 ? 'scroll-to-div' : '' }}"
                 data-id="{{ $message->id }}">
                <div class="d-flex flex-column justify-content-center align-items-end w-100">
                    <div class="d-flex flex-column align-items-end width-msg-send position-relative">
                        <div class="d-flex justify-content-between align-items-center msg-send-mw w-100 mb-2">
                            <div class="font-weight-500 font-size-14 color-344054 mr-3">@lang('You')</div>
                            <div
                                class="font-weight-400 font-12 color-475467">{{ formatHourMinute($message->created_at) }}</div>
                        </div>
                        <div class="message-file message-general d-flex justify-content-start align-items-start w-100">
                            <div class="background-file d-flex justify-content-center align-items-center mr-3">
                                <div class="icon-file"></div>
                            </div>
                            <div class="d-flex flex-column align-items-start w-90">
                                <a class="font-weight-500 font-14 color-344054 long-text w-80 download-url" target="${target}" href="{{ $message->file->download_url ?? 'javascript:;' }}"
                                     title="{{ $message->file->name }}">{{ $message->file->name }}</a>
                                <div
                                    class="font-weight-400 font-14 color-475467">{{ convertFileSize($message->file->size) }}</div>
                            </div>
                        </div>
                    </div>
                    @include('frontend.includes.reaction-wrapper', [
                        'id' => $message->id,
                        'reactions' => $message->reactions ?? [],
                        'align' => 'right'
                    ])
                </div>
            </div>
        @endif
    @else
        @if($message->type == TYPE_MESSAGE_TEXT)
            <div class="message-receive message-chat-box" id="{{ $key == 0 ? 'scroll-to-div' : '' }}"
                 data-id="{{ $message->id }}">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="d-flex justify-content-start align-items-start">
                        <div class="mr-2 position-relative">
                            @if($userInfo->isFreelancer())
                                <img src="{{ asset($userInfo->avatar ? $userInfo->logo : '/img/avatar_default.svg') }}"
                                     alt="" class="avatar-chat">
                            @else
                                <img
                                    src="{{ asset(optional($userInfo->company)->logo ? optional($userInfo->company)->avatar : '/img/avatar_default.svg') }}"
                                    alt="" class="avatar-chat">
                            @endif
                            <img src="{{ asset($message->sender->isOnline() ? '/img/status_online_icon.svg' : '/img/status_offline_icon.svg') }}" alt="" class="status-user">
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-start w-100">
                            <div class="d-flex flex-column align-items-start position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-2 w-100">
                                    <div class="font-14 font-weight-600 color-000000 long-text user-name-chat mr-3"
                                         title="{{ $userInfo->name }}">{{ $userInfo->name }}</div>
                                    <div
                                        class="font-12 font-weight-400 color-475467">{{ formatHourMinute($message->created_at) }}</div>
                                </div>
                                <div
                                    class="font-weight-400 font-16 color-101828 message-general message-text position-relative">
                                    <div class="text-break">{!! nl2br(e($message->message ?? '')) !!}</div>
                                </div>
                            </div>
                            @include('frontend.includes.reaction-wrapper', [
                                'id' => $message->id,
                                'reactions' => $message->reactions ?? [],
                                'align' => 'left'
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @elseif($message->type == TYPE_MESSAGE_FILE)
            <div class="message-receive message-chat-box" id="{{ $key == 0 ? 'scroll-to-div' : '' }}"
                 data-id="{{ $message->id }}">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="d-flex justify-content-start align-items-start">
                        <div class="mr-2 position-relative">
                            @if($userInfo->isFreelancer())
                                <img src="{{ asset($userInfo->avatar ? $userInfo->logo : '/img/avatar_default.svg') }}"
                                     alt="" class="avatar-chat">
                            @else
                                <img
                                    src="{{ asset(optional($userInfo->company)->logo ? optional($userInfo->company)->avatar : '/img/avatar_default.svg') }}"
                                    alt="" class="avatar-chat">
                            @endif
                            <img src="{{ asset($message->sender->isOnline() ? '/img/status_online_icon.svg' : '/img/status_offline_icon.svg') }}" alt="" class="status-user">
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-start w-100">
                            <div class="d-flex flex-column align-items-start position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-2 w-100">
                                    <div class="font-14 font-weight-600 color-000000 long-text user-name-chat mr-3"
                                         title="{{ $userInfo->name }}">{{ $userInfo->name }}</div>
                                    <div
                                        class="font-12 font-weight-400 color-475467">{{ formatHourMinute($message->created_at) }}</div>
                                </div>
                                <div
                                    class="message-file message-general d-flex justify-content-start align-items-start w-100 position-relative">
                                    <div class="background-file d-flex justify-content-center align-items-center mr-3">
                                        <div class="icon-file"></div>
                                    </div>
                                    <div class="d-flex flex-column align-items-start w-90">
                                        <a class="font-weight-500 font-14 color-344054 long-text w-80 download-url" target="${target}" href="{{ $message->file->download_url ?? 'javascript:;' }}"
                                             title="{{ $message->file->name }}">
                                            {{ $message->file->name }}</a>
                                        <div
                                            class="font-weight-400 font-14 color-475467">{{ convertFileSize($message->file->size) }}</div>
                                    </div>
                                </div>
                            </div>
                            @include('frontend.includes.reaction-wrapper', [
                                'id' => $message->id,
                                'reactions' => $message->reactions ?? [],
                                'align' => 'left'
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endforeach
<input type="hidden" class="is-load-more-msg"
       value="{{ $messages->where('id', optional($oldest)->id)->first() ? 1 : 0 }}">
