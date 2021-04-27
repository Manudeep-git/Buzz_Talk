<?php
    require './config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Message.php");

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
    crossorigin="anonymous"></script>
</head>
<body>
        <a href="index.php">
            <i class="fas fa-arrow-circle-left"></i>
            Back
        </a>
        <div class="search">

            <form action="search.php" method="GET" name="search_form">
                <div class="input-group mb-3">
                    <input type="text" 
                        class="form-control"
                        onkeyup="getLiveSearchUsers(this.value,'<?php echo $userLoggedIn?>')"
                        id="search_text_input"
                        name="user"
                        placeholder="Search users.."
                        autocomplete="off" 
                        aria-describedby="button-addon2"
                    />

                    <div class="input-group-append">
                        <button class="btn btn-info" type="button" id="button-addon2">
                            <i class="fas fa-search"></i></button>
                    </div>
                    
                </div>
            </form>

            <div class="search_results">
            </div>

        </div>
</body>
</html>