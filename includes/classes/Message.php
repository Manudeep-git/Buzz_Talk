<?php
class Message {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	//Latest message- user can be a sender or recipient
	public function getMostRecentInteraction() {
		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedId = $this->user_obj->getUserId();
		
		//Last interaction
		$query = mysqli_query($this->con, "SELECT from_uid, to_uid
											FROM messages 
											WHERE from_uid='$userLoggedId' OR  to_uid='$userLoggedId' 
											ORDER BY message_id DESC LIMIT 1");

		//If no messages , return false
		if(mysqli_num_rows($query) === 0)
			return false;

		$row = mysqli_fetch_array($query);
		$user_to = $row['to_uid'];
		$user_from = $row['from_uid'];

		//If userLogged is not a recipient return to_uid
		if($user_to !== $userLoggedId){
			$user_to_query = mysqli_query($this->con,"SELECT user_name from usernames where user_id='$user_to'");
			$user_to_row = mysqli_fetch_array($user_to_query);
			$user_to_name = $user_to_row['user_name'];
			return $user_to_name;
		}
		else{
			$user_from_query = mysqli_query($this->con,"SELECT user_name from usernames where user_id='$user_from'");
			$user_from_row = mysqli_fetch_array($user_from_query);
			$user_from_name = $user_from_row['user_name'];
			return $user_from_name;
		}

	}

	public function sendMessage($user_to, $body, $date) {

		if($body != "") {
			$userLoggedId = $this->user_obj->getUserId();
			$query = mysqli_query($this->con, "INSERT INTO messages VALUES('', '$userLoggedId', '$user_to', '$body', '$date',0)");
		}
	}

	public function getMessages($other_User_Name) {
		$userLoggedId = $this->user_obj->getUserId();
		$userLoggedIn = $this->user_obj->getUsername();
		$data = "";

		$other_user_obj = new User($this->con,$other_User_Name);
		$other_user_id = $other_user_obj->getUserId();

		//userLogged could be a recipient or sender
		$get_messages_query = mysqli_query($this->con, "SELECT * 
														FROM messages 
														WHERE (to_uid='$userLoggedId' AND from_uid='$other_user_id') 
														OR (from_uid='$userLoggedId' AND to_uid='$other_user_id')");

		while($row = mysqli_fetch_array($get_messages_query)) 
		{
			$user_to = $row['to_uid'];
			$user_from = $row['from_uid'];
			$body = $row['msg_content'];

			$div_top = ($user_to == $userLoggedId) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
			$data = $data . $div_top . $body . "</div><br><br>";
		}
		return $data;
	}

	public function getLatestMessage($userLoggedIn, $user2) {

		$userLoggedId = $this->user_obj->getUserId();
		$details_array = array();

		$other_user_obj = new User($this->con,$user2);
		$other_user_id = $other_user_obj->getUserId();

		$query = mysqli_query($this->con, "SELECT msg_content, to_uid, from_uid,created_at
										   FROM messages 
										   WHERE (to_uid='$userLoggedId' AND from_uid='$other_user_id')
										   OR (to_uid='$other_user_id' AND from_uid='$userLoggedId') 
										   ORDER BY message_id DESC LIMIT 1");

		$row = mysqli_fetch_array($query);
		$sent_by = ($row['to_uid'] == $userLoggedId) ? "They said: " : "You said: ";

		//Timeframe
		$date_time_now = date("Y-m-d H:i:s");
		$start_date = new DateTime($row['created_at']); //Time of message
		$end_date = new DateTime($date_time_now); //Current time
		$interval = $start_date->diff($end_date); //Difference between dates 
		if($interval->y >= 1) {
			if($interval === 1)
				$time_message = $interval->y . " year ago"; //1 year ago
			else 
				$time_message = $interval->y . " years ago"; //1+ year ago
		}
		else if ($interval->m >= 1) {
			if($interval->d == 0) {
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

		array_push($details_array, $sent_by);
		array_push($details_array, $row['msg_content']);
		array_push($details_array, $time_message);

		return $details_array;
	}

	public function getConvos() {
		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedId = $this->user_obj->getUserId();
		$return_string = "";
		$convos = array();

		//Select all queries from messages where user is sender or recipient
		$query = mysqli_query($this->con, "SELECT from_uid, to_uid FROM messages 
										   WHERE to_uid='$userLoggedId' OR from_uid='$userLoggedId' 
										   ORDER BY message_id DESC");

		while($row = mysqli_fetch_array($query)) {
			$user_to_push = ($row['to_uid'] != $userLoggedId) ? $row['to_uid'] : $row['from_uid'];

			$user_push_query = mysqli_query($this->con, "SELECT user_name from usernames where user_id='$user_to_push'");
			$username_row = mysqli_fetch_array($user_push_query);
			$user_to_push = $username_row['user_name'];

			if(!in_array($user_to_push, $convos)) {
				array_push($convos, $user_to_push);
			}
		}

		foreach($convos as $username) {
			$user_found_obj = new User($this->con, $username);
			$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots; 

			$return_string .= "<a href='messages.php?u=$username'> <div class='user_found_messages'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
								" . $user_found_obj->getFirstAndLastName() . "
								<span class='timestamp_smaller' id='grey'> " . $latest_message_details[2] . "</span>
								<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
								</div>
								</a>";
		}

		return $return_string;

	}

}

?>