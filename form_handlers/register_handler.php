<?php 

//Declaring Form_variables

$fname = ""; //First name
$lname = ""; //second name
$username = "";//username
$b_date = ""; //birth_date
$reg_em = ""; // email
$reg_em2 = ""; //confirm email
$reg_password = ""; //password
$reg_password2 = ""; //confirm password
$date = ""; //created_date
$err_array = array();  //error messages


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

    //User_Name
    $username = test_input($_POST['display_name']);
    $_SESSION['username'] = $username;

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

    //validating username
    $username_check = mysqli_query($con,"SELECT user_name from usernames where user_name='$username'");

    $username_rows = mysqli_num_rows($username_check);

    if($username_rows>0){
        array_push($err_array, "User name not available <br>");
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

        
        //Inserting values into database
        $query = mysqli_query($con,
            "INSERT INTO users values('','$fname','$lname','$reg_em','$b_date','$date','$reg_password',
                                './assets/images/Default Profile Pictures/head_emerald.png',1);"               
        );


        $last_insert_id = mysqli_insert_id($con);

        $query = mysqli_query($con, "INSERT INTO usernames values('$username','$last_insert_id');");

        array_push($err_array,"<span style='color : #14C800;'>You're all set! Go ahead and Login</span><br>");
    }

}
    if(empty($err_array)){
        session_unset();
    }

?>