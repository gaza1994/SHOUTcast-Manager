<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Control the Servers
 * @website: http://scottishbordersdesign.co.uk/
*/
require ('header.php');
$outgoingurl = $_SERVER['HTTP_REFERER'];
?>
      <?php
if (suspendStatus($_GET['id']) == '1') { ?>
<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button><b>ERROR!</b> This server has been suspended.</div> <?php
    require ('footer.php');
    exit();
}
if ($_REQUEST['action'] == "start") {
    startstream($_REQUEST['id']);
    echo msgbox("Stream on port " . $_REQUEST['control'] . " is started!", $outgoingurl . '?return=1');
}
if ($_REQUEST['action'] == "stop") {
    stopautoDJ($_REQUEST['id']);
	usleep(2000);
    stopstream($_REQUEST['id']);
    echo msgbox("Stream on port " . $_REQUEST['control'] . " is stopped!", $outgoingurl . '?return=2');
}
if ($_REQUEST['action'] == "restart") {
    stopautoDJ($_REQUEST['id']);
    stopstream($_REQUEST['id']);
    usleep(2000);
    startstream($_REQUEST['id']);
    echo msgbox("Stream on port " . $_REQUEST['control'] . " has restarted!", $outgoingurl . '?return=3');
}
if (isset($_GET['restart-all'])) {
    restartAll();
}
if (isset($_GET['start-all'])) {
    startAll();
}
if (isset($_GET['stop-all'])) {
    stopAll();
}
?>
   
<?php require('footer.php');
?>