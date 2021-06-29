<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form enctype="multipart/form-data" action="/table" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    Select a file: <input name="userfile" type="file" />
    <input type="submit" value="Send file" />
</form>
</body>
</html>
