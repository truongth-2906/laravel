import { renderHtmlTemplate, escapeHtml } from './common';
import { ONLINE_STATUS } from '../global';

const SRC_IMG_ONLINE_STATUS = '/img/status_online_icon.svg';
const SRC_IMG_OFFLINE_STATUS = '/img/status_offline_icon.svg';

const message = {
    init: () => {
        let paramSearchUserChat = {
            nameUser: '',
        };

        if ($('.chat-wrapper').length > 0) {
            $('.chat-wrapper').scrollTop($('.chat-wrapper')[0].scrollHeight);
        }

        if ($('.body-message').children().length === 0) {
            $('.sidebar-message').addClass('d-block');
        }

        $(document).on('keyup', '.container-message .ipt-search-user-chat', async function () {
            paramSearchUserChat.nameUser = $(this).val();
            await message.searchUserChat(paramSearchUserChat);
        });

        $(document).on('click', '.list-message .messages', function () {
            $('.list-message .messages').removeClass('active');
            $(this).addClass('active').removeClass('un-read');
        });

        $(document).on('click', '.collapse-sidebar-chat', function () {
            const sidebar = $('.sidebar-message');
            const display = sidebar.css('display');
            if (display === 'none') {
                sidebar.css({'display': 'flex', 'width': '320px'});
            } else {
                sidebar.css({'display': 'none', 'width': '30%'});
            }
        });

        $(document).on('click', '.attachment-file-chat', function () {
            $('#choose-attachment-send').click();
        });

        $(document).on('click', '#form-submit-chat-message .btn-send-message', function () {
            message.submitChatMessage($(this).hasClass('submit-on-mobile'));
        });

        $(document).on('change', '#choose-attachment-send', function (e) {
            $('.file-before-upload, .delete-file-before-upload').remove();
            const files = e.target.files[0];
            if (files) {
                let size = files.size;
                if (size / 1024 / 1024 > 5) {
                    $('.validate-message').text('File upload must not greater than 5MB');
                    return;
                }
                size = convertFileSize(size);
                const fileTemplate = renderHtmlTemplate('file-send-template', {
                    filename: escapeHtml(files.name) + '123',
                    filesize: size,
                    download_url: 'javascript:;',
                    target: '_self'
                });
                let html = `
                    <div class="file-before-upload mr-2">
                        ${fileTemplate}
                    </div>
                    <span class="delete-file-before-upload">&times;</span>`;

                $('.container-file-before-upload').html(html);
                $("html, body").animate({scrollTop: $(document).height()}, 1000);
            }
        });

        $(document).on('click', '.delete-file-before-upload', function () {
            $('#choose-attachment-send').val('');
            $('.file-before-upload, .delete-file-before-upload').remove();
        });

        $('.content-chat-msg-send').keypress(function (e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                message.submitChatMessage($(this).parent().hasClass('send-message-wrapper-mobile'));
            }
        });

        $('.container-message .chat-wrapper').scroll(function () {
            if ($('.container-message .chat-wrapper').scrollTop() === 0) {
                const messageId = $('.chat-wrapper .message-chat-box').eq(0).data('id');
                const loadElement = $('.is-load-more-msg');
                if (loadElement.eq(0).val() === '0') {
                    message.loadMoreMessage({messageId});
                }
            }
        });

        $(document).on('keyup', '#modal-list-user-chat .filter-user-chat', function () {
            const keyword = $(this).val().trim();
            message.searchListUserChat({ keyword });
        });

        $('#modal-list-user-chat .list-user-chat').scroll(function () {
            const element = $('#modal-list-user-chat .list-user-chat');
            const params = {
                keyword: $('#modal-list-user-chat .filter-user-chat').val().trim(),
                offset: $('#modal-list-user-chat .list-user-chat .user-chat').length
            }
            if (element.scrollTop() + element.innerHeight() === $(this)[0].scrollHeight) {
                if ($('.is-load-more-user:last').val() === '0') {
                    message.searchListUserChat(params);
                }
            }
        });

        $(document).on('click', '.sidebar-message #btn-action-message', function () {
            $('#modal-list-user-chat .filter-user-chat').val('');
            const keyword = '';
            message.searchListUserChat({ keyword });
            $('#modal-list-user-chat').modal('show');
        });
    },

    searchListUserChat: (data) => {
        $.ajax({
            url: '/message/list-user',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: 'GET',
            data,
            datatype: 'json'
        }).done((response) => {
            if (data.offset) {
                $('#modal-list-user-chat .list-user-chat').append(response.html);
            } else {
                $('#modal-list-user-chat .list-user-chat').html(response.html);
            }
        });
    },

    loadMoreMessage: (data) => {
        $.ajax({
            url: window.location.pathname,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: 'GET',
            data,
            datatype: 'json'
        }).done((response) => {
            $('.container-message .chat-wrapper').prepend(response.html).find('.reacts-wrapper:not(.initialed)').initEmoji();
            const userId = getIdOnUrl();
            const scrStatus = $(`.list-user-chat .user-chat-message[data-id="${userId}"] img.status-user`).attr('src');
            $('.chat-wrapper .message-receive img.status-user[src!="${scrStatus}"]').attr('src', scrStatus);
            $('.chat-wrapper').animate({
                scrollTop: $('#scroll-to-div').offset().top
            }, 0);
            $('.chat-wrapper .message-chat-box').attr('id', '');
            const element = $(`.group-msg-by-day:contains(${response.time})`);
            if (element.length > 1) {
                element.eq(1).remove();
            }
        });
    },

    submitChatMessage: (devise) => {
        $('.validate-message').text('');
        let contentMsg = $('#form-submit-chat-message .content-chat-msg-send').eq(0);
        if (devise) {
            contentMsg = $('#form-submit-chat-message .content-chat-msg-send').eq(1);
        }
        const msgText = contentMsg.val().trim();
        const msgFile = $('#choose-attachment-send').val();
        if (!msgText && msgFile === '') {
            return;
        }
        if (msgText.length > 10000) {
            $('.validate-message').text('The content message must not be greater than 10000 characters.');
            return;
        }
        contentMsg.attr('name', 'message');
        const formData = new FormData($('#form-submit-chat-message')[0]);
        $('#form-submit-chat-message .content-chat-msg-send, #choose-attachment-send').val('');
        const idsTemp = renderMessageSend(msgText, msgFile);
        message.createMessage(formData, idsTemp);
        message.renderSidebarChat(msgText, msgFile);
    },

    createMessage: (data, idsTemp) => {
        const id = getIdOnUrl();
        $.ajax({
            url: '/message/chat/' + id,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: 'POST',
            data: data,
            contentType: false,
            processData: false,
        }).done((response) => {
            const data = response.data || {};
            if ('message' in data && data.message) {
                $('.chat-wrapper').find(`.reacts-wrapper[data-id="${idsTemp.messageId}"]`).attr('data-id', data.message.id || idsTemp.messageId);
            }
            if ('messageFile' in data && data.messageFile) {
                $(`.message-chat-box[data-id="${idsTemp.fileId}"]`).find('.download-url').attr('href', data.messageFile.file.download_url || 'javascript:;').attr('target', data.messageFile.file.download_url ? '_blank' : '_self');
                $('.chat-wrapper').find(`.reacts-wrapper[data-id="${idsTemp.fileId}"]`).attr('data-id', data.messageFile.id || idsTemp.fileId);
            }
        }).fail(() => {
            toastr.error('Send message failed.')
        });
    },

    searchUserChat: async (paramSearchUserChat) => {
        $.ajax({
            url: '/message',
            method: 'GET',
            dataType: 'json',
            data: paramSearchUserChat,
        }).done((response) => {
            if (response.html) {
                $('.list-user-chat').html(response.html);
            }
        }).fail(() => {
            toastr.error('Find user fail!');
        })
    },

    markIsReadMessage: () => {
        const id = getIdOnUrl();
        $.ajax({
            url: '/message/mark-is-read',
            method: 'POST',
            dataType: 'json',
            data: { id },
        }).done((response) => {
            if (response.count > 0) {
                $('#compact-number-unread-message').css('display', 'block').text(response.count);
            }
        });
    },

    renderSidebarChat: (text, file) => {
        const srcAvatar = $('.avatar-header-chat img').eq(0).attr('src');
        const name = $('.header-body .user-name').eq(0).text();
        const email = $('.header-body .email-direct-user').val();
        const url = new URL(window.location.href);
        const receiverId = getIdOnUrl();
        let href = url.href.split('/');
        href.pop();
        let newUrl = href.join('/') + '/' + receiverId;
        let content = escapeHtml(text);
        const srcStatus = $('.tag__status-online img').data('status') === 0 ? $('img.src-img-offline-user').attr('src') : $('img.src-img-online-user').attr('src')
        if (file) {
            content = '<span class="font-14 font-weight-500 color-2200A5">You sent an attachment.</span>';
        }
        const html = renderHtmlTemplate('sidebar-item-template', {
            id: receiverId,
            url: newUrl,
            srcAvatar: srcAvatar,
            name: escapeHtml(name),
            email: escapeHtml(email),
            time: moment().fromNow(),
            content: content,
            srcStatus: srcStatus
        });

        $(`.sidebar-message .list-message .user-chat-message[data-id="${receiverId}"]`).remove();
        $('.sidebar-message .list-message').prepend(html);
        $('.header-sidebar .total-user').text($('.sidebar-message .list-message .user-chat-message').length);
    },
}

