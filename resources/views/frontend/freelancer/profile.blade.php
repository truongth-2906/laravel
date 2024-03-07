@extends('frontend.layouts.app')

@section('title', __('Profile Freelancer'))

@section('content')
    <div class="container flex-column wrapper">
        <div class="cover-photo position-relative">
            <div class="avatar-profile flex-center">
                <img class="w-100 rounded-circle h-100"
                     src="{{ asset($freelancer->avatar ? $freelancer->logo : '/img/avatar_default.svg') }}"
                     alt="Company logo">
            </div>
        </div>
        <div class="{{ $freelancer->active == IS_ACTIVE  ? 'icon-verified-profile' : '' }}"></div>
        <div class="w-100 avatar-content d-flex justify-content-end">
            <div class="d-flex justify-content-between form-title-profile title-send pb-3">
                <div class="columns sp-title">
                    <div class="font-weight-600 font-size-30 color-2200A5 name-text">{{ $freelancer->name }}</div>
                    @if($freelancer->country_id)
                        <div
                            class="name-company">@lang("I'm a freelancer based in") {{ $freelancer->country->name }}</div>
                    @endif
                </div>
                <div class="flex-center flex-column">
                    <a href="{{ !auth()->user()->is_hidden ? route(USER_CHAT_MESSAGE_ROUTE, $freelancer->id) : 'javascript:;' }}"
                        class="btn btn-general-action d-flex justify-content-center align-items-center text-decoration-none {{ !auth()->user()->is_hidden ? 'hover-button' : 'cursor-not-allow' }} sp-btn-send mt-3">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('SEND MESSAGE')</div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row col-md-12 mt-title border-top pl-0 pr-0 mt-4">
            <div class="col-md-6 pt-4 pl-0 pr-0">
                <div class="profile-title mb-2">
                    @lang("About me")
                </div>
                <p class="show-read-more">
                    {!! nl2br($freelancer->bio) !!}
                </p>
            </div>
            <div class="col-md-6 pt-4 pl-0 pr-0 location">
                <div class="profile-title mb-2">
                    @lang("Location")
                </div>
                <div class="row ml-btn">
                    @if(optional($freelancer->country)->name)
                        <img src="{{ asset('img/country/' . optional($freelancer->country)->code . '.png') }}" alt=""
                             class="img-size mr-2">
                        <div>
                            {{ optional($freelancer->country)->name }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="columns-section mt-5 form-group-base form-exp">
            <div class="columns-section-child">
                <div class="profile-title">@lang("RPA Software")</div>
                <div class="d-flex mt-3">
                    @foreach($freelancer->categories as $category)
                        <div
                            class="status-category mr-2 job-requirement-tag mb-1 mt-1  max-width-content {{ $category->class }}">
                            {{ $category->name }}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="columns-section-child sp-mt-20">
                <div class="profile-title">@lang("RPA Experience")</div>
                <div class="mt-3">
                    @if($freelancer->experience_id)
                        <div class="status-category mr-2 job-requirement-tag mb-1 mt-1  max-width-content">
                            {{ optional($freelancer->experience)->name }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="columns-section mt-5 form-group-base form-exp">
            <div class="columns-section-child sp-mt-20">
                <div class="profile-title">@lang("Portfolio projects")</div>
                <div class="d-flex flex-column font-weight-600 mt-3">
                    @foreach($freelancer->portfolios as $portfolio)
                        <a class="pb-2 color-000000" href="{{ route('frontend.download', ['filename' => $portfolio->file]) }}" target="_blank">
                            <div class="d-flex">
                                @if(explode('.',$portfolio->file)[1] == 'pdf')
                                    <img src="{{ asset('/img/icon-file-pdf.svg') }}" alt="pdf">
                                    <span class="ml-2">
                                        {{ $portfolio->name }}
                                    </span>
                                @else
                                    <img src="{{ asset('/img/icon-file-doc.svg') }}" alt="doc,docx">
                                    <span class="ml-2">
                                        {{ $portfolio->name }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
