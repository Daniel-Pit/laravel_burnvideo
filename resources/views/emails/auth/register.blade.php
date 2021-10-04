<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Welcome to burnvideo.net, {{ $username }}</h2>

<div>
    <p>
        Thank you for signing up. To login, we need to activate your account. Please click on the link below
    </p>

    {{ route('account.activate', [$code]) }}
</div>
</body>
</html>
