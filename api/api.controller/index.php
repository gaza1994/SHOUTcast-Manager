<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: API Control Panel
 * @website: http://scottishbordersdesign.co.uk/
 */

header('Access-Control-Allow-Origin: *');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
if ($_GET['api-key'] == '0000-0000-00') {
    echo <<<ERRORTEXT

Your API key is incorrect, please check it!

ERRORTEXT;
    exit;
}
require('../../include/functions.inc.php');
error_reporting(0);
$db     = dbConnect();
$config = settings();
function getServerFromToken($key, $port) {
    $db      = dbConnect();
    $config  = settings();
    $db->orderBy("user_id","ASC");
    $db->where("api_key", $key);
    $theUser = $db->get("members");
    $owner   = getuid($theUser[0]['username']);
    $db->orderBy("PortBase","ASC");
    $db->where("owner", $owner);
    $db->where("PortBase", $port);
    $server = $db->get("servers");
    if (empty($server)) {
        echo <<<ERRORTEXT
Your API key is incorrect, please check it!
ERRORTEXT;
        exit;
    }
    if (suspendStatus($server[0]['id']) == '1') {
        return '';
    } else {
        return $server[0];
    }
}

function getusernamefromtoken($key) {
    $db      = dbConnect();
    $config  = settings();
    $db->orderBy("user_id","ASC");
    $db->where("api_key", $key);
    $theUser = $db->get("members");

    return $theUser[0]['username'];
}

function api_startServer($id) {
    startstream($id);
    return true;
}

function api_stopServer($id) {
    stopautodj($id);
    usleep(2000);
    stopstream($id);
    return true;
}

function api_restartServer($id) {
    stopautodj($id);
    usleep(2000);
    api_stopServer($id);
    usleep(2000);
    api_startServer($id);
    usleep(2000);
    api_startAutoDJ($id, 'system');
}

function api_stopAutoDJ($id) {
    $db     = dbConnect();
    $config = settings();
    $db->where('id', $id);
    $db->update('servers', array(
        'autodj' => '0',
        'autodj_active' => '0'
    ));
    stopautoDJ($id);
    return true;
}

function api_startAutoDJ($id, $username) {
    $db             = dbConnect();
    $config         = settings();
    $myid           = getuid($username);
    $theServer      = getServerById($id);
    $db->where('id', $id);
    $db->update('servers', array(
        'autodj' => '1',
        'autodj_active' => '1'
    ));
    $ices     = $config['sc_trans'];
    $pid      = shell_exec("$ices " . $config['sbd_path'] . "/servers/autodj_" . $theServer[0]['PortBase'] . $theServer[0]['servername'] . ".conf > /dev/null & echo $!");
    $cleanpid = trim($pid);
    system("echo $cleanpid > " . $config['sbd_path'] . "/servers/autodj_" . $theServer[0]['PortBase'] . $theServer[0]['servername'] . ".pid");
    return true;
}

function rebuildautodjsettings($id, $settings, $username) {
    $db             = dbConnect();
    $config         = settings();
    $myid           = getuid($username);
    $theServer      = getServerById($id);
    $stream_title   = base64_decode($settings['stream_title']);
    $stream_genre   = base64_decode($settings['stream_genre']);
    $stream_website = base64_decode($settings['stream_website']);
    $shuffle        = base64_decode($settings['shuffle']);
    $output         = '';
    if (empty($stream_title)) {
        $stream_title = $theServer[0]['TitleFormat'];
    } else {
        $output .= 'Stream Title, ';
    }
    if (empty($stream_genre)) {
        $stream_genre = $theServer[0]['genre'];
    } else {
        $output .= 'Stream Genre, ';
    }
    if (empty($stream_website)) {
        $stream_website = $theServer[0]['website'];
    } else {
        $output .= 'Stream Website, ';
    }
    if (empty($shuffle)) {
        $shuffle = $theServer[0]['random'];
    } else {
        $output .= 'Stream Shuffle, ';
    }
    $db->rawQuery('UPDATE servers SET TitleFormat="' . $stream_title . '", genre="' . $stream_genre . '", website="' . $stream_website . '", random="' . $shuffle . '" WHERE id="' . $id . '"');
    rebuildConf($id);
    rebuildAutoDJ($id);
    $output .= 'Have been updated.<br />';
    die("$output");
}

function rebuildserversettings($id, $settings, $username) {
    $db            = dbConnect();
    $config        = settings();
    $myid          = getuid($username);
    $theServer     = getServerById($id);
    $song_history  = base64_decode($settings['song_history']);
    $public_server = base64_decode($settings['public_server']);
    $output        = '';
    if (empty($song_history)) {
        $song_history = $theServer[0]['ShowLastSongs'];
    } else {
        $output .= 'Song History, ';
    }
    if (empty($public_server)) {
        $public_server = $theServer[0]['PublicServer'];
    } else {
        $output .= 'Public Status ';
    }
    $db->rawQuery('UPDATE servers SET ShowLastSongs="' . $song_history . '", PublicServer="' . $public_server . '" WHERE id="' . $id . '"');
    rebuildConf($id);
    rebuildAutoDJ($id);
    if (!empty($output)) {
        $output .= 'have been updated.<br />';
    } else {
        $output = 'Nothing has changed';
    }
    die("$output");
}

function changeBroadcastPwAPI($id, $settings, $username) {
    $db                 = dbConnect();
    $config             = settings();
    $myid               = getuid($username);
    $theServer          = getServerById($id);
    $broadcast_password = base64_decode($settings['broadcast_password']);
    $output             = null;
    if (empty($broadcast_password)) {
        $broadcast_password = $theServer[0]['broadcast_password'];
    } else {
        $output .= 'Broadcast Password ';
    }
    $db->rawQuery('UPDATE servers SET Password="' . $broadcast_password . '" WHERE id="' . $id . '"');
    rebuildConf($id);
    rebuildAutoDJ($id);
    if (!empty($output)) {
        $output .= 'has been updated.<br />';
    } else {
        $output = 'Nothing has changed';
    }
    die("$output");
}

function rebuildautodjplaylist($serverid, $settings, $username) {
    $db       = dbConnect();
    $config   = settings();
    $myid     = getuid($username);
    $theServer      = getServerById($serverid);
    $output   = '';
    $output2  = "The Tracks:<br />";
    unset($settings['settings']);
    foreach ($settings as $id => $name) {
        $id = str_replace('track_', '', $id);
        $output .= "{$config[sbd_path]}/autodj/mp3s/{$theServer[0][PortBase]}/{$name}\n";
        $output2 .= "{$name}<br />";
    }
    file_put_contents("{$config[sbd_path]}/playlists/autodj_{$theServer[0][PortBase]}.lst", $output);
    $output2 .= "Have been added to the playlist";
    die($output2);
}

function getAutoDJStatus($sid) {
    $db   = dbConnect();
    $db->where("id", $sid);
    $row = $db->get("servers");
    if ($row[0]['autodj_active'] == '1') {
        return "On";
    } else {
        return "Off";
    }
}

function getServerStatus($sid) {
    $db   = dbConnect();
    $db->where("id", $sid);
    $row = $db->get("servers");
    if ($row[0]['enabled'] == '1') {
        return "On";
    } else {
        return "Off";
    }
}

function get_data($url) {
    error_reporting(0);
    $ch        = curl_init();
    $timeout   = 5;
    $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function connectToShoutcast($host, $port, $adminpass) {
    $url = get_data("http://{$host}:{$port}/admin.cgi?pass={$adminpass}&mode=viewxml");
    $p   = xml_parser_create();
    xml_parse_into_struct($p, $url, $vals, $index);
    xml_parser_free($p);
    return $vals;
}

function connectToShoutcastAdmin($host, $port, $adminpass) {
    $url = get_data("http://{$host}:{$port}/admin.cgi?pass={$adminpass}");
    $p   = xml_parser_create();
    xml_parse_into_struct($p, $url, $vals, $index);
    xml_parser_free($p);
    return $url;
}

function getServerInformation($sid) {
    $db            = dbConnect();
    $config        = settings();

    $db->where("id", $sid);
    $server = $db->getOne("servers");
    $host          = $config['host_addr'];
    $port          = $server['PortBase'];
    $adminpassword = $server['AdminPassword'];
    $information   = connectToShoutcast($host, $port, $adminpassword);
    foreach ($information as $key => $value) {
        $content[$value['tag']] = $value['value'];
    }
    return $content;
}

function getSourceIP($sid) {
    $db            = dbConnect();
    $config        = settings();
    $db->where("id", $sid);
    $server        = $db->getOne('servers');
    $host          = $config['host_addr'];
    $port          = $server['PortBase'];
    $adminpassword = $server['AdminPassword'];
    $information   = connectToShoutcastAdmin($host, $port, $adminpassword);

    $dom = new DOMDocument();
    $dom->loadHTML($information);
    foreach($dom->getElementsByTagName('td') as $td) {
        $info[] = $td->nodeValue . '<br/>';
    }

    foreach ($info as $key => $value) {
        if (strpos($value,'[kick]') !== false) {
            $sourceIPKey =  $info[$key];
            $sourceIPKey = str_replace("[kick]", "", $sourceIPKey);
            $sourceIPKey = strip_tags($sourceIPKey);
            break;
        }
    }

    return $sourceIPKey;
}

function logAPI($username, $host, $url, $action, $timestamp) {
    $db          = dbConnect();
    $insert = array(
                    'user' => $username,
                    'host' => $host,
                    'url' => $url,
                    'action' => $action,
                    'timestamp' => $timestamp
                     );

    $add = $db->insert('api_events', $insert);
}

function prettyAction() {
    if ($_GET['action'] == "autodj") {
        $output = "AutoDJ - " . ucfirst( $_GET['autodj'] );
        return $output;
    }

    return "Server - " . ucfirst( $_GET['action'] );
}

if ($config['api_enabled'] == '1') {
    if (isset($_GET['api-key']) || isset($_POST['api-key'])) {
        $server   = getServerFromToken($_GET['api-key'], $_GET['port']);
        $serverid = $server['id'];
        $username = getusernamefromtoken($_GET['api-key']);

        if ($_GET['action'] == 'info'){
            //
        } else {
            $prettyAction = prettyAction();
            if (isset($_SERVER['HTTP_REFERER'])) {
                $httprefferer = $_SERVER['HTTP_REFERER'];
            } else {
                $httprefferer = 'API';
            }
            logAPI($username, $_SERVER['REMOTE_ADDR'], $httprefferer, $prettyAction, date("d-m-Y h:i:s A"));
        }
        switch ($_GET['action']) {
            case 'start':
                if (getServerStatus($serverid) == 'Off') {
                    if (api_startServer($serverid)) {
                        die("Server Started Successfully");
                    } else {
                        die("Error");
                    }
                } else {
                    die("Server is Online");
                }
                break;
            case 'broadcast-password':
                if (isset($_POST['settings'])) {
                    $settings = $_POST;
                    
                    changeBroadcastPwAPI($serverid, $settings, $username);
                } else {
                    die("No Settings Passed");
                }
                break;
            case 'status':
                die(getServerStatus($serverid));
                break;
            case 'settings':
                if (isset($_POST['settings'])) {
                    $settings = $_POST;
                    
                    rebuildserversettings($serverid, $settings, $username);
                } else {
                    die("No Settings Passed");
                }
                break;
            case 'stop':
                if (getServerStatus($serverid) == 'On') {
                    if (api_stopServer($serverid)) {
                        die("Server Stopped Successfully");
                    } else {
                        die("Error");
                    }
                } else {
                    die('Server is Offline');
                }
                break;
            case 'info':
                $information = getServerInformation($serverid);
                switch ($_GET['info']) {
                    case 'song':
                        die($information['SONGTITLE']);
                        break;
                    case 'dj':
                        die($information['SERVERTITLE']);
                        break;
                    case 'live-dj':
                         die("API Removed in api v1.4 use 'dj'");
                        break;
                    case 'booked-dj':
                         die("API Removed in api v1.4 use 'dj'");
                        break;
                    case 'genre':
                        die($information['SERVERGENRE']);
                        break;
                    default:
                        break;
                }
                break;
            case 'schedule':
                switch ($_GET['schedule']) {
                    case 'add':
                        die("API Removed in api v1.4");
                        break;

                    case 'cancel':
                        die("API Removed in api v1.4");
                        break;

                    case 'autodj':
                        die("API Removed in api v1.4");
                        break;
                    default:
                         die("API Removed in api v1.4");
                        break;
                }
                break;
            case 'autodj':
                switch ($_GET['autodj']) {
                    case 'playlist':
                        if (isset($_POST['settings'])) {
                            $settings = $_POST;
                            
                            rebuildautodjplaylist($serverid, $settings, $username);
                        } else {
                            die("No Playlist Passed");
                        }
                        break;
                    case 'status':
                        die(getAutoDJStatus($serverid));
                        break;
                    case 'settings':
                        if (isset($_POST['settings'])) {
                            $settings = $_POST;
                            
                            rebuildautodjsettings($serverid, $settings, $username);
                        } else {
                            die("No Settings Passed");
                        }
                        break;
                    case 'start':
                        $information = getServerInformation($serverid);
                        $sourceIP = getSourceIP($serverid);
                        $config = settings();
                        $host = $config['host_addr'];

                        if (getAutoDJStatus($serverid) == 'Off' && $sourceIP !== $host) {
                            // First Check - autoDJ appears to be off!
                            // Now Check if someone is connected!
                            if (!empty($sourceIP)) {
                                die("ERROR: There is someone on the radio!");
                            } else {
                                if (api_startAutoDJ($serverid, $username)) {
                                    die("AutoDJ Enabled Successfully");
                                } else {
                                    die("Error");
                                }
                            }
                        } else {
                            die("Error: AutoDJ Already on!");
                        }
                        break;
                    case 'stop':
                        if (getAutoDJStatus($serverid) == 'On') {
                            if (api_stopAutoDJ($serverid)) {
                                die("AutoDJ Disabled Successfully");
                            } else {
                                die("Error");
                            }
                        } else {
                            die("Error: AutoDJ Already Off!");
                        }
                        break;
                    default:
                        die("No Action Selected");
                        break;
                }
                break;
            default:
                die("No Action Selected");
                break;
        }
    } else {
        echo <<<ERRORTEXT
Your API key is incorrect, please check it!
ERRORTEXT;
        exit;
    }
} else {
    die("The API is Offline...");
}