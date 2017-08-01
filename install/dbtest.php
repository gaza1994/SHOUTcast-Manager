<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Install Test
 * @website: http://scottishbordersdesign.co.uk/
*/
include "../include/functions.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Database Test</title>
</head>
<body>
	<?php 
		$hostname = $_GET['host'];
		$username = $_GET['uname'];
		$password = $_GET['dbpass'];
		$database = $_GET['database'];
		$db = dbTest($hostname, $username, $password, $database);
		if ($db) {
			$alert = 'Connection Succes!';
		} else {
			$alert = 'Connection Failed! ' . $db->getLastError();
		}

		echo 'Testing connection: ' . $alert . '<br />';
	?>
</body>
</html>
