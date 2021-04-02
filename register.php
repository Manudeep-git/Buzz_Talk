<?php
    session_start();
    $con = mysqli_connect("localhost","root","","social");
    
    if(mysqli_connect_errno()){
        echo "Failed to connect:".mysqli_connect_errno();
    }

    $fname = ""; //First name
    $lname = ""; //second name
    $b_date = ""; //birth_date
    $reg_em = ""; // email
    $reg_em2 = ""; //confirm email
    $reg_password = ""; //password
    $reg_password2 = ""; //confirm password
    $date = ""; //created_date
    $err_array = array();  //error messages

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
        $_SESSION['fname'] = $fname; //stores first name into session variable
        
        //Last_Name
        $lname = test_input($_POST['lname']); 
        $_SESSION['lname'] = $lname;

        //Birth-date
        $b_date = test_input($_POST['b_date']);
        $b_date = strtotime($b_date); 
        $b_date = date('Y-m-d',$b_date);  
        $_SESSION['b_date'] = $b_date;    

        //Email
        $reg_em = test_input($_POST["reg_em"]);
        $_SESSION['reg_em'] = $reg_em;  

        //Email2
        $reg_em2 = test_input($_POST["reg_em2"]); 
        $_SESSION['reg_em2'] = $reg_em2;

        //password
        $reg_password = test_input($_POST['reg_password']);
        $reg_password2 = test_input($_POST['reg_password2']);

        //Date
        $date = date("Y-m-d"); //current date

        //Emails-match
        if($reg_em == $reg_em2){
             //check if email is in valid format
             if(filter_var($reg_em, FILTER_VALIDATE_EMAIL)){
                $reg_em = filter_var($reg_em,FILTER_VALIDATE_EMAIL);//filtering email to a valid format
                
                //check if email already exists
                $e_check = mysqli_query($con,"SELECT email from users where email='$reg_em'");

                $num_rows = mysqli_num_rows($e_check);

                if($num_rows>0){
                    array_push($err_array,"Email already in use <br>");
                }
            }

            else{
                array_push($err_array,"Invalid email Format <br>");
            }
        }
        else{
           array_push($err_array,"Emails don't match <br>");
        }

        //Validating remaining values
        if(strlen($fname)>25 || strlen($fname)<2){
           array_push($err_array,"First name should be between 2 and 25 characters <br>");
        }

        if(strlen($lname)>25 || strlen($lname)<2){
            array_push($err_array,"Last name should be between 2 and 25 characters <br>");
        }

        if($reg_password!=$reg_password2){
            array_push($err_array,"Passwords do not match <br>");
        }
        else{
            if(preg_match('/[^A-Za-z0-9]/',$reg_password)){
                array_push($err_array,"Password can only contain characters or numbers <br>");
            }
        }

        if(strlen($reg_password)>20 || strlen($reg_password)<8){
            array_push($err_array,"Password must be atleast 8 characters and maximum of 20 characters <br>");
        }

        //Hashing password before inserting data - Check if err_array is empty
        if(empty($err_array)){
            //hash password
            $reg_password = md5($reg_password);

            $username = strtolower($fname." ".$lname);

        //Inserting values into database
            $query = mysqli_query($con,
                "INSERT INTO users values('','$fname','$lname','$reg_em','$b_date','$date','$reg_password','no');"               
            );

            $last_insert_id = mysqli_insert_id($con);

            $query = mysqli_query($con, "INSERT INTO usernames values('$username','$last_insert_id');");

            array_push($err_array,"<span style='color : #14C800;'>You're all set! Go ahead and Login</span><br>");
        }

    }
    session_unset();
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