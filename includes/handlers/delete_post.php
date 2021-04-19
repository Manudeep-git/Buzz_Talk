<?php 
		require '../../config/config.php';
		echo '<script> console.log("coming here") </script>';
		
		if(isset($_GET['post_id']))
			$post_id = $_GET['post_id'];

		if(isset($_POST['result'])) {
			if($_POST['result'] === 'true')
				$query = mysqli_query($con, "UPDATE contents SET deleted=1 WHERE content_id='$post_id'");
		}

?>