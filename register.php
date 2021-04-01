<?php

    $con = mysqli_connect("mysql.eecs.ku.edu","saimanudeep","roh9do4U","saimanudeep");
    
    if(mysqli_connect_errno()){
        echo "Failed to connect:".mysqli_connect_errno();
    }

    $

    $fname = ""; //First name
    $lname = ""; //second name
    $reg_em = ""; // email
    $reg_em2 = ""; //confirm email
    $reg_password = ""; //password
    $reg_password2 = ""; //confirm password
    $date = ""; //created_date
    $err_array = "";  //error messages

    //$vals = array($fname => 'fname',$lname => 'lname',$reg_em => 'reg_em',$em2 => 'reg_em2');
    //$form_refs = array('fname','lname','reg_em','reg_em2');
    //$pass = array($password => 'reg_password',$password2 => 'reg_password2');

    function test_input($data){
        $data = strip_tags($data);// strip_tags discards any html tags in data
        if($data=='fname' or $data=='lname'){
            $data = str_replace(" ","",$data); //removes spaces
            $data = ucfirst(strtolower($data));// capitalizes first letter and rest lowercase
        }
        elseif($data=='reg_em' or $data=='reg_em2'){
            $data = str_replace(" ","",$data);
            $data = strtolower($data);
        }
        return $data;
    }

    if(isset($_POST['register_button'])){
        //registration form values
        
        //First_Name
        $fname = test_input($_POST['fname']);
        
        //Last_Name
        $lname = test_input($_POST['lname']); 

        //Email
        $reg_em = test_input($_POST["reg_em"]);

        //Email2
        $reg_em2 = test_input($_POST["reg_em2"]); 

        //password
        $reg_password = test_input($_POST['reg_password']);
        $reg_password2 = test_input($_POST['reg_password2']);

        //Date
        $date = date("Y-m-d"); //current date

        //Emails-match
        if($reg_em == $reg_em2){
             //check if email is in valid format
             if(filter_var($reg_em, FILTER_VALIDATE_EMAIL)){
                $reg_em = filter_var($reg_em,FILTER_VALIDATE_EMAIL);
                echo $reg_em;
            }
            else{
                echo "Invalid email Format";
            }
        }
        else{
           echo "Emails don't match";
        }
    }

    // $query = mysqli_query($con,"INSERT INTO test values(default,'Manudeep')")

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
            <label for="firstName">FirstName:</label>
            <input type="text" name="fname" placeholder="Enter First Name" required>
        </div>
        <div>
            <label for="lastName">LastName:</label>
            <input type="text" name="lname" placeholder="Enter Second Name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="reg_em" placeholder="Enter Email" required>
        </div>
        <div>
            <label for="email2">Confirm Email:</label>
            <input type="email" name="reg_em2" placeholder="Enter email again" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="reg_password" placeholder="Enter Password" required>
        </div>
        <div>
            <label for="c_password">Confirm Password:</label>
            <input type="password" name="reg_password2" placeholder="Enter password again" required>
        </div>
        <button class="btn text-center" type="submit" name="register_button" >Register</button>
    </form>
</body>
</html>