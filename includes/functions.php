<?php
    function escape($string)
    {
        global $conn;
        return mysqli_real_escape_string($conn,$string);
    }

    function getToken($len)
    {
        $rand_str=md5(uniqid(mt_rand(),true));
        $base64_encode=base64_encode($rand_str);
        $modified_base64_encode=str_replace(array('+','='), array('',''), $base64_encode);
        $token=substr($modified_base64_encode,0,$len);
        return $token;
    }

    function selectorUserByToken($token)
    {
        global $conn;
        $query="SELECT User_name FROM remember_me WHERE Selector='$token' AND Is_expired=0";
        $query_conn=mysqli_query($conn,$query);
        if(!$query_conn)
        {
            die("Query failed".mysqli_error($conn));
        }

        $result=mysqli_fetch_assoc($query_conn);

        $user_name=$result['User_name'];

        $query1="SELECT * FROM users WHERE User_name='$user_name'";
        $query_conn1=mysqli_query($conn,$query1);
        if(!$query_conn1)
        {
            die("Query failed".mysqli_error($conn));
        }

        $result1=mysqli_fetch_assoc($query_conn1);
        return $result1['First_name'] . " " . $result1['Last_name'];
    }

    function isAlreadyLoggedIn()
    {
        global $conn;
        date_default_timezone_set("asia/dhaka");
        $current_date=date("Y-m-d H:i:s");

        if(isset($_COOKIE['_ucv_']))
        {
            $selector=escape(base64_decode($_COOKIE['_ucv_']));

            $query="SELECT * FROM remember_me WHERE Selector='$selector' AND Is_expired=0";
            $query_conn=mysqli_query($conn,$query);

            if(!$query_conn)
            {
                die("Query Failed".mysqli_error($conn)); 
            }

            $result=mysqli_fetch_assoc($query_conn);

            if(mysqli_num_rows($query_conn) ==1)
            {
                $expire_date=$result['Expire_date'];

                if ($expire_date >= $current_date)
                {
                    $name=selectorUserByToken($selector);
                    $_SESSION['name']= $name;
                    return true;
                }
            }
        }
    }
?>