<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: API - WHMCS Server Module
 * @website: http://scottishbordersdesign.co.uk/
 * @useage: See Developer Docs
*/
session_start();
$_SESSION['username'] = "WHMCS";
require ('../include/functions.inc.php');
ob_start();

if (isset($_POST['shoutcusername'])) {
    $db = dbConnect();
    $config = settings();
    $srvname = $_POST['srvname'];
    $srvname = preg_replace('/\s/', '_', $srvname);
    $id = getid_by_srvname($srvname);
    $port = getport_by_id($id);
    $userid = getuid($_POST['shoutcusername']);
    //////////////////////////////////////////
    //
    //Stop the server before changing it
    //
    //////////////////////////////////////////
    stopautodj($id);
    usleep(2000);
    stopstream($id);
    /* Change */
    apiUpgradeDowngrade($userid, $id, $_POST);
    //
    $output = ob_get_clean();
    die('success');
} else {
    echo 'No Post Info!';
}
session_destroy();
?>