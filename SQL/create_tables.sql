DROP TABLE tbl_workoutlog_category_endurance;
DROP TABLE tbl_workoutlog_category_set;
DROP TABLE tbl_workoutlog_exercise;
DROP TABLE tbl_workoutlog_exercisename;
DROP TABLE tbl_workoutlog_workout;
DROP TABLE tbl_workoutlog_users;

CREATE TABLE tbl_workoutlog_users
(
	UserId INT NOT NULL AUTO_INCREMENT,
	UserName VARCHAR(50) NOT NULL UNIQUE,
	Password VARCHAR(255) NOT NULL,
	IsAdmin BIT NOT NULL DEFAULT 0,
	PRIMARY KEY (UserId)
);

CREATE TABLE tbl_workoutlog_workout
(
	WorkoutId INT NOT NULL AUTO_INCREMENT,
	UserId INT NOT NULL,
	WorkoutDate DATE NOT NULL,
	PRIMARY KEY (WorkoutId),
	FOREIGN KEY (UserId) REFERENCES tbl_workoutlog_users(UserId)
);

 CREATE TABLE tbl_workoutlog_exercisename
 (
	ExerciseNameId INT NOT NULL AUTO_INCREMENT,
	ExerciseName VARCHAR(100),
	ExerciseCategory VARCHAR(50), -- 0 for exercises with sets/reps, 1 for exercises with time and distance. Allows room for additions
	PRIMARY KEY (ExerciseNameId)
 );

CREATE TABLE tbl_workoutlog_exercise
(
	ExerciseId INT NOT NULL AUTO_INCREMENT,
	WorkoutId INT NOT NULL,
	ExerciseNameId INT NOT NULL,
	PRIMARY KEY (ExerciseId),
	FOREIGN KEY (WorkoutId) REFERENCES tbl_workoutlog_workout(WorkoutId),
	FOREIGN KEY (ExerciseNameId) REFERENCES tbl_workoutlog_exercisename(ExerciseNameId)
 );
 
 CREATE TABLE tbl_workoutlog_category_endurance
 (
	EnduranceId INT NOT NULL AUTO_INCREMENT,
	ExerciseId INT NOT NULL,
	Meters DOUBLE,
	Milliseconds DOUBLE,
	Notes VARCHAR(200),
	PRIMARY KEY (EnduranceId),
	FOREIGN KEY (ExerciseId) REFERENCES tbl_workoutlog_exercise(ExerciseId)
 );
 
 CREATE TABLE tbl_workoutlog_category_set
 (
	SetId INT NOT NULL AUTO_INCREMENT,
	ExerciseId INT NOT NULL,
	Weight INT,
	WeightUnit VARCHAR(100),
	NumOfReps INT NOT NULL,
	Notes VARCHAR(200),
	PRIMARY KEY (SetId),
	FOREIGN KEY (ExerciseId) REFERENCES tbl_workoutlog_exercise(ExerciseId)
 );
 