function convertFileSize(size) {
    const UNIT = 1024;
    if (size < UNIT * UNIT) {
        size = (size / UNIT).toFixed(1) + 'KB';
    } else {
        size = (size / UNIT / UNIT).toFixed(1) + 'MB';
    }
    return size;
}

function channelChatRoom() {
    if (isExistReceiver()) {
        const senderId = getIdOnUrl();
        Echo.private('chatroom.' + senderId + '.' + RECEIVER_ID)
            .listen('ChatMessage', (data) => {
                renderMessageReceiver(data.message);
                message.markIsReadMessage();
            });
        Echo.private('chatroom.' + RECEIVER_ID + '.' + RECEIVER_ID)
            .listen('ChatMessage', (data) => {
                renderMessageMySelf(data.message);
            });
    }
}

function channelOverviewChat() {
    if (isExistReceiver()) {
        Echo.private('chat_overview.' + RECEIVER_ID)
            .listen('OverviewMessage', (data) => {
                renderOverviewChat(data);
            });
    }
}

function renderOverviewChat(data) {
    const senderId = getIdOnUrl();
    if (data.countUnRead > 0 && parseInt(senderId) !== data.message.sender_id) {
        $('#compact-number-unread-message').css('display', 'block').text(data.countUnRead);
    }
    $(`.sidebar-message .list-message .user-chat-message[data-id="${data.message.sender_id}"]`).remove();
    $('.sidebar-message .list-message').prepend(data.view);
    $('.header-sidebar .total-user').text($('.sidebar-message .list-message .user-chat-message').length);
}

