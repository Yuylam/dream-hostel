<?php
    require("connection.php");

    if(filter_has_var(INPUT_POST, 'submit')){
        // Get Form Data
        $matricnumber = htmlspecialchars($_POST['matricNumber']);
        $password = htmlspecialchars($_POST['password']);
    
        // Check if Matric number and Password are not empty
        if (!empty($matricnumber) && !empty($password)) {
            session_start();

            // Use prepared statement to prevent SQL injection
            $sql = "SELECT matricNumber, password FROM students WHERE matricNumber=? AND password=?";
            
            $stmt = mysqli_prepare($sambung, $sql);
            mysqli_stmt_bind_param($stmt, 'ss', $matricnumber, $password);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            echo "SQL Query: $sql<br>";
            echo "Matric Number: $matricnumber<br>";
            echo "Password: $password<br>";
            echo "Row: ";
            print_r($row);

            if ($row) {
                $_SESSION['matricNumber'] = $matricnumber;
                $_SESSION['password'] = $password;
                header("Location: home.php");
                exit();
            } else {
                echo "<script> 
                    alert('Your matric number or password is incorrect');
                    window.location = 'login.php' 
                    </script>";
            }
            mysqli_stmt_close($stmt);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Dream Hostel</title>
    <link rel="stylesheet" href="login.css">
    
</head>

<body>
    <h1>𝔽𝕚𝕟𝕕 𝕐𝕠𝕦𝕣 𝔻𝕣𝕖𝕒𝕞 ℍ𝕠𝕤𝕥𝕖𝕝</h1>
    <p>🪴☁️🏡📚</p>

    <div id="login">
        <h3>LOGIN</h3>
        <form action="login.php" method="POST">
            <p>Matric number: <input type="text" name="matricNumber" required ></p>
            <p>Password: <input type="password" name="password" required ></p>
            <button type="submit" name="submit" value="Submit">Log in</button>
        </form>

        <p>Don't have an account?
            <a href="register.php">Register here</a>
        </p>
    </div>
    <script src="your-script.js"></script>
</body>
</html>