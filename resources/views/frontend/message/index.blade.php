@extends('frontend.layouts.app', ['chat' => true])

@section('title', __('Messages'))

@section('content')
    <div class="container-fluid container-message d-flex justify-content-between align-items-start w-100 p-0">
        <div class="sidebar-message w-30 h-100">
            <div class="d-flex flex-column justify-content-start">
                <div class="header-sidebar d-flex justify-content-between align-items-center w-90 pt-3 pb-3 pl-3">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="font-weight-600 color-000000 font-size-18 mr-2">@lang('Messages')</div>
                        <div class="font-weight-500 color-2200A5 total-user pl-2 pr-2">{{ count($lastMessages) }}</div>
                    </div>
                    <button type="button" class="d-flex justify-content-center align-items-center btn-action-message" @if(!auth()->user()->is_hidden) id="btn-action-message" @else disabled @endif>
                        <img src="{{ asset('/img/edit_msg_icon.svg') }}" alt="">
                    </button>
                    @if ($lastMessages->count())
                        <button class="collapse-sidebar-chat mr-2">
                            <img src="{{ asset('/img/backend/sidebar/option.svg') }}" alt="">
                        </button>
                    @endif
                </div>
                <input type="text" class="search-message ipt-search-user-chat w-90 color-475467 font-weight-400 font-size-16 mb-3 ml-2"
                       placeholder="@lang('Search')">
            </div>
            <div class="list-message w-100 d-flex list-user-chat flex-column align-items-start">
               @include('frontend.message.list_user_chat')
            </div>
        </div>
        <div class="body-message h-100">
            @if (isset($messages))
                @include('frontend.message.chat_box')
            @endif
        </div>
        <img src="{{ asset('/img/add_reaction.svg') }}" class="d-none src-img-reaction" alt="">
        <img src="{{ asset('/img/status_online_icon.svg') }}" class="d-none src-img-online" alt="">
    </div>
    @include('frontend.message.user_chat_modal')
@endsection

@push('before-scripts')
    <script>
        const emojis = @json($emojis);
    </script>
@endpush