function renderMessageMySelf(message) {
    let html = '';
    const messageId = randomId();
    const fileId = randomId();
    if (message.text.length !== 0) {
        html += renderHtmlTemplate('message-type-default-send-template', {
            time: moment().format('HH:mm'),
            message: escapeHtml(message.text.message).replace(/\r?\n/g, '<br/>'),
            id: messageId,
        });
    }
    if (message.file.length !== 0) {
        const fileTemplate = renderHtmlTemplate('file-send-template', {
            filename: escapeHtml(message.file.file.name),
            filesize: convertFileSize(message.file.file.size),
            download_url: message.file.file.download_url,
            target: message.file.file.download_url ? '_blank' : '_self',
        });
        html += renderHtmlTemplate('message-type-file-send-template', {
            time: moment().format('HH:mm'),
            message: fileTemplate,
            id: fileId,
        });
    }
    renderLineDay();
    $('.chat-wrapper')
        .append(html)
        .scrollTop($('.chat-wrapper')[0].scrollHeight)
        .last()
        .find(`.reacts-wrapper[data-id="${messageId}"], .reacts-wrapper[data-id="${fileId}"]`)
        .initEmoji();
    if (message.text.length !== 0) {
        $('.chat-wrapper').find(`.reacts-wrapper[data-id="${messageId}"]`).attr('data-id', message.text.id || messageId);
    }
    if (message.file.length !== 0) {
        $('.chat-wrapper').find(`.reacts-wrapper[data-id="${fileId}"]`).attr('data-id', message.file.message.id || fileId);
    }

}

