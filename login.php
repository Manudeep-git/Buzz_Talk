<?php 
    require './config/config.php';
    require './form_handlers/login_handler.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css" 
          integrity="sha512-8bHTC73gkZ7rZ7vpqUQThUDhqcNFyYi2xgDgPDHc+GXVGHXq+xPjynxIopALmOPqzo9JZj0k6OqqewdGO3EsrQ==" 
          crossorigin="anonymous" />
     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" 
          integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="./assets/css/login_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <div class="login_box">
            <div class="login_header">
                <h1 class="ui header">Buzz Talk</h1>
                 <p class="ui dividing header">Login Below</p>
            </div>
            <form action="login.php" class="ui form" method="POST">
                <div id="log_email" class="field">
                    <input type="text" id="email" name="log_email" placeholder="Email address" 
                        class = "ui input focus"
                        value= "<?php
                            if(isset($_SESSION['log_email'])) echo $_SESSION['log_email'];
                        ?>"
                        required
                    />
                </div>
                <div id="log_password" class="field required">
                    <input class = "ui input focus" type="password" id="log_password" name="log_password" placeholder="Password" required>
                </div>
                <?php if(in_array("Email or Password was incorrect<br>",$log_array)) 
                        echo "Email or Password was incorrect<br>"; ?>
                <button class="btn btn-primary" type="submit" name="login_button" >Login</button>
                <br>
                <a href="./register.php" id="signup" class="signup">Need an account? Create One</a>
            </form>
        </div>
    </div>
</body>
</html>