<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Footer
 * @website: http://scottishbordersdesign.co.uk/
*/

// require_once ('include/functions.inc.php');

// $config = settings();

$menu = substr(strtolower(basename($_SERVER['PHP_SELF'])),0,strlen(basename($_SERVER['PHP_SELF']))-4);

$smarty->display('footer.tpl');

?>