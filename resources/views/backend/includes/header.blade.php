<header id="header-content">
    <div class="long-text">
        @lang('Welcome back, :NAME', ['name' => $logged_in_user->name])
    </div>
    <button class="logout hover-button" type="button"
            data-toggle="modal" data-target="#logout"
    >
        <span>@lang('Logout')</span>
        <img src="{{ asset('/img/backend/round-right.svg') }}" alt="Icon logout">
    </button>
</header>
