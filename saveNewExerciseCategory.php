<?php
    session_start();
    include "Includes/databaseClassMySQLi.php";
    include "Includes/commonFuncs.php";
    
    if(isset($_POST["newCategoryName"]) && isset($_POST["dataField"]) && isset($_SESSION["username"]))
    {
        //var_dump($_POST);
        $db = new database();
		$db->pick_db("workoutlog");

        if (isAdmin($_SESSION["username"]))
        {
            if (!preg_match('/^[\w-]+$/', $_POST["newCategoryName"]))
            {
                echo false;
                exit;
            }

            $query = "CREATE TABLE tbl_workoutlog_category_" . $_POST["newCategoryName"] . " ( " 
                        . $_POST["newCategoryName"] . "Id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (" . $_POST["newCategoryName"] . "Id), " 
                        . "ExerciseId INT NOT NULL, FOREIGN KEY (ExerciseId) REFERENCES tbl_workoutlog_exercise(ExerciseId), ";
            foreach ($_POST["dataField"] as $field)
            {
                if (!preg_match('/^[\w-]+$/', $field["name"]))
                {
                    echo false;
                    exit;
                }

                if ($field["category"] == "text")
                {
                    $type = "VARCHAR(500)";
                }
                else
                {
                    $type = "INT";
                }
                $query = $query . $field["name"] . " " . $type . ", ";
            }
            $query = substr($query,0,-2) . ")"; //get rid of trailing comma and space for last field

            $res = $db->send_sql($query);
            echo true;  
            
        }  
        else 
        {
            echo "<p>Unauthorized</p>";
        }                                
    }

    else 
    {
        echo false;
    }

?>