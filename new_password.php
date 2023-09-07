<?php ob_start();?>
<?php  $currentpage = " Set Password" ?>
<?php require_once("includes/header.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php require_once("includes/db.php"); ?>
    <div class="container">
        <div class="content">
            <h2 class="heading">New Password</h2>

            <?php
                if(isset($_GET['eid']) && isset($_GET['token']) && isset($_GET['exd']))
                {
                    $user_email=urldecode(base64_decode($_GET['eid']));
                    $validation_key=urldecode(base64_decode($_GET['token']));
                    $expire=urldecode(base64_decode($_GET['exd']));

                    date_default_timezone_set("asia/dhaka");
                    $current_date=date("Y-m-d H:i:s");

                    if($expire <= $current_date)
                    {
                        echo "<div class='notification'>Sorry! This link has been expired.</div>";
                    }
                    else
                    {
                        $check=true;
                        if(isset($_POST['submit']))
                        {
                            $user_pass=escape($_POST['new-password']);
                            $user_con_pass=escape($_POST['confirm-new-password']);

                            if ($user_pass==$user_con_pass)
                            {
                                $pattern_up="/^.*(?=.{4,56})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$/";
                                if(!preg_match($pattern_up,$user_pass))
                                {
                                    $errPass="Must be atleast 4 character long ,1 upper case ,1lower case letter,and 1 number exist ";
                                }
                            }
                            else
                            {
                                $errPass="Password doesn't matched";
                            }

                            if(!isset($errPass))
                            {
                                $query="SELECT * FROM users WHERE User_email='$user_email' AND Validation_key='$validation_key' AND Is_active=1";
                                $query_conn=mysqli_query($conn,$query);

                                if(!$query_conn)
                                {
                                    die("Query Failed".mysqli_error($conn));
                                }

                                if(mysqli_num_rows($query_conn) ==1 )
                                {
                                    $password=password_hash($user_pass,PASSWORD_BCRYPT,['cost'=>10]);
                                    
                                    $query1="UPDATE users SET User_password='$password' WHERE Validation_key='$validation_key' AND User_email='$user_email' AND Is_active=1";
                                    $query1_conn=mysqli_query($conn,$query1);

                                    if(!$query1_conn)
                                    {
                                        die("Query Failed".mysqli_error($conn));
                                    }
                                    else
                                    {
                                        $query2="UPDATE users SET Validation_key=0 WHERE User_email='$user_email' AND Validation_key='$validation_key' AND Is_active=1";
                                        $query2_conn=mysqli_query($conn,$query2);

                                        if(!$query2_conn)
                                        {
                                            die("Query Failed".mysqli_error($conn));
                                        }

                                        echo "<div class='notification'>New password successfully created.</div>";
                                        header("Refresh:3; url='login.php'");
                                    }
                                }
                                else
                                {
                                    echo "<div class='notification'>Invalid link.</div>";
                                }
                            }
                        }
                    }
                }
                else
                {
                    echo "<div class='notification'>Something went wrong.</div>";
                }
                if(isset($errPass))
                {
                    echo "<div class='notification'>{$errPass}</div>";
                }
            ?>

            
            <form action="" method="POST">
                <div class="input-box">
                    <input type="password" class="input-control" placeholder="New password" name="new-password" required <?php echo !isset($check)?"disabled":""; ?> >
                </div>
                <div class="input-box">
                    <input type="password" class="input-control" placeholder="Confirm new password" name="confirm-new-password" required <?php echo !isset($check)?"disabled":""; ?> >
                </div>
                <div class="input-box">
                    <input type="submit" class="input-submit" value="SUBMIT" name="submit" <?php echo !isset($check)?"disabled":""; ?> >
                </div>
            </form>

        </div> 
    </div>
<?php require_once("includes/footer.php"); ?>