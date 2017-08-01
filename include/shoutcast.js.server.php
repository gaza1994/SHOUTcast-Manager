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
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$SHOUTcastServer.":".$SHOUTcastPort."/index.html".$SHOUTcastSID);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
$SHOUTcastData = curl_exec($ch);
curl_close($ch);
print 'function doGetStats(){';
if (!$SHOUTcastData) {
    $SHOUTcastStatus['refused'] = utf8_encode(str_replace('"', '\"', $SHOUTcastStatus['refused']));
    print 'document.getElementById(\'SHOUTcastStatus\').innerHTML="'.$SHOUTcastStatus['refused'].'";'.PHP_EOL;
    $SHOUTcastServerIP = gethostbyname($SHOUTcastServer);
    print PHP_EOL.'/* If the server at http://'.$SHOUTcastServer.':'.$SHOUTcastPort.'/ is online'.PHP_EOL.'then you may need to contact your web host and ask them to'.PHP_EOL.'allow you to connect to \''.$SHOUTcastServerIP.'\' using php cURL. */';
    exit;
}
if ($SHOUTcastVersion == 2) {
    preg_match_all('/<font class="default"><b>(.*?)<\/b>/si', $SHOUTcastData, $matches);
} else {
    preg_match_all('/<font class=default><b>(.*?)<\/b>/si', $SHOUTcastData, $matches);
}
$i = 1;
$last = count($matches[1]);
foreach ($matches[1] as $val) {
    $val = str_replace('"', '\"', $val);
    if ($i == 1) {
        if ($val == "Server is currently down.") {
            $SHOUTcastStatus['down'] = utf8_encode(str_replace('"', '\"', $SHOUTcastStatus['down']));
            print 'document.getElementById(\'SHOUTcastStatus\').innerHTML="'.$SHOUTcastStatus['down'].'";'.PHP_EOL;
        } else {
            $SHOUTcastStatus['up'] = utf8_encode(str_replace('"', '\"', $SHOUTcastStatus['up']));
            print 'document.getElementById(\'SHOUTcastStatus\').innerHTML="'.$SHOUTcastStatus['up'].'";'.PHP_EOL;
        }
    } else if ($i == 2) {
        preg_match_all('!\d+!', $val, $streamStatusMatches);        
        print 'document.getElementById(\'SHOUTcastBitrate\').innerHTML="'.$streamStatusMatches[0][0].' kbps";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastListeners\').innerHTML="'.$streamStatusMatches[0][1].'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastListenersMax\').innerHTML="'.$streamStatusMatches[0][2].'";'.PHP_EOL;
    } else if ($i == 3) {
        print 'document.getElementById(\'SHOUTcastListenersPeak\').innerHTML="'.$val.'";'.PHP_EOL;
        print 'document.getElementById(\'SHOUTcastListenersMax2\').innerHTML="'.$streamStatusMatches[0][2].'";'.PHP_EOL;
    } else if ($i == 4) {
        print 'document.getElementById(\'SHOUTcastAverageListenTime\').innerHTML="'.$val.'";'.PHP_EOL;
    } else if ($i == 5) {
        print 'document.getElementById(\'SHOUTcastTitle\').innerHTML="'.$val.'";'.PHP_EOL;
    } else if ($i == 6) {
        if ($val == "audio/mpeg") {
            print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="MP3";'.PHP_EOL;
        } else if ($val == "audio/aac") {
            print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="AAC+";'.PHP_EOL;
        } else if ($val == "video/nsv") {
            print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="NSV Video";'.PHP_EOL;
        } else {
            print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="Unknown";'.PHP_EOL;
        }
    } else if ($i == 7) {
        print 'document.getElementById(\'SHOUTcastGenre\').innerHTML="'.$val.'";'.PHP_EOL;
    } else if ($i == $last) {
        print 'document.getElementById(\'SHOUTcastSong\').innerHTML="'.$val.'";'.PHP_EOL;
    }
    $i++;
}
print '}';
?>