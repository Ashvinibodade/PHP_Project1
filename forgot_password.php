<?php  $currentpage = "Password Recovery" ?>
<?php require_once("includes/header.php"); ?>

    <div class="container">
        <div class="content">
            <h2 class="heading">Password Recovery</h2>

            <?php
                if(isset($_POST['password_recovery']))
                {
                    $user_name=escape($_POST['user_name']);
                    $user_email=escape($_POST['user_email']);

                    $query="SELECT * FROM users WHERE User_name='$user_name' AND User_email='$user_email' AND IS_active='1' ";
                    $query_conn=mysqli_query($conn,$query);

                    if(!$query_conn)
                    {
                        die("Query failed".mysqli_error($conn));
                    }

                    if (mysqli_num_rows($query_conn)==1)
                    {
                        if(!isset($_COOKIE['_unp_']))
                        {
                            $user_name=escape($_POST['user_name']);
                            $user_email=escape($_POST['user_email']);

                            date_default_timezone_set("asia/dhaka");

                            $mail->addAddress($_POST['user_email']);
                            $email=$_POST['user_email'];

                            $token=getToken(32);
                            $encoded_token=base64_encode(urlencode($token));

                            $email=base64_encode(urlencode($_POST['user_email']));

                            $expired_date=date("Y-m-d H:i:s" , time()+60*20);
                            $expired_date=base64_encode(urlencode($expired_date));

                            $query="UPDATE users SET Validation_key='$token' WHERE User_name='$user_name' AND User_email='$user_email' AND Is_active=1" ;
                            $query_conn=mysqli_query($conn,$query);

                            if(!$query_conn)
                            {
                                die("Query Failed".mysqli_error($conn));
                            }
                            else
                            {
                                $mail->Subject = "Password reset request";
                                $mail->Body = "
                                                <h2>Follow the following link to reset password</h2>
                                                <a href='localhost/Secured_login_Registration_sys_using_PHP/registration/new_password.php?eid={$email}&token={$encoded_token}&exd={$expired_date}'> Click here to create new password </a>
                                                <p>This link is valid for 20 mins.</p>";

                                if ($mail->send())
                                {
                                    setcookie('_unp_' , getToken(16),time()+60*20 ,'','','',true);
                                    echo "<div class='notification'>Check ur email for password reset link</div>";
                                }
                            }
                        }
                        else
                        {
                            echo "<div class='notification'>You must be waite at least 20 minutes for another request</div>";
                        }
                    }
                    else
                    {
                        echo "<div class='notification'>Sorry! User not found</div>";
                    }
                }
            ?>

            
            <form action="" method="POST">
                <div class="input-box">
                    <input type="text" class="input-control" placeholder="Username" name="user_name" required>
                </div>
                <div class="input-box">
                    <input type="email" class="input-control" placeholder="Email address" name="user_email" required>
                </div>
                <div class="input-box">
                    <input type="submit" class="input-submit" value="RECOVER PASSWORD" name="password_recovery" required>
                </div>
            </form>
        </div>
    </div>
<?php require_once("includes/footer.php"); ?>