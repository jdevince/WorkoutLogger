<?php
	include("Includes/header.php");
?>
<br>
<h1> Welcome to Ribbon of Friends! </h1>
<br><br>
<?php
    if (!isset($_SESSION["username"]))
    { 
        echo "<h2> Please log in or sign up. </h2>";
    }  
?>

<?php include("Includes/footer.php"); ?>
