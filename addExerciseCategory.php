<?php
	include("Includes/header.php");

    if (isset($_SESSION["username"]))
						{
                            if (!isAdmin($_SESSION["username"]))
                            {
                                {
                                    header($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized', true, 401);
                                    echo "<p>Unauthorized</p>";
                                    exit;
                                }
                            }
                        }
    else 
    {
        header($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized', true, 401);
        echo "<p>Unauthorized</p>";
        exit;
    }
?>

<form role='form' id='addExerciseCategoryForm' class='form-horizontal'>
    <div class='form-group'>
        <label class='control-label col-sm-3 col-sm-offset-2' for='newCategoryName'>New Exercise Category Name:</label>
        <div class='col-sm-3 col-sm-offset-0 col-xs-8 col-xs-offset-2'>
	        <input type='text' class='form-control' name='newCategoryName' required>
        </div>
    </div>
    <div class='form-group dataField'>
        <label class='control-label col-sm-2 col-sm-offset-3' for='dataField[0]'>Data Field Name:</label>
        <div class='col-sm-3 col-sm-offset-0 col-xs-8 col-xs-offset-2'>
	        <input type='text' class='form-control' name='dataField[0][name]' required>
        </div>
        <div class='col-sm-1'>
            <select name='dataField[0][category]' class='ExerciseCategorySelect'>
                <option value='text'>Text</option>
                <option value='number'>Number</option>
            </select>
        </div>
    </div>

    <button type='button' id='addDataField' class='btn btn-default center-block'>Add Data Field</button>
    <br>
    <button type='submit' class="btn btn-default center-block">Submit</button>
</form>

<?php include("Includes/footer.php"); ?>