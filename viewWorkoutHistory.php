<?php
	include("Includes/header.php");
?>

<?php
include "Includes/class.FastTemplate.php";

$tpl = new FastTemplate("Templates");

$tpl->define(array(
	"WorkoutHistoryPage"=>"workoutHistory.html",
	"WorkoutTable"=>"workoutTable.html",
	"WorkoutRow"=>"workoutRow.html",
	"exerciseData"=>"exerciseData.html"));

if(isset($_SESSION["username"]))
{
	$allCategoriesDetails = json_decode(getAllCategoriesDetails(), TRUE);

    $db = new database();
	$db->pick_db("workoutlog");
	
    $userId = getUserIdFromUsername($_SESSION["username"]);

	$query = "SELECT WorkoutDate, WorkoutId FROM tbl_workoutlog_workout WHERE UserId = " . $userId . " ORDER BY WorkoutDate DESC";
	$res = $db->send_sql($query);			
	$workouts = $res->fetch_all(MYSQLI_ASSOC);

	if(count($workouts)==0)
	{
		$tpl->assign("WORKOUTTABLES","<p>You haven't recorded any workouts yet!</p>");
	}		
	else
	{
		foreach ($workouts as $workout) //For each workout for this user
			{
                $tpl->clear("EXERCISEROW");
				
                $query = "SELECT ExerciseId, ExerciseNameId FROM tbl_workoutlog_exercise WHERE WorkoutId = " . $workout['WorkoutId'];
                $res = $db->send_sql($query);
                $exercises = $res->fetch_all(MYSQLI_ASSOC);

                //Find maximum number of sets for any exercise
                $exerciseIds = "(";
                foreach($exercises as $exercise)
                {
                    $exerciseIds = $exerciseIds . $exercise['ExerciseId'] . ",";
                }
                $exerciseIds = substr($exerciseIds,0,-1) . ")";

                $query =    "SELECT MAX(cnt) as max
                            FROM
                            (
                                SELECT COUNT(*) AS cnt
                                FROM tbl_workoutlog_exercise e ";
                foreach ($allCategoriesDetails as $category => $details)
                {
                    $query .= "LEFT JOIN tbl_workoutlog_category_" . $category . " `" . $category . "` ON e.ExerciseId = `" . $category . "`.ExerciseId ";
                }
                                
                $query .=     "WHERE e.WorkoutId = " . $workout['WorkoutId'] . " 
                                AND e.ExerciseId IN " . $exerciseIds . " 
                                GROUP BY e.ExerciseId
                            ) AS tbl";

                $res = $db->send_sql($query);
	            $row = $res->fetch_assoc();
	            $maxSetsCount = $row['max'];
                foreach($exercises as $exercise) //For each exercise in this workout
                {
                    $tpl->clear("EXERCISEDATACOL");

                    $query = "SELECT ExerciseName, ExerciseCategory FROM tbl_workoutlog_exercisename WHERE ExerciseNameId = " . $exercise['ExerciseNameId'];
                    $res = $db->send_sql($query);
                    $row = $res->fetch_assoc();

                    $exerciseName = $row['ExerciseName'];
                    $exerciseCategory = $row['ExerciseCategory'];

                    if ($exerciseCategory == 'set')
                    {
                        $query = "SELECT Weight, WeightUnit, NumOfReps, Notes FROM tbl_workoutlog_category_set WHERE ExerciseId = " . $exercise['ExerciseId'];
                        $res = $db->send_sql($query);
                        $sets = $res->fetch_all(MYSQLI_ASSOC);
                        $roundCount = count($sets);
                        foreach($sets as $set) //For each set of the exercise
                        { 
                            if ($set['WeightUnit'] == "pounds")
                                $shortUnit = "#";
                            else if ($set['WeightUnit'] == 'kilograms')
                                $shortUnit = 'kgs';
                            else
                                $shortUnit = '';

                            $setString = $set['NumOfReps'] . "@" . $set['Weight'] . $shortUnit;
                            if ($set['Notes'] != "")
                                $setString .= " (" . $set['Notes'] . ")";
                            $tpl->assign("EXERCISEDATA",$setString);
                            $tpl->parse("EXERCISEDATACOL", ".exerciseData");
                        }
                    }
                    else if ($exerciseCategory == 'endurance')
                    {
                        $query = "SELECT Meters, Milliseconds, Notes FROM tbl_workoutlog_category_endurance WHERE ExerciseId = " . $exercise['ExerciseId'];
                        $res = $db->send_sql($query);
                        $rounds = $res->fetch_all(MYSQLI_ASSOC);
                        $roundCount = count($rounds);
                        foreach($rounds as $round) //For each set of the exercise
                        { 
                            $miles = round(($round['Meters'] / 1609.344),2);
                            $time = getTimeStringFromMilliseconds($round['Milliseconds']);
                            $pace = getPace($miles,$round['Milliseconds']);

                            $roundString = $miles . " miles in " . $time . ". Pace= " . $pace . " per mile";
                            if ($round['Notes'] != "")
                                $roundString .= " (" . $round['Notes'] . ")";
                            $tpl->assign("EXERCISEDATA",$roundString);
                            $tpl->parse("EXERCISEDATACOL", ".exerciseData");
                        }
                    }
                    
                    else //Admin added Category 
                    {
                        $query = "SELECT * FROM tbl_workoutlog_category_" . $exerciseCategory . " WHERE ExerciseId = " . $exercise['ExerciseId'];
                        $res = $db->send_sql($query);
                        $rounds = $res->fetch_all(MYSQLI_ASSOC);
                        $roundCount = count($rounds);
                        foreach ($rounds as $round)
                        {
                            $roundString = "";
                            foreach ($round as $fieldName => $fieldData)
                            {
                                if (strtolower($fieldName) != strtolower(($exerciseCategory . "Id")) && strtolower($fieldName) != strtolower("ExerciseId"))
                                    $roundString .= $fieldName . "=>" . $fieldData . ", ";
                            }
                            $roundString = substr($roundString,0,-2);
                            $tpl->assign("EXERCISEDATA",$roundString);
                            $tpl->parse("EXERCISEDATACOL", ".exerciseData");
                        }
                    }

                    
                    while ($roundCount < $maxSetsCount)
                    {
                        $tpl->assign("EXERCISEDATA","");
                        $tpl->parse("EXERCISEDATACOL", ".exerciseData");
                        $roundCount +=1;
                    }

                    $tpl->assign("EXERCISENAME",$exerciseName);
                    $tpl->parse("EXERCISEROW", ".WorkoutRow");
                }

                $tpl->assign("WORKOUTTITLE", $workout['WorkoutDate']);
                $tpl->assign("COLSPAN", $maxSetsCount);
                $tpl->parse("WORKOUTTABLES", ".WorkoutTable");
            }		
	}
}	
else
{
	$tpl->assign("WORKOUTTABLES","<p>Please log in</p>");
}

$tpl->parse("WORKOUTTABLES","WorkoutHistoryPage");
$tpl->FastPrint();

function getTimeStringFromMilliseconds($milliseconds)
{
    $hours = floor($milliseconds / 3600000);
    $milliseconds = $milliseconds - ($hours * 3600000);

    $minutes = floor($milliseconds / 60000);
    $milliseconds = $milliseconds - ($minutes * 60000);

    $seconds = round($milliseconds / 1000);

    return $hours . ":" . $minutes . ":" . $seconds;
}

function getPace($miles, $milliseconds)
{
    $millisecondsForOneMile = ($milliseconds / $miles);
    return getTimeStringFromMilliseconds($millisecondsForOneMile);
}

?>

<?php include("Includes/footer.php"); ?>