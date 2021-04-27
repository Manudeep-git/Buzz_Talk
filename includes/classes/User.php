<?php
class User {
	//users table
	private $user;//$userLoggedIn
	private $userId;
	//usernames table
	private $usernames;
	private $con;
	//friends
	private $friend_array = array();
	private $followers_array = array();
	private $following_array = array();

	

	// public function __construct($con, $user){
	// 	$this->con = $con;
	// 	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
	// 	$this->user = mysqli_fetch_array($user_details_query);
	// }

	public function __construct($con,$user)
	{
		$this->con= $con;
		//$user passed in here is the loggedin user object
		$username_details = mysqli_query($con, "SELECT * FROM usernames WHERE user_name='$user'");
		$this->usernames = mysqli_fetch_array($username_details);
		$this->userId= $this->usernames['user_id'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE user_id='$this->userId'");
		$this->user = mysqli_fetch_array($user_details_query);
	}

	public function getUserDetails(){
		return $this->user;
	}

	public function getUserNameDetails(){
		return $this->usernames;
	}

	public function getUserId(){
		return $this->userId;
	}

	public function getUsername() {
		return $this->usernames['user_name'];
	}

	public function getNumContents() {
		$query = mysqli_query($this->con, "SELECT * FROM contents WHERE user_id='$this->userId' and deleted=0");
		$contents = mysqli_num_rows($query);
		return $contents;
	}

	public function getFirstAndLastName() {
		return $this->user['first_name'] . " " . $this->user['last_name'];
	}

	public function getProfilePic() {
		return $this->user['profile_pic'];
	}


	public function isClosed() {
		if($this->user['is_active'] === 0)
			return true;
		else 
			return false;
	}


	//Store all the followers and following of current user in a array
	public function getFriendArray() {
		$userId = $this->getUserId();
		//$friend_names_aray = array();
		$query = mysqli_query($this->con, "SELECT * 
										   FROM follows
										   WHERE user_id='$userId'
										   OR follower_id='$userId'");

		while($row = mysqli_fetch_array($query)){
			if($row['user_id'] === $userId){
				if(!in_array($row['follower_id'],$this->friend_array)){
					array_push($this->friend_array,$row['follower_id']);
				}
			}
			else if($row['follower_id'] === $userId){
				if(!in_array($row['user_id'],$this->friend_array)){
					array_push($this->friend_array,$row['user_id']);
				}
			}
		}
		array_push($this->friend_array,$userId);
		return $this->friend_array;
	}

	public function getFollowersArray(){
		$userId = $this->getUserId();
		$query = mysqli_query($this->con, "SELECT * 
										   FROM follows
										   WHERE user_id='$userId'");
		while($row = mysqli_fetch_array($query)){
			array_push($this->followers_array,$row['follower_id']);
		}

		return $this->followers_array;
	}

	public function getFollowingArray(){
		$userId = $this->getUserId();
		$query = mysqli_query($this->con, "SELECT * 
										   FROM follows
										   WHERE follower_id='$userId'");

		while($row = mysqli_fetch_array($query)){
			array_push($this->following_array,$row['user_id']);
		}

		return $this->following_array;
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

	//Check if user is following logged in user
	public function isFollowedBy($userId) {
		$followers_array = $this->getFollowersArray();
		if(in_array($userId,$followers_array)){
			return true;
		}
		else{
			return false;
		}
	}

	public function isFollowing($userId) {
		$following_array = $this->getFollowingArray();
		if(in_array($userId,$following_array)){
			return true;
		}
		else{
			return false;
		}
	}

	public function getFollowers(){
		$userId = $this->userId;
		$query = mysqli_query($this->con, "SELECT * 
										   FROM follows
										   WHERE user_id='$userId'");
		return mysqli_num_rows($query);
	}

	public function getFollowing(){
		$userId = $this->userId;
		$query = mysqli_query($this->con, "SELECT * 
										   FROM follows
										   WHERE follower_id='$userId'");
		return mysqli_num_rows($query);
	}


	public function unFollow($user_to_remove) {
		$logged_in_user = $this->getUserId();

		$query = mysqli_query($this->con, "DELETE FROM FOLLOWS WHERE user_id='$user_to_remove' and follower_id='$logged_in_user'");
	}

	public function follow($user_to_follow){
		$logged_in_user = $this->getUserId();

		$query = mysqli_query($this->con, "INSERT INTO FOLLOWS VALUES('$user_to_follow','$logged_in_user')");
		
	}

	public function getMutualFriends($searched_user_friends_array) {
		$mutualFriends = 0;
		$user_friends_array = $this->getFriendArray();


		foreach($user_friends_array as $i) {

			foreach($searched_user_friends_array as $j) {

				if($i == $j) {
					$mutualFriends++;
				}
			}
		}
		if($mutualFriends>2){
			return $mutualFriends-2;
		}
		return 0;
	}

}

?>