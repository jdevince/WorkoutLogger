<?php
	include("Includes/header.php");
    
    if(!isset($_SESSION["username"]))
    {
        echo "<p>Please log in</p>";
        exit;
    }
?>

<script>
    var exerciseCategoriesArray = <?php echo getAllCategoriesDetails(); ?>;
</script>

<form role='form' id='addWorkoutForm' class='form-horizontal'>
    <div id='workoutDateGroup' class='form-group'>
        <label class='control-label col-sm-1 col-sm-offset-4' for='date'>Date:</label>
        <div class='col-sm-3 col-sm-offset-0 col-xs-8 col-xs-offset-2'>
	        <input type='date' class='form-control' name='date' required>
        </div>
    </div> 
    <br><br>
    <button type='button' id='addExercise' class='btn btn-default col-md-2 col-md-offset-4'>Add Exercise</button>
    <button type='button' id='removeExercise' class='btn btn-default col-md-2'>Remove Exercise</button>
    <br><br>
    <button type='submit' class="btn btn-default center-block">Submit</button>

</form>



<?php include("Includes/footer.php"); ?>