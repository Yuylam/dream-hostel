<?php
$namahos="localhost";
$user_mysql="root";
$password_mysql="";
$pdata_mysql="dreamhostel";
$sambung=mysqli_connect($namahos,$user_mysql,$password_mysql,$pdata_mysql)or die
	("Data not found");

	if(mysqli_connect_errno()){
        // Connection Failed
        echo 'Failed to connect to MySQL '.mysqli_connect_errno();
    }
	/*
mysqli_select_db($sambung,$pdata_mysql)or die
	("Database not connected");

	*/
?>
