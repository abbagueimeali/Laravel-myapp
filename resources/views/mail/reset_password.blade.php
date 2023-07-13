<h1>Hi {{ $name }} Please reset your password </h1>

<p>We rerceived a request to change your password.
    If you are not initiator of this request, please let us know for the security of your account.
    <br>If you are the initiator click to link below to reset your password <br>
    <a href="{{ route('app_changepassword', ['token' => $activation_token]) }}" target="_blank">Reset Password</a>
</p>

<p>
    Myapp security team.
</p>
