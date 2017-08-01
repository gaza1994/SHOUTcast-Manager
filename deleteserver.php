<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Remove Server and Config files
* @website: http://scottishbordersdesign.co.uk/
*/

$id = $_REQUEST['id'];
$port = $_REQUEST['port'];
$srvname = $_REQUEST['srvname'];

$db = dbConnect();
$config = settings();
$username = $_SESSION['username'];
//////////////////////////////////////////
//
//Stop the server before deleting it and stop the autoDJ
//
//////////////////////////////////////////
stopautodj($id);
usleep(2000);
stopstream($id);

// Delete FTP
$db->where("Portbase", $port);
$ftpAccounts = $db->get("ftp");
foreach ($ftpAccounts as $key => $value) {
	$db->where("id", $value['id']);
	$db->delete("ftp");
	cpanel_api( 'del_ftp', $value['username'], null, null, null, null, 1 );
}

// Delete  server's configuration and process id
deletefile($config['sbd_path']."/servers/".$port.$srvname.".conf");
deletefile($config['sbd_path']."/servers/".$port.$srvname.".pid");

// Delete  server's AutoDJ configuration and process id
deletefile($config['sbd_path']."/servers/autodj_".$port.$srvname.".conf");
deletefile($config['sbd_path']."/servers/autodj_".$port.$srvname.".pid");

// Delete server's MRTG related files
deletefile($config['sbd_path']."/mrtg/".$port.$id.".log");
deletefile($config['sbd_path']."/mrtg/".$port.$id.".old");
deletefile($config['sbd_path']."/mrtg/".$port.$id.".html");
deletefile($config['sbd_path']."/mrtg/".$port.$id."-day.png");
deletefile($config['sbd_path']."/mrtg/".$port.$id."-week.png");
deletefile($config['sbd_path']."/mrtg/".$port.$id."-month.png");
deletefile($config['sbd_path']."/mrtg/".$port.$id."-year.png");

// Delete server's log files
deletefile($config['sbd_path']."/logs/".$port.$srvname.".log");
deletefile($config['sbd_path']."/logs/".$port.$srvname.".w3c.log");

// Delete server's AutoDJ related files
deleteDir($config['sbd_path']."/autodj/mp3s/".$port.'/');
deletefile($config['sbd_path']."/playlists/autodj_".$port.".lst");

// Delete server from database
$db->where('PortBase', $port);
$db->where('id', $id);
$db->delete('servers');

$db->where('name', $port);
$db->delete('playlist');

$db->where('port', $port);
$db->delete('playlist_content');

/* empty the database of media files for this port */
$db->where('port', $port);
$db->delete('media');

// Add to event log
$event = " Deleted server on port $port";
addevent($_SESSION['username'],$event);
echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Success!</b><b> Server with port base ".$port." is deleted.</b>";
echo "<br><br>Click <a href=\"/home.php\">here</a> to return to the front page.</div>";

// Remove server's port and regenerate MRTG
cleanmrtg($port);
generatemrtg();
?>
