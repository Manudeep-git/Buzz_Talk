<?php 
	include("includes/header.php");

	$message_obj = new Message($con, $userLoggedIn);

	if(isset($_GET['u']))
		$user_to = $_GET['u'];
	else {
		$user_to = $message_obj->getMostRecentInteraction();
		if($user_to == false)
			$user_to = 'new';
	}

	if($user_to != "new")
		$user_to_obj = new User($con, $user_to);

	if(isset($_POST['post_message'])) {

		if(isset($_POST['message_body'])) {
			$body = mysqli_real_escape_string($con, $_POST['message_body']);
			$date = date("Y-m-d H:i:s");
			$message_obj->sendMessage($user_to_obj->getUserId(), $body, $date);
		}
} 
?>
		<div class="user_details column">
			<!-- user_image -->
            <div class="user-image" >
                <img style="max-width: 130px;" src="<?php echo $user['profile_pic']; ?>"/>
            </div>
			<br>
			<!-- user_details -->
            <div class="user_details_left_right">
                <a href='<?php echo $userLoggedIn;?>'><?php echo $user['first_name'].' '.$user['last_name']; ?></a>
                <br>
                <?php 
                    $user_obj = new User($con,$userLoggedIn);

                    echo "Followers:"." ".$user_obj->getFollowers()."<br>";
                    echo "Following:"." ".$user_obj->getFollowing()."<br>";
                    echo "Contents:"." ".$user_obj->getNumContents();
                ?>   
            </div>
        </div>

		<div class="main_column column" id="main_column">
			<?php  
			if($user_to != "new"){
				echo "<h4><a href='$user_to'>" .$user_to_obj->getFirstAndLastName() . "</a></h4><hr><br>";

				echo "<div class='loaded_messages' id='scroll_messages'>";
					echo $message_obj->getMessages($user_to);
				echo "</div>";
			}
			else {
				echo "<h4>New Message</h4>";
			}
			?>



			<div class="message_post">
				<form action="" method="POST">
					<?php
					if($user_to == "new") {
						echo "Select the friend you would like to message <br><br>";
						?> 
						To: <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'>

						<?php
						echo "<div class='results'></div>";
					}
					else {
						echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
						echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
					}

					?>
				</form>

			</div>

			<script>
				//Load a message at bottom of page and go there when latest message is sent
				var div = document.getElementById("scroll_messages");
				div.scrollTop = div.scrollHeight;
			</script>

			<hr>
			<div  id="conversations">
					<h4>Conversations</h4>

					<div class="loaded_conversations">
						<?php echo $message_obj->getConvos(); ?>
					</div>
					<br>
			</div>
	</div>
		<div class="user_details column" id="conversations" style="height: 40px;">
				<a id="new_message" style="max-width: 200px;"href="messages.php?u=new">New Message</a>
		</div>