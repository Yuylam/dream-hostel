<?php
    require("connection.php");

    session_start();
    $mn = $_SESSION['matricNumber'];
    
    // Submitted
    if (isset($_POST["submit"])){
        if (isset($_POST['current-college'], $_POST['desired-college'], $_POST['types-of-room'])) {
            // Insert Information
            $cg = htmlspecialchars($_POST['current-college']);
            $cw = htmlspecialchars($_POST['desired-college']);
            $tg = htmlspecialchars($_POST['types-of-room']);

            // If get same college
            if($cg == $cw){
                echo "<script> 
                alert('Current college cannot be the same as desired college');
                window.location = 'new.php'; 
                </script>";
            }
            else{
                // Get gender
                $geQuery ="SELECT gender FROM students WHERE matricNumber =?";
                $stmt = mysqli_prepare($sambung, $geQuery);
                mysqli_stmt_bind_param($stmt, 's', $mn);
                mysqli_stmt_execute($stmt);
                $geResult = mysqli_stmt_get_result($stmt);
                $geRow = mysqli_fetch_assoc($geResult);
                $ge = $geRow['gender'];
                mysqli_stmt_close($stmt);

                // Insert information
                $insertQuery = "INSERT INTO exchangeinfo (matricNumber, gender, collegeGet, typeGet, collegeWant) VALUES ('$mn', '$ge', '$cg', '$tg', '$cw')";
                mysqli_query($sambung, $insertQuery);

                // Find match instantly because a new match and only be formed with a new input (MATCH FOR 2 People)
                $pairQuery = "SELECT matricNumber 
                                FROM exchangeinfo
                                WHERE collegeGet=? AND collegeWant=? AND gender=? AND status ='MA'
                                ORDER BY recordId
                                LIMIT 1";
                $stmt = mysqli_prepare($sambung, $pairQuery);
                mysqli_stmt_bind_param($stmt, 'sss', $cw, $cg, $ge);
                mysqli_stmt_execute($stmt);
                $pairResult = mysqli_stmt_get_result($stmt);
                $pairRow = mysqli_fetch_assoc($pairResult);
                $pair = $pairRow['matricNumber'];

                // Check if got pair
                if(!empty($pair)){
                    // Add a new entry to exchangecondition table and track status
                    $insertNewQuery = "INSERT INTO exchangecondition (student1, student2, acceptance) VALUES ('$pair', '$mn', 'PE');";
                    mysqli_query($sambung, $insertNewQuery);
                    
                    $updateExchangeStatQuery = "UPDATE exchangeinfo SET status = 'PE' WHERE matricNumber IN (?, ?)";
                    $stmt = mysqli_prepare($sambung, $updateExchangeStatQuery);
                    mysqli_stmt_bind_param($stmt, 'ss', $pair, $mn);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    // Update exchangeId into the exchangeinfo table
                    $getExchangeIdQuery = "SELECT exchangeId 
                                            FROM exchangecondition 
                                            WHERE student1 =? OR student1 =?";
                    $stmt = mysqli_prepare($sambung, $getExchangeIdQuery);
                    mysqli_stmt_bind_param($stmt, 'ss', $pair, $mn);
                    mysqli_stmt_execute($stmt);
                    $eiResult = mysqli_stmt_get_result($stmt);
                    $eiRow = mysqli_fetch_assoc($eiResult);
                    $ei = $eiRow['exchangeId'];
                    mysqli_stmt_close($stmt);  // Close the statement
                    
                    $updateExchangeIdQuery = "UPDATE exchangeinfo 
                                                SET exchangeId =?
                                                WHERE matricNumber IN (?, ?);";
                    $stmt = mysqli_prepare($sambung, $updateExchangeIdQuery);
                    mysqli_stmt_bind_param($stmt, 'sss', $ei, $pair, $mn);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    header('Location:home.php');

                // If not get pair
                } else {
                    // Counter for OFFSET
                    $find3count1 = 0;
                    do {
                        $end = FALSE;
                        // Find match for 3
                        // Find first match, collegeGet(Match1) = collegeWant(User)
                        $find3Query1 = "SELECT matricNumber, collegeGet, collegeWant
                                        FROM exchangeinfo
                                        WHERE collegeGet = ? AND status = 'MA' AND gender = ?
                                        ORDER BY recordId
                                        LIMIT 1 OFFSET $find3count1";
                        $stmt = mysqli_prepare($sambung, $find3Query1);
                        mysqli_stmt_bind_param($stmt, 'ss', $cw, $ge);
                        mysqli_stmt_execute($stmt);
                        $find3Result = mysqli_stmt_get_result($stmt);
                        $find3Row = mysqli_fetch_assoc($find3Result);
                        $mn1 = $find3Row['matricNumber'];
                        $cg1 = $find3Row['collegeGet'];
                        $cw1 = $find3Row['collegeWant'];
                        mysqli_stmt_close($stmt);
                        $find3count1 += 1;
                        
                        // If 1st match found
                        if (!empty($mn1)){
                            // Find 2nd match, collegeGet(Match2) = collegeWant(Match1) AND collegeWant(Match2) = collegeGet(User)
                            $find3Query2 = "SELECT matricNumber, collegeGet, collegeWant
                                            FROM exchangeinfo
                                            WHERE collegeWant = ? AND collegeGet = ? AND status = 'MA' AND gender = ?
                                            ORDER BY recordId
                                            LIMIT 1;";
                            $stmt = mysqli_prepare($sambung, $find3Query2);
                            mysqli_stmt_bind_param($stmt, 'sss', $cg, $cw1, $ge);
                            mysqli_stmt_execute($stmt);
                            $find3Result = mysqli_stmt_get_result($stmt);
                            $find3Row = mysqli_fetch_assoc($find3Result);
                            $mn2 = $find3Row['matricNumber'];
                            $cg2 = $find3Row['collegeGet'];
                            $cw2 = $find3Row['collegeWant'];
                            mysqli_stmt_close($stmt);
                            
                            // If match2 found
                            if (!empty($mn2)){
                                $insertNewQuery = "INSERT INTO exchangecondition (student1, student2, student3, acceptance) VALUES ('$mn', '$mn1', '$mn2','PE');";
                                mysqli_query($sambung, $insertNewQuery);

                                $getExchangeIdQuery = "SELECT exchangeId 
                                                    FROM exchangecondition 
                                                    WHERE student1 IN (?, ?, ?)";
                                $stmt = mysqli_prepare($sambung, $getExchangeIdQuery);
                                mysqli_stmt_bind_param($stmt, 'sss', $mn, $mn1, $mn2);
                                mysqli_stmt_execute($stmt);
                                $eiResult = mysqli_stmt_get_result($stmt);
                                $eiRow = mysqli_fetch_assoc($eiResult);
                                $ei = $eiRow['exchangeId'];
                                mysqli_stmt_close($stmt); 
                                
                                $upStat = "UPDATE exchangeInfo
                                            SET status = 'PE',
                                                exchangeID = '$ei'
                                            WHERE matricNumber IN ('$mn', '$mn1', '$mn2')";
                                mysqli_query($sambung, $upStat);
                                
                                // Boolean
                                $end = TRUE;
                            }
                        }
                        else 
                            $end = TRUE;
                    }
                    while (!$end);
                    
                    header('Location:home.php');
                
                } 
            }
        }// If form not filled
        else echo "Form fields are not set.";
    }
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Dream Hostel-new match</title>
    <link rel="stylesheet" href="new.css">
