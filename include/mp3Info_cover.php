<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Media Manager
* @website: http://scottishbordersdesign.co.uk/
*/
/* DONT CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
/* -- DONT CACHE -- */

include("functions.inc.php");
$error = null;
if (isset($_GET['trackID']) && is_numeric($_GET['trackID']) && isset($_GET['portbase'])) {
	$portbase = $_GET['portbase'];
	$trackID = $_GET['trackID'];
	$trackCover = getMP3Info($trackID, $portbase, true);
	header("Content-Type: " . $trackCover['image_mime']);
	header("Content-Length: " . $trackCover['datalength']);
	echo($trackCover['data']);
} 
?>