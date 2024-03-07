<!DOCTYPE html>
<html>
<head>
    <title>Contact Us Automatorr</title>
</head>
<body>
<p>Email : {{ $data['email'] }}</p>
<p>Full Name : {{ $data['full_name'] }}</p>
<p>Message : {!! nl2br($data['message']) !!}</p>
<br>
<p>@lang('Thank you!')</p>
</body>
</html>
