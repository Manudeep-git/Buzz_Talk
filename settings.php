<?php
   include("./includes/header.php");
   include("./form_handlers/settings_handler.php");

	//getting user details
	$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE user_id='$userLoggedId'");
	$row = mysqli_fetch_array($user_data_query);

	$first_name = $row['first_name'];
	$last_name = $row['last_name'];
	$email = $row['email'];
?>

<div class="settings_main_column column">
    <h4>Settings Page</h4>

	<div class="card mb-3" style="max-width: 500px;">
  		<div class="row no-gutters">
    		<div class="col-md-4">
				<img src="<?php echo $user['profile_pic']?>" alt="...">
    		</div>
			<div class="col-md-8">
				<div class="card-body">
					<h5 class="card-title"><?php echo ucfirst($username['user_name']) ?><h5>
					<a href="#" class="btn btn-primary">Upload Profile picture</a>
				</div>
			</div>
  		</div>
	</div>
	<br>

	<form action="settings.php" method="POST">
		First Name: <input type="text" name="first_name" value="<?php echo $first_name; ?>" id="settings_input"><br>
		Last Name: <input type="text" name="last_name" value="<?php echo $last_name; ?>" id="settings_input"><br>
		Email: <input type="text" name="email" value="<?php echo $email; ?>" id="settings_input"><br>

		<?php echo $display_msg; ?>

		<input type="submit" name="update_details" id="save_details" value="Update Details" class="btn btn-primary"><br>
	</form>
	<hr>


    <!-- Change Password -->

	<h5>Change Password</h5>
	<form action="settings.php" method="POST">
		Old Password: <input type="password" name="old_password" id="settings_input"><br>
		New Password: <input type="password" name="new_password_1" id="settings_input"><br>
		New Password Again: <input type="password" name="new_password_2" id="settings_input"><br>

		<?php echo $password_message; ?>

		<input type="submit" name="update_password" id="save_details" value="Update Password" class="btn btn-primary"><br>
	</form>

	<hr>

	<h5>Close Account</h5>
	<form action="settings.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Close Account" class="btn btn-danger">
	</form>

</div>
