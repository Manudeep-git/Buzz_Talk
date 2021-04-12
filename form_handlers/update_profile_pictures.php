<?php
    //To DO:This Page will inlcude all the api calls to update user profile pics -- later 
    $imageUrl = './assets/images/Default Profile Pictures/head_carrot.png';

    mysqli_query($con,"UPDATE users set profile_pic='$imageUrl' where user_id in (1,3)");
?>
