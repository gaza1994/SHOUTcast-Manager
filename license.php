<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Login
 * @website: http://scottishbordersdesign.co.uk/
*/
ob_start();
session_start();
require ('include/functions.inc.php');
$db = dbConnect();
$config = settings();

if (!isset($_GET['t'])) {
    $_GET['t'] = '';
}

switch ($_GET['t']) {
    case '1':
        // Invalid
        $type = "Invalid";
        $action = "Please re-check your license key and re-enter it below.<br />If your license is correct, please try re-issuing the license in the Scottish Borders Design Client Area.<br />If you still need help, please contact us.";
        $showform = '1';
    break;
    case '2':
        // Expired
        $type = "Expired";
        $action = "Your License has expired and removed from our system, If you still wish to have a license please re-order one.";
        $showform = '1';
    break;
    case '3':
        // Suspended
        $type = "Suspended";
        $action = "Your license has been suspended, please check the Scottish Borders Design Client Area for more information.";
    break;
    case '4':
        // Suspended
        $type = "Error";
        $action = "It appears we are having trouble connecting to the license server.";
    break;
    default:
        if (isset($_POST['license_key'])) {
            $key = htmlentities($_POST['license_key']);
            $db->where('id', '16');
            $db->update('config', array('value' => $key));
            $type = "Updated";
            $updated = '1';
            $action = "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Updated!</b> Your license has been updated<br />Please try to <a href='./login.php'>log in</a></div>";
        } else {
            die("Error");
        }
    break;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html class="bg-black">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>[License <?php echo $type; ?>] SHOUTcast Client - License Checker</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="templates/default/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="templates/default/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="templates/default/css/AdminLTE.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <link rel="shortcut icon" type="image/x-icon" href="templates/default/imgs/favicon.ico">
    </head>

    <body class="bg-black">
        <div class="form-box" id="login-box">
            <div class="header"><?php echo $type; ?> License</div>
            <form action="license.php" name="form1" method="post">

              <input type="hidden" name="Submit" value="Login" >
                <div class="body bg-gray">
                    <div class="input-group">
                        <p><?php echo $action; ?></p>
                    </div> 

                    <?php 
                    if (isset($showform)) {
                        if (useraccess($_SESSION['username']) < "5") { ?>
                            <div class="input-group"><p>&nbsp;</p></div> <?php
                        } else { ?>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <input type="text" class="form-control" id="license_key" name="license_key" placeholder="License Key">
                            </div>
                    <?php } } ?>     
                </div>

                <div class="footer"> 
                 <?php 
                 if (isset($showform)) {
                    if (useraccess($_SESSION['username']) < "5") { ?>
                       <p style="text-align:center;"> An administrator needs to take action, please contact them.</p><p style="text-align:center;"> You are logged in as <?php echo $_SESSION['username'] ?> <a href="logout.php">(Logout)</a> </p> <?php
                } else { ?>                                                              
                    <button type="submit" class="btn bg-olive btn-block">Check License</button>  
                    <p style="text-align:center;"> You are logged in as <?php echo $_SESSION['username'] ?> <a href="logout.php">(Logout)</a> </p>
                <?php } } ?>   
                </div>
            </form>

            <div class="margin text-center">
                <span><a href="http://scottishbordersdesing.co.uk/">Scottish Borders Design</a> SHOUTcast Client</span>
            </div>
        <!-- jQuery 2.0.2 -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="templates/default/js/bootstrap.min.js" type="text/javascript"></script>        

    </div>
</section>