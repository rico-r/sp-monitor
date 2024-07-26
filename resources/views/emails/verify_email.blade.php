<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        h1 {
            color: rgb(43, 21, 235);
        }
    </style>
</head>

<body>
    <h1>Email Verification Mail</h1>

    <h4>Terimakasih telah mendaftar.</h4>

    Klik link dibawah ini untuk memverifikasikan email:<br>
    <a href="{{ route('user.verify', $token) }}">Verify Email</a>
</body>

</html>