</head>


<body>
    <input type="button" onclick="location.href='logout.php';" value="Logout" style="float: right; margin-right: 150px"/>
    <br>
    <h1>𝔽𝕚𝕟𝕕 𝕐𝕠𝕦𝕣 𝔻𝕣𝕖𝕒𝕞 ℍ𝕠𝕤𝕥𝕖𝕝</h1>
    <p>🪴☁️🏡📚</p>

    <h2>Start a new match:
    </h2>
    <form action=" new.php " method="POST">
        <section id="current-college">
            <h3>Your current college:</h3>
            <label for="current-college">Your current college:</label>
            <select id="current-college" name="current-college">
        <option value="KTDI">KTDI</option>
        <option value="KTHO">KTHO</option>
        <option value="KTF">KTF</option>
        <option value="KDSE">KDSE</option>
        <option value="KRP">KRP</option>
        <option value="K910">K9K10</option>
        <option value="KP">KP</option>
        <option value="KTR">KTR</option>
        <option value="KTC">KTC</option>
    </select>


        </section>




        <section id="desired-college">
            <h3>Your desired college:</h3>
            <label for="desired-college">Your desired college:</label>
            <select id="desired-college" name="desired-college">
        <option value="KTDI">KTDI</option>
        <option value="KTHO">KTHO</option>
        <option value="KTF">KTF</option>
        <option value="KDSE">KDSE</option>
        <option value="KRP">KRP</option>
        <option value="K910">K9K10</option>
        <option value="KP">KP</option>
        <option value="KTR">KTR</option>
        <option value="KTC">KTC</option>
    </select>
        </section>


        <br>
        <section id="types of room">
            <h3>Type of your current room:</h3>
            <label for="types-of-room">Type of your current room:</label>
            <select id="types-of-room" name="types-of-room">
        <option value="SX ">single + no bathroom </option>
        <option value="ST ">single + bathroom </option>
        <option value="BX">double + no bathroom </option>
        <option value="BT">double + bathroom</option>
            </select>
        </section>


        <br>
        <div id="buttons ">
            <button type="submit" name="submit" value="submit">Submit</button>

        </div>
    </form>
</body>


</html>
