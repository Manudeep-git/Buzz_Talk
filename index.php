<?php
    include("./includes/header.php");//pastes all code from header.php
    // session_destroy(); //destroys all session variables

    if(isset($_POST['post'])){
        $post = new Post($con,$userLoggedIn);
        $post->submitPost($_POST['post_text']);

        //disabling form resubmission
        header("Location: index.php");
    }
?>  
        <div class="user_details column">
            <div class="user-image">
                <img src="<?php echo $user['profile_pic']; ?>"/>
            </div>
            <div class="user_details_left_right">
                <a href='<?php echo $userLoggedIn;?>'><?php echo $user['first_name'].' '.$user['last_name']; ?></a>
                <br>
                <?php 

                    $query = mysqli_query($con,"SELECT * 
                                                FROM follows 
                                                WHERE $userLoggedId=follows.user_id;");
                    $friends = mysqli_num_rows($query);

                    echo "Friends:"." ".$friends;
                ?>   
            </div>
        </div>

        <div class="main_column column">
            <form class="post_form" action="index.php" method="POST">
                <textarea name="post_text" id="post_text" placeholder="What's buzzing"></textarea>
                <input class="btn btn-secondary" type="submit" name="post" id="post_button" value="Post">
                <hr>
            </form>
            <?php
                $post = new Post($con,$userLoggedIn);
                echo $post->loadPostsFriends();
            ?>
        </div>
    </div>
</body>
</html>