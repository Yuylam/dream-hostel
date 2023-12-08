<?php
    session_start();
    require("connection.php");
    if (isset($_POST["submit"])) {
        $matricnumber = $_POST['MatricNumber'];
        $password = $_POST['Password'];
        $name = $_POST['Name'];
        $phonenumber = $_POST['PhoneNumber'];
        $email = $_POST['Email'];
        $gender = $_POST['Gender'];

        $query1 = "INSERT INTO contact(matricNumber, name, email, phone) VALUES('$matricnumber', '$name', '$email', '$phonenumber')";
        $query2 = "INSERT INTO students(matricNumber, password, gender) VALUES ('$matricnumber', '$password', '$gender')";

        if(mysqli_query($sambung, $query1)){
            if(mysqli_query($sambung, $query2)){
            header('Location:login.php');
            }
        } else {
            echo "<script> 
                alert('Invalid registration');
                window.location = 'register.php'; 
                </script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Dream Hostel-register</title>
    <link rel="stylesheet" href="register.css">
</head>


<body>
    <h1>𝔽𝕚𝕟𝕕 𝕐𝕠𝕦𝕣 𝔻𝕣𝕖𝕒𝕞 ℍ𝕠𝕤𝕥𝕖𝕝</h1>
    <p>🪴☁️🏡📚</p>


    <div id="register">
        <h3>Fill in your details here: </h3>
        <form action="register.php" method="POST">


            <p>1. Matric Number:</p>
            <input type="text" name="MatricNumber" value="<?php echo isset($_POST['MatricNumber']) ? $matricnumber : ''; ?>" required>


            <p>2. Password:</p>
            <input type="password" name="Password" value="<?php echo isset($_POST['Password']) ? $password : ''; ?>" required>


            <p>3. Name (as per NRIC):</p>
            <input type="text" name="Name" value="<?php echo isset($_POST['Name']) ? $name : ''; ?>" required>


            <p>4. Phone number:</p>
            <input type="text" name="PhoneNumber" value="<?php echo isset($_POST['PhoneNumber']) ? $phonenumber : ''; ?>" required>


            <p>5. Email (preferred graduate mail):</p>
            <input type="email" name="Email" value="<?php echo isset($_POST['Email']) ? $email : ''; ?>" required>


            <p>6. Gender:</p>
            <label>
        <input type="radio" name="Gender" value="M" required> Male
    </label>
            <label>
        <input type="radio" name="Gender" value="F" required> Female
    </label>

            <br>
            <button type="submit" name="submit" value="Submit">Submit</button>
        </form>
    </div>


</body>


</html>
