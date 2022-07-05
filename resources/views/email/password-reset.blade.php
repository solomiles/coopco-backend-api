<h1>We have received your request to reset your account password</h1>
<p>Kindly click the following link to recover your password:</p>

{{ route('member.reset-password', $data['token']) }}

<p>The allowed duration of the link is 30 minutes from the time the message was sent</p>
