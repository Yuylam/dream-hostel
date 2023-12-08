<?php
    require("connection.php");

    session_start();
    $mn = $_SESSION['matricNumber'];

    $statusQuery = "SELECT status FROM exchangeInfo WHERE matricNumber = '$mn'";
    $statusResult = mysqli_query($sambung, $statusQuery);
    $statusRow = mysqli_fetch_assoc($statusResult);
    $status = $statusRow['status'];

    // Get results to be displayed
    $getEIquery = "SELECT exchangeId 
                    FROM exchangeinfo
                    WHERE matricnumber = ?";
    $stmt = mysqli_prepare($sambung, $getEIquery);
    mysqli_stmt_bind_param($stmt, 's', $mn);
    mysqli_stmt_execute($stmt);
    $eiResult = mysqli_stmt_get_result($stmt);
    $eiRow = mysqli_fetch_assoc($eiResult);
    $ei = $eiRow['exchangeId'];
    mysqli_stmt_close($stmt);

    $getMNquery = "SELECT *
                    FROM exchangecondition
                    WHERE exchangeId = ?";
    $stmt = mysqli_prepare($sambung, $getMNquery);
    mysqli_stmt_bind_param($stmt, 's', $ei);
    mysqli_stmt_execute($stmt);
    $mnResult = mysqli_stmt_get_result($stmt);
    $mnRow = mysqli_fetch_assoc($mnResult);
    $s1 = $mnRow['student1'];
    $s2 = $mnRow['student2'];
    $s3 = $mnRow['student3'];
    mysqli_stmt_close($stmt);
    
    // Get contact information to display
    $getCOquery = "SELECT *
                    FROM contact
                    WHERE matricNumber IN (?, ?, ?)
                    ORDER BY matricNumber;";
    $stmt = mysqli_prepare($sambung, $getCOquery);
    mysqli_stmt_bind_param($stmt, 'sss', $s1, $s2, $s3);
    mysqli_stmt_execute($stmt);
    $coResults = array();
    $coResultSet = mysqli_stmt_get_result($stmt);
    while ($coRow = mysqli_fetch_assoc($coResultSet)) {
        $coResults[] = $coRow;
    }
    for ($i = 0; $i < count($coResults); $i++) {
        $name[$i] = $coResults[$i]['name'];
        $phone[$i] = $coResults[$i]['phone'];
        $email[$i] = $coResults[$i]['email'];
    }

    // Get college get to display current college
    $getCGquery = "SELECT collegeGet, typeGet, collegeWant
                FROM exchangeInfo
                WHERE matricNumber IN (?, ?, ?)
                ORDER BY matricNumber;";
    $stmt = mysqli_prepare($sambung, $getCGquery);
    mysqli_stmt_bind_param($stmt, 'sss', $s1, $s2, $s3);
    mysqli_stmt_execute($stmt);
    $cgResults = array();
    $cgResultSet = mysqli_stmt_get_result($stmt);
    while ($cgRow = mysqli_fetch_assoc($cgResultSet)) {
        $cgResults[] = $cgRow;
    }
    for ($i = 0; $i < count($cgResults); $i++) {
        $cg[$i] = $cgResults[$i]['collegeGet'];
        $tg[$i] = $cgResults[$i]['typeGet'];
        $cw[$i] = $cgResults[$i]['collegeWant'];
    }

    for ($i = 0; $i < count($cgResults); $i++) {
        if($tg[$i] == 'SX')
            $tg[$i] = "Single Without Bathroom";
        if($tg[$i] == 'ST')
            $tg[$i] = "Single With Bathroom";
        if($tg[$i] == 'BX')
            $tg[$i] = "Double Without Bathroom";
        if($tg[$i] == 'BT')
            $tg[$i] = "Double With Bathroom";
    }

    # End get information to display

    // Update acceptance
    if (isset($_POST["accept"])){
        $decision = $_POST["accept"];
        // Update Exchange Condition

        if($mn == $s1){
            $upAcc1 = "UPDATE exchangeCondition
                    SET acceptance1 = 1
                    WHERE student1 = '$mn';";
            mysqli_query($sambung, $upAcc1);
        }
        
        if($mn == $s2){
            $upAcc2 = "UPDATE exchangeCondition
                    SET acceptance2 = 1
                    WHERE student2 = '$mn';";
            mysqli_query($sambung, $upAcc2);
        }
        
        if($mn == $s3){
            $upAcc3 = "UPDATE exchangeCondition
                        SET acceptance3 = 1
                        WHERE student3 = '$mn';";
            mysqli_query($sambung, $upAcc3);
        }

        $upStatPM = "UPDATE exchangeInfo
                        SET status = 'PM'
                        WHERE matricNumber = '$mn';";
        mysqli_query($sambung, $upStatPM);

        $upAccOverall = "UPDATE exchangeCondition
                        SET acceptance = 'SU'
                        WHERE (student1 IS NOT NULL AND 
                            student2 IS NOT NULL AND
                            student3 IS NOT NULL AND
                            acceptance1 = 1 AND 
                            acceptance2 = 1 AND 
                            acceptance3 = 1) OR
                            (student1 IS NOT NULL AND 
                            student2 IS NOT NULL AND
                            student3 IS NULL AND
                            acceptance1 = 1 AND 
                            acceptance2 = 1 AND 
                            acceptance3 = 0);";
        mysqli_query($sambung, $upAccOverall);
        
        $accQuery = "SELECT acceptance
                    FROM exchangecondition
                    WHERE exchangeId = ?";
        $stmt = mysqli_prepare($sambung, $accQuery);
        mysqli_stmt_bind_param($stmt, 's', $ei);
        mysqli_stmt_execute($stmt);
        $accResult = mysqli_stmt_get_result($stmt);
        $accRow = mysqli_fetch_assoc($accResult);
        $acc = $accRow['acceptance'];

        if($acc == 'SU'){
            $upStat = "UPDATE exchangeInfo
                    SET status = 'SU'
                    WHERE matricNumber IN ('$s1', '$s2', '$s3')";
            mysqli_query($sambung, $upStat);
        }
        header('Location:home.php');
    }
    # End update acceptance

    // Update decline
    if (isset($_POST["decline"])){
        $decision = $_POST["decline"];

        // Get decline student's info
        $getInfoquery = "SELECT *
                        FROM exchangeInfo
                        WHERE matricNumber = ?";
        $stmt = mysqli_prepare($sambung, $getInfoquery);
        mysqli_stmt_bind_param($stmt, 's', $mn);
        mysqli_stmt_execute($stmt);
        $inResult = mysqli_stmt_get_result($stmt);
        $inRow = mysqli_fetch_assoc($inResult);
        $dmn = $mn;
        $dcg = $inRow['collegeGet'];
        $dge = $inRow['gender'];
        $dtg = $inRow['typeGet'];
        $dcw = $inRow['collegeWant'];
        mysqli_stmt_close($stmt);

        // Delete Current Exchange (No Problem)
        $deleteExchangeConditionQuery = "DELETE FROM `exchangeCondition` WHERE exchangeId = ?";
        $stmt = mysqli_prepare($sambung, $deleteExchangeConditionQuery);
        mysqli_stmt_bind_param($stmt, 's', $ei);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete exchange info for decline student
        $deleteExchangeInfoQuery = "DELETE FROM `exchangeinfo` WHERE matricNumber = ?";
        $stmt = mysqli_prepare($sambung, $deleteExchangeInfoQuery);
        mysqli_stmt_bind_param($stmt, 's', $dmn);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

/**/        // Get infomation of other students
        $getOTquery = "SELECT *
                    FROM exchangeInfo
                    WHERE exchangeId = ?;";
        $stmt = mysqli_prepare($sambung, $getOTquery);
        mysqli_stmt_bind_param($stmt, 's', $ei);
        mysqli_stmt_execute($stmt);
        $otResults = array();
        $otResultSet = mysqli_stmt_get_result($stmt);
        while ($otRow = mysqli_fetch_assoc($otResultSet)) {
            $otResults[] = $otRow;
        }
        for ($i = 0; $i < count($otResults); $i++) {
            $omn[$i] = $otResults[$i]['matricNumber'];
            echo $omn[$i];
            $ocg[$i] = $otResults[$i]['collegeGet'];
            echo $ocg[$i];
            $ocw[$i] = $otResults[$i]['collegeWant'];
            echo $ocw[$i];
        }

        // Update status
        $upStatMA = "UPDATE exchangeInfo
                    SET status = 'MA'
                    WHERE exchangeId = {$ei};";
        mysqli_query($sambung, $upStatMA);

        // Remove exchangeId
        $upExId = "UPDATE exchangeInfo
                    SET exchangeId = NULL
                    WHERE exchangeId = {$ei};";
        mysqli_query($sambung, $upExId);
        
        // Find pair
        for ($i = 0; $i < count($otResults); $i++){
            $pairQuery = "SELECT matricNumber 
                                FROM exchangeinfo
                                WHERE collegeGet=? AND collegeWant=? AND gender=? AND status ='MA'
                                ORDER BY recordId
                                LIMIT 1";
            $stmt = mysqli_prepare($sambung, $pairQuery);
            mysqli_stmt_bind_param($stmt, 'sss', $ocw[$i], $ocg[$i], $dge);
            mysqli_stmt_execute($stmt);
            $pairResult = mysqli_stmt_get_result($stmt);
            $pairRow = mysqli_fetch_assoc($pairResult);
            $pair = $pairRow['matricNumber'];

            // Check if got pair
            if(!empty($pair)){
                // Add a new entry to exchangecondition table and track status
                $insertNewQuery = "INSERT INTO exchangecondition (student1, student2, acceptance) VALUES ('$pair', '$omn[$i]', 'PE');";
                mysqli_query($sambung, $insertNewQuery);
                
                $updateExchangeStatQuery = "UPDATE exchangeinfo SET status = 'PE' WHERE matricNumber IN (?, ?)";
                $stmt = mysqli_prepare($sambung, $updateExchangeStatQuery);
                mysqli_stmt_bind_param($stmt, 'ss', $pair, $omn[$i]);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Update exchangeId into the exchangeinfo table
                $getExchangeIdQuery = "SELECT exchangeId 
                                        FROM exchangecondition 
                                        WHERE student1 =? OR student1 =?";
                $stmt = mysqli_prepare($sambung, $getExchangeIdQuery);
                mysqli_stmt_bind_param($stmt, 'ss', $pair, $omn[$i]);
                mysqli_stmt_execute($stmt);
                $eiResult = mysqli_stmt_get_result($stmt);
                $eiRow = mysqli_fetch_assoc($eiResult);
                $ei = $eiRow['exchangeId'];
                mysqli_stmt_close($stmt);  // Close the statement
                
                $updateExchangeIdQuery = "UPDATE exchangeinfo 
                                            SET exchangeId =?
                                            WHERE matricNumber IN (?, ?);";
                $stmt = mysqli_prepare($sambung, $updateExchangeIdQuery);
                mysqli_stmt_bind_param($stmt, 'sss', $ei, $pair, $omn[$i]);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        // Insert back deleted data to queue again (No Problem)
        $insertExchangeInfoQuery = "INSERT INTO `exchangeinfo` (matricNumber, collegeGet, gender, typeGet, collegeWant) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($sambung, $insertExchangeInfoQuery);
        mysqli_stmt_bind_param($stmt, 'sssss', $dmn, $dcg, $dge, $dtg, $dcw);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header('Location:home.php');
    }
    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find your Dream Hostel-confirm</title>
    <link rel="stylesheet" href="confirm.css">
</head>

<body>
    <input type="button" onclick="location.href='logout.php';" value="Logout" style="float: right; margin-right: 150px"/>
    <br>
    <h1>𝔽𝕚𝕟𝕕 𝕐𝕠𝕦𝕣 𝔻𝕣𝕖𝕒𝕞 ℍ𝕠𝕤𝕥𝕖𝕝</h1>
    <p>🪴☁️🏡📚</p>
    <h2>Your matching results are as below:</h2>
    <section id="user-details">
        <h3>Student 1 details:</h3>
        <p>Name: <?php echo $name[0]; ?></p>
        <p>Phone number: <?php echo $phone[0]; ?></p>
        <p>Email: <?php echo $email[0]; ?></p>
        <p>Current college: <?php echo $cg[0]; ?></p>
        <p>Type Get: <?php echo $tg[0]; ?></p>
        <p>Desired college: <?php echo $cw[0]; ?></p>
    </section>

    <section id="match-details">
        <h3>Student 2 details:</h3>
        <p>Name: <?php echo $name[1]; ?></p>
        <p>Phone number: <?php echo $phone[1]; ?></p>
        <p>Email: <?php echo $email[1]; ?></p>
        <p>Current college: <?php echo $cg[1]; ?></p>
        <p>Type Get: <?php echo $tg[1]; ?></p>
        <p>Desired college: <?php echo $cw[1]; ?></p>
    </section>
    <?php if (!empty($s3)){ ?>
    <section id="match-details">
        <h3>Student 3 details:</h3>
        <p>Name: <?php echo $name[2]; ?></p>
        <p>Phone number: <?php echo $phone[2]; ?></p>
        <p>Email: <?php echo $email[2]; ?></p>
        <p>Current college: <?php echo $cg[2]; ?></p>
        <p>Type Get: <?php echo $tg[2]; ?></p>
        <p>Desired college: <?php echo $cw[2]; ?></p>
    </section>
    <?php } ?>

    <br>

    <?php if ($status != "PM"){ ?>
    <div id="buttons">
        <form action="" method="post">
            <button type="submit" name="accept" value="accept">Accept</button>
            <button type="submit" name="decline" value="decline">Decline</button>
        </form>
    </div>
    <?php } ?>

</body>

</html>