function renderMessageSend(text, file) {
    let html = '';
    const messageId = randomId();
    const fileId = randomId();
    if (text) {
        html += renderHtmlTemplate('message-type-default-send-template', {
            time: moment().format('HH:mm'),
            message: escapeHtml(text).replace(/\r?\n/g, '<br/>'),
            id: messageId,
        });
    }
    if (file) {
        const htmlFile = $('.file-before-upload').html();
        html += renderHtmlTemplate('message-type-file-send-template', {
            time: moment().format('HH:mm'),
            message: htmlFile,
            id: fileId,
        });
    }

    $('.file-before-upload, .delete-file-before-upload').remove();
    renderLineDay();
    $('.chat-wrapper')
        .append(html)
        .scrollTop($('.chat-wrapper')[0].scrollHeight)
        .last()
        .find(`.reacts-wrapper[data-id="${messageId}"], .reacts-wrapper[data-id="${fileId}"]`)
        .initEmoji();

    return {
        messageId,
        fileId
    };
}

function renderMessageReceiver(message) {
    const srcImg = $('.avatar-header-chat img.w-100').attr('src');
    let html = '';
    let id = randomId();
    if (message.text.length !== 0) {
        let contentMsg = '';
        message.text.message.split('\n').map(val => {
            contentMsg += `<p class="text-break">${val.replace(/</g, '&lt;')}</p>`
        });
        id = message.text.id;
        html += renderHtmlTemplate('message-type-default-receive-template', {
            avatar: srcImg,
            senderName: escapeHtml(message.text.user.name),
            time: moment(message.text.message.created_at).format('HH:mm'),
            message: contentMsg,
            id: id
        });
    }
    if (message.file.length !== 0) {
        id = message.file.message.id;
        html += renderHtmlTemplate('message-type-file-receive-template', {
            avatar: srcImg,
            senderName: escapeHtml(message.file.user.name),
            time: moment(message.file.message.created_at).format('HH:mm'),
            filename: escapeHtml(message.file.file.name),
            filesize: convertFileSize(message.file.file.size),
            id: id,
            download_url: message.file.file.download_url || 'javascript:;',
            target: message.file.file.download_url ? '_blank' : '_self',
        });
    }

    const isScroll = $('.chat-wrapper')[0].scrollTop + $('.chat-wrapper')[0].clientHeight === $('.chat-wrapper')[0].scrollHeight;
    renderLineDay();
    $('.chat-wrapper')
        .append(html)
        .last()
        .find(`.reacts-wrapper[data-id="${id}"]`)
        .initEmoji();

    if (isScroll) {
        $('.chat-wrapper').scrollTop($('.chat-wrapper')[0].scrollHeight);
    }
}

function renderLineDay() {
    if ($('.group-msg-by-day:contains(Today)').length === 0) {
        $('.chat-wrapper').append('<div class="w-100 border-bottom-EAECF0 text-center font-14 color-475467 font-weight-500 group-msg-by-day">Today</div>')
    }
}

function randomId() {
    const id = Math.random().toString(36).slice(2, 7);
    if ($('.chat-wrapper').find(`.reacts-wrapper[data-id="${id}"]`).length) {
        return randomId();
    }
    return id;
}

function getIdOnUrl() {
    const url = new URL(window.location.href);
    const id = url.pathname.split('/').pop() || null;

    return id;
}

function isExistReceiver() {
    return typeof RECEIVER_ID !== 'undefined';
}

export function handleUserStatusOnline(user, status) {
    if ('id' in user && isExistReceiver() && user.id != RECEIVER_ID) {
        const src = status == ONLINE_STATUS ? SRC_IMG_ONLINE_STATUS : SRC_IMG_OFFLINE_STATUS;
        $(`.list-user-chat .user-chat-message[data-id="${user.id || null}"] img.status-user`).attr('src', src);
        if (user.id == getIdOnUrl()) {
            const statusOnline = status == ONLINE_STATUS ? renderHtmlTemplate('online-status-template') : renderHtmlTemplate('offline-status-template');

            $('.chat-wrapper .message-receive img.status-user').attr('src', src);
            $('#user-status-online').html(statusOnline);
        }
    }
}

export const emoji = (function ($, EmojiConvertor, RECEIVER_ID, emojis) {
    let emoji;
    let emojisContent = [];
    const REACTED_CLASS = 'has_reacted';
    let isInitial = false;

    function init() {
        if (isInitial === false) {
            $.fn.initEmoji = function () {
                if ($(this).hasClass('reacts-wrapper') && !$(this).hasClass('initialed')) {
                    if ($(this).find('.reacts-suggest').length) {
                        registerEmojiSuggest($(this).find('.reacts-suggest'));
                    }
                    if ($(this).find('.reacts-selected').length) {
                        renderEmojiSelected($(this).find('.reacts-selected'));
                    }
                }
            }

            initEmojiConvertor();
            registerReactionChannel();

            $(document).on('click', handleClickWithOutReactionSuggest);

            $(document).on('click', '.reacts-wrapper .btn-open-react img', handleShowReactionSuggest);

            $(document).on('click', '.reacts-wrapper:not(.react-textarea) .reacts-suggest .react', handleSelectReaction);

            $(document).on('click', '.reacts-wrapper:not(.react-textarea) .reacts-selected .react', handleRemoveReaction);

            $(document).on('click', '.reacts-wrapper.react-textarea .reacts-suggest .react', addIconToTextarea);

            isInitial = true;
        }
    }

    function initEmojiConvertor() {
        emoji = new EmojiConvertor();
        emoji.allow_caps = true;
        emoji.replace_mode = 'unified';
        emoji.allow_native = true;

        $.each(emojis, function (_i, {content}) {
            emojisContent.push(content);
        });

        $('.reacts-wrapper:not(.initialed)').initEmoji();
    }

    function registerEmojiSuggest(selector) {
        $(selector).html(() => {
            let html = '';
            $.each(emojis, function (_i, { id, content }) {
                html += renderHtmlTemplate('react-template', {
                            id: id,
                            content: content,
                            contentColon: emoji.replace_colons(content)
                        });
            });

            return html;
        });
        $(selector).closest('.reacts-wrapper').addClass('initialed');
    }

    function renderEmojiSelected(selector) {
        $.each($(selector), function (_i, parent) {
            let isSuccess = false;
            $.each($(parent).find('.react'), function (_j, child) {
                if (validateEmoji($(child).data('content'))) {
                    isSuccess = true;
                    $(child).html(emoji.replace_colons($(child).data('content') + $(child).html()));
                }
            });
            if (isSuccess) {
                $(parent).show();
            }
        });
    }

    function handleShowReactionSuggest() {
        let parent = $(this).parent();
        if (parent.hasClass('show')) {
            parent.removeClass('show');
        } else {
            $('.btn-open-react').removeClass('show');
            parent.addClass('show');
        }
    }

    function handleClickWithOutReactionSuggest(e) {
        if (
            !$(e.target).hasClass('reacts-suggest') &&
            !$(e.target).parent().hasClass('reacts-suggest') &&
            !$(e.target).hasClass('btn-open-react') &&
            !$(e.target).parent().hasClass('btn-open-react')
        ) {
            $('.btn-open-react').removeClass('show');
        }
    }

    function handleSelectReaction() {
        try {
            const content = $(this).data('content');
            const reactId = $(this).data('id');
            const parent = $(this).closest('.reacts-wrapper');
            const id = parent.data('id');
            const reactSelected = parent.find(`.reacts-selected .react[data-content="${content}"].${REACTED_CLASS}`);
            parent.find('.btn-open-react').removeClass('show');
            if (reactSelected.length <= 0) {
                addReaction(id, reactId, content, parent);
            } else {
                removeReaction(id, reactId, content, parent);
            }
        } catch (error) {
            toastr.error('Reaction failed.');
        }
    }

    function handleRemoveReaction() {
        try {
            const reactId = $(this).data('id');
            const content = $(this).data('content');
            const parent = $(this).closest('.reacts-wrapper');
            const id = parent.data('id');
            if ($(this).hasClass(REACTED_CLASS)) {
                removeReaction(id, reactId, content, parent);
            } else {
                addReaction(id, reactId, content, parent);
            }
        } catch (error) {
            toastr.error('Reaction failed.');
        }
    }

    function addReaction(id, reactId, content, parentWrapper) {
        if (id && !isNaN(Number(id)) && reactId && validateEmoji(content)) {
            $.ajax({
                type: "POST",
                url: `/message/${id}/reaction`,
                data: {
                    reaction: reactId
                },
                dataType: "json",
                success: function () {
                    const react = parentWrapper.find(`.reacts-selected .react[data-id="${reactId}"]`);
                    const count = Number(react.find('.count').attr('data-count')) || 0;
                    if (count == 0) {
                        parentWrapper.find('.reacts-selected').append(
                            renderHtmlTemplate('react-template', {
                                id: reactId,
                                content,
                                contentColon: emoji.replace_colons(content),
                                count: 1,
                                class: REACTED_CLASS
                            })
                        ).show();
                    } else {
                        react.addClass(REACTED_CLASS);
                        react.find('.count').attr('data-count', count + 1).text(count + 1);
                    }
                },
                error: function () {
                    toastr.error('Reaction failed.');
                }
            });
        }
    }

    function removeReaction(id, reactId, content, parentWrapper) {
        if (id && !isNaN(Number(id)) && reactId && validateEmoji(content)) {
            $.ajax({
                type: "DELETE",
                url: `/message/${id}/reaction`,
                data: {
                    reaction: reactId
                },
                dataType: "json",
                success: function () {
                    const react = parentWrapper.find(`.reacts-selected .react[data-id="${reactId}"]`);
                    const count = Number(react.find('.count').attr('data-count')) || 0;
                    if (count <= 1) {
                        react.remove();
                    } else {
                        react.removeClass(REACTED_CLASS);
                        react.find('.count').attr('data-count', count - 1).text(count - 1);
                    }
                    if (parentWrapper.find(`.reacts-selected .react`).length <= 0) {
                        parentWrapper.find(`.reacts-selected`).hide();
                    }
                },
                error: function () {
                    toastr.error('Reaction failed.');
                }
            });
        }
    }

    function validateEmoji(content) {
        return emojisContent.includes(content);
    }

    function registerReactionChannel() {
        if (RECEIVER_ID) {
            Echo.private('reaction_message.' + RECEIVER_ID)
                .listen('.push.reaction_message', (e) => {
                    try {
                        const { message } = e;
                        const enemyId = getIdOnUrl();
                        const reactsSelected = $('.chat-wrapper').find(`.reacts-wrapper[data-id="${message.id}"] .reacts-selected`);
                        if ((message.sender_id == enemyId || message.sender_id == RECEIVER_ID) && reactsSelected.length) {
                            reactsSelected.html(() => {
                                let reacts = '';
                                const data = message.sender_id == enemyId ? message.reactions : message.your_reactions;
                                $.each(data, function (i, react) {
                                    reacts +=
                                    renderHtmlTemplate('react-template', {
                                        id: react.emoji_id,
                                        content: react.emoji_content,
                                        contentColon: emoji.replace_colons(react.emoji_content),
                                        count: react.count,
                                        class: react.is_reacted ? REACTED_CLASS : ''
                                    });
                                });

                                return reacts;
                            });

                            if (reactsSelected.find('.react').length) {
                                reactsSelected.show();
                            } else {
                                reactsSelected.hide();
                            }
                        }
                    } catch (error) {
                        //error
                    }
                });
        }
    }

    function addIconToTextarea() {
        const parent = $(this).closest('.reacts-wrapper');
        const content = $(this).data('content');
        parent.find('.btn-open-react').removeClass('show');
        const textarea = $(`textarea[id="${parent.data('textarea')}"]`);
        if (textarea.length) {
            textarea.val(textarea.val() + emoji.replace_colons(content));
            textarea.trigger('focus');
        }
    }

    return { init };
})(
    jQuery,
    EmojiConvertor,
    typeof RECEIVER_ID !== 'undefined' ? RECEIVER_ID : false,
    typeof emojis !== 'undefined' ? emojis : [],
);

$(function () {
    emoji.init();
    message.init();
    channelChatRoom();
    channelOverviewChat();
});
