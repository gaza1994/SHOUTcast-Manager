<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Logout
* @website: http://scottishbordersdesign.co.uk/
*/
session_start();
session_destroy();
header('Location: login.php');
?>
