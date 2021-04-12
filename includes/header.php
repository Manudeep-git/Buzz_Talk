<!-- This file stores the header which is common for every page in the website -->
<?php
    require './config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Message.php");
    include("includes/classes/Notification.php");

    //include './form_handlers/update_profile_pictures.php';
    if(isset($_SESSION['username'])){
        $userLoggedIn = $_SESSION['username'];
        $userLoggedId = $_SESSION['user_id'];
        $username_details = mysqli_query($con, "SELECT * from usernames where user_name='$userLoggedIn'");

        $user_details = mysqli_query($con,"SELECT * FROM users where user_id='$userLoggedId'");

        $username = mysqli_fetch_array($username_details);

        $user = mysqli_fetch_array($user_details);
    }
    else{
        header("Location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SocialGram</title> 
    <!-- Font-awesome css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" 
        integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" 
        crossorigin="anonymous" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" 
          integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <!-- Regular css -->
    <link rel="stylesheet" href="./includes/style.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" 
            crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="layout_bar">
        <div class="logo">
            <a href="index.php">
                BuzzTalk
            </a>
        </div>
        <nav>
            <a style="padding-right: 17px;" href="#">
                <i class="fas fa-user-friends"></i> 
                Friends
            </a>
            <a style="padding-right: 17px;" href="#">
                <i class="fab fa-facebook-messenger"></i>
                Messages
            </a>
            <a style="padding-right: 17px;" href="#">
                <i class="fas fa-user-cog"></i>
                Settings
            </a>
        </nav>
    </div>
    <div class="top_bar">
        <div class="container">
            <nav>
                <a  href="index.php">
                    <i class="fas fa-home"></i> 
                    <!-- Home -->
                </a>
            </nav>
            <nav>
                <a href='<?php echo $userLoggedIn;?>'>
                        <i class="fas fa-user"></i>
                        <?php echo ucfirst($username['user_name']) ?>
                </a>
            </nav>
            <nav>
                <a href="#">
                    <i class="fas fa-bell"></i> 
                    <!-- Notifications -->
                </a>
            </nav>
            <nav>
                <a href="./includes/handlers/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <!-- Signout -->
                </a>
            </nav>
        </div>
    </div>
    <div class="wrapper">
