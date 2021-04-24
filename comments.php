<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="./includes/style.css">
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
										JOIN usernames USING(user_id)
										WHERE content_id='$post_id' and removed=0
										ORDER BY comment_id ASC");
	$count = mysqli_num_rows($get_comments);

	if($count != 0) {

		while($comment = mysqli_fetch_array($get_comments)) {

			$comment_body = $comment['comment_text'];
			$commented_by = $comment['user_name'];
			$date_added = $comment['created_at'];
			$removed = $comment['removed'];

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


			?>
			<div class="comment_section">

				<a href="<?php echo $commented_by?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic();?>" title="<?php echo $commented_by; ?>" style="float:left;" height="30"></a>
				<a href="<?php echo $commented_by?>" target="_parent"> <b> <?php echo ucfirst($commented_by); ?> </b></a>
				&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?> 
				<hr>

			</div>
			<?php

		}
	}
	else {
		echo "<center><br><br>No Comments to Show!</center>";
	}

	?>
</body>
</html>