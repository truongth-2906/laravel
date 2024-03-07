import { renderHtmlTemplate, escapeHtml } from '../frontend/common';
import moment from 'moment';

const Message = (function ($, EmojiConvertor, emojis) {
    let searchTimeout = null;
    let emojisContent = [];
    let emoji;
    let currentPage;
    let isInitial = false;
    let hasNextPage = true;
    let isLoading = false;
    const TYPE_MESSAGE_FILE = 2;
    const TYPE_FREELANCER = 'freelancer';
    const DEFAULT_AVATAR_PATH = '/img/avatar_default.svg';

    function init(page, hasNextPageArg) {
        if (isInitial === false) {
            $.fn.initEmoji = function () {
                if ($(this).hasClass('reacts-wrapper') && !$(this).hasClass('initialed')) {
                    if ($(this).find('.reacts-selected').length) {
                        renderEmojiSelected($(this).find('.reacts-selected'));
                    }
                }
            };
            initEmojiConvertor();
            scrollToBottom();
            listener();
            page = Number(page);
            currentPage = isNaN(page) ? 1 : page;
            hasNextPage = Boolean(hasNextPageArg);
            isInitial = true;
        }
    }

    function listener() {
        $(document).on('keyup change paste', '#search-message-form input[name="search"]', search);

        $(document).on('click', '.btn-sort-date', sort);

        $('#messages-group-detail-wrapper').on('scroll', handleScrollToTop);
    }

    function sort() {
        const url = new URL(location.href);
        const searchParams = url.searchParams;
        const params = {
            search: searchParams.get('search') || '',
            order_by: $(this).data('order-by'),
            page: searchParams.get('page') || 1,
        };
        return get(params);
    }

    function search() {
        const url = new URL(location.href);
        const searchParams = url.searchParams;
        const params = {
            search: $(this).val(),
            order_by: searchParams.get('order_by') || 'DESC',
            page: searchParams.get('page') || 1,
        };
        return get(params);
    }

    function get({search = '', order_by = 'DESC', page = 1}) {
        if (searchTimeout !== null) {
            clearTimeout(searchTimeout);
        }
        searchTimeout = setTimeout(() => {
            $.ajax({
                type: "GET",
                url: "/admin/messages",
                data: {
                    search,
                    order_by,
                    page,
                },
                dataType: "json",
                success: function (response) {
                    if (response.html) {
                        $('.list-wrapper').html(response.html);
                    }
                    $('.total-message').text(`${response.total || 0} Message Group`)
                    window.history.replaceState(
                        null,
                        null,
                        `?search=${search}&order_by=${order_by}&page=${page}`
                    );
                },
                error: function () {
                    toastr.error('An error has occurred.');
                }
            });
        }, 800);
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

    function renderEmojiSelected(selector) {
        $.each($(selector), function (_i, parent) {
            let isSuccess = false;
            $.each($(parent).find('.react'), function (_j, child) {
                $(child).tooltip('update')
                if (validateEmoji($(child).data('content'))) {
                    isSuccess = true;
                    $(child).html(emoji.replace_colons($(child).data('content') + $(child).html()));
                }
            });
            if (isSuccess) {
                $(parent).show();
            }
        });
        $(selector).closest('.reacts-wrapper').addClass('initialed');
    }

    function validateEmoji(content) {
        return emojisContent.includes(content);
    }

    function scrollToBottom() {
        if ($("#messages-group-detail-wrapper").length) {
            $("#messages-group-detail-wrapper").scrollTop($("#messages-group-detail-wrapper")[0].scrollHeight);
        }
    }

    function loadMore() {
        if (hasNextPage && currentPage && isLoading === false) {
            isLoading = true;
            $.ajax({
                type: "GET",
                url: `${location.pathname}?page=${currentPage + 1}`,
                dataType: "json",
                success: function (response) {
                    if (response.data) {
                        prependMessage(response.data);
                    }
                    currentPage = Number(response.current_page);
                    hasNextPage = Boolean(response.has_next_page);
                },
                error: function (response) {
                    toastr.error('An error has occurred.');
                },
                complete: function () {
                    isLoading = false;
                }
            });
        }
    }

    function handleScrollToTop() {
        const top = Number($(this).scrollTop());
        if (!isNaN(top) && top === 0) {
            return loadMore();
        }
    }

    function prependMessage(data) {
        let firstChild = $(`#messages-group-detail-wrapper > div`).first();
        let date = null;
        let reCalculateCurrentPosition = function () {
            return $("#messages-group-detail-wrapper").scrollTop() + firstChild.position().top + (firstChild.hasClass('day-line') ? 20 : 0);
        };

        $.each(data, function (i, item) {
            let currentPosition = reCalculateCurrentPosition();
            let isLastEach = i == Object.keys(data).length - 1;
            let createdDate = moment(item.created_at).format('YYYY-MM-DD');

            if ($(`.message-item[data-index="${item.id}"]`).length == 0) {
                try {
                    if (date == null) {
                        date = createdDate.toString();
                    }

                    if ((date != createdDate.toString() || date == createdDate.toString() && isLastEach) && firstChild.hasClass('day-line') && firstChild.data('date') == date) {
                        firstChild = firstChild.next().first();
                    }

                    $('#messages-group-detail-wrapper').prepend(renderMessageHtmlTemplate(item));
                    $('.reacts-wrapper:not(.initialed)').initEmoji();
                    currentPosition = reCalculateCurrentPosition();
                    $("#messages-group-detail-wrapper").scrollTop(currentPosition);

                    //append day line
                    if (date != createdDate.toString() || date == createdDate.toString() && isLastEach) {
                        $(`.day-line[data-date="${date}"]`).remove();

                        if (date != createdDate.toString()) {
                            $('#messages-group-detail-wrapper > div').first().after(`<div class="day-line" data-date="${date}">${formatDateChat(date)}</div>`);
                            date = createdDate.toString();
                        }

                        if (date == createdDate.toString() && isLastEach) {
                            $('#messages-group-detail-wrapper').prepend(`<div class="day-line" data-date="${date}">${formatDateChat(date)}</div>`);
                        }
                    }
                } catch (error) {
                    //catch error
                }
            }
            if (isLastEach && currentPosition != 0) {
                $("#messages-group-detail-wrapper").animate({scrollTop: currentPosition - 300});
            }
        });
    }

    function renderMessageHtmlTemplate(item) {
        const pathname = location.pathname.replace(/^\/|\/$/g, '').split('/');
        const senderId = pathname[2];
        const dateCreated = new Date(item.created_at);
        let reactionsHtml = '';

        $.each(item.reactions, function (_i, react) {
            reactionsHtml += renderHtmlTemplate('react-item-template', {
                emoji_content: react.emoji_content,
                title: react.title,
                count: react.count
            });
        });

        let options = {
            index: item.id,
            align: item.sender_id == senderId ? 'message-left' : 'message-right',
            avatar: item.sender.type == TYPE_FREELANCER ? (item.sender.avatar ? item.sender.logo : DEFAULT_AVATAR_PATH) : (item.sender?.company?.logo ? item.sender?.company?.avatar : DEFAULT_AVATAR_PATH),
            username: item.sender.name,
            time: `${dateCreated.getHours()}:${dateCreated.getMinutes()}`,
            reactions: reactionsHtml,
            watched_status: item.is_read ? 'Seen' : 'Not Seen'
        };
        let tmp = 'default-message-template';

        if (item.type == TYPE_MESSAGE_FILE && item.file !== null) {
            tmp = 'file-message-template';
            options = {
                ...options,
                filename: escapeHtml(item.file.name),
                file_size: convertFileSize(item.file.size),
                download_url: item.file.download_url
            }
        } else {
            options = {
                ...options,
                content: escapeHtml(item.message),
            }
        }

        return renderHtmlTemplate(tmp, options);
    }

    function convertFileSize(size)
    {
        if (!size) {
            return 0;
        }
        const unit = 1024;
        if (size < unit * unit) {
            size = Math.round(size / unit, 1) + 'KB';
        } else {
            size = Math.round(size / unit / unit, 1) + 'MB';
        }
        return size;
    }

    function formatDateChat(date)
    {
        const dateMoment = moment(date);
        const currentDate = moment(new Date());
        if (dateMoment.isSame(currentDate)) {
            return 'Today';
        } else {
            if (currentDate.diff(dateMoment, 'days') == 1) {
                return 'Yesterday';
            }
        }
        return dateMoment.format('DD MMM YYYY');
    }

    return { init };
})(
    jQuery,
    EmojiConvertor,
    typeof emojis !== 'undefined' ? emojis : [],);

$(function () {
    Message.init(typeof page !== 'undefined' ? page : 1, typeof hasNextPage !== 'undefined' ? hasNextPage : true);
});
