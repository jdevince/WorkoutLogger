<?php
	include("Includes/header.php");
?>

<h2>Create a new account</h2>

<form role="form" id="signUpForm" class="form-horizontal">
	<div class="center-block text-center">
		<div class="form-group">
			<label class="control-label col-sm-1 col-sm-offset-4" for="username">Username: </label>
			<div class="col-sm-3 col-sm-offset-0 col-xs-8 col-xs-offset-2">
				<input type="text" class="form-control" name="username" id="username" placeholder="Enter your username" required>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-1 col-sm-offset-4" for="password">Password: </label>
			<div class="col-sm-3 col-sm-offset-0 col-xs-8 col-xs-offset-2">
				<input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
			</div>
		</div>
		<button type="submit" id="signUpSubmit" class="btn btn-default">Submit</button>
	</div>		
</form>
<p id="createAccountErrorMsg"></p>

<?php include("Includes/footer.php"); ?>