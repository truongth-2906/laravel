<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset('img/logo.png') }}" />

    <title>{{ appName() }} | @lang('Wait for redirect')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/brand/favicon_32x32.png') }}">
    @include('backend.includes.open_graph')
    <style>
        div {
            text-align: center;
            margin: 20vh auto 0px;
        }
    </style>
</head>
<body>
    <div>@lang('Payment is successful, please wait while you are being redirected...')</div>
    <script>
        const redirectRoute = '{{ $redirectRoute ?? "" }}';
        const paymentsRoute = '{{ $paymentsRoute }}';
        const parentPage = window.opener || null;

        if (parentPage) {
            if (redirectRoute) {
                parentPage.location.href = redirectRoute;
            }
            parentPage.focus();
            window.close();
        } else {
            window.location.href = paymentsRoute;
        }
    </script>
</body>
</html>
