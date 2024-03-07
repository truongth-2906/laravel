<!doctype html>
<html lang="{{ htmlLang() }}" @langrtl dir="rtl" @endlangrtl>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="echo-token" content="{{ echo_token() }}">
    <title>{{ appName() }} | @yield('title')</title>
    <meta name="description" content="@yield('meta_description', appName())">
    <meta name="author" content="@yield('meta_author', 'DehaSoft')">
    <meta name="app-name" content="{{ appName() }}">
    @include('backend.includes.open_graph')
    @yield('meta')

    @stack('before-styles')
    <link href="{{ mix('css/frontend.css') }}" rel="stylesheet">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/brand/favicon_32x32.png') }}">
    @stack('after-styles')
</head>
<body class="c-app">
@include('frontend.includes.sidebar')
@include('includes.partials.loader')

<div class="body-content c-wrapper c-fixed-components bg-white {{ checkRoute(IS_USER) ? 'color-F9FAFB' : '' }} @if(isset($chat)) body-content-chat @endif">
    @if(checkRoute(IS_USER))
        @include('frontend.includes.header')
    @endif
    @include('includes.partials.read-only')
    @include('includes.partials.logged-in-as')
    @include('includes.partials.announcements')
    @include('frontend.auth.logout')
    @if($logged_in_user->isFreelancer() && ($logged_in_user->isDeclined() || ($logged_in_user->isPending() && is_null($logged_in_user->identity_passbase))))
        @include('frontend.auth.kyc-modal')
    @endif
    @if(auth()->user()->is_hidden)
        @include('frontend.auth.alert-account-hidden-modal')
    @endif
    <div id="app" class="c-body">
        <main>
            <div class="">
                <div class="fade-in @if(!isset($chat)) pb-5 @endif">
                    @yield('content')
                    @include('frontend.notification.toast')
                </div><!--fade-in-->
            </div><!--container-fluid-->
        </main>
    </div><!--app-->
</div>

@stack('before-scripts')
<script>
    @if (auth()->check())
    const RECEIVER_ID = "{{ auth()->id() ?? 'null' }}";
    const RECEIVER_TYPE = "{{ auth()->user()->type ?? 'null' }}";
    const SESSION_LIFETIME = '{{ config("session.lifetime") }}';
    @endif
</script>
<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="{{ mix('js/frontend.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    @if(Session::has('message'))
        toastr.options =
        {
            "closeButton": true,
            "progressBar": true
        }
    toastr.success("{{ session('message') }}");
    @endif

        @if(Session::has('error'))
        toastr.options =
        {
            "closeButton": true,
            "progressBar": true
        }
    toastr.error("{{ session('error') }}");
    @endif

        @if(Session::has('info'))
        toastr.options =
        {
            "closeButton": true,
            "progressBar": true
        }
    toastr.info("{{ session('info') }}");
    @endif

        @if(Session::has('warning'))
        toastr.options =
        {
            "closeButton": true,
            "progressBar": true
        }
    toastr.warning("{{ session('warning') }}");
    @endif
</script>
@stack('after-scripts')
</body>
</html>
