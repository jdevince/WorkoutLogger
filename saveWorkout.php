<?php
    session_start();
    include "Includes/databaseClassMySQLi.php";
    include("Includes/commonFuncs.php");
    
    if(isset($_POST["exercises"]) && isset($_POST["date"]) && isset($_SESSION["username"]))
    {
        $exercises = $_POST["exercises"];
        $date = $_POST["date"];
        $username = $_SESSION["username"];

        $db = new database();
		$db->pick_db("workoutlog");

        $userId = getUserIdFromUsername($username);
        
        if ($userId != null)
        {
            //Add workout to tbl_workoutlog_workout, get workoutId
            $stmt = $db->prepare("INSERT INTO tbl_workoutlog_workout (UserId, WorkoutDate) VALUES (?, STR_TO_DATE(?, '%Y-%m-%d'))");
            $stmt->bind_param('is',$userId, $date);
            $stmt->execute();
            if ($stmt->affected_rows == 1)
            {
                $stmt->free_result();
                $res = $db->send_sql("SELECT LAST_INSERT_ID() AS Id");
                if ($res->num_rows > 0)
                {
                    $row = $res->fetch_assoc();                     
                    $workoutId = $row['Id'];

                    foreach ($exercises as $exercise)
                    {
                        //Add each exercise to tbl_WorkoutLog_Exercise linking to the workoutId                      
                        $stmt = $db->prepare("SELECT ExerciseNameId FROM tbl_workoutlog_exercisename WHERE ExerciseName = ?");
                        $stmt->bind_param('s',$exercise['name']);
                        $stmt->execute();
                        $stmt->bind_result($exerciseNameId);
                        $stmt->fetch();
                        $stmt->free_result();
                        if ($exerciseNameId == null)
                        {
                            //New exercise name, add it to tbl_workoutlog_exercisename
                            $stmt = $db->prepare("INSERT INTO tbl_workoutlog_exercisename (ExerciseName, ExerciseCategory) VALUE (?, ?)");
                            $stmt->bind_param('ss',$exercise['name'], $exercise['category']);
                            $stmt->execute();
                            $stmt->free_result();
                            $res = $db->send_sql("SELECT LAST_INSERT_ID() AS Id");
                            $row = $res->fetch_assoc();                     
                            $exerciseNameId = $row['Id'];
                        }

                        $db->send_sql("INSERT INTO tbl_workoutlog_exercise (WorkoutId, ExerciseNameId) VALUES (" . $workoutId . ", " . $exerciseNameId . ")");
                        $res = $db->send_sql("SELECT LAST_INSERT_ID() AS Id");
                        if ($res->num_rows > 0)
                        {
                            $row = $res->fetch_assoc();                     
                            $exerciseId = $row['Id'];
                            
                            foreach ($exercise['rounds'] as $round)
                            {
                                //Add each round
                                if ($exercise['category'] == 'set')
                                {
                                    $stmt = $db->prepare("INSERT INTO tbl_workoutlog_category_set (ExerciseId, Weight, WeightUnit, NumOfReps, Notes) VALUES (?,?,?,?,?)");
                                    $stmt->bind_param('iisis',$exerciseId, $round['weight'],$round['weightUnit'],$round['reps'],$round['notes']);
                                    $stmt->execute();
                                    $stmt->free_result();
                                }
                                else if ($exercise['category'] == 'endurance')
                                {
                                    $meters = $round['distanceUnit'] == 'kilometers' ? $round['distance'] * 1000 : $round['distance'] * 1609.344;
                                    $milliseconds = getMillisecondsFromTime($round['time']);

                                    $stmt = $db->prepare("INSERT INTO tbl_workoutlog_category_endurance (ExerciseId, Meters, Milliseconds, Notes) VALUES (?,?,?,?)");
                                    $stmt->bind_param('iiis',$exerciseId, $meters, $milliseconds, $round['notes']);
                                    $stmt->execute();
                                    $stmt->free_result();
                                }
                                else //Admin added category
                                {
                                    $fields = "(ExerciseId, ";
                                    $values = "(" . $exerciseId . ", ";
                                    foreach ($round as $fieldName => $fieldData)
                                    {
                                        $fields .= $fieldName . ", ";
                                        $allCategoriesDetails = json_decode(getAllCategoriesDetails(), TRUE);

                                        $type = $allCategoriesDetails[$exercise['category']][$fieldName];
                                        if ($type == "varchar")
                                            $values .= "'" . $db->real_escape_string($fieldData) . "', "; //Add single quotes for strings
                                        else if ($type == "int")
                                        {
                                            if ($fieldData == "")
                                                $fieldData = 0;
                                            $values .= $db->real_escape_string($fieldData) . ", ";
                                        }
                                            
                                    }
                                    $fields = substr($fields,0,-2) . ")";
                                    $values = substr($values,0,-2) . ")";

                                    $query = "INSERT INTO tbl_workoutlog_category_" . $db->real_escape_string($exercise['category']) . " " . $fields . " VALUES " . $values;
                                    
                                    $db->send_sql($query);
                                }
                            }
                        }
                    }
                }
            }
        }
        echo true;
    }
    else
    {
        echo false;
    }


    function getMillisecondsFromTime($time)
    {
        $timeArray = explode("/",$time);

        $hours = $timeArray[0];
        $minutes = $timeArray[1];
        $seconds = $timeArray[2];

        return ($hours * (60*60*1000)) + ($minutes * (60*1000)) + ($seconds * 1000);
    }
    

?>