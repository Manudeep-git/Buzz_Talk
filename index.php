<?php
    include("./includes/header.php");//pastes all code from header.php
    // session_destroy(); //destroys all session variables

    if(isset($_POST['post'])){
        $uploadOk = 1;
        $imageName = $_FILES['fileToUpload']['name'];
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

        <div class="card" style="width: 19%; float: left;">
            <img src="<?php echo $user['profile_pic']?>" alt="...">
            <div class="card-body">
                <h5><a href='<?php echo $userLoggedIn;?>'><?php echo $user['first_name'].' '.$user['last_name']; ?></a></h5>
                <br>
                <?php 
                   $user_obj = new User($con,$userLoggedIn);
    
                   echo "<h5>Followers:"." ".$user_obj->getFollowers()."<br></h5>";
                   echo "<h5>Following:"." ".$user_obj->getFollowing()."<br></h5>";
                   echo "<h5>Contents:"." ".$user_obj->getNumContents()."</h5>";
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