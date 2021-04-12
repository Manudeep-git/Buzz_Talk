<?php
    require './config/config.php';
    require './form_handlers/register_handler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BuzzTalk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css" 
          integrity="sha512-8bHTC73gkZ7rZ7vpqUQThUDhqcNFyYi2xgDgPDHc+GXVGHXq+xPjynxIopALmOPqzo9JZj0k6OqqewdGO3EsrQ==" 
          crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="./assets/css/register_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <div class="register_box">
            <div class="register_header">
                <h1 class="ui header">Buzz Talk</h1>
                 <p class="ui dividing header">Signup Below</p>
            </div> 
            <form action="register.php" class="ui form" method="POST">
                <div class="field required ">
                    <!-- <label for="firstName">First Name</label> -->
                    <input type="text" name="fname" placeholder="Enter First Name"
                        class="ui input focus" 
                        value = "<?php 
                            if(isset($_SESSION['fname'])){
                                echo $_SESSION['fname'];
                            }
                        ?>"
                        required
                    />
                    <?php if(in_array("First name should be between 2 and 25 characters <br>",$err_array,true)) 
                        echo "First name should be between 2 and 25 characters <br>"; ?>
                </div>
                <div class="required field">
                    <!-- <label for="lastName">Last Name</label> -->
                    <input type="text" name="lname" placeholder="Enter Last Name" 
                        class="ui input focus"
                        value = "<?php 
                            if(isset($_SESSION['lname'])){
                                echo $_SESSION['lname'];
                            }
                        ?>"
                        required
                    />
                    <?php if(in_array("Last name should be between 2 and 25 characters <br>",$err_array,true)) 
                        echo "Last name should be between 2 and 25 characters <br>"; ?>
                </div>
                <div class="required field">
                    <!-- <label for="lastName">Display Name</label> -->
                    <input type="text" name="display_name" placeholder="Display Name"
                        class="ui input focus"
                        value = "<?php 
                            if(isset($_SESSION['username'])){
                                echo $_SESSION['username'];
                            }
                        ?>"
                        required
                    />
                    <?php if(in_array("User name not available <br>",$err_array,true)) 
                        echo "User name not available <br>"; ?>
                </div>
                <div id="birth_date" class="required field">
                    <!-- <label for="lastName">Birth Date</label> -->
                    <input type="text" name="b_date" placeholder="MM/DD/YYYY"
                        class="ui input focus"
                        value = "<?php 
                            if(isset($_SESSION['b_date'])) echo $_SESSION['b_date'];
                        ?>"
                        required
                    />
                </div>
                <div class="required field">
                    <!-- <label for="lastName">Email</label> -->
                    <input type="email" name="reg_em" placeholder="Enter Email" 
                        class="ui input focus"
                        value = "<?php 
                            if(isset($_SESSION['reg_em'])){
                                echo $_SESSION['reg_em'];
                            }
                        ?>"
                        required
                    />
                    <!-- Display errors if email already in use and if format is invalid -->
                    <?php if(in_array("Email already in use <br>",$err_array)) 
                            echo "Email already in use <br>";
                        elseif(in_array("Invalid email Format <br>",$err_array)) 
                            echo"Invalid email Format <br>"; ?>

                </div>
                <div class="required field">
                    <!-- <label for="email2">Confirm Email</label> -->
                    <input type="email" name="reg_em2" placeholder="Enter email again"
                        class="ui input focus"
                        value = "<?php 
                            if(isset($_SESSION['reg_em2'])){
                                echo $_SESSION['reg_em2'];
                            }
                        ?>"
                        required
                    />

                    <!-- Display error if emails don't match -->
                    <?php if(in_array("Emails don't match <br>",$err_array)) echo "Emails don't match <br>"; ?>
                </div>
                <div class="required field">
                    <!-- <label for="password">Password</label> -->
                    <input class="ui input focus" type="password" name="reg_password" placeholder="Enter Password" required>

                    <!-- Display error message if password is invalid format and also for length -->
                    <?php if(in_array("Password can only contain characters or numbers <br>",$err_array)) 
                            echo "Password can only contain characters or numbers <br>";
                        elseif(in_array("Password must be atleast 8 characters and maximum of 20 characters <br>",$err_array))
                            echo "Password must be atleast 8 characters and maximum of 20 characters <br>"; ?>
                </div>
                <div class="required">
                    <!-- <label for="confirm_pswd">Confirm Password</label> -->
                    <input class="ui input focus" type="password" name="reg_password2" placeholder="Enter password again" required>

                    <!-- Display error message if passwords don't match -->
                    <?php if(in_array("Passwords do not match <br>",$err_array)) 
                        echo "Passwords do not match <br>"; ?>
                </div>
                <button class="ui primary button" type="submit" name="register_button" >Register</button>
                <br>
                <?php if(in_array("<span style='color : #14C800;'>You're all set! Go ahead and Login</span><br>",$err_array)) 
                        echo "<span style='color : #14C800;'>You're all set! Go ahead and Login</span><br>"; ?>
                <br>
                <a href="./login.php" id="signin" class="signin">Already have an account? Sign in here!</a>
            </form>
        </div>
    </div>
</body>
</html>