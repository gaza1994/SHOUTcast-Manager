<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Broadcast Password Change
 * @website: http://scottishbordersdesign.co.uk/
 */
session_start();
if (!isset($_POST['id'])) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
if (!isset($_POST['broadcast'])) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
if (strlen($_POST['broadcast']) < 4) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
include('functions.inc.php');
$db = dbConnect();
$username = $_SESSION['username'];
$config = settings();
$id = $_POST['id'];
$djpassword = $_POST['broadcast'];
$db->rawQuery('UPDATE servers SET Password="'.$djpassword.'" WHERE id="'.$id.'"');
rebuildConf($id);
rebuildAutoDJ($id);
die("Password Changed!");
?>