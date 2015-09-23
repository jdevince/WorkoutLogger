<?php include "Includes/databaseClassMySQLi.php"; 
        include "Includes/commonFuncs.php";
?>
<!DOCTYPE HTML>
<HTML lang="en-US">
<HEAD>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>Workout Logger</title>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="Includes/javascript.js"></script>
	<link rel="stylesheet" type="text/css" href="Includes/style.css">
</HEAD>
<BODY>
	<h1> Workout Logger </h1>
	<?php session_start(); if (isset($_SESSION["username"])){ echo "<p class=\"text-center\">Logged in as: " . $_SESSION["username"] . "</p>";} ?>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#headerNavbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>  
				</button>
				<a class="navbar-brand" href="index.php">Workout Logger</a>
			</div>
			<div class="collapse navbar-collapse" id="headerNavbar">
				<ul class="nav navbar-nav">
					<li><a href="addWorkout.php">Add New Workout</a></li>
					<li><a href="viewWorkoutHistory.php">View Workout History</a></li>
                    <?php
                        if (isset($_SESSION["username"]))
						{
                            if (isAdmin($_SESSION["username"]))
                            {
                                echo "<li><a href='addExerciseCategory.php'>Add Exercise Category</a></li>";
                            } 
                        }

                    ?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<?php
						if (isset($_SESSION["username"]))
						{
							echo "<li><a id=\"logoutButton\" href=\"login.php\"><span class=\"glyphicon glyphicon-log-in\"></span> Logout</a></li>";
						}
						else
						{
							echo "
								<li><a href=\"signUp.php\"><span class=\"glyphicon glyphicon-user\"></span> Sign Up</a></li>
								<li><a href=\"login.php\"><span class=\"glyphicon glyphicon-log-in\"></span> Login</a></li>
								";
						}
					?>
					
				 </ul>
			</div>
		</div>
	</nav>