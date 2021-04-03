<?php

    ob_start(); //Turns on output buffering

    session_start();//starts a new session

    $timezone = date_default_timezone_set("America/Chicago");//default timezone for the entire code

    $con = mysqli_connect("localhost","root","","social");
    
    if(mysqli_connect_errno()){
        echo "Failed to connect:".mysqli_connect_errno();
    }
?>