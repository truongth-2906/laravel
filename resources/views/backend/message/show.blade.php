@extends('backend.layouts.app')

@section('title', __('Manage Messages'))

@section('content')
    <div class="container-fluid container-freelancer pl-0 pr-0 mb-5">
        <div class="w-100 header-freelancer">
            <div class="d-flex flex-column align-items-start">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="color-000000 font-18 font-weight-bold mr-2">@lang('Message Detail')</div>
                </div>
            </div>
        </div>

        <div class="w-100 messages-group-detail-wrapper" id="messages-group-detail-wrapper">
            @php
                $date = null;
            @endphp
            @forelse ($messages->reverse() as $message)
                @if ($date != date('Ymd', strtotime($message->created_at)))
                    <div class="day-line" data-date="{{ $message->created_at->format('Y-m-d') }}">{{ formatDateChat($message->created_at) }}</div>
                    @php
                        $date = date('Ymd', strtotime($message->created_at));
                    @endphp
                @endif

                @if (!is_null($message->file) && $message->type == TYPE_MESSAGE_FILE)
                    @include('backend.message.file-message')
                @else
                    @include('backend.message.default-message')
                @endif
            @empty
                <div class="w-100 text-danger text-center py-5">@lang('No data')</div>
            @endforelse
        </div>
    </div>

    <script type="text/template" data-template="default-message-template">
        <div data-index="${index}" class="message-item ${align}">
            <div class="user-avatar">
                <img src="${avatar}" alt="user-avatar rounded-56" width="40px" height="40px">
            </div>
            <div class="message-details">
                <div class="user-name-and-time">
                    <span class="user-name" title="${username}">${username}</span>
                    <span class="time">${time}</span>
                </div>
                <div class="message-content">${content}</div>
                <div class="reacts-wrapper">
                    <div class="reacts-selected" style="display: none;">
                        ${reactions}
                    </div>
                </div>
                <span class="watched-status">${watched_status}</span>
            </div>
        </div>
    </script>

    <script type="text/template" data-template="file-message-template">
        <div data-index="${index}" class="message-item ${align}">
            <div class="user-avatar">
                <img src="${avatar}" alt="user-avatar rounded-56" width="40px" height="40px">
            </div>
            <div class="message-details">
                <div class="user-name-and-time">
                    <span class="user-name" title="${username}">${username}</span>
                    <span class="time">${time}</span>
                </div>
                <div class="message-content type-file">
                    <div class="file-icon">
                        <img src="{{ asset('/img/pdf-icon.svg') }}" alt="user-avatar" width="15px" height="15px">
                    </div>
                    <div class="file-detail">
                        <a href="${download_url}" target="_blank" class="filename download-url"
                            title="${filename}">${filename}</a>
                        <div class="file-size">${file_size}</div>
                    </div>
                </div>
                <div class="reacts-wrapper">
                    <div class="reacts-selected" style="display: none;">
                        ${reactions}
                    </div>
                </div>
                <span class="watched-status">${watched_status}</span>
            </div>
        </div>
    </script>

    <script type="text/template" data-template="react-item-template">
        <span class="react" data-content="${emoji_content}" title="${title} reacted." data-toggle="tooltip" data-placement="top">
            <span class="count" data-count="${count}">${count}</span>
        </span>
    </script>
@endsection

@push('before-scripts')
    <script>
        const emojis = @json($emojis);
        const page = {{ $messages->currentPage() }};
        const hasNextPage = {{ $messages->nextPageUrl() ? 'true' : 'false' }};
    </script>
@endpush
