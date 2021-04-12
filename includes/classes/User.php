<?php
class User {
	//users table
	private $user;//$userLoggedIn
	private $userId;
	//usernames table
	private $usernames;
	private $con;
	private $friend_array;

	// public function __construct($con, $user){
	// 	$this->con = $con;
	// 	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
	// 	$this->user = mysqli_fetch_array($user_details_query);
	// }

	public function __construct($con,$user)
	{
		$this->con= $con;
		$this->friend_array = array();
		//$user passed in here is the loggedin user object
		$username_details = mysqli_query($con, "SELECT * FROM usernames WHERE user_name='$user'");
		$this->usernames = mysqli_fetch_array($username_details);
		$this->userId= $this->usernames['user_id'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE user_id='$this->userId'");
		$this->user = mysqli_fetch_array($user_details_query);
	}

	public function getUserId(){
		return $this->userId;
	}

	public function getUsername() {
		return $this->usernames['user_name'];
	}

	public function getNumContents() {
		$query = mysqli_query($this->con, "SELECT * FROM contents WHERE user_id='$this->userId'");
		$contents = mysqli_num_rows($query);
		return $contents;
	}

	public function getFirstAndLastName() {
		$query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE user_id='$this->userId'");
		$row = mysqli_fetch_array($query);
		return $row['first_name'] . " " . $row['last_name'];
	}

	public function getProfilePic() {
		$userId = $this->user['user_id'];
		$query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE user_id='$userId'");
		$row = mysqli_fetch_array($query);
		return $row['profile_pic'];
	}


	public function isClosed() {
		$userId = $this->user['user_id'];
		$query = mysqli_query($this->con, "SELECT is_active FROM users WHERE user_id='$userId'");
		$row = mysqli_fetch_array($query);

		if($row['is_active'] === 0)
			return true;
		else 
			return false;
	}


	//Store all the followers and following of current user in a array
	public function getFriendArray() {
		$userId = $this->userId;
		//$friend_names_aray = array();
		$query = mysqli_query($this->con, "SELECT * 
										   FROM follows
										   WHERE user_id='$userId'
										   OR follower_id='$userId'");
		while($row = mysqli_fetch_array($query)){
			if($row['user_id'] == $userId){
				array_push($this->friend_array,$row['follower_id']);
			}
			else{
				array_push($this->friend_array,$row['user_id']);
			}
		}
		array_push($this->friend_array,$userId);
		return $this->friend_array;
	}


	//Only load posts from friends - need to be in friend_array
	public function isFriend($userId) {
		$friends_array = $this->getFriendArray();
		if(in_array($userId,$friends_array)){
			return true;
		}
		else{
			return false;
		}
	}

	//-----------------------------------------------------------------------------------------------

	//Not Looked at
	public function didReceiveRequest($user_from) {
		$user_to = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	//Not Looked at
	public function didSendRequest($user_to) {
		$user_from = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	//Not Looked at
	public function removeFollower($user_to_remove) {
		$logged_in_user = $this->user['username'];

		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
		$row = mysqli_fetch_array($query);
		$friend_array_username = $row['friend_array'];

		$new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']);
		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'");

		$new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove'");
	}

	//Not Looked at
	public function sendRequest($user_to) {
		$user_from = $this->user['username'];
		$query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES('', '$user_to', '$user_from')");
	}

	//Not Looked at
	public function getMutualFriends($user_to_check) {
		$mutualFriends = 0;
		$user_array = $this->user['friend_array'];
		$user_array_explode = explode(",", $user_array);

		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_check'");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friend_array'];
		$user_to_check_array_explode = explode(",", $user_to_check_array);

		foreach($user_array_explode as $i) {

			foreach($user_to_check_array_explode as $j) {

				if($i == $j && $i != "") {
					$mutualFriends++;
				}
			}
		}
		return $mutualFriends;

	}




}

?>