<?php session_start();?>
<?php  $currentpage = "Login" ?>
<?php require_once("includes/header.php"); ?>
    <div class="container">
        <div class="content">
            <h2 class="heading">Login</h2>

            <?php

                // Google recaptcha
                $public_key="6LdmbfgnAAAAAFg4b30qpGfIztTjroU4PHR7S6Gw";
                $private_key="6LdmbfgnAAAAAGrmLOSVlhAEgIAVqexM6eTEU575";
                $url="https://www.google.com/recaptcha/api/siteverify";

                if(isset($_POST['resend']))
                {
                    if(!isset($_COOKIE['_utt_']))
                    {
                        $user_name=escape($_POST['user_name']);
                        $user_email=escape($_POST['user_email']);

                        date_default_timezone_set("asia/dhaka");

                        $mail->addAddress($_POST['user_email']);
                        $token=getToken(32);
                        $email=base64_encode(urlencode($_POST['user_email']));
                        $expired_date=date("Y-m-d H:i:s" , time()+60*20);
                        $expired_date=base64_encode(urlencode($expired_date));

                        $query="UPDATE users SET Validation_key='$token' WHERE User_name='$user_name' AND User_email='$user_email' AND Is_active=0" ;
                        $query_conn=mysqli_query($conn,$query);

                        if(!$query_conn)
                        {
                            die("Query Failed".mysqli_error($conn));
                        }
                        else
                        {
                            $mail->Subject = "Verify your Email";
                            $mail->Body = "
                                            <h2>Follow the link to verify </h2>
                                            <a href='localhost/Secured_login_Registration_sys_using_PHP/registration/activation.php?eid={$email}&token={$token}&&exd={$expired_date}'> Click here to verify </a>
                                            <p>This link is valid for 20 mins.</p>";

                            if ($mail->send())
                            {
                                setcookie('_utt_' , getToken(16),time()+60*20 ,'','','',true);
                                echo "<div class='notification'>Check ur email for activation link</div>";
                            }
                        }
                    }
                    else
                    {
                        echo "<div class='notification'>You must be waite at least 20 minutes for another request</div>";
                    }
                }

                $isAuthenticated=false;
                if (isset($_POST['login']))
                {

                    // Google recaptcha
                    $response_key=$_POST['g-recaptcha-response'];

                    // https://www.google.com/recaptcha/api/siteverify?secret=$private_key&response=$response_key&remoteip=currentScriptIpAddress
                    $response=file_get_contents($url . "?secret=" . $private_key . "&response=" . $response_key . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
                    $response=json_decode($response);
                    
                    if(!($response->success ==1))
                    {
                        $errcaptcha="Wrong Captcha";
                    }   

                    $user_name=escape($_POST['user_name']);
                    $user_email=escape($_POST['user_email']);
                    $user_password=escape($_POST['user_password']);

                    $query="SELECT * FROM users WHERE User_name='$user_name' AND User_email='$user_email' ";
                    // echo "Query: " . $query; // Debug line
                    $query_conn=mysqli_query($conn,$query);
                    if(!$query_conn)
                    {
                        die("Query Failed".mysqli_error($conn));
                    }
                    
                    $result=mysqli_fetch_assoc($query_conn);

                    // verify password
                    if (password_verify($user_password,$result['User_password']))
                    {
                        if ($result['Is_active']==1)
                        {
                            if(!isset($errcaptcha))
                            {
                                $isAuthenticated=true;
                                echo "<div class='notification'>Log In Successfull</div>";
                               
                            }
                        }
                        else
                        {
                            if(!isset($errcaptcha))
                            {
                                echo "<div class='notification'>You are not verified user.
                                <form method='POST'>
                                <input type='text' value={$user_name} name='user_name' hidden>
                                <input type='email' value={$user_email} name='user_email' hidden>
                                <input type='submit' class='resend' name='resend' value='click here for activation link ' >
                                </form></div>";
                            }
                        }
                    }
                    else
                    {
                        echo "<div class='notification'>Invalid username or email or password.</div>";
                    }
                }

                if($isAuthenticated)
                {
                    if(!empty($_POST['remember-me']))
                    {
                        $selector=getToken(32);
                        $encoded_selector=base64_encode($selector);
                        setcookie('_ucv_' , $encoded_selector, time() + 60*60*24*2, '', '', '',true);

                        date_default_timezone_set("asia/dhaka");
                        $expire=date("Y-m-d H:i:s" , time()+ 60*60*24*2);

                        //insert into rememeber_me
                        $query="INSERT INTO remember_me (User_name,Selector,Expire_date,Is_expired) VALUES ('$user_name','$selector','$expire',0) ";
                        $query_conn=mysqli_query($conn,$query);
                        if(!$query_conn)
                        {
                            die("Query Failed".mysqli_error($conn));
                        }
                        // else
                        // {
                        //     echo "Inserted";
                        // }
                    }
                    $_SESSION['login']='success';
                    header("Refresh:1,url=index.php");
                }

                // if(isAlreadyLoggedIn())
                // {
                //     echo "Logged in";
                // }
                // else
                // {
                //     echo "Not logged in";
                // }
            ?>
            
            <form action="login.php" method="POST">
                <div class="input-box">
                    <input type="text" class="input-control" placeholder="Username" name="user_name" required>
                </div>

                <div class="input-box">
                    <input type="email" class="input-control" placeholder="Email address" name="user_email" required>
                </div>

                <div class="input-box">
                    <input type="password" class="input-control" placeholder="Enter password" name="user_password" required>
                </div>

                <div class="input-box rm-box">
                    <div>
                        <input type="checkbox" id="remember-me" class="remember-me" name="remember-me">
                        <label for="remember-me">Remember me</label>
                    </div>
                    <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                </div>

                <div class="g-recaptcha" data-sitekey="<?php echo $public_key; ?>"></div>
                <?php echo isset($errcaptcha) ? "<span class='error'>{$errcaptcha}</span>":"";?>

                <div class="input-box">
                    <input type="submit" class="input-submit" value="LOGIN" name="login">
                </div>

                <div class="login-cta"><span>Don't have an account?</span> <a href="sign_up.php">Sign up here</a></div>
            </form>

        </div>
    </div>
<?php require_once("includes/footer.php"); ?>