<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="./includes/style.css">
	<!-- Jquery js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
     integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" 
     crossorigin="anonymous">
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js" 
    integrity="sha512-XKa9Hemdy1Ui3KSGgJdgMyYlUg1gM+QhL6cnlyTe2qzMCYm4nAZ1PsVerQzTTXzonUR+dmswHqgJPuwCq1MaAg==" 
    crossorigin="anonymous">
    </script>
    <!-- Bootbox Js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" 
    integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" 
    crossorigin="anonymous"></script>
</head>
<body>
	<?php  

		require './config/config.php';
		include("./includes/classes/User.php");
		include("./includes/classes/Post.php");

		if(isset($_SESSION['username'])){
			$userLoggedIn = $_SESSION['username'];
			$userLoggedId = $_SESSION['user_id'];
			
			$user_obj = new User($con,$userLoggedIn);
	
			$username = $user_obj->getUserNameDetails();
	
			$user = $user_obj->getUserDetails();
		}
		else {
			header("Location: register.php");
		}

	//Get id of post
	if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}

	$user_query = mysqli_query($con, "SELECT *  
									  FROM contents c
									  JOIN usernames un  USING(user_id)
									  WHERE c.content_id='$post_id'");

	$row = mysqli_fetch_array($user_query);

	$commented_by = $row['user_id'];

	if(isset($_POST['postComment' . $post_id])) {

		$comment_body = $_POST['comment_body'];
		$comment_body = mysqli_escape_string($con, $comment_body);
		$date_time_now = date("Y-m-d H:i:s");
		$insert_comment = mysqli_query($con, "INSERT INTO comments VALUES ('',null,'$post_id', '$userLoggedId',
																					'$date_time_now', '$comment_body', '')");

		echo "<p>Comment Posted! </p>";
	}
	?>
	<form action="comments.php?post_id=<?php echo $post_id; ?>" id="comment_form" method="POST">
		<textarea name="comment_body"></textarea>
		<input type="submit" name="postComment<?php echo $post_id; ?>" value="Post!">
	</form>

	<!-- Load comments -->
	<?php  
	$get_comments = mysqli_query($con, "SELECT * 
										FROM comments 
										JOIN usernames ON comments.user_id = usernames.user_id
										JOIN users ON comments.user_id = users.user_id
										WHERE content_id='$post_id' and removed=0 and users.is_active=1
										ORDER BY comment_id ASC");
	$count = mysqli_num_rows($get_comments);

	if($count != 0) {

		while($comment = mysqli_fetch_array($get_comments)) {

			$comment_body = $comment['comment_text'];
			$commented_by = $comment['user_name'];
			$date_added = $comment['created_at'];
			$removed = $comment['removed'];
			$id = $comment['comment_id'];

			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_added); //Time of comment
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 
			if($interval->y >= 1) {
				if($interval === 1)
					$time_message = $interval->y . " year ago"; //1 year ago
				else 
					$time_message = $interval->y . " years ago"; //1+ year ago
			}
			else if ($interval->m >= 1) {

				if($interval->m == 1) {
					$time_message = $interval->m . " month ago";
				}
				else {
					$time_message = $interval->m . " months ago";
				}

			}
			else if($interval->d >= 1) {
				if($interval->d == 1) {
					$time_message = "Yesterday";
				}
				else {
					$time_message = $interval->d . " days ago";
				}
			}
			else if($interval->h >= 1) {
				if($interval->h == 1) {
					$time_message = $interval->h . " hour ago";
				}
				else {
					$time_message = $interval->h . " hours ago";
				}
			}
			else if($interval->i >= 1) {
				if($interval->i == 1) {
					$time_message = $interval->i . " min ago";
				}
				else {
					$time_message = $interval->i . " mins ago";
				}
			}
			else {
				if($interval->s < 30) {
					$time_message = "Just now";
				}
				else {
					$time_message = $interval->s . " seconds ago";
				}
			}

			$user_obj = new User($con, $commented_by);

			if($userLoggedId === $comment['user_id'])
					$delete_button = "<button class='delete_button btn-danger' id='comment$id'>Delete</button>";
			else 
					$delete_button = "";


			?>
			<div class="comment_section">
				<a href="<?php echo $commented_by?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic();?>" title="<?php echo $commented_by; ?>" style="float:left;" height="30"></a>
				<a href="<?php echo $commented_by?>" target="_parent"> <b> <?php echo ucfirst($commented_by); ?> </b></a>
				&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?> 
				<?php echo  $delete_button ?>
				<hr>
			</div>
			<!-- Delete comment functinality -->
			<script>
				$(document).ready(() => {
					$('#comment<?php echo $id; ?>').on('click', () => {
							bootbox.confirm("Are you sure you want to delete this post?", (result) => {
									console.log(<?php echo $id;?>);
									$.post('./includes/handlers/delete_comment.php?comment_id=<?php echo $id;?>',{result: result});
									console.log(result);
									if(result)
										location.reload();
							});
						});
					});
			</script>
			<?php

		}
	}
	else {
		echo "<center><br><br>No Comments to Show!</center>";
	}

	?>
</body>
</html>