<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ appName() }} | @lang('Verify your Identity')</title>
    <meta name="description" content="@yield('meta_description', appName())">
    <meta name="author" content="@yield('meta_author', 'DehaSoft')">
    @include('backend.includes.open_graph')
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%
        }

        body {
            margin: 0;
            background: #ffffff;
            font-family: "Arial", serif;
        }

        a {
            background-color: transparent
        }

        [hidden] {
            display: none
        }

        html {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            line-height: 1.5
        }

        *,
        :after,
        :before {
            box-sizing: border-box;
            border: 0 solid #e2e8f0
        }

        a {
            color: inherit;
            text-decoration: inherit
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
        }

        .integration-divider {
            padding-top: 5vh;
        }

        .title {
            font-size: 36px;
            font-weight: 600;
            color: #033156;
            white-space: pre-line;
            text-align: center;
            line-height: 44px;
        }

        .subtitle {
            margin-bottom: 20px;
            margin-top: 13px;
            opacity: 0.75;
            font-size: 16px;
            font-weight: 500;
            color: #506b80;
            white-space: pre-line;
            text-align: center;
        }

        .passbase {
            margin: 30px 40px;
            height: 25px;
        }

        .hosted-link-button {
            background-color: #000;
            border: none;
            color: #eee;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 4px;
        }
    </style>
</head>

<body class="antialiased">

<div class="container">
    <h2 class="title">@lang('Verify your identity now')</h2>
    <p class="subtitle">
        @lang('Please, click the button below and complete the steps.')
    </p>
    <div id="passbase-button"></div>
</div>

<script type="text/javascript" src="https://unpkg.com/@passbase/button"></script>
<script type="text/javascript">
    const userEmail = "{{ Auth::user()->email }}"
    const element = document.getElementById("passbase-button");
    const apiKey = "{{ config('passbase.publish_key') }}";

    Passbase.renderButton(element, apiKey, {
        onFinish: (identityAccessKey) => {
            console.log("Verification completed.")
        },
        onError: (errorCode) => {
            console.log("Error: ", errorCode)
        },
        onStart: () => {
        },
        prefillAttributes: {
            email: userEmail,
        }
    });
</script>
</body>

</html>
