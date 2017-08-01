<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Header
 * @website: http://scottishbordersdesign.co.uk/
*/
ob_start();
session_start();
if (file_exists("install/install.php")) {
    header('Location: install/install.php');
    exit;
}
require('include/functions.inc.php');
$config = settings();
$userdeets = getUserDetails($_SESSION['username']);
    
    $menu = substr(strtolower(basename($_SERVER['PHP_SELF'])),0,strlen(basename($_SERVER['PHP_SELF']))-4);

    if(!$_SESSION['username']){
        header("Location: ".$config['web_addr']."/login.php");
    	exit();
    }
    $db = dbConnect();
    checkSSL();
    $owner = getuid($_SESSION['username']);
    $db->where("owner", $owner);
    $getserver = $db->getOne("servers");

    $gravatar = getgravatar($_SESSION['username']);

    if (useraccess($_SESSION['username']) < "5") {
        $smarty->assign("logginmessage", $userdeets['fname'] . ' ' . $userdeets['lname'] . ' - ' . $_LANG['customer']);
    } else { 
        $smarty->assign("logginmessage", $userdeets['fname'] . ' ' . $userdeets['lname'] . ' - ' . $_LANG['administrator']);
    }

    if ($config['api_enabled'] == '1') {
        $smarty->assign("apistatus", '<small class="badge pull-right bg-green">'. $_LANG['menuapi-status-online'] .'</small>');
    } else {
        $smarty->assign("apistatus", '<small class="badge pull-right bg-red">'. $_LANG['menuapi-status-offline'] .'</small>');
    }

    $smarty->assign("config",$config);
    $smarty->assign("menu_selected", $menu);
    $smarty->assign("web_addr", $config['web_addr']);
    $smarty->assign("myserver", $getserver);
    $smarty->assign("client", $userdeets);
    $smarty->assign("getgravatar", $gravatar);
    $smarty->assign("_GET", $_GET);
    $smarty->assign("userlevel", useraccess($_SESSION['username']));

    $smarty->display('header.tpl');
?>