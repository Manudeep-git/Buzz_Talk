<?php 
    require './config/config.php';
    require './form_handlers/login_handler.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
</head>
<body>
    <form action="login.php"  method="POST">
        <div id="log_email">
            <input type="text" id="email" name="log_email" placeholder="Email address" 
                value= "<?php
                    if(isset($_SESSION['log_email'])) echo $_SESSION['log_email'];
                ?>"
                required
            />
        </div>
        <div id="log_password">
            <input type="password" id="log_password" name="log_password" placeholder="Password" required>
        </div>
        <button class="login_btn" type="submit" name="login_button" >Login</button>
    </form>
    <?php if(in_array("Email or Password was incorrect<br>",$log_array)) 
                  echo "Email or Password was incorrect<br>"; ?>
</body>
</html>