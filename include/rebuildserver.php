<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Rebuild everything.
 * @website: http://scottishbordersdesign.co.uk/
 */
session_start();
include('functions.inc.php');
$id = $_GET['x'];
$port = $_GET['p'];
$serverName = $_GET['y'];
rebuildConf($id);
rebuildAutoDJ($id);
restart_server($port, $serverName);
die('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Success!</b> Server Rebuild Successful.</div>');