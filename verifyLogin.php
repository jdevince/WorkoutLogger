<?php
	include "Includes/databaseClassMySQLi.php";
	
	if(isset($_POST["username"]) && isset($_POST["password"]))
	{
		$db = new database();
		$db->pick_db("workoutlog");
		
		$query = "SELECT Password FROM tbl_workoutlog_users WHERE UserName = '" . $_POST["username"] . "'";
		$res = $db->send_sql($query);
		
        $stmt = $db->prepare("SELECT Password FROM tbl_workoutlog_users WHERE UserName = ?");
        
        $stmt->bind_param('s',$_POST["username"]);
        $stmt->execute();
        $stmt->bind_result($passHash);
        $stmt->fetch();

		if (password_verify($_POST["password"],$passHash))
		{
			//User/Pass combo valid
			session_start();
			$_SESSION["username"]=$_POST["username"];
			$result = true;
		}
		
	}
	echo isset($result) ? $result : false;

?>