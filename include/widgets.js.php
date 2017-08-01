<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Widgets Generator.
 * @website: http://scottishbordersdesign.co.uk/
 */
error_reporting(0); // stop warnings from breaking the JS
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("content-type: application/javascript");
include ('functions.inc.php');
$config = settings();

$SHOUTcastServer = $config['host_addr'];
$SHOUTcastPort = $_GET['port'];
$SHOUTcastSID = 1; //
if ($_GET['type'] == 'stats') {
    if (strpos($config['sc_serv'], '2.3.5') !== false) {
        $url = "http://".$SHOUTcastServer.":".$SHOUTcastPort."/stats?sid=1";
        $nice_url = urlencode($url);
        $sc_stats = @simplexml_load_file($nice_url);

        print 'function doGetStats(){';
            if (!$sc_stats || $sc_stats->CONTENT == "") {
                print ' document.getElementById(\'SHOUTcastStatus\').innerHTML="Offline";'.PHP_EOL;
                print '}';
                exit;
            } else {
                print 'document.getElementById(\'SHOUTcastStatus\').innerHTML="Online";'.PHP_EOL;
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
    } else {
        $SHOUTcastVersion = 1; // 1 or 2
        $SHOUTcastSID = 1; // Usually 1
        $showHistory = 0; // 1 or 0
        $tableClass = "table table-striped";
        $SHOUTcastStatus['refused'] = "Connection Refused (Server Offline)"; // Server off
        $SHOUTcastStatus['down'] = "Offline"; // No source
        $SHOUTcastStatus['up'] = "Online"; // Streaming
        if ($SHOUTcastVersion == 2) {
            $SHOUTcastSID = "?sid=" . $SHOUTcastSID;
        } else {
            unset($SHOUTcastSID);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://" . $SHOUTcastServer . ":" . $SHOUTcastPort . "/index.html");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        $SHOUTcastData = curl_exec($ch);
        curl_close($ch);
        print 'function radioStats(){' . PHP_EOL;
        if (!$SHOUTcastData) {
            $SHOUTcastStatus['refused'] = utf8_encode(str_replace('"', '\"', $SHOUTcastStatus['refused']));
            print 'document.getElementById(\'SHOUTcastStatus\').innerHTML="' . $SHOUTcastStatus['refused'] . '";' . PHP_EOL;
            print '}radioStats();' . PHP_EOL;
            $SHOUTcastServerIP = gethostbyname($SHOUTcastServer);
            print PHP_EOL . '/* If the server at http://' . $SHOUTcastServer . ':' . $SHOUTcastPort . '/ is online' . PHP_EOL . 'then you may need to contact your web host and ask them to' . PHP_EOL . 'allow you to connect to \'' . $SHOUTcastServerIP . '\' using php cURL. */';
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
                    print 'document.getElementById(\'SHOUTcastStatus\').innerHTML="' . $SHOUTcastStatus['down'] . '";' . PHP_EOL;
                } else {
                    $SHOUTcastStatus['up'] = utf8_encode(str_replace('"', '\"', $SHOUTcastStatus['up']));
                    print 'document.getElementById(\'SHOUTcastStatus\').innerHTML="' . $SHOUTcastStatus['up'] . '";' . PHP_EOL;
                }
            } else if ($i == 2) {
                preg_match_all('!\d+!', $val, $streamStatusMatches);
                print 'document.getElementById(\'SHOUTcastBitrate\').innerHTML="' . $streamStatusMatches[0][0] . ' kbps";' . PHP_EOL;
                print 'document.getElementById(\'SHOUTcastListeners\').innerHTML="' . $streamStatusMatches[0][1] . '";' . PHP_EOL;
                print 'document.getElementById(\'SHOUTcastListenersMax\').innerHTML="' . $streamStatusMatches[0][2] . '";' . PHP_EOL;
            } else if ($i == 3) {
                print 'document.getElementById(\'SHOUTcastListenersPeak\').innerHTML="' . $val . '";' . PHP_EOL;
                print 'document.getElementById(\'SHOUTcastListenersMax2\').innerHTML="' . $streamStatusMatches[0][2] . '";' . PHP_EOL;
            } else if ($i == 4) {
                print 'document.getElementById(\'SHOUTcastAverageListenTime\').innerHTML="' . $val . '";' . PHP_EOL;
            } else if ($i == 5) {
                print 'document.getElementById(\'SHOUTcastTitle\').innerHTML="' . $val . '";' . PHP_EOL;
            } else if ($i == 6) {
                if ($val == "audio/mpeg") {
                    print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="MP3";' . PHP_EOL;
                } else if ($val == "audio/aac") {
                    print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="AAC+";' . PHP_EOL;
                } else if ($val == "video/nsv") {
                    print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="NSV Video";' . PHP_EOL;
                } else {
                    print 'document.getElementById(\'SHOUTcastFormat\').innerHTML="Unknown";' . PHP_EOL;
                }
            } else if ($i == 7) {
                print 'document.getElementById(\'SHOUTcastGenre\').innerHTML="' . $val . '";' . PHP_EOL;
            } else if ($i == $last) {
                print 'document.getElementById(\'SHOUTcastSong\').innerHTML="' . $val . '";' . PHP_EOL;
            }
            $i++;
        }
        if ($showHistory == 1) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://" . $SHOUTcastServer . ":" . $SHOUTcastPort . "/played.html" . $SHOUTcastSID);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            $SHOUTcastData = curl_exec($ch);
            curl_close($ch);
            if ($SHOUTcastData) {
                print 'document.getElementById(\'SHOUTcastHistory\').innerHTML=';
                $match = preg_match_all('/<\/td><td>(.*?)<\/tr><tr>/si', $SHOUTcastData, $matches);
                $i = 1;
                foreach ($matches[1] as $val) {
                    if ($val != "") {
                        if ($i == 3) {
                            $show = 1;
                        }
                        if ($show) {
                            if (strpos($val, '</table>') !== false) {
                                break;
                            }
                            print utf8_encode(str_replace('"', '\"', "  {$val}"));
                        }
                        $i++;
                    }
                }
                print "\";" . PHP_EOL;
            }
        }
        print '}radioStats();' . PHP_EOL;
    }
}
if ($_GET['type'] == 'player') {
    if (isset($_GET['autoplay']) && $_GET['autoplay'] == 'true') {
        $xtra = "autoplay";
    }
    if (!isset($_GET['autoplay']) || $_GET['autoplay'] == 'false') {
        $xtra = "";
    }
    $playercode = '<audio ' . $xtra . ' controls=\'controls\'><source src=\'http://' . $config['host_addr'] . ':' . $SHOUTcastPort . '/;livestream.mp3\' type=\'audio/mpeg\'>Your browser does not support the audio element.</audio>';
    print 'document.getElementById(\'scplayer\').innerHTML="' . $playercode . '";' . PHP_EOL;
}
if ($_GET['type'] == 'flashplayer') {
    if (isset($_GET['autoplay']) && $_GET['autoplay'] == 'true') {
        $xtra = "true";
    }
    if (!isset($_GET['autoplay']) || $_GET['autoplay'] == 'false') {
        $xtra = "false";
    }
    $playercode = "<embed type='application/x-shockwave-flash' src='" . $config['web_addr'] . "/include/widgets/flash/player.swf' width='340' height='30' style='display: block !important;' id='player' name='player' bgcolor='#FFFFFF' quality='high' allowfullscreen='true' allowscriptaccess='always' flashvars='skin=" . $config['web_addr'] . "/include/widgets/flash/skins/dangdang.swf&amp;amp;title=Live Stream&amp;amp;type=sound&amp;file=http://" . $SHOUTcastServer . ":" . $SHOUTcastPort . "/;stream.mp3&amp;amp;13202692901&amp;amp;duration=-1&amp;id=scplayer&amp;autostart=" . $xtra . "'>";
    print "document.getElementById('scflashplayer').innerHTML=\"" . $playercode . "\";" . PHP_EOL;
}
if ($_GET['type'] == 'tunein') {
    $siteurl = $config['web_addr'];
    $tuneincode = "<style>#tuneincss a{text-decoration:none;}#tuneincss a:hover{text-decoration:underline;}</style><table style='margin: 0 auto;' id='tuneincss'> <tr> <td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.pls'> <img src='{$siteurl}/include/widgets/playlists/assets/winamp.png' alt=''> </a> &nbsp; </td><td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.m3u'> <img src='{$siteurl}/include/widgets/playlists/assets/quicktime.png' alt=''> </a> &nbsp; </td><td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.raw'> <img src='{$siteurl}/include/widgets/playlists/assets/realplayer.png' alt=''> </a> &nbsp; </td><td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.asx'> <img src='{$siteurl}/include/widgets/playlists/assets/wmp.png' alt=''> </a> &nbsp; </td><td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.itunes.pls'> <img src='{$siteurl}/include/widgets/playlists/assets/itunes.png' alt=''> </a> </td></tr><tr> <td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.pls'> Winamp </a> &nbsp; </td><td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.m3u'> Quick Time </a> &nbsp; </td><td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.raw'> Real Player </a> &nbsp; </td><td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.asx'> Windows Media Player </a> &nbsp; </td><td align='center' style='margin: 0 auto;text-align: center;' valign='middle'> <a target='_blank' href='{$siteurl}/download-playlist/{$SHOUTcastPort}.itunes.pls'> iTunes </a> </td></tr></table>";
    print "document.getElementById('tuneinwidget').innerHTML=\"" . $tuneincode . "\";" . PHP_EOL;
}
?>