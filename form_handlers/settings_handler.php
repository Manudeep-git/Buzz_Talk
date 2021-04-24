<?php 

    //Updates happen when user clicks update details button
    if(isset($_POST['update_details'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        //check if new email entered is logged in users email id or if that email doesn't exist
        //If user with new email id value doesn't exist, then update goes fine
        $email_query = mysqli_query($con,"SELECT * FROM users WHERE email='$email'");
        $row = mysqli_fetch_array($email_query);
        $user_returned = $row['user_id'];

        if($user_returned === "" || $user_returned === $userLoggedId ){
            $display_msg =  "Updated details successfully!<br><br>";

            //Update query
            $query = mysqli_query($con, "UPDATE users SET first_name='$first_name'
                                        ,last_name='$last_name',email='$email'
                                        WHERE user_id=$userLoggedId");
        }
        else{
            $display_msg = "Email is already in use!<br><br>";
        }
    }
    else{
        $display_msg="";
    }


    // -----------------------------Update Password------------------------------
    if(isset($_POST['update_password'])) {

        $old_password = strip_tags($_POST['old_password']);
        $new_password_1 = strip_tags($_POST['new_password_1']);
        $new_password_2 = strip_tags($_POST['new_password_2']);
    
        $password_query = mysqli_query($con, "SELECT password FROM users WHERE user_id='$userLoggedId'");
        $row = mysqli_fetch_array($password_query);
        $db_password = $row['password'];
    
        if(md5($old_password) === $db_password) {
    
            if($new_password_1 === $new_password_2) {
    
    
                if(strlen($new_password_1) < 8) {
                    $password_message = "Sorry, your password must be atleast 8 characters<br><br>";
                }	
                else {
                    $new_password_md5 = md5($new_password_1);
                    $password_query = mysqli_query($con, "UPDATE users SET password='$new_password_md5' WHERE user_id='$userLoggedId'");
                    $password_message = "Password has been changed!<br><br>";
                }
    
            }
            else {
                $password_message = "Your two new passwords need to match!<br><br>";
            }
    
        }
        else {
                $password_message = "The old password is incorrect! <br><br>";
        }
    
    }
    else {
        $password_message = "";
    }
    
    
    if(isset($_POST['close_account'])) {
        header("Location: account_close.php");
    }

?>
