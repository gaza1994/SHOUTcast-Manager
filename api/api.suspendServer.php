<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: API - WHMCS Server Module
 * @website: http://scottishbordersdesign.co.uk/
 * @useage: See Developer Docs
*/
require ('../include/functions.inc.php');
ob_start();
if (isset($_POST['shoutcusername'])) {
    $db = dbConnect();
    $config = settings();
    $srvname = $_POST['srvname'];
    $srvname = preg_replace('/\s/', '_', $srvname);
    $id = getid_by_srvname($srvname);
    $port = getport_by_id($id);
    //////////////////////////////////////////
    //
    //Stop the server before dissabeling it.
    //
    //////////////////////////////////////////
    stopautodj($id);
    usleep(2000);
    stopstream($id);
    /* suspend */
    suspendServer($id);
    //
    $output = ob_get_clean();
    die('success');
} else {
    echo 'No Post Info!';
}
?>