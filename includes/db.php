<?php
    define("DB_HOST","localhost");
    define("DB_USER","root");
    define("DB_PASSWORD","1234");
    define("DB_NAME","registration");

    // database connect

    $conn=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

    if (!$conn)
    {
        die("Connection failed".mysqli_error());
    }
?>