<?php
	 include("./includes/header.php");

	if(isset($_POST['cancel'])) {
		header("Location: settings.php");
	}

	if(isset($_POST['close_account'])) {
		//Update is_active but don't delete the user
		$account_close_query = mysqli_query($con, "UPDATE users SET is_active=0 WHERE user_id='$userLoggedId'");
		session_destroy();
		header("Location: register.php");
	}


?>

<div class="main_column column">

	<div class="alert alert-primary" role="alert">
		You are about to close your account, please re-check!
	</div>

	<h4>Close Account</h4>

	Are you sure you want to close your account?<br><br>

	<form action="account_close.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Yes!" class="btn-danger settings_submit">
		<input type="submit" name="cancel" id="update_details" value="No, Keep it Active" class="info settings_submit">
	</form>

</div>