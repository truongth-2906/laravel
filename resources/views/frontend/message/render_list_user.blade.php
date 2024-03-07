@forelse($users as $user)
    <a href="{{ route(USER_CHAT_MESSAGE_ROUTE, $user->id) }}">
        <div class="d-flex justify-content-start align-items-center user-chat mb-1">
            @if($user->isFreelancer())
                <img src="{{ asset($user->avatar ? $user->logo : '/img/avatar_default.svg') }}" alt="" class="avatar-chat mr-2">
            @else
                <img src="{{ asset(optional($user->company)->logo ? optional($user->company)->avatar : '/img/avatar_default.svg') }}" alt="" class="avatar-chat mr-2">
            @endif
            <div class="font-16 font-weight-500 color-475467 long-text" title="{{ $user->name }}">{{ $user->name }}</div>
        </div>
    </a>
@empty
    <div class="text-center font-weight-500 font-16 color-475467">@lang('No data')</div>
@endforelse
<input type="hidden" class="is-load-more-user" value="{{ $users->where('id', optional($latest)->id)->first() ? 1 : 0 }}">
