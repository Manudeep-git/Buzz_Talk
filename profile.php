<?php
    include("./includes/header.php");

    if(isset($_GET['profile_username'])){
        $username = $_GET['profile_username'];
        $profile_user_obj = new User($con,$username);
        $friends_array = $profile_user_obj->getFriendArray();
        $friends_count = count($friends_array)-1;
        $profile_userId = $profile_user_obj->getUserId();
    }

    //unfollow previously following
    if(isset($_POST['remove_friend'])) {
        $user = new User($con, $userLoggedIn);
        $user->unFollow($profile_userId);
    }

    if(isset($_POST['add_friend'])) {
        $user = new User($con, $userLoggedIn);
        $user->follow($profile_userId);
    }


?>
    <style type="text/css">
	 	.wrapper {
	 		margin-left: 0px;
			padding-left: 0px;
	 	}

 	</style>

    <div class="profile_left">
 		<img src="<?php echo $user_obj->getProfilePic(); ?>">

 		<div class="profile_info">
 			<p><?php echo "Posts: " . $profile_user_obj->getNumContents(); ?></p>
 			<p><?php echo "Followers: " . $profile_user_obj-> getFollowers() ?></p>
             <p><?php echo "Following: " . $profile_user_obj-> getFollowing() ?></p>
 		</div>

 		<form action="<?php echo $username; ?>" method="POST">
 			<?php 
                $logged_in_user_obj = new User($con, $userLoggedIn); 

                if($userLoggedIn != $username) {

                    if($logged_in_user_obj->isFollowing($profile_userId)) {
                        echo '<input type="submit" name="remove_friend" class="danger" value="Unfollow"><br>';
                    }
                    else 
                        echo '<input type="submit" name="add_friend" class="success" value="Follow"><br>';
                }
 			?>
 		</form>
        
        <?php 
            if($userLoggedIn === $username){
                echo '<input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something"><br>';
            }
        ?>

 	</div>
    
    <!-- Profile posts -->
    <div class="profile_main_column column">
        <ul class="nav nav-tabs" role="tablist" id="profileTabs">
            <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">
                Newsfeed</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
                <div class="posts_area"></div>
                <img id="loading" src="assets/images/icons/loading.gif">
            </div>
        </div>
    </div>
    

    <!-- Modal -->
        <div class="modal fade" id="post_form" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                 <div class="modal-header">
                    <h5 class="modal-title" id="postModalLabel">Post Something!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                 </div>

                <div class="modal-body">
                    <p>This will appear on your profile</p>
                    <form class="profile_post" action="" method="POST">
      		            <div class="form-group">
      			            <textarea class="form-control" name="post_body"></textarea>
      			            <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
      		            </div>
      	            </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
                </div>
            </div>
           </div>
        </div>
    
        <script>
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            var profileUsername = '<?php echo $username; ?>';

            $(document).ready(function() {

                $('#loading').show();

                //Original ajax request for loading first posts 
                $.ajax({
                url: "./includes/handlers/load_profile_posts.php",
                type: "POST",
                data: "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                cache:false,

                success: function(data) {

                    $('#loading').hide();
                    $('.posts_area').html(data);
                    }
                });
            });

        </script>

</div>
</body>
</html>