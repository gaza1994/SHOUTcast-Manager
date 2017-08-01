<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Database Details
* @website: http://scottishbordersdesign.co.uk/
*/

////////////////////////////////////////////////////////////////////////
//
// Set MySQL Variables
//
////////////////////////////////////////////////////////////////////////
$dbhost = "localhost"; 	//Database Host
$dbport = "3306";	//Database TCP Port
$dbuser = "";  	//Database User
$dbpass = "";  	//Database Password 
$dbname = "";  	//Database Username

/* Error control setting
To turn errors off set to (default): 0
To turn errors on set to: E_ALL
*/
error_reporting(0);

/* Set Default Timezone */
date_default_timezone_set('Europe/London');
?>