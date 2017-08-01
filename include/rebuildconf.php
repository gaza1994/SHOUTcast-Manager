<?php
session_start();
if ( !isset($_POST['id']) || !isset($_POST['broadcastpw']) || !isset($_POST['adminpw']) || !isset($_POST['songHistory']) || !isset($_POST['publicserver']) || !isset($_POST['portbase']) || !isset($_POST['allowed']) ) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
if (strlen($_POST['broadcastpw']) < 4) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
if (strlen($_POST['adminpw']) < 4) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
if (strlen($_POST['songHistory']) < 1) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
include('functions.inc.php');
$db = dbConnect();
$username = $_SESSION['username'];
$config = settings();
$id = $_POST['id'];
$db->rawQuery('UPDATE servers SET Password="'.$_POST['broadcastpw'].'", AdminPassword="'.$_POST['adminpw'].'", ShowLastSongs="'.$_POST['songHistory'].'", PublicServer="'.$_POST['publicserver'].'" WHERE id="'.$id.'"');
rebuildConf($id);
rebuildAutoDJ($id);
die("Settings Saved");
?>