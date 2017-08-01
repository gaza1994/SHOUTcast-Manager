<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: AutoDJ Removal
 * @website: http://scottishbordersdesign.co.uk/
 */
session_start();
include("../include/functions.inc.php");
$config = settings();
$db = dbConnect();
$files = $db->get("media");

foreach ($files as $key => $file) {
	$mp3 = $file['files'];
	if (file_exists($mp3)) {

	} else {
		$db->where('id', $file['id']);
		$remove = $db->delete('media');
		if ($remove) {
			echo "Removed";
		} else {
			echo "ERROR! remoing $file[name]";
		}
	}
}
$db = dbConnect();
$config = settings();
$myid = getuid($_SESSION['username']);
$myserver = getmyservers_byid($myid);
$port = $myserver['PortBase'];

if (!isset($_POST['trackURL'])) {
	die("Invalid File");
}

$track_url = base64_decode($_POST['trackURL']);
$track_url = str_replace($config['media_url'], "", $track_url);

$track_url = $config['media_path'].$track_url;
if (file_exists( urldecode( $track_url ) ) ) {
	if ( unlink(urldecode( $track_url )) ) {
		die(true);
	} else {
		die(false);
	}
}
?>