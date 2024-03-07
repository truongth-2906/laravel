<!doctype html>
<html lang="{{ htmlLang() }}" @langrtl dir="rtl" @endlangrtl>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ appName() }} | @lang('Verify Your E-mail Address')</title>
    <meta name="description" content="@yield('meta_description', appName())">
    <meta name="author" content="@yield('meta_author', 'DehaSoft')">
    @include('backend.includes.open_graph')
    <style>
        .d-flex {
            display: -ms-flexbox !important;
            display: flex !important
        }

        .justify-content-between {
            -ms-flex-pack: justify !important;
            justify-content: space-between !important;
        }

        .align-items-center {
            -ms-flex-align: center !important;
            align-items: center !important
        }

        .digit-group input {
            width: 80px;
            height: 80px;
            line-height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: 1000;
            color: #2200A5;
            margin: 0 2px;
            border-radius: 18px;
            border: 2px solid #2200A5;
        }

        ::-webkit-input-placeholder {
            font-weight: 800;
            color: #9c9a9a;
        }

        :-ms-input-placeholder {
            font-weight: 800;
            color: #9c9a9a;
        }

        ::placeholder {
            font-weight: 900;
            color: #9c9a9a;
        }

        .mt-3 {
            margin-top: 30px;
        }

        .mt-5 {
            margin-top: 50px;
        }

        .ml-5 {
            margin-left: 50px;
        }

        .color-344054 {
            color: #344054;

        }

        .color-2200A5 {
            color: #2200A5;
        }

        .btn-general-action {
            height: 40px;
            color: #2200a5 !important;
            background-color: #eef2f6 !important;
            border: 1px solid #e3e8ef;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
            border-radius: 17px;
            padding: 15px;
        }

        .btn-general-action:hover {
            background-color: #c2e830 !important;
            color: #2200a5 !important;
        }

        a {
            text-decoration: none
        }

        .w-640 {
            width: 640px;
        }

        .pr-5 {
            padding-right: 5px;
        }

        .font-size-16 {
            font-size: 16px;
            font-weight: 500;
        }

        .mr-2 {
            margin-right: 20px;
        }
    </style>
</head>
<body>
<div>
    <div>
        <div id="app" class="c-body">
            <main>
                <div class="ml-5 w-640">
                    <div class="mt-5">
                        <img src="{{ asset('/img/logo.png') }}" alt="logo" title="Logo automator">
                    </div>
                    @yield('content')
                    <div class="color-344054 mt-5">
                        <p>@lang('Thanks,</br> The Automatorr team')</p>
                    </div>
                    <div class="mt-5">
                        <p>
                            @lang('This email was sent to') <span>{{ $user->email }}</span>
                            @lang('. If you\'d rather not receive this kind of email, you can') <span
                                class="color-2200A5">@lang('unsubscribe') </span>@lang(' or') <span
                                class="color-2200A5">@lang(' manage your email preferences.')</span>
                        </p>
                        <p>
                            @lang('© 2077 Automatorr, 100 Smith Street, Melbourne VIC 3000')
                        </p>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <img src="{{ asset('/img/logo-small.svg') }}" alt="">
                        </div>
                        <div class="align-items-center">
                            <img class="pr-5" src="{{ asset('/img/icon-facebook.svg') }}" alt="">
                            <img class="pr-5" src="{{ asset('/img/icon-twitter.svg') }}" alt="">
                            <img class="pr-5" src="{{ asset('/img/icon-instagram.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </main>
        </div><!--app-->
    </div><!--content-->
</div><!--app-->
</body>
</html>
