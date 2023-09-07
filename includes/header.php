<?php ob_start(); ?>
<?php require_once("db.php"); ?>
<?php require_once("functions.php"); ?>
<?php
    if(isset($_SESSION['login']))
    {
        header("Location:index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $currentpage; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
    if(isAlreadyLoggedIn())
    {
        header("Location:index.php");
        exist;
    }
    require_once("vendor/autoload.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $mail = new PHPMailer();

    // $mail->SMTPDebug = 2; // Enable verbose debugging
    // $mail->Debugoutput = 'html'; // Display debug output as HTML


    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = "smtp.gmail.com";
    $mail->Port = "465";
    $mail->SMTPSecure = "ssl";

    $mail->Username="ashvinibodade234";
    $mail->Password="kqnh gdyw pfrr ylzd";

    $mail->setFrom("ashvinibodade234@gmail.com");
    $mail->addReplyTo("no-reply@ashvinibodade234.com");
    $mail->isHTML();

    
?>