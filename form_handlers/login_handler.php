<?php

    $log_array = array();
    if(isset($_POST['login_button'])){

        $log_email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //makes sure email is in correct form
        $_SESSION['log_email'] = $log_email; //storing email into session variable

        $log_password =  md5($_POST['log_password']); //getting md5 version of password

        $login_details = mysqli_query($con, "SELECT * FROM users WHERE email='$log_email' and password='$log_password';");
        $result_rows = mysqli_num_rows($login_details);

        if($result_rows==1){
            $row = mysqli_fetch_array($login_details);
            $user_id = $row['user_id'];
            $username_details = mysqli_query($con,"SELECT user_name from usernames where user_id='$user_id'");
            $username = mysqli_fetch_array($username_details)['user_name'];

            $_SESSION['username'] = $username; //storing the session username
            $_SESSION['user_id'] = $user_id;// storing the session user_id
            header("Location: index.php");
            exit();
        }
        else{
            array_push($log_array, "Email or Password was incorrect<br>");
        }
    }

    if(empty($log_array)){
        session_unset();
    }
?>
