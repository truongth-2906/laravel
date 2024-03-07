<div class="header-body d-flex justify-content-between align-items-center w-100">
    <div class="d-flex justify-content-start align-items-start">
        <button class="collapse-sidebar-chat mr-2">
            <img src="{{ asset('/img/backend/sidebar/option.svg') }}" alt="">
        </button>
        <div class="mr-2 position-relative avatar-header-chat">
            @if ($userInfo->isFreelancer())
                <img src="{{ asset($userInfo->avatar ? $userInfo->logo : '/img/avatar_default.svg') }}" alt=""
                    class="w-100 rounded-56">
            @else
                <img src="{{ asset(optional($userInfo->company)->logo ? optional($userInfo->company)->avatar : '/img/avatar_default.svg') }}"
                    alt="" class="w-100 rounded-56">
            @endif
            <img src="{{ asset('/img/verified-tick.svg') }}" alt="" class="status-user">
        </div>
        <div class="d-flex flex-column align-items-start mr-2">
            <div class="font-14 font-weight-600 color-000000 long-text user-name"
                title="{{ optional($userInfo)->name }}">{{ optional($userInfo)->name }}</div>
            <div class="font-14 font-weight-400 color-475467 long-text user-name" title="">
                {{ optional($userInfo)->email }}</div>
        </div>
        <div id="user-status-online">
            @if ($userInfo->isOnline())
                <div class="tag tag__sm tag_pill tag__success tag__status-online">
                    <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1" data-status="1">
                    <div class="color-496300 font-12 font-weight-500 text-status-user">@lang('Online')</div>
                </div>
            @else
                <div class="tag tag__sm tag_pill tag__secondary tag__status-online">
                    <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1" data-status="0">
                    <div class="color-496300 font-12 font-weight-500 text-status-user">@lang('Offline')</div>
                </div>
            @endif
        </div>
    </div>
    <a target="_blank" class="redirect-profile" rel="noopener"
       href="{{ $userInfo->isFreelancer() ? route('frontend.freelancer.profile', $userInfo->id) : route('frontend.employer.profile', $userInfo->id) }}">
        <button type="button"
            class="btn-general-action color-2200A5 font-weight-600 font-14">@lang('View profile')</button>
    </a>
    <input type="hidden" class="email-direct-user" value="{{ $userInfo->email }}">
</div>

<div class="d-flex flex-column justify-content-between group-message-chat">
    <div class="chat-wrapper">
        @include('frontend.message.message')
    </div>
    <span class="text-danger validate-message"></span>
    <form id="form-submit-chat-message" method="post" enctype="multipart/form-data">
        @csrf
        <div class="send-message-wrapper mb-3 position-relative">
            <textarea type="text" class="w-100 font-16 color-000000 content-chat-msg-send" placeholder="@lang('Send a message')" id="messageTextarea"></textarea>
            <div class="action-send-msg">
                <img src="{{ asset('/img/attachment_icon.svg') }}" alt=""
                    class="mr-3 cursor-pointer attachment-file-chat">
                <div class="reacts-wrapper reacts-wrapper__right react-textarea mr-3 pt-0" data-textarea="messageTextarea">
                    <div class="btn-open-react">
                        <div class="reacts-suggest"></div>
                        <img src="{{ asset('/img/add_reaction.svg') }}" alt="" class="cursor-pointer">
                    </div>
                </div>
                <button type="button"
                    class="btn-general-action color-2200A5 font-14 font-weight-600 btn-send-message">@lang('Send')</button>
            </div>
        </div>
        <div class="send-message-wrapper-mobile justify-content-between align-items-center mb-3 position-relative">
            <textarea type="text" class="font-16 color-000000 w-60 content-chat-msg-send mr-1" id="messageTextareaMobile" placeholder="@lang('Send a message')"></textarea>
            <div class="w-35 d-flex justify-content-end align-items-center">
                <img src="{{ asset('/img/attachment_icon.svg') }}" alt=""
                    class="cursor-pointer attachment-file-chat">
                <div class="reacts-wrapper reacts-wrapper__right react-textarea justify-content-center mx-2 mx-sm-3 p-0 w-auto" data-textarea="messageTextareaMobile">
                    <div class="btn-open-react">
                        <div class="reacts-suggest"></div>
                        <img src="{{ asset('/img/add_reaction.svg') }}" alt="" class="cursor-pointer">
                    </div>
                </div>
                <button type="button"
                    class="justify-content-center align-items-center btn-send-message submit-on-mobile">
                    <img src="{{ asset('/img/send_icon.svg') }}" alt="">
                </button>
            </div>
        </div>
        <input name="file" type="file" hidden id="choose-attachment-send">
    </form>
    <img src="{{ asset('/img/add_reaction.svg') }}" class="d-none src-img-reaction" alt="">
    <div class="container-file-before-upload position-relative"></div>
</div>
<img src="{{ asset('/img/status_online_icon.svg') }}" alt="" class="d-none src-img-online-user">
<img src="{{ asset('/img/status_offline_icon.svg') }}" alt="" class="d-none src-img-offline-user">

<script type="text/template" data-template="react-template">
    <span class="react ${class}" data-content="${content}" data-id="${id}">
        ${contentColon}
        <span class="count" data-count="${count}">${count}</span>
    </span>
</script>

<script type="text/template" data-template="message-type-default-send-template">
    <div class="message-send message-chat-box"  data-id="${id}">
        <div class="d-flex flex-column justify-content-center align-items-end w-100">
            <div class="d-flex flex-column align-items-end width-msg-send">
                <div class="d-flex justify-content-between align-items-center msg-send-mw w-100 mb-2">
                    <div class="font-weight-500 font-size-14 color-344054 mr-3">@lang('You')</div>
                    <div
                        class="font-weight-400 font-12 color-475467">${time}</div>
                </div>
                <div class="font-16 font-weight-400 color-2200A5 message-general message-text msg-send-mw">
                    <div class="text-break">${message}</div>
                </div>
            </div>
            @include('frontend.includes.reaction-wrapper', [
                'id' => '${id}',
                'reactions' => [],
                'align' => 'right'
            ])
        </div>
    </div>
</script>

<script type="text/template" data-template="message-type-file-send-template">
    <div class="message-send message-chat-box"  data-id="${id}">
        <div class="d-flex flex-column justify-content-center align-items-end w-100">
            <div class="d-flex flex-column align-items-end width-msg-send position-relative">
                <div class="d-flex justify-content-between align-items-center msg-send-mw w-100 mb-2">
                    <div class="font-weight-500 font-size-14 color-344054 mr-3">@lang('You')</div>
                    <div
                        class="font-weight-400 font-12 color-475467">${time}</div>
                </div>
                ${message}
            </div>
            @include('frontend.includes.reaction-wrapper', [
                'id' => '${id}',
                'reactions' => [],
                'align' => 'right'
            ])
        </div>
    </div>
</script>

<script type="text/template" data-template="file-send-template">
    <div class="message-file message-general d-flex justify-content-start align-items-start w-100">
        <div class="background-file d-flex justify-content-center align-items-center mr-3">
            <div class="icon-file"></div>
        </div>
        <div class="d-flex flex-column align-items-start w-90">
            <a href="${download_url}" target="${target}" class="font-weight-500 font-14 color-344054 long-text w-80 download-url" title="${filename}">${filename}</a>
            <div class="font-weight-400 font-14 color-475467">${filesize}</div>
        </div>
    </div>
</script>

<script type="text/template" data-template="message-type-default-receive-template">
    <div class="message-receive message-chat-box"  data-id="${id}">
        <div class="d-flex justify-content-start align-items-center">
            <div class="d-flex justify-content-start align-items-start">
                <div class="mr-2 position-relative">
                    <img src="${avatar}" alt="" class="avatar-chat">
                    <img src="{{ asset('/img/status_online_icon.svg') }}" alt="" class="status-user">
                </div>
                <div class="d-flex flex-column justify-content-center align-items-start w-100">
                    <div class="d-flex flex-column align-items-start position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2 w-100">
                            <div class="font-14 font-weight-600 color-000000 long-text user-name-chat mr-3" title="${senderName}">${senderName}</div>
                            <div
                                class="font-12 font-weight-400 color-475467">${time}</div>
                        </div>
                        <div
                            class="font-weight-400 font-16 color-101828 message-general message-text position-relative">
                            <div class="text-break">${message}</div>
                        </div>
                    </div>
                    @include('frontend.includes.reaction-wrapper', [
                        'id' => '${id}',
                        'reactions' => [],
                        'align' => 'left'
                    ])
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" data-template="message-type-file-receive-template">
    <div class="message-receive message-chat-box"  data-id="${id}">
        <div class="d-flex justify-content-start align-items-center">
            <div class="d-flex justify-content-start align-items-start">
                <div class="mr-2 position-relative">
                    <img src="${avatar}" alt="" class="avatar-chat">
                    <img src="{{ asset('/img/status_online_icon.svg') }}" alt="" class="status-user">
                </div>
                <div class="d-flex flex-column justify-content-center align-items-start w-100">
                    <div class="d-flex flex-column align-items-start position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2 w-100">
                            <div class="font-14 font-weight-600 color-000000 long-text user-name-chat mr-3" title="${senderName}">${senderName}</div>
                            <div class="font-12 font-weight-400 color-475467">${time}</div>
                        </div>
                        <div
                            class="message-file message-general d-flex justify-content-start align-items-start w-100 position-relative">
                            <div class="background-file d-flex justify-content-center align-items-center mr-3">
                                <div class="icon-file"></div>
                            </div>
                            <div class="d-flex flex-column align-items-start w-90">
                                <a class="font-weight-500 font-14 color-344054 long-text w-80 download-url" title="${filename}" href="${download_url}" target="${target}">${filename}</a>
                                <div class="font-weight-400 font-14 color-475467">${filesize}</div>
                            </div>
                        </div>
                    </div>
                    @include('frontend.includes.reaction-wrapper', [
                        'id' => '${id}',
                        'reactions' => [],
                        'align' => 'left'
                    ])
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" data-template="sidebar-item-template">
    <a href="${url}" class="w-100 user-chat-message" data-id="${id}">
        <div class="d-flex flex-column align-items-start cursor-pointer messages w-100 active">
            <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="mr-2 position-relative avatar-chat">
                        <img src="${srcAvatar}" alt="" class="avatar-chat">
                        <img src="${srcStatus}" alt="" class="status-user">
                    </div>
                    <div class="d-flex flex-column align-items-start">
                        <div class="font-14 font-weight-600 color-000000 long-text user-name" title="${name}">${name}</div>
                        <div class="font-14 font-weight-400 color-475467 long-text user-name" title="${email}">${email}</div>
                    </div>
                </div>
                <div class="font-14 font-weight-400 color-475467">${time}</div>
            </div>
            <div class="font-weight-400 font-14 color-475467 content-message">${content}</div>
        </div>
    </a>
</script>

<script type="text/template" data-template="online-status-template">
    <div class="tag tag__sm tag_pill tag__success tag__status-online">
        <img src="{{ asset('/img/dot_active_icon.svg') }}" alt="" class="mr-1">
        <div class="color-496300 font-12 font-weight-500">@lang('Online')</div>
    </div>
</script>

<script type="text/template" data-template="offline-status-template">
    <div class="tag tag__sm tag_pill tag__secondary tag__status-online">
        <img src="{{ asset('/img/dot_inactive_icon.svg') }}" alt="" class="mr-1">
        <div class="color-496300 font-12 font-weight-500">@lang('Offline')</div>
    </div>
</script>
