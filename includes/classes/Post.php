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
		echo 'comes here';
		echo  '<script>console.log($imageName)</script>';
		echo '<script>console.log($body)</script>';

		$body = mysqli_real_escape_string($this->con, $body);//escapes single quotes

		$body = str_replace('\r\n', '\n',$body);//replace line break with HTML line break
		$body = nl2br($body);//new line to -> break

		$check_empty = preg_replace('/\s+/', '', $body); //Deletes all extra spaces 
      
		if($check_empty != "" || $imageName!= "") {//If it does not contain only spaces

			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get userId
			$added_by = $this->user_obj->getUserId();

			//insert content 
			$query = mysqli_query($this->con, "INSERT INTO contents VALUES('','$added_by','$date_added',1,'')");
			$last_insert_id = mysqli_insert_id($this->con);//returns last insert id
			// if($imageName!=""){
			$img_query = mysqli_query($this->con,"INSERT INTO photos VALUES('$last_insert_id','$imageName','$body')");
			// }
			// else{
			// $query2 = mysqli_query($this->con,"INSERT INTO posts VALUES('$last_insert_id','$body')");
			// }
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

			if($photo!=""){
				echo "<script>console.log('$photo')</script>";
			}

			$added_by = $row['user_name'];
			$date_time = $row['date_added'];

			//Check if user who posted, has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if($added_by_obj->isClosed()) {
				continue;
			}


			$user_logged_obj = new User($this->con, $userLoggedIn);

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

				$str .= "<div class='status_post' id='$id'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								$delete_button
							</div>
							
							<div id='post_body'>
								$body
								<br>
								$imageDiv
								<br>
							</div>
							<p>For Comments</p>
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

	public function loadPostsWithPhotos(){
		$userLoggedId = $this->user_obj->getUserId();
		$userLoggedIn = $this->user_obj->getUsername();
		
		$str = ""; //String to return

		$posts_image_query = mysqli_query($this->con, "SELECT *
													FROM contents c
													JOIN photos ph  ON c.content_id = ph.content_id
													JOIN users u  ON c.user_id = u.user_id
													JOIN usernames un ON c.user_id=un.user_id
													WHERE c.deleted=0 AND u.is_active=1
													ORDER BY c.content_id DESC");
		

		while($row = mysqli_fetch_array($posts_image_query)) {

			$id = $row['content_id'];
			$body = $row['photo_quote'];
			$added_by = $row['user_name'];
			$date_time = $row['date_added'];
			$image_path = $row['photo'];

			//Check if user who posted, has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if($added_by_obj->isClosed()) {
				continue;
			}


			$user_logged_obj = new User($this->con, $userLoggedIn);

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

				if($image_path != "") {
					$imageDiv = "<div class='postedImage'>
									<img src='$image_path'>
								</div>";
				}
				else {
					$imageDiv = "";
				}

				$str .= "<div class='status_post' id='$id'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								$delete_button
							</div>
							
							<div id='post_body'>
								$body
								<br>
								$imageDiv
								<br>
								<br>
							</div>
							<p>For Comments</p>
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
	
	public function loadPostsFriends2($data, $limit) {

		$page = $data['page']; 
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM contents WHERE deleted=0 ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by)){

					if($num_iterations++ < $start)
						continue; 


					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					else 
						$delete_button = "";


					$user_details_query = mysqli_query($this->con, "SELECT 
																first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


					?>
					<script> 
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


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
						if($interval->d === 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " days ago";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " month". $days;
						}
						else {
							$time_message = $interval->m . " months". $days;
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

					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr>";
				}

				?>
				<script>

					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});


					});

				</script>
				<?php

			} //End while loop

			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		}

		echo $str;
	}

	
	public function loadProfilePosts($data) {

		// $page = $data['page']; 
		$profile_user_obj = new User($this->con,$data);
		$profile_user_id = $profile_user_obj->getUserId();
		$userLoggedIn = $this->user_obj->getUsername();

		// if($page == 1) 
		// 	$start = 0;
		// else 
		// 	$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, 
											"SELECT *
											FROM contents c
											JOIN posts p USING(content_id) 
											JOIN users u USING(user_id)
											JOIN usernames un USING(user_id)
											WHERE c.user_id='$profile_user_id' and deleted=0
											ORDER BY content_id DESC"
											);

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['content_id'];
				$body = $row['post_content'];
				$added_by = $row['user_name'];
				$date_time = $row['date_added'];

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}


				// if($num_iterations++ < $start)
				// 	continue; 


				//Once 10 posts have been loaded, break
				

				if($userLoggedIn == $added_by)
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				else 
					$delete_button = "";


				//user-details
				$first_name = $row['first_name'];
				$last_name = $row['last_name'];
				$profile_pic = $row['profile_pic'];

				if($userLoggedIn === $added_by)
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>Delete</button>";
				else 
					$delete_button = "";


				?>
				<?php

				// $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
				// $comments_check_num = mysqli_num_rows($comments_check);


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

				$str .= "<div class='status_post' '>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								$delete_button
							</div>
							<div id='post_body'>
								$body
								<br>
								<br>
								<br>
							</div>
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

				// if($count > $limit) 
				// 	$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
				// 				<input type='hidden' class='noMorePosts' value='false'>";
				// else 
				// 	$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		}

		echo $str;
	}
}

?>