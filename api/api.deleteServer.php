<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: API - WHMCS Server Module
 * @website: http://scottishbordersdesign.co.uk/
 * @useage: See Developer Docs
*/
require ('../include/functions.inc.php');
ob_start();

if (isset($_POST['shoutcusername'])) {
    $db = dbConnect();
    $config = settings();
    $srvname = $_POST['srvname'];
    $srvname = preg_replace('/\s/', '_', $srvname);
    $id = getid_by_srvname($srvname);
    $port = getport_by_id($id);
    $userid = getuid($_POST['shoutcusername']);
    //////////////////////////////////////////
    //
    //Stop the server before deleting it and stop the autoDJ
    //
    //////////////////////////////////////////
	stopautodj($id);
	usleep(2000);
    stopstream($id);
	
    $username = $_POST['shoutcusername'];
    deluser($userid, $username);
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
    addevent('WHMCS', $event);
    echo "<b>Server with port base " . $port . " is deleted.</b>";
    echo "<br><br>Click <a href=\"home.php\">here</a> to return to the front page.";
    // Remove server's port and regenerate MRTG
    cleanmrtg($port);
    generatemrtg();
    $output = ob_get_clean();
    die('success');
} else {
    echo 'No Post Info!';
}
// $username = $_POST['username'];
// $id = getuid($scUsername);
// deluser($id, $username);

?>