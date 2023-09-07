<?php
    require_once("includes/db.php");
    require_once("includes/functions.php");
    ob_start();
    session_start();

    if(isset($_COOKIE['_ucv_']))
    {
        global $conn;
        $selector=escape(base64_decode($_COOKIE['_ucv_']));

        $query="UPDATE remember_me SET Is_expired='-1' WHERE Selector='$selector' AND Is_expired=0";
        $query_conn=mysqli_query($conn,$query);
        if(!$query_conn)
        {
            die("Query failed".mysqli_error($conn));
        }
        setcookie('_ucv_','',time()-60*60);
    }

    if(isset($_SESSION['login']))
    {
        session_destroy();
        unset($_SESSION['login']);
        unset($_SESSION['name']);
        header("Location: login.php");
    }

    header("Location: login.php"); 
?>