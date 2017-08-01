<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Control AutoDJ using JS
 * @website: http://scottishbordersdesign.co.uk/
 */
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('functions.inc.php');
$config = settings();
$db = dbConnect();
$files = $db->get("media");

foreach ($files as $key => $file) {
	$mp3 = $file['files'];
	if (file_exists($mp3)) {
		//
	} else {
		$db->where('id', $file['id']);
		$remove = $db->delete('media');
		if ($remove) {
			echo "Removed";
		} else {
			echo "ERROR! removing $file[name]";
		}
	}
}

$db = dbConnect();
$config = settings();
$username = $_GET['username'];
$portNumber = $_GET['PortBase'];
$myid = getuid($username);

$serverids = getmyserversMulti_byid($myid);
foreach ($serverids as $key => $theserver) {
	if ($theserver['PortBase'] == $_GET['PortBase']) {
		$serverid = $serverids[$key];
	}
}

if ($serverid['autodj_active'] == '1' && $serverid['enabled'] == '1') {
	$db->where('id', $serverid['id']);
	$db->update('servers', array('autodj' => '0', 'autodj_active' => '0'));
	stopautoDJ($serverid['id']);
?>
<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Success!</b> Auto DJ has been disabled!</div>
<?php
}
if ($serverid['autodj_active'] == '0' && $serverid['enabled'] == '1') {
	if (checkAutoDJFolder($serverid['PortBase'], $serverid) == 'success') {
		$db->where('id', $serverid['id']);
		$db->update('servers', array('autodj' => '1', 'autodj_active' => '1'));
	      $autodjBin = $config['sc_trans'];
	      $pid = shell_exec("$autodjBin ".$config['sbd_path']."/servers/autodj_".$serverid['PortBase'].$serverid['servername'].".conf > /dev/null & echo $!");
	      $cleanpid = trim($pid);
	      system("echo $cleanpid > ".$config['sbd_path']."/servers/autodj_".$serverid['PortBase'].$serverid['servername'].".pid");
	      $evenautodj = "With AutoDJ ";
	} else {
		die('<div class="alert alert-danger alert-dismissable"><i class="fa fa-times"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> Auto DJ cannot be enabled! You need atleast 2 tracks to enable autodj.</div>');
	}

?>
<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Success!</b> Auto DJ has been enabled!</div>
<?php
}

if ($serverid['enabled'] == '0') {
	?>
<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> Your server must be turned on before autoDJ can be enabled!</div>
	<?php
}
?>