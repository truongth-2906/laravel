@extends('frontend.layouts.app')

@section('title', __('Profile Employer'))

@section('content')
    <div class="container flex-column wrapper">
        <div class="cover-photo position-relative">
            <div class="avatar-profile flex-center">
                <img class="w-100 rounded-circle h-100"
                     src="{{ asset(optional($employer->company)->logo ? optional($employer->company)->avatar : '/img/avatar_default.svg') }}"
                     alt="Company logo">
            </div>
        </div>
        <div class="position-relative {{ $employer->active == IS_ACTIVE  ? 'icon-verified-profile' : '' }}"></div>
        <div class="w-100 avatar-content d-flex justify-content-end">
            <div class="d-flex justify-content-between form-title-profile title-send pb-3">
                <div class="columns sp-title">
                    <div class="font-weight-600 font-size-30 color-2200A5 name-text">{{ $employer->name }}</div>
                    @if($employer->company_id && $employer->country_id)
                        <div
                            class="name-company">@lang('We are') {{ $employer->company->name }} @lang('in the industry based in') {{ $employer->country->name }}</div>
                    @endif
                </div>
                <div class="flex-center flex-column">
                    <a href="{{ !auth()->user()->is_hidden ? route(USER_CHAT_MESSAGE_ROUTE, $employer->id) : 'javascript:;' }}"
                        class="btn btn-general-action d-flex justify-content-center align-items-center text-decoration-none {{ !auth()->user()->is_hidden ? 'hover-button' : 'cursor-not-allow' }} sp-btn-send mt-3">
                        <div class="color-2200A5 font-14 font-weight-bold">@lang('SEND MESSAGE')</div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row col-md-12 mt-title border-top pl-0 pr-0 mt-4">
            <div class="col-md-6 pt-4 pl-0 pr-0">
                <div class="profile-title mb-2">
                    @lang("About us")
                </div>
                <p class="show-read-more">
                    {!! nl2br($employer->bio) !!}
                </p>
            </div>
            <div class="col-md-6 pt-4 pl-0 pr-0 location">
                <div class="profile-title mb-2">
                    @lang("Location")
                </div>
                <div class="row ml-btn">
                    @if(optional($employer->country)->name)
                        <img src="{{ asset('img/country/' . optional($employer->country)->code . '.png') }}" alt=""
                             class="img-size mr-2">
                        <div>
                            {{ optional($employer->country)->name }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="columns-section mt-5 form-group-base form-exp">
            <div class="columns-section-child">
                <div class="profile-title">@lang("Business sector")</div>
                <div class="d-flex flex-wrap mt-3">
                    @if($employer->sector_id)
                        <div class="status-category mr-2 job-requirement-tag mb-1 mt-1  max-width-content">
                            {{ $employer->sector->name }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-12 profile-title pl-0 pr-0 mt-4">
            @lang("Company documents")
            <div class="d-flex flex-column mt-3">
                @foreach($employer->portfolios as $portfolio)
                    <a class="pb-2 color-000000 text-14" href="{{ route('frontend.download', ['filename' => $portfolio->file]) }}" target="_blank">
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
@endsection
