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
</head>
<body>
    <form action="register.php" method="POST">
        <div>
            <!-- <label for="firstName">FirstName:</label> -->
            <input type="text" name="fname" placeholder="Enter First Name" 
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
        <div>
            <!-- <label for="lastName">LastName:</label> -->
            <input type="text" name="lname" placeholder="Enter Last Name" 
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
        <div>
            <!-- <label for="lastName">LastName:</label> -->
            <input type="text" name="display_name" placeholder="Display Name" 
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
        <div id="birth_date">
            <input type="text" name="b_date" placeholder="MM/DD/YYYY"
                value = "<?php 
                    if(isset($_SESSION['b_date'])) echo $_SESSION['b_date'];
                ?>"
                required
            />
        </div>
        <div>
            <input type="email" name="reg_em" placeholder="Enter Email" 
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
        <div>
            <!-- <label for="email2">Confirm Email:</label> -->
            <input type="email" name="reg_em2" placeholder="Enter email again" 
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
        <div>
            <input type="password" name="reg_password" placeholder="Enter Password" required>

            <!-- Display error message if password is invalid format and also for length -->
            <?php if(in_array("Password can only contain characters or numbers <br>",$err_array)) 
                    echo "Password can only contain characters or numbers <br>";
                  elseif(in_array("Password must be atleast 8 characters and maximum of 20 characters <br>",$err_array))
                    echo "Password must be atleast 8 characters and maximum of 20 characters <br>"; ?>
        </div>
        <div>
            <input type="password" name="reg_password2" placeholder="Enter password again" required>

            <!-- Display error message if passwords don't match -->
            <?php if(in_array("Passwords do not match <br>",$err_array)) 
                  echo "Passwords do not match <br>"; ?>
        </div>
        <button class="btn text-center" type="submit" name="register_button" >Register</button>
        <br>
        <?php if(in_array("<span style='color : #14C800;'>You're all set! Go ahead and Login</span><br>",$err_array)) 
                  echo "<span style='color : #14C800;'>You're all set! Go ahead and Login</span><br>"; ?>
    </form>
</body>
</html>