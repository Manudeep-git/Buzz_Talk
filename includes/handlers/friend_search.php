<?php  
	include("../../config/config.php");
	include("../classes/User.php");

	$query = $_POST['query'];
	$userLoggedIn = $_POST['userLoggedIn'];

	$names = explode(" ", $query);//split string by space delimiter

	//Users can be searched through user_name, first_name, last_name, so we have to handle all three cases
	$usersReturned = mysqli_query($con, "SELECT * 
										FROM usernames 
										JOIN users USING(user_id)
										WHERE user_name LIKE '$query%' AND is_active=1 LIMIT 5");

	//Searching though first name and last name
	if(count($names) == 2) {
		$usersReturned = mysqli_query($con, "SELECT * 
											 FROM users JOIN usernames using(user_id)
											 WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') 
											 AND is_active=1 LIMIT 5");
	}

	//Using only one name apart from user_name
	elseif(mysqli_num_rows($usersReturned)===0) {
		$usersReturned = mysqli_query($con, "SELECT * 
											 FROM users JOIN usernames using(user_id)
											 WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') 
											 AND is_active=1 LIMIT 5");
	}

	if($query != "") {
		while($row = mysqli_fetch_array($usersReturned)) {

			$user = new User($con, $userLoggedIn);

			if($row['user_name'] != $userLoggedIn) {
				$user_obj = new User($con, $row['user_name']);
				$user_obj_friends_array = $user_obj->getFriendArray();
				$mutual_friends = $user->getMutualFriends($user_obj_friends_array) . " Mutual Friends";
			}
			else {
				$mutual_friends = "";
			}

			echo "<div class='resultDisplay'>
					<a href='messages.php?u=" . $row['user_name'] . "' style='color: #000'>
						<div class='liveSearchProfilePic'>
							<img src='". $row['profile_pic'] . "'>
						</div>

						<div class='liveSearchText'>
							".$row['first_name'] . " " . $row['last_name']. "
							<p style='margin: 0;'>". $row['user_name'] . "</p>
							<p id='grey'>".$mutual_friends . "</p>
						</div>
					</a>
				</div>";
		}
	}

?>