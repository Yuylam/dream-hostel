<?php
    require("connection.php");

    session_start();
    $mn = $_SESSION['matricNumber'];

    // Get status
    $statusQuery = "SELECT status FROM exchangeInfo WHERE matricNumber = '$mn'";
    $statusResult = mysqli_query($sambung, $statusQuery);
    $statusRow = mysqli_fetch_assoc($statusResult);
    $status = $statusRow['status'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Dream Hostel-home</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <input type="button" onclick="location.href='logout.php';" value="Logout" style="float: right; margin-right: 150px"/>
    <br>
    <h1>𝔽𝕚𝕟𝕕 𝕐𝕠𝕦𝕣 𝔻𝕣𝕖𝕒𝕞 ℍ𝕠𝕤𝕥𝕖𝕝</h1>
    <p>🪴☁️🏡📚</p>
    <div id="timeline">

    <!-- If no record -->
    <?php if (empty($status)){ ?>
        <div class="circle" id="circle1">
            <p>No record found</p>
        </div>
        <div id="start">
            <a href="new.php"> Start a new match!</a>
        </div>
    <?php } ?>
    
    <?php if ($status == "MA"){ ?>
        <div class="circle" id="circle2">
            <p>Matching<br><br>Please come back later to check if a match is found</p>
        </div>
    <?php } ?>
    
    <?php if ($status == "PE"){ ?>
        <div class="circle" id="circle3">
            <p>A match is found<br><br>Pending Confirmation</p>
        </div>
        <div id="confirm">
            <a href="confirm.php"> confirm</a>
        </div>
    <?php } ?>

    <?php if ($status == "PM"){ ?>
        <div class="circle" id="circle3">
            <p>Pending Match Confirmation<br><br>Please contact your match for negotiation.</p>
        </div>
        <div id="confirm">
            <a href="confirm.php">Review Match</a>
        </div>
    <?php } ?>
    
    <?php if ($status == "SU"){ ?>
        <div class="circle" id="circle4">
            <p>Successful!</p>
        </div>
        <div id="confirm">
            <a href="confirm.php">Review Match</a>
        </div>
    <?php } ?>

    </div>

</body>

</html>
