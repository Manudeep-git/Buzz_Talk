<html>
<head>
	<title></title>
	<!-- Font-awesome css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" 
        integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" 
        crossorigin="anonymous" />
	<link rel="stylesheet" type="text/css" href="./includes/style.css">
</head>
<body>

	<style type="text/css">
		body{
			background-color: #fff;
		}

		form {
			position: absolute;
			top:0;
			padding-bottom: 2%;
		}
	</style>

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

		$get_likes = mysqli_query($con, "SELECT * FROM likes WHERE content_id='$post_id'");
		$total_likes = mysqli_num_rows($get_likes);

		$user_liked_check =  mysqli_query($con, "SELECT * FROM likes WHERE content_id='$post_id' and user_id='$userLoggedId'");
		$user_liked_rows = mysqli_num_rows($user_liked_check);

		//Like button
		if(isset($_POST['like_button'])) {
			$total_likes++;
			$query = mysqli_query($con, "INSERT INTO likes values('$post_id','$userLoggedId')");
			$user_liked_rows=$user_liked_rows+1;
		}
		
		//Unlike button
		if(isset($_POST['unlike_button'])) {
			$total_likes--;
			$query = mysqli_query($con, "DELETE FROM likes WHERE content_id='$post_id' and user_id='$userLoggedId'");
			$user_liked_rows=$user_liked_rows-1;
		}

		//Show Unlike only if user liked the post/content
		if($user_liked_rows === 1) {
			echo '<form action="likes.php?post_id=' . $post_id . '" method="POST">
					<div class="like_value">
						Likes('. $total_likes .')
					</div>
					&nbsp;
					<i class="far fa-thumbs-down"></i>
					<input type="submit" class="comment_like" name="unlike_button" value="Unlike">
				</form>
			';
		}
		else {
			echo '<form action="likes.php?post_id=' . $post_id . '" method="POST">
					<div class="like_value">
						Likes('. $total_likes .')
					</div>
					&nbsp;
					<i class="far fa-thumbs-up"></i>
					<input type="submit" class="comment_like" name="like_button" value="Like">
				</form>
			';
		}
	?>

</body>
</html>