<?php
    session_start();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find your Dream Hostel-logout</title>
    <style>
        body {
            background: rgba(0, 128, 0, 0.1);
        }
        .center {
            padding: 70px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="center">
        <h1>𝔽𝕚𝕟𝕕 𝕐𝕠𝕦𝕣 𝔻𝕣𝕖𝕒𝕞 ℍ𝕠𝕤𝕥𝕖𝕝</h1>
        <p>🪴☁️🏡📚</p>

        <h2>Logged Out</h2>
        <br>
        <input type="button" onclick="location.href='login.php';" value="Login" />
    </div>
</body>
</html>

