@extends('frontend.layouts.app')

@section('title', __('Saved List'))

@section('content')
    <div id="saved-wrapper" class="{{ $parentSelector }} p-32">
        <div class="saved-wrapper-child">
            <div class="w-100 d-flex justify-content-center align-items-center wrapper-header">
                <a href="{{ route('frontend.employer.saved.freelancer') }}"
                    class="btn btn-general-action mr-3 hover-button d-flex align-items-center justify-content-center {{ $type != SAVED_JOB ? 'active' : '' }}">
                    <div class="color-2200A5 font-14 font-weight-600 text-uppercase">@lang('Freelancers')</div>
                </a>
                <a href="{{ route('frontend.employer.saved.job') }}"
                    class="btn btn-general-action hover-button d-flex align-items-center justify-content-center {{ $type == SAVED_JOB ? 'active' : '' }}">
                    <div class="color-2200A5 font-14 font-weight-600 text-uppercase">@lang('Jobs')</div>
                </a>
            </div>
            <div class="w-100 wrapper-body">
                <div class="w-100 d-flex justify-content-center align-items-center search-wrapper">
                    <form action="" method="GET" class="d-flex justify-content-center align-items-center"
                        id="search-form">
                        <input type="text" name="hot_search" placeholder="@lang('Search')" form="hot-search-form"
                            data-old-value="{{ request()->query('hot_search', '') }}">
                        <button type="button" data-toggle="modal" data-target="{{ $filterModalId }}"
                            class="btn btn-filter btn-general-action d-flex justify-content-start align-items-center hover-button">
                            <img src="{{ asset('/img/filter_icon.svg') }}" alt="">
                            <div class="color-2200A5 font-14 font-weight-600 text-uppercase pc-device ml-2">
                                @lang('FILTERS')</div>
                        </button>
                    </form>
                </div>
                @yield('table')
            </div>
        </div>
    </div>
@endsection

@push('before-scripts')
    <script>
        const TYPE_SAVED = "{{ $type != SAVED_JOB ? SAVED_FREELANCER : SAVED_JOB }}";
    </script>
@endpush
