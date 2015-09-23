$(document).ready(function () {
    if ($("#addWorkoutForm").length) //addWorkout.php
    {
        AddExercise();
    }


	$("#signUpForm").submit( function() {
		var request = $.ajax({
		   method: "POST",
		   url: "createNewAccount.php",
		   data: $("#signUpForm").serialize()
		 });

		 request.done( function(msg) {
			 if (msg == true)
			 {
				 window.location.href = "addWorkout.php";
			 }
			 else
			 {
				//alert(msg);	
				//alert("That username is taken. Please try a new one.");
				$("#createAccountErrorMsg").text("That username is taken. Please try a new one.");
			 }
		 });
		 return false;
	});
	
	$("#loginForm").submit( function() {
		var request = $.ajax({
		   method: "POST",
		   url: "verifyLogin.php",
		   data: $("#loginForm").serialize()
		 });

		 request.done( function(msg) {
			 if (msg == true)
			 {
				 window.location.href = "viewWorkoutHistory.php";
			 }
			 else
			 {
				//alert(msg);	
				//alert("That username/password combo was incorrect. Please try again.");
				$("#loginErrorMsg").text("That username/password combo was incorrect. Please try again.");
			 }
		 });
		 return false;
	});
	
	$("#logoutButton").click( function() {
		var request = $.ajax({
		   url: "logout.php"
		 });
	})

    //
    //
    //addWorkout.php
    //
    //

    //Add Set Button Click Handler
	$(document).on('click', '.addSet', function () {
	    AddSet($(this).parent());
	    $(this).prev(".setRow").children().first().children().focus(); //Focus on the first input of the new set
	});

    //Remove Set Button Click Handler
	$(document).on('click', '.removeSet', function () {
	    if ($(this).parent().children('.setRow').length > 1)
	    {
	        $(this).parent().children('.setRow').last().remove();
	    }
	});

    //Add Exercise Button Click Handler
	$("#addExercise").click(function () {
	    AddExercise();
	    $(this).prevAll(".exercise").first().children(".setRow").first().children().first().children().focus();
	});

    //Remove Exercise Button Click Handler
	$("#removeExercise").click(function () {
	    if ($(".exercise").length > 1)
	    {
	        $(".exercise").last().remove();
	    }
	});

    //Category Changed
	$(document).on('change', '.categorySelect', function () {
	    var category = $(this).find(":selected").text();
	    var oldExerciseDiv = $(this).closest(".exercise");
	    var exerciseIndex = oldExerciseDiv.prevAll(".exercise").length;
	    var newExerciseDiv = $(getNewExerciseHtml(exerciseIndex, category));
	    oldExerciseDiv.replaceWith(newExerciseDiv);
	    newExerciseDiv.children(".setRow").first().children().first().children().focus();
	});

	function getCategorySelect(exerciseIndex, selectedCategory)
	{
	    var categorySelect = "<select class='categorySelect' name=\"exercises[" + exerciseIndex + "][category]\">";
	    for (var key in exerciseCategoriesArray)
	    {
	        if (exerciseCategoriesArray.hasOwnProperty(key))
	        {
	            if (key == selectedCategory)
	                categorySelect += "<option selected='selected' value='" + key + "'>" + key + "</option>"
	            else
	                categorySelect += "<option value='" + key + "'>" + key + "</option>"
	        }
	    }
	    categorySelect += "</select>";
	    return categorySelect;
	}

	function AddExercise() {
	    var exerciseIndex = $(".exercise").length;
	    var newExercise = getNewExerciseHtml(exerciseIndex, "set");

	    if (exerciseIndex == 0)
	    {
	        $("#workoutDateGroup").after(newExercise);
	    }
	    else
	    {
	        $(".exercise").last().after(newExercise);
	    }
	}

	function getNewExerciseHtml(exerciseIndex, category)
	{
	    return "    <div class='exercise'> \
                        <br><br> \
                        <div class='form-group setRow'> \
                            <div class='col-sm-2 col-sm-offset-1'> \
                                <input type='text' class='form-control' name=\"exercises[" + exerciseIndex + "][name]\" placeholder='Exercise Name' required> \
                            </div> \
                            <div class='col-sm-1'>"
                                + getCategorySelect(exerciseIndex, category) +
                            "</div>" + getNewRoundString(exerciseIndex, 0, category) +
                        "</div> \
                        <button type='button' class='addSet btn btn-default col-md-2 col-md-offset-4'>Add Set</button> \
                        <button type='button' class='removeSet btn btn-default col-md-2'>Remove Set</button> \
                    </div>";
	}

	function getNewRoundString(exerciseIndex, setIndex, category)
	{
	    var offset = "";
	    if (setIndex != 0)
	    {
	        offset = " col-sm-offset-4";
	    }
	    if (category == 'set')
	    {
	        var newSet = "  <div class='col-sm-2" + offset + "'>\
                                <input type='number' class='form-control' name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][weight]\" placeholder='Weight'>\
                            </div>\
                            <div class='col-sm-1'>\
                                <select name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][weightUnit]\">\
                                    <option value='pounds'>lbs</option>\
                                    <option value='kilograms'>kgs</option>\
                                </select>\
                            </div>\
                            <div class='col-sm-2'>\
                                <input type='number' class='form-control' name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][reps]\" placeholder='Reps'>\
                            </div>  \
                            <div class='col-sm-2'>\
                                <input type='text' class='form-control' name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][notes]\" placeholder='Notes'>\
                            </div>";
	    }

	    else if (category == 'endurance')
	    {
	        var newSet = "  <div class='col-sm-2" + offset + "'>\
                                <input type='number' class='form-control' name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][distance]\" placeholder='Distance'>\
                            </div>\
                            <div class='col-sm-1'>\
                                <select name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][distanceUnit]\">\
                                    <option value='miles'>Miles</option>\
                                    <option value='kilometers'>Kilometers</option>\
                                </select>\
                            </div>\
                            <div class='col-sm-2'>\
                                <input type='text' class='form-control' name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][time]\" placeholder='Time: hh/mm/ss' pattern='[0-9][0-9]/[0-9][0-9]/[0-9][0-9]'>\
                            </div>  \
                            <div class='col-sm-2'>\
                                <input type='text' class='form-control' name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][notes]\" placeholder='Notes'>\
                            </div>";
	    }
	    
	    else //Admin created 
	    {
	        var fields = exerciseCategoriesArray[category];
	        var newSet = "";

	        for (var key in fields)
	        {
	            var type = "";
	            if (fields[key] == "int")
	                type = "number";
	            else if (fields[key] == "varchar")
	                type = "text";

	            newSet += " <div class='col-sm-2" + offset + "'> \
                                <input type='" + type + "' class='form-control' name=\"exercises[" + exerciseIndex + "][rounds][" + setIndex + "][" + key + "]\" placeholder='" + key + "'> \
                            </div>";

	            offset = ""; //only offset first div
	        }
	    }
	    return newSet;
	}

	function AddSet(exerciseDiv) {
	    var setIndex = exerciseDiv.children('.setRow').length;
	    var exerciseIndex = exerciseDiv.prevAll('.exercise').length;
	    var category = exerciseDiv.find('.categorySelect option:selected').text();
	    var newSet =   "<div class='form-group setRow'>" + getNewRoundString(exerciseIndex, setIndex, category) + "</div>";
	    exerciseDiv.children('.setRow').last().after(newSet);
	}

	$("#addWorkoutForm").submit(function () {
	    var request = $.ajax({
	        method: "POST",
	        url: "saveWorkout.php",
	        data: $("#addWorkoutForm").serialize()
	    });

	    request.done(function (msg) {
	        if (msg == true) {
	            $("#addWorkoutForm").after("<p id='saved'>Saved!</p>");
	            setTimeout(function () { $("#saved").after("<p>Redirecting to Workout History...</p>"); }, 500);
	            setTimeout(function () { window.location.href = "viewWorkoutHistory.php"; }, 3000);
	        }
	        else {
	            alert(msg);	
	            $("#addWorkoutForm").after("<p>Error saving. Please refresh the page and try again.</p>");
	        }
	    });
	    $(this).children("button[type=submit]").prop("disabled", true);
	    
	    return false;
	});

    //
    //
    //viewWorkoutHistory.php
    //
    //

	var dataFieldIndex = 1;
	$("#addDataField").click(function () {
	    //var dataFieldIndex = $(this).prevAll('.dataField').length; //Cannot use when removing elements

	    var newDataField = "<div class='form-group dataField'> \
                                <label class='control-label col-sm-2 col-sm-offset-3' for='dataField[" + dataFieldIndex + "][name]'>Data Field Name:</label> \
                                <div class='col-sm-3 col-sm-offset-0 col-xs-8 col-xs-offset-2'> \
	                                <input type='text' class='form-control' name='dataField[" + dataFieldIndex + "][name]' required> \
                                </div> \
                                <div class='col-sm-1'> \
                                    <select name='dataField[" + dataFieldIndex + "][category]' class='ExerciseCategorySelect'> \
                                        <option value='text'>Text</option> \
                                        <option value='number'>Number</option> \
                                    </select> \
                                </div> \
                                <div class='removeDataFieldButton glyphicon glyphicon-remove col-xs-1' style='cursor:pointer'></div> \
                            </div>";
	    dataFieldIndex = dataFieldIndex + 1;
	    $(this).before(newDataField);
	});

	$(document).on('click', '.removeDataFieldButton', function () {
	    $(this).parent().remove();
	});

	$("#addExerciseCategoryForm").submit(function () {
	    var request = $.ajax({
	        method: "POST",
	        url: "saveNewExerciseCategory.php",
	        data: $("#addExerciseCategoryForm").serialize()
	    });

	    request.done(function (msg) {
	        if (msg == true) {
	            $("#addExerciseCategoryForm").after("<p>Saved!</p>");
	        }
	        else {
	            //alert(msg);
	            $("#addExerciseCategoryForm").after("<p>Error. Ensure that only letters, numbers, underscores, and hyphens are entered.</p>");
	        }
	    });
	    return false;
	});
});
