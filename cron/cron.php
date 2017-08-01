<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Schedules all the CRON scripts
 * @website: http://scottishbordersdesign.co.uk/
 */
error_reporting(0);
session_start();
$_SESSION['username'] = "SYSTEM";
include("../include/functions.inc.php");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$config = settings();
$db = dbConnect();

echo "<h1>Cron Report</h1>";

/* MP3 Cleanup */
echo "<h2>MP3 Cleanup <small> - Removes Old MP3s that no longer exist</small></h2>";
$files = $db->get("media");
foreach ($files as $key => $file) {
	$mp3 = $file['files'];
	if (file_exists($mp3)) {
		echo "{$mp3} exists, no need for removal. <br />";
	} else {
		$db->where('id', $file['id']);
		$remove = $db->delete('media');
		if ($remove) {
			echo "Removed";
			$serverid=getport_by_id($file['port']);
			rebuildautodj($serverid);
		} else {
			echo "ERROR! removing $file[name] <br />";
		}
	}
}
echo "<hr>";

/* Check if any servers need to be started after crash */
echo "<h2>Server Crash Report <small> - Check if any servers need to be started after a crash</small></h2>";
cron_check_and_start();
echo "<hr>";

/* Check servers bitrates and stop server if needed */
echo "<h2>Bitrate Check <small> - If a server is over their alloted bitrate limit, stop the server</small></h2>";
$db->orderBy("Portbase", "DESC");
$data = $db->get("servers");
foreach ($data as $key => $result) {
    $result = index_array($result);
    $fp = @fsockopen($config['host_addr'], $result[4], $errno, $errstr, 30);
    if ($fp) {
        fputs($fp, "GET /7.html HTTP/1.0\r\nUser-Agent: XML Reader(Mozilla Compatible)\r\n\r\n");
        while (!feof($fp)) {
            $dataset = fgets($fp, 1000);
        }
        fclose($fp);
        $entries        = explode(",", $dataset);
        $listener       = $entries[0];
        $status         = $entries[1];
        $listenerpeak   = $entries[2];
        $maxlisteners   = $entries[3];
        $totallisteners = $entries[4];
        $bitrate        = $entries[5];
        $songtitel      = $entries[6];
        
        if ($result[4] != '') {
            $id       = $result[0];
            $port     = $result[4];
            $srvname  = $result[34];
            $max_rate = $result[45];
            if ($bitrate > $max_rate) {
                echo "<u><strong>$srvname</strong></u><br>Violating Port: $port<br>Current Bitrate: $bitrate kbps<br>Max Bitrate: $max_rate kbps<br><strong>Stopping and Emailing ...</strong><br>";
                cron_stop_server($port, $id, $srvname, $bitrate, $max_rate);
            } else {
                if ($bitrate == '0') {
                        echo "<u><strong>$srvname</strong></u><br> Server Online but no stream<br><br>";
                } else {
                        echo "<u><strong>$srvname</strong></u><br> Current Bitrate: $bitrate kbps<br>Max Bitrate: $max_rate kbps <br><br>";
                }
                
            }
        }
    }
}
echo "<hr>";

/* Check server autoDJ files */
echo "<h2>AutoDJ Check <small> - If the system detects an AutoDJ stream about to crash it will stop the server.</small></h2>";
$servers = $db->get("servers");
foreach ($servers as $key => $server) {
	checkAutoDJFolder($server['PortBase'], $server);
}
echo "<hr>";