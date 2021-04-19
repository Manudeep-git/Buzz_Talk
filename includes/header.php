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
        
        $user_obj = new User($con,$userLoggedIn);

        $username = $user_obj->getUserNameDetails();

        $user = $user_obj->getUserDetails();
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
    <!-- Jquery js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
     integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" 
     crossorigin="anonymous">
    </script>
    <script src="./assets/js/profile.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js" 
    integrity="sha512-XKa9Hemdy1Ui3KSGgJdgMyYlUg1gM+QhL6cnlyTe2qzMCYm4nAZ1PsVerQzTTXzonUR+dmswHqgJPuwCq1MaAg==" 
    crossorigin="anonymous">
    </script>
    <!-- Bootbox Js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" 
    integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" 
    crossorigin="anonymous"></script>
    <!-- Axios Js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js" 
    integrity="sha512-bZS47S7sPOxkjU/4Bt0zrhEtWx0y0CRkhEp8IckzK+ltifIIE9EMIMTuT/mEzoIMewUINruDBIR/jJnbguonqQ==" 
    crossorigin="anonymous"></script>
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
            <a style="padding-right: 17px;" href="messages.php">
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
                    Home
                </a>
            </nav>
            <nav>
                <a href='<?php echo $userLoggedIn;?>'>
                        <i class="fas fa-user"></i>
                        <?php echo ucfirst($username['user_name']) ?>
                </a>
            </nav>
            <nav>
                <a href="./includes/handlers/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Signout
                </a>
            </nav>
        </div>
    </div>
    <div class="wrapper">
