<?php
    require_once("includes/db.php");
    require_once("includes/functions.php");

    if (isset($_GET['eid']) and isset($_GET['token']) and isset($_GET['exd']))
    {
        $validation_key=$_GET['token'];
        $email=urldecode(base64_decode($_GET['eid']));
        $expire=urldecode(base64_decode($_GET['exd']));

        date_default_timezone_set("asia/dhaka");
        $current_date=date("Y-m-d H:i:s");

        if ($current_date >= $expire)
        {
            echo "Link has been expired";
        }
        else
        {
            $query1="SELECT * FROM users WHERE User_email='$email' AND Validation_key='$validation_key' AND Is_active=1";
            $query_con1=mysqli_query($conn,$query1);

            if (!$query_con1)
            {
                die("Query Failed".mysqli_error($conn));
            }

            $count=mysqli_num_rows($query_con1);

            if($count==1)
            {
                echo "Email is already verified";
            }
            else
            {
                // Query
                $query="UPDATE users SET Is_active=1 WHERE User_email='$email' AND Validation_key='$validation_key'";
                $query_con=mysqli_query($conn,$query);

                if (!$query_con)
                {
                    die("Query Failed".mysqli_error($conn));
                }
                else
                {
                    echo "Email has been successfully Verified";
                }
            }
        }
    }
?>