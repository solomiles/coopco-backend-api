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
    <form action="" method="post">

        @if($errors->any())
            {{ implode('', $errors->all('<div>:message</div>')) }}
        @endif

        <h4>Please enter new password and confirm it</h4>
        <div>
            <label for="">Password</label>
            <input type="password" name="password" id="">
        </div>
        <div>
            <label for="">Verify Password</label>
            <input type="password" name="password_verify" id="">
        </div>
        <div>
            <button type="submit">Submit</button>
        </div>
    </form>
    </div>
</body>
</html>
