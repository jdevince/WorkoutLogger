<?php
    function getAllCategoriesDetails() {
        $db = new database();
	    $db->pick_db("workoutlog");

        $res = $db->send_sql("SELECT table_name, column_name, data_type FROM information_schema.columns WHERE table_schema='workoutlog' and table_name LIKE 'tbl_workoutlog_category_%'");
        $categories = $res->fetch_all(MYSQLI_ASSOC);
   
        foreach ($categories as $category)
        {
            $categoryName = substr($category['table_name'],24);
            $columnName = $category['column_name'];
            $columnDataType = $category['data_type'];
            if (strtolower($columnName) != strtolower($categoryName . "Id") && strtolower($columnName) != strtolower("ExerciseId"))
                $allCategoriesDetails[$categoryName][$columnName] = $columnDataType;
        }
        return json_encode($allCategoriesDetails);
    }

    function isAdmin($username)
    {
        $db = new database();
		$db->pick_db("workoutlog");
        if ($stmt = $db->prepare("SELECT IsAdmin FROM tbl_workoutlog_users WHERE UserName = ?"))
        {
            $stmt->bind_param('s',$username);
            $stmt->execute();
            $stmt->bind_result($IsAdmin);
            $stmt->fetch();
            if ($IsAdmin == 1)
            {
                $result = true;
            }
        }
        $db->disconnect();
        return isset($result) ? $result : false;
    }

    function getUserIdFromUsername($username)
    {
        $db = new database();
		$db->pick_db("workoutlog");
        if ($stmt = $db->prepare("SELECT UserId FROM tbl_workoutlog_users WHERE UserName = ?"))
        {
            $stmt->bind_param('s',$username);
            $stmt->execute();
            $stmt->bind_result($userId);
            $stmt->fetch();
            $result = $userId;
        }
        $db->disconnect();
        return isset($result) ? $result : null;
    }
?>