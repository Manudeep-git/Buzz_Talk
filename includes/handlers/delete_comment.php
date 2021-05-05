<?php 
		require '../../config/config.php';
		
		if(isset($_GET['comment_id']))
			$comment_id = $_GET['comment_id'];

		if(isset($_POST['result'])) {
			if($_POST['result'] === 'true')
				$query = mysqli_query($con, "UPDATE comments SET removed=1 WHERE comment_id='$comment_id'");
		}

?>