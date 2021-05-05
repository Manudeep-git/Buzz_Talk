<?php

    ob_start(); //Turns on output buffering

    session_start();//starts a new session

    $timezone = date_default_timezone_set("America/Chicago");//default timezone for the entire code
    $cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
    $cleardb_server = $cleardb_url["us-cdbr-east-03.cleardb.com"];
    $cleardb_username = $cleardb_url["bd0ad42a728153"];
    $cleardb_password = $cleardb_url["a9256163"];
    $cleardb_db = substr($cleardb_url["heroku_a4123219fdcd06e"],1);
    $active_group = 'default';
    $query_builder = TRUE;
    
    // Connect to DB
    $con = mysqli_connect($cleardb_server, $cleardb_username, $cleardb_password, $cleardb_db);


    // $con = mysqli_connect("localhost","root","179601","social");
    
    if(mysqli_connect_errno()){
        echo "Failed to connect:".mysqli_connect_errno();
    }
?>