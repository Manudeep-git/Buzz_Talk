<?php
class Post {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con,$user);
	}

	public function submitPost($body,$imageName) {
		$body = strip_tags($body); //removes html tags 

		$body = mysqli_real_escape_string($this->con, $body);//escapes single quotes

		$body = str_replace('\r\n', '\n',$body);//replace line break with HTML line break
		$body = nl2br($body);//new line to -> break

		$check_empty = preg_replace('/\s+/', '', $body); //Deletes all extra spaces 
      
		if($check_empty != "" || $imageName!= "") {//If it does not contain only spaces

			//For videos
			$body_array = preg_split("/\s+/",$check_empty);
			$vide0_indicator = false;

			foreach($body_array as $key => $value){
				if(strpos($value,"www.youtube.com/watch?v=")!==false){
					$vide0_indicator=true;
					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;
				}
			}

			$check_empty = implode(" ", $body_array);

			echo '<script>console.log("$check_empty")</script>';

			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get userId
			$added_by = $this->user_obj->getUserId();

			//insert content 
			$query = mysqli_query($this->con, "INSERT INTO contents VALUES('','$added_by','$date_added',1,0)");
			$last_insert_id = mysqli_insert_id($this->con);//returns last insert id
			if($imageName!=""){
				$img_query = mysqli_query($this->con,"INSERT INTO photos VALUES('$last_insert_id','$imageName','$body')");
			}
			elseif($vide0_indicator){
				$video_query = mysqli_query($this -> con,"INSERT INTO videos VALUES('last_insert_id','$check_empty')");
			}
			else{
				$query2 = mysqli_query($this->con,"INSERT INTO posts VALUES('$last_insert_id','$body')");
			}
		}
	}

	public function loadPostsFriends(){
		
		$userLoggedId = $this->user_obj->getUserId();
		$userLoggedIn = $this->user_obj->getUsername();
		
		$str = ""; //String to return

		$data_query = mysqli_query($this->con, "SELECT *
												FROM contents c
												JOIN users u  ON c.user_id = u.user_id
												JOIN usernames un ON c.user_id=un.user_id
												WHERE c.deleted=0 AND u.is_active=1
												ORDER BY c.date_added DESC");
		

		while($row = mysqli_fetch_array($data_query)) {
			$id = $row['content_id'];

			$posts_query = mysqli_query($this->con, "SELECT * FROM posts where content_id=$id;");
			$images_query = mysqli_query($this->con, "SELECT * FROM photos WHERE content_id=$id");
			$videos_query = mysqli_query($this->con, "SELECT * FROM videos WHERE content_id=$id");
			$photo="";
			$postbody="";
			$videobody="";

			$post_row = mysqli_fetch_array($posts_query);
			$photo_row = mysqli_fetch_array($images_query);
			$video_row = mysqli_fetch_array($videos_query);

			if(mysqli_num_rows($posts_query)==1){
				$postbody = $post_row['post_content'];
			}

			if(mysqli_num_rows($images_query)==1){
				$photo_quote = $photo_row['photo_quote'];
				$photo = $photo_row['photo'];
			}

			if(mysqli_num_rows($videos_query)==1){
				$videobody = $video_row['video'];
			}

			$added_by = $row['user_name'];
			$date_time = $row['date_added'];

			//Check if user who posted, has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if($added_by_obj->isClosed()) {
				continue;
			}


			$user_logged_obj = new User($this->con, $userLoggedIn);

			//Logged user can either be a follower of followed by owner of content
			if($user_logged_obj->isFriend($row['user_id'])){

				//user-details
				$first_name = $row['first_name'];
				$last_name = $row['last_name'];
				$profile_pic = $row['profile_pic'];


				if($userLoggedIn === $added_by)
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>
										Delete&nbsp;<i class='fas fa-trash' size:3x></i></button>";
				else 
					$delete_button = "";

				?>
				<script>
					$(document).ready(function () {
							$('#<?php echo $id; ?>').on('click', () => {
							
							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						})
					});
				</script>
				
				<?php

				//Check for comments for each post of users friends
				$comments_check = mysqli_query($this->con, "SELECT * 
															FROM comments 
															WHERE content_id='$id' and removed=0");
				$comments_check_num = mysqli_num_rows($comments_check);

				//Check for likes for each post of users friends
				$likes_check = mysqli_query($this->con, "SELECT * 
															FROM likes 
															WHERE content_id='$id'");

				$likes_check_num = mysqli_num_rows($likes_check);

				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
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
						$time_message = $interval->i . " minute ago";
					}
					else {
						$time_message = $interval->i . " minutes ago";
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

				if($photo != "") {
					$imageDiv = "<div class='postedImage'>
									<img src='$photo'>
								</div>";
					$body = $photo_quote;
				}
				else {
					$imageDiv = "";
					$body = $postbody;
				}

				if($videobody!=""){
					$body = $videobody;
				}

				$added_by2 = ucfirst($added_by);

				$str .= "<div class='status_post' id='$id'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $added_by2 </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								$delete_button
							</div>
							
							<div id='post_body'>
								$body
								<br>
								$imageDiv
								<br>
							</div>

							<div class='newsfeedPostOptions'>
								<i class='fas fa-comment'></i>
								Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
								<iframe src='likes.php?post_id=$id' id='like_iframe' scrolling='no'></iframe>
							</div>
						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comments.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
						</div>
						<hr>";
			}
			?>
			<!-- Delete post functinality -->
			<script>

				$(document).ready(() => {
					$('#post<?php echo $id; ?>').on('click', () => {
						bootbox.confirm("Are you sure you want to delete this post?", (result) => {
							console.log(<?php echo $id;?>);
							$.post('./includes/handlers/delete_post.php?post_id=<?php echo $id;?>',{result: result});
							console.log(result);
							if(result)
								location.reload();
						});
					});
				});
			</script>
			<?php
		}
		
			echo $str;
	}
	
	public function loadProfilePosts($data) {
		
		$profile_user_obj = new User($this->con,$data);
		$profile_user_id = $profile_user_obj->getUserId();
		$userLoggedIn = $this->user_obj->getUsername();


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, 
											"SELECT *
											FROM contents c
											JOIN users u ON c.user_id = u.user_id
											JOIN usernames un ON c.user_id=un.user_id
											WHERE c.user_id='$profile_user_id' and c.deleted=0 and u.is_active=1
											ORDER BY c.date_added DESC"
											);

		if(mysqli_num_rows($data_query) > 0) {

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['content_id'];

				$posts_query = mysqli_query($this->con, "SELECT * FROM posts where content_id=$id;");
				$images_query = mysqli_query($this->con, "SELECT * FROM photos WHERE content_id=$id");
				$photo="";
				$postbody="";

				$post_row = mysqli_fetch_array($posts_query);
				$photo_row = mysqli_fetch_array($images_query);

				if(mysqli_num_rows($posts_query)==1){
					$postbody = $post_row['post_content'];
				}
	
				if(mysqli_num_rows($images_query)==1){
					$photo_quote = $photo_row['photo_quote'];
					$photo = $photo_row['photo'];
				}

				$added_by = $row['user_name'];
				$date_time = $row['date_added'];

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				if($userLoggedIn == $added_by)
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>Delete</button>";
				else 
					$delete_button = "";


				//user-details
				$first_name = $row['first_name'];
				$last_name = $row['last_name'];
				$profile_pic = $row['profile_pic'];


				?>
				<script>
					$(document).ready(function () {
							$('#<?php echo $id; ?>').on('click', () => {
							
							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						})
					});
				</script>
				<?php

				//Check for comments for each post of users friends
				$comments_check = mysqli_query($this->con, "SELECT * 
															FROM comments 
															WHERE content_id='$id' and removed=0");
				$comments_check_num = mysqli_num_rows($comments_check);

				//Check for likes for each post of users friends
				$likes_check = mysqli_query($this->con, "SELECT * 
															FROM likes 
															WHERE content_id='$id'");

				$likes_check_num = mysqli_num_rows($likes_check);

				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
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
						$time_message = $interval->i . " minute ago";
					}
					else {
						$time_message = $interval->i . " minutes ago";
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

				if($photo != "") {
					$imageDiv = "<div class='postedImage'>
									<img src='$photo'>
								</div>";
					$body = $photo_quote;
				}
				else {
					$imageDiv = "";
					$body = $postbody;
				}

				$added_by2 = ucfirst($added_by);

				$str .= "<div class='status_post' id='$id'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $added_by2 </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								$delete_button
							</div>
							<div id='post_body'>
								$body
								<br>
								$imageDiv
								<br>
								<br>
							</div>

							<div class='newsfeedPostOptions'>
								<i class='fas fa-comment'></i>
								Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
								<iframe src='likes.php?post_id=$id' id='like_iframe' scrolling='no'></iframe>
							</div>
						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comments.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
						</div>
						<hr>";

				?>
				<script>
					$(document).ready(() => {
					$('#post<?php echo $id; ?>').on('click', () => {
							bootbox.confirm("Are you sure you want to delete this post?", (result) => {
									console.log(<?php echo $id;?>);
									$.post('./includes/handlers/delete_post.php?post_id=<?php echo $id;?>',{result: result});
									console.log(result);
									if(result)
										location.reload();
							});
						});
					});
				</script>

				<?php

			} //End while

		}

		echo $str;
	}
}

?>