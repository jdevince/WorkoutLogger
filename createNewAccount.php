<?php

include "Includes/databaseClassMySQLi.php";

if(isset($_POST["username"]) && isset($_POST["password"]))
{
	$encryptedPass = password_hash($_POST["password"], PASSWORD_DEFAULT);
	
	$db = new database();
	$db->pick_db("workoutlog");

	if ($stmt = $db->prepare("INSERT INTO tbl_workoutlog_users (UserName, Password) VALUES (?,?)"))
    {
        $stmt->bind_param('ss',$_POST["username"], $encryptedPass);
        $stmt->execute();
        if ($stmt->affected_rows == 1)
        {
            //Account successfully created, now log the person in
		    session_start();
		    $_SESSION["username"]=$_POST["username"];
		    echo true;
        }
    }
}

?>