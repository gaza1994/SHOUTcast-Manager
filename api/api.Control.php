<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: API - WHMCS Server Module
 * @website: http://scottishbordersdesign.co.uk/
 * @useage: See Developer Docs
*/
require ('../include/functions.inc.php');
$config = settings();
ob_start();
switch ($_POST['action']) {
    case 'start':
        $id = getid_by_srvname($_POST['srvname']);
        startstream($id);
        $output = ob_get_clean();
        die("success");
    break;
    case 'stop':
        $id = getid_by_srvname($_POST['srvname']);
        stopautodj($id);
        usleep(2000);
        stopstream($id);
        $output = ob_get_clean();
        die("success");
    break;
    case 'restart':
        $id = getid_by_srvname($_POST['srvname']);
        stopautodj($id);
        usleep(2000);
        restart_server($port, $_POST['srvname']);
        $output = ob_get_clean();
        die("success");
    break;
    case 'autodj':
       // todo        
    break;
    default:
        $output = ob_get_clean();
        die("Error");
    break;
}