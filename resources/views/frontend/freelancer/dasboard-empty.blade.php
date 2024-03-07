<div class="content-dashboard">
    <img class="icon-delete pc-device" src="{{ asset('/img/features-icon-search.svg') }}" alt="">
    <div
        class="font-size-16 font-weight-600 font-content text-color-content mt-3">@lang("No recommended Jobs Found")</div>
    <div
        class="font-size-14 font-weight-400">@lang("Find some jobs on the portal or edit your profile to make yourself more appealing to employers.")</div>
    <div class="d-flex justify-content-center align-items-center">
        <button
            class="btn btn-general-action d-flex justify-content-start align-items-center mr-3 hover-button">
            <img src="{{ asset('/img/search-icon.svg') }}" alt="" class="mr-2">
            <div class="color-2200A5 font-14 font-weight-bold">@lang('FIND JOBS')</div>
        </button>
        <a href="#"
           class="btn btn-general-action d-flex justify-content-center align-items-center hover-button">
            <img src="{{ asset('/img/backend/sidebar/setting.svg') }}" alt="" class="mr-2">
            <div class="color-2200A5 font-14 font-weight-bold">@lang('EDIT YOUR PROFILE')</div>
        </a>
    </div>
</div>
