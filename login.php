<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Login
 * @website: http://scottishbordersdesign.co.uk/
 */
ob_start();
session_start();
if (file_exists("install/install.php")) {
    header('Location: install/install.php');
    exit;
}
require('include/functions.inc.php');
$db     = dbConnect();
$config = settings();
$_LANG = _LANG();
$smarty->assign("lang", $_LANG);

checkSSL();

if (isset($_SESSION['username'])) {
    $db->where("username", $_SESSION['username']);
}    
$userExsists = $db->getOne("members");

if (isset($_SESSION['username']) && $db->count > 0) {
    header('Location: home.php');
} else {

    if (isset($_POST['2stepusername']) && isset($_POST['verifycode'])) {
        // do the 2 Step check!
        include ('include/GoogleAuthenticator.php');
        $authenticator = new PHPGangsta_GoogleAuthenticator();
        $verifyCode = $_POST['verifycode'];
        $db->where("username", $_POST['2stepusername']);
        $member     = $db->getOne("members");
        $secret     = $member['2stepauth'];
        $tolerance = 1;
        $checkResult = $authenticator->verifyCode($secret, $verifyCode, $tolerance);
        if ($checkResult) {
            $_SESSION["ip"]       = getenv('REMOTE_ADDR');
            $_SESSION["username"] = $_POST['2stepusername'];
            addevent($_POST['2stepusername'], "logged in from " . getenv('REMOTE_ADDR') . " using 2 Step Authentication");
            header('Location: ' . $config['web_addr'] . '/home.php');
        } else {
            $error = "2 Step authentication failed, please try again.";
        }
    }

    if (isset($_REQUEST['Submit'])) {
        if (!$_POST['username'] || !$_POST['password']) {
            $error = $_LANG['loginerror']['allfields'];
        } else {
            if (login_check($_POST['username'], $_POST['password'])) {
                if (google_auth_part_check($_POST['username'])) {
                    header('Location: ' . $config['web_addr'] . '/home.php');
                    $_SESSION["ip"]       = getenv('REMOTE_ADDR');
                    $_SESSION["username"] = $_POST['username'];
                    addevent($_POST['username'], "logged in from " . getenv('REMOTE_ADDR'));
                } else {
                    // we have 2 Step!
                    $smarty->assign("2stepcheck", TRUE);
                    $smarty->assign("2stepusername", $_POST['username']);
                }

            } else {
                $error = $_LANG['loginerror']['invalid'];
            }
        }
    } else {
        if (isset($_GET['logout'])) {
            session_destroy();
            $error = $_LANG['loginerror']['sessionerror'];
            echo form($error);
        } else {
            if (!isset($_SESSION['username']) && !isset($_POST['verifycode'])) {
                $error = $_LANG['loginerror']['welcome2'];
            }
        }
    }
    if (isset($error)) {
      $smarty->assign("error", $error);
    }

    $smarty->display('login.tpl');
}
?>