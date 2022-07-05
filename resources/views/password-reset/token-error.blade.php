<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <div style="margin: auto;">
        @if(gettype($errors) == 'object' && $errors->any())
            {{ implode('', $errors->all('<div>:message</div>')) }}
        @else
            <h3> THIS TOKEN IS EXPIRED! </h3>
        @endif
    </div>
</body>
</html>
