<?php
    include("./includes/header.php");//pastes all code from header.php
    // session_destroy(); //destroys all session variables

    if(isset($_POST['post'])){
        $uploadOk = 1;
        echo "<script>console.log('$uploadOk')</script>";
        $imageName = $_FILES['fileToUpload']['name'];
        echo "<script>console.log('$imageName')</script>";
        $errorMessage = "";
    
        if($imageName != "") {
            $targetDir = "assets/images/posts/";
            $imageName = $targetDir. uniqid() .basename($imageName);
            $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);
    
            if($_FILES['fileToUpload']['size'] > 10000000) {
                $errorMessage = "Sorry your file is too large";
                $uploadOk = 0;
            }
    
            if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
                $errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
                $uploadOk = 0;
                echo "<script>console.log('came inside here')</script>";
            }
    
            if($uploadOk) {
                if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
                    //image upload and moving successful
                }
                else {
                    //image did not upload
                    $uploadOk = 0;
                }
            }
    
        }

        if($uploadOk) {
            $post = new Post($con, $userLoggedIn);
            $post->submitPost($_POST['post_text'],$imageName);
        }
        else {
            echo "<div style='text-align:center;' class='alert alert-danger'>
                    $errorMessage
                </div>";
        }

        //disabling form resubmission
        header("Location: index.php");
    }
?>  
        <div class="user_details column">
            <div class="user-image">
                <img src="<?php echo $user['profile_pic']; ?>"/>
            </div>
            <br>
            <div class="user_details_left_right">
                <a href='<?php echo $userLoggedIn;?>'><?php echo $user['first_name'].' '.$user['last_name']; ?></a>
                <br>
                <?php 
                   $user_obj = new User($con,$userLoggedIn);
    
                   echo "Followers:"." ".$user_obj->getFollowers()."<br>";
                   echo "Following:"." ".$user_obj->getFollowing()."<br>";
                   echo "Contents:"." ".$user_obj->getNumContents();
                ?>   
            </div>
        </div>

        <div class="main_column column">
            <form class="post_form" action="index.php" method="POST"  enctype="multipart/form-data">
                <textarea name="post_text" id="post_text" placeholder="What's buzzing!"></textarea>
                <input class="btn btn-secondary" type="submit" name="post" id="post_button" value="Post">
                <input type="file" name="fileToUpload" id="fileToUpload">
                <hr>
            </form>

            <div class="posts_area"></div>
		    <img id="loading" src="assets/images/icons/loading.gif">
            
        </div>
        
        <!-- Loading Posts asynchronously using ajax -->
        <script>
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
           

            $(document).ready(function() {

                $('#loading').show();

                //Original ajax request for loading first posts 
                $.ajax({
                    url: "./includes/handlers/load_posts.php",
                    type: "POST",
                    data: "userLoggedIn=" + userLoggedIn,
                    cache:false,
                    success: function(response) {
                        $('#loading').hide();
                        $('.posts_area').html(response);
                    },
                    error: (e) => {
                        console.log("Error:"+e);
                    }
                });
            });

	    </script>
    </div>
</body>
</html>