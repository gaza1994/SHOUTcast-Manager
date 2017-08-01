<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Rebuild AutoDJ Config
 * @website: http://scottishbordersdesign.co.uk/
 */
session_start();
if ( !isset($_POST['id']) || !isset($_POST['streamtitle']) || !isset($_POST['streamgenre']) || !isset($_POST['publicserver']) || !isset($_POST['streamshuffle']) || !isset($_POST['streamwebsite']) || !isset($_POST['allowed']) ) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
if (strlen($_POST['streamtitle']) < 4) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
if (strlen($_POST['streamgenre']) < 4) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
if (strlen($_POST['streamwebsite']) < 4) {
	header('HTTP/1.1 503 Service Unavailable');
	die();
}
include('functions.inc.php');
$db = dbConnect();
$username = $_SESSION['username'];
$config = settings();
$id = $_POST['id'];
$db->rawQuery('UPDATE servers SET TitleFormat="'.$_POST['streamtitle'].'", genre="'.$_POST['streamgenre'].'", website="'.$_POST['streamwebsite'].'", random="'.$_POST['streamshuffle'].'", PublicServer="'.$_POST['publicserver'].'", autodj_crossfadeMode="'.$_POST['crossfadeMode'].'", autodj_crossfadeseconds="'.$_POST['crossfadeseconds'].'" WHERE id="'.$id.'"');
rebuildConf($id);
rebuildautodj_with_bitrate($id, $_POST['autodjBitrate']);
die("Settings Saved");
?>