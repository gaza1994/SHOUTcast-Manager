<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Multi Media files uploader.
 * @website: http://scottishbordersdesign.co.uk/
 */
include('functions.inc.php');
session_start();
if (empty($_SESSION['username']) || empty($_POST)) {
    die("No Auth.");
}
$config = settings();
$port = $_POST['portbase'];
$storeFolder = $config['media_path'] . $port.'/';

if (!empty($_FILES)) {
    $tempFile = $_FILES['file']['tmp_name'];
    $targetFile =  $storeFolder. $_FILES['file']['name'];

    if ( move_uploaded_file($tempFile,$targetFile) ) {
    	die(true);
   	} else {
   		die(false);
    }
} else {
	die(false);
}
?>   