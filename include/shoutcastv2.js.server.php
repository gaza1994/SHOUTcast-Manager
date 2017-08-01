<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: SHOUTcast Stats Update
 * @website: http://scottishbordersdesign.co.uk/
 */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include("functions.inc.php");
$config = settings();
$SHOUTcastServer = $config['host_addr']; // IP or hostname
$SHOUTcastPort = $_GET['PortBase'];
$SHOUTcastVersion = 1; // 1 or 2
$SHOUTcastSID = 1; // Usually 1
$showHistory = 1; // 1 or 0
$tableClass = "table table-striped";
$SHOUTcastStatus['refused'] = " - Connection Refused (Server Offline)"; // Server off
$SHOUTcastStatus['down'] = " - Offline"; // No source
$SHOUTcastStatus['up'] = " - Online"; // Streaming
if ($SHOUTcastVersion == 2) {
    $SHOUTcastSID = "?sid=".$SHOUTcastSID;
} else {
    $SHOUTcastSID = '';
}

$url = "http://".$SHOUTcastServer.":".$SHOUTcastPort."/stats?sid=1";
$nice_url = urlencode($url);
$sc_stats = @simplexml_load_file($nice_url);

print 'function doGetStats(){';
    if ($sc_stats->CONTENT == "") {
        $SHOUTcastStatus['refused'] = utf8_encode(str_replace('"', '\"', $SHOUTcastStatus['refused']));
        print ' document.getElementById(\'SHOUTcastStatus\').innerHTML="'.$SHOUTcastStatus['refused'].'";'.PHP_EOL;
        print '}';
        exit;
    } else {
        print 'document.getElementById(\'SHOUTcastStatus\').innerHTML="'.$SHOUTcastStatus['up'].'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastBitrate\').innerHTML="'.$sc_stats->BITRATE.' kbps";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastListeners\').innerHTML="'.$sc_stats->CURRENTLISTENERS.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastListenersMax\').innerHTML="'.$sc_stats->MAXLISTENERS.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastListenersPeak\').innerHTML="'.$sc_stats->CURRENTLISTENERS.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastListenersMax2\').innerHTML="'.$sc_stats->MAXLISTENERS.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastAverageListenTime\').innerHTML="'.$sc_stats->AVERAGETIME.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastTitle\').innerHTML="'.$sc_stats->SERVERTITLE.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="'.$sc_stats->CONTENT.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastGenre\').innerHTML="'.$sc_stats->SERVERGENRE.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastSong\').innerHTML="'.$sc_stats->SONGTITLE.'";'.PHP_EOL;
    }
print '}';
?>