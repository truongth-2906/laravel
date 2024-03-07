<div id="sidebar" class="d-flex flex-column">
    <div class="w-100">
        <div class="icon-dashboard cursor-pointer">
            <a href="{{ route('admin.freelancer.index') }}">
                <img src="{{ asset('/img/backend/sidebar/automator.svg') }}" alt="Icon dashboard">
            </a>
        </div>

        <div class="menu-sidebar">
            <div class="{{ Route::is(ADMIN_DASHBOARD) ? 'tab-active' : '' }} cursor-pointer"
                 onclick="location.href='{{ route('admin.dashboard') }}'">
                <a href="{{ route('admin.dashboard') }}" class="d-flex justify-content-start align-items-center">
                    <div class="icon icon-home filter-color-active"></div>
                    <div class="color-active">@lang('Home')</div>
                </a>
            </div>
            <div class="{{ Route::is(ADMIN_FREELANCER_LIST) ? 'tab-active' : '' }} cursor-pointer"
                 onclick="location.href='{{ route('admin.freelancer.index') }}'">
                <a href="{{ route('admin.freelancer.index') }}"
                   class="d-flex justify-content-start align-items-center">
                    <div class="icon icon-freelancer filter-color-active"></div>
                    <div class="color-active">@lang('Manage Freelancers')</div>
                </a>
            </div>
            <div class="{{ Route::is(ADMIN_EMPLOYER_LIST) ? 'tab-active' : '' }} cursor-pointer"
                 onclick="location.href='{{ route('admin.employer.index') }}'">
                <a href="{{ route('admin.employer.index') }}"
                   class="d-flex justify-content-start align-items-center">
                    <div class="icon icon-employer filter-color-active"></div>
                    <div class="color-active">@lang('Manage Employers')</div>
                </a>
            </div>
            <div class="{{ Route::is(ADMIN_JOB_LIST) ? 'tab-active' : '' }} cursor-pointer"
                 onclick="location.href='{{ route('admin.job.index') }}'">
                <a href="{{ route('admin.job.index') }}" class="d-flex justify-content-start align-items-center">
                    <div class="icon icon-job filter-color-active"></div>
                    <div class="color-active">@lang('Manage Jobs')</div>
                </a>
            </div>
            <div class="{{ Route::is('admin.messages.*') ? 'tab-active' : '' }} cursor-pointer"
                 onclick="location.href='{{ route('admin.messages.index') }}'">
                <a href="{{ route('admin.messages.index') }}" class="d-flex justify-content-start align-items-center">
                    <div class="icon icon-message filter-color-active"></div>
                    <div class="color-active">@lang('Manage Messages')</div>
                </a>
            </div>
            <div class="{{ Route::is('admin.vouchers.*') ? 'tab-active' : '' }} cursor-pointer"
                 onclick="location.href='{{ route('admin.vouchers.index') }}'">
                <a href="{{ route('admin.vouchers.index') }}" class="d-flex justify-content-start align-items-center">
                    <div class="icon icon-voucher filter-color-active"></div>
                    <div class="color-active">@lang('Manage Voucher')</div>
                </a>
            </div>
        </div>
    </div>

    <div class="w-100">
        <div class="menu-sidebar menu-sidebar-bottom">
            <div class="cursor-pointer">
                <div class="icon icon-support"></div>
                <div>@lang('Support')</div>
            </div>
            <div class="{{ Route::is(ADMIN_SETTING_LIST) ? 'tab-active' : '' }} cursor-pointer"
                 onclick="location.href='{{ route('admin.setting.password') }}'">
                <a href="{{ route('admin.setting.password') }}" class="d-flex justify-content-start align-items-center">
                    <div class="icon icon-job filter-color-active"></div>
                    <div class="color-active">@lang('Settings')</div>
                </a>
            </div>
        </div>

        <div class="d-flex w-100 align-items-start group-user-info justify-content-between">
            <div class="d-flex align-items-center">
                <div class="icon-avatar-img">
                    <img src="/img/backend/sidebar/user-solid.svg" alt="{{ $logged_in_user->email ?? '' }}">
                </div>
                <div class="name-and-mail-sidebar">
                    <div>{{ $logged_in_user->name }}</div>
                    <div>{{ $logged_in_user->email }}</div>
                </div>
            </div>
            <button type="button" class="btn-logout cursor-pointer" data-toggle="modal" data-target="#logout">
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
