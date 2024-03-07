<div id="sidebar" class="d-flex flex-column">
    <div class="w-100">
        <div class="icon-dashboard cursor-pointer mt-0">
            <div id="sidebar-mobile" class="pl-0">
                <button type="button">
                    <img src="{{ asset('/img/backend/sidebar/option.svg') }}" alt="Icon option">
                </button>
            </div>
            <div class="pt-3 pl-3">
                <a href="/">
                    <img src="{{ asset('/img/backend/sidebar/automator.svg') }}" alt="Icon dashboard">
                </a>
            </div>
        </div>

        <div class="menu-sidebar">
            <div
                class="{{ Route::is(FREELANCER_INDEX) || Route::is(EMPLOYER_INDEX) || Route::is(FREELANCER_JOB_DETAIL) ? 'tab-active' : '' }} cursor-pointer"
                onclick="location.href='/'">
                <a href="/" class="d-flex justify-content-start align-items-center">
                    <div class="icon icon-home filter-color-active"></div>
                    <div class="color-active">@lang('Home')</div>
                </a>
            </div>
            @if ($logged_in_user->isEmployer())
                <div class="{{ Route::is(JOB_INDEX) ? 'tab-active' : '' }} cursor-pointer"
                     onclick="location.href='{{ route(JOB_INDEX) }}'">
                    <a href="{{ route(JOB_INDEX) }}" class="d-flex justify-content-start align-items-center">
                        <div class="icon icon-freelancer filter-color-active"></div>
                        <div class="color-active">@lang('Available Jobs')</div>
                    </a>
                </div>
            @endif
            @if ($logged_in_user->isEmployer())
                <div class="{{ Route::is(EMPLOYER_PAYMENT_INDEX) ? 'tab-active' : '' }} cursor-pointer"
                     onclick="location.href='{{ route(EMPLOYER_PAYMENT_INDEX) }}'">
                    <a href="{{ route(EMPLOYER_PAYMENT_INDEX) }}"
                       class="d-flex justify-content-start align-items-center">
                        <div class="icon icon-employer filter-color-active"></div>
                        <div class="color-active">@lang('Billing and Payments')</div>
                    </a>
                </div>
            @else
                <div class="{{ Route::is(FREELANCER_PAYMENT_INDEX) ? 'tab-active' : '' }} cursor-pointer"
                     onclick="location.href='{{ route(FREELANCER_PAYMENT_INDEX) }}'">
                    <a href="{{ route(FREELANCER_PAYMENT_INDEX) }}"
                       class="d-flex justify-content-start align-items-center">
                        <div class="icon icon-employer filter-color-active"></div>
                        <div class="color-active">@lang('Billing and Payments')</div>
                    </a>
                </div>
            @endif
            @if ($logged_in_user->isEmployer())
                <div class="{{ Route::is(EMPLOYER_CREATE_JOB) ? 'tab-active' : '' }} cursor-pointer"
                     onclick="location.href='{{ route(EMPLOYER_CREATE_JOB) }}'">
                    <a href="{{ route(EMPLOYER_CREATE_JOB) }}" class="d-flex justify-content-start align-items-center">
                        <div class="icon icon-job filter-color-active"></div>
                        <div class="color-active">@lang('Create a Job')</div>
                    </a>
                </div>
                <div class="{{ Route::is(FIND_FREELANCER) ? 'tab-active' : '' }} cursor-pointer"
                     onclick="location.href='{{ route(FIND_FREELANCER) }}'">
                    <a href="{{ route(FIND_FREELANCER) }}" class="d-flex justify-content-start align-items-center">
                        <div class="icon icon-search filter-color-active"></div>
                        <div class="color-active">@lang('Find Freelancers')</div>
                    </a>
                </div>
                <div
                    class="{{ Route::is(SAVED_FREELANCER_ROUTER) || Route::is(SAVED_JOB_ROUTER) ? 'tab-active' : '' }} cursor-pointer"
                    onclick="location.href='{{ Route::is(SAVED_JOB_ROUTER) ? route(SAVED_JOB_ROUTER) : route(SAVED_FREELANCER_ROUTER) }}'">
                    <a href="{{ Route::is(SAVED_JOB_ROUTER) ? route(SAVED_JOB_ROUTER) : route(SAVED_FREELANCER_ROUTER) }}"
                       class="d-flex justify-content-start align-items-center">
                        <div class="icon icon-heart filter-color-active"></div>
                        <div class="color-active">@lang('Saved List')</div>
                    </a>
                </div>
            @else
                <div
                    class="{{ Route::is(FREELANCER_JOB_APPLICATIONS) || Route::is(FREELANCER_JOB_SAVED) || Route::is(FREELANCER_JOB_DONE) || Route::is(FREELANCER_JOB_DONE_PREVIEW) ? 'tab-active' : '' }} cursor-pointer"
                    onclick="location.href='{{ Route::is(FREELANCER_JOB_APPLICATIONS) ? route(FREELANCER_JOB_APPLICATIONS) : (Route::is(FREELANCER_JOB_DONE) || Route::is(FREELANCER_JOB_DONE_PREVIEW) ? route(FREELANCER_JOB_DONE) : route(FREELANCER_JOB_SAVED)) }}'">
                    <a href="{{ Route::is(FREELANCER_JOB_APPLICATIONS) ? route(FREELANCER_JOB_APPLICATIONS) : (Route::is(FREELANCER_JOB_DONE) || Route::is(FREELANCER_JOB_DONE_PREVIEW) ? route(FREELANCER_JOB_DONE) : route(FREELANCER_JOB_SAVED)) }}"
                       class="d-flex justify-content-start align-items-center">
                        <div class="icon icon-job filter-color-active"></div>
                        <div class="color-active">@lang('Job Applications')</div>
                    </a>
                </div>
            @endif
            <div
                class="cursor-pointer {{ Route::is(CHAT_MESSAGE_ROUTE) || Route::is(USER_CHAT_MESSAGE_ROUTE) ? 'tab-active' : '' }}"
                onclick="location.href='{{ route(CHAT_MESSAGE_ROUTE) }}'">
                <a href="{{ route(CHAT_MESSAGE_ROUTE) }}" class="d-flex justify-content-start align-items-center w-100">
                    <div class="icon icon-message filter-color-active"></div>
                    <div class="color-active flex-grow-1 d-flex justify-content-between align-item-center">
                        <span>@lang('Messages')</span>
                        <span class="number-unread" id="compact-number-unread-message"
                              data-number="{{ $numberUnreadMessage }}"
                              style="{{ isset($numberUnreadMessage) && $numberUnreadMessage > 0 ? '' : 'display: none;' }}">{{ $numberUnreadMessage > 100 ? '100+' : $numberUnreadMessage }}</span>
                    </div>
                </a>
            </div>
            @if ($logged_in_user->isEmployer() || $logged_in_user->isFreelancer())
                <div class="{{ Route::is('*.notifications.index') ? 'tab-active' : '' }} cursor-pointer"
                     onclick="location.href='{{ notificationRoute('index') }}'">
                    <a href="{{ notificationRoute('index') }}"
                       class="d-flex justify-content-start align-items-center w-100">
                        <div class="icon icon-notification filter-color-active"></div>
                        <div class="color-active flex-grow-1 d-flex justify-content-between align-item-center">
                            <span>@lang('Notifications')</span>
                            <span class="number-unread" id="compact-number-unread-notification"
                                  data-number="{{ $numberUnreadNotification }}"
                                  style="{{ isset($numberUnreadNotification) && $numberUnreadNotification > 0 ? '' : 'display: none;' }}">{{ $numberUnreadNotification > 100 ? '100+' : $numberUnreadNotification }}</span>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="w-100">
        <div class="menu-sidebar menu-sidebar-bottom">
            <div class="cursor-pointer" data-toggle="modal" data-target="#modal-support">
                <div class="icon icon-support"></div>
                <div>@lang('Support')</div>
            </div>
            <div
                onclick="window.location='{{ $logged_in_user->isEmployer() ? route('frontend.employer.setting') : route('frontend.freelancer.setting') }}'"
                class="{{ Route::is(FREELANCER_SETTING, EMPLOYER_SETTING) ? 'tab-active' : '' }} cursor-pointer">
                <div class="icon icon-setting filter-color-active"></div>
                <div class="color-active">@lang('Settings')</div>
            </div>
        </div>

        <div class="d-flex w-100 align-items-start group-user-info justify-content-between">
            <div class="d-flex align-items-center">
                <div class="icon-avatar-img">
                    @if ($logged_in_user->isEmployer())
                        <a href="{{ route('frontend.employer.profile', $logged_in_user->id) }}">
                            <img class="h-100"
                                 src="{{ asset(optional($logged_in_user->company)->logo ? optional($logged_in_user->company)->avatar : '/img/backend/sidebar/user-solid.svg') }}"
                                 alt="{{ $logged_in_user->email ?? '' }}">
                        </a>
                    @else
                        <a href="{{ route('frontend.freelancer.profile', $logged_in_user->id) }}">
                            <img class="h-100"
                                 src="{{ asset($logged_in_user->avatar ? $logged_in_user->logo : '/img/backend/sidebar/user-solid.svg') }}"
                                 alt="{{ $logged_in_user->email ?? '' }}">
                        </a>
                    @endif
                </div>
                <div class="name-and-mail-sidebar">
                    <div>{{ $logged_in_user->name }}</div>
                    <div>{{ $logged_in_user->email }}</div>
                </div>
            </div>
            <button type="button" class="btn-logout" data-toggle="modal" data-target="#logout">
                <img src="/img/backend/sidebar/logout.svg" alt="Icon logout">
            </button>
        </div>
    </div>
</div>
<div id="sidebar-mobile" class="bg-white w-100">
    <div class="icon-dashboard">
        <img src="{{ asset('/img/backend/sidebar/automator.svg') }}" alt="Icon dashboard">
    </div>
    <button type="button">
        <img src="{{ asset('/img/backend/sidebar/option.svg') }}" alt="Icon option">
    </button>
</div>
@include('frontend.support.modal-support')
