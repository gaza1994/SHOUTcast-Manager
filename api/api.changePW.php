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
$db = dbConnect();
if (isset($_POST['newpw']) && !empty($_POST['newpw']) ) {
    $uid = getuid($_POST['scusername']);
    $email = $_POST['email'];
    $password = md5($_POST['newpw']);
    apiChangePassowrd($uid, $password);
    $db->where('servername', $_POST['srvname']);
    $db->update('servers', array('AdminPassword' => $_POST['newpw']));
    die('success');
}
if (isset($_POST['djnewpw']) && !empty($_POST['djnewpw'])) {
	$db->where('servername', $_POST['srvname']);
    $db->update('servers', array('Password' => $_POST['djnewpw']));
    die('success');
}
?>