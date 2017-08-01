<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Home
* @website: http://scottishbordersdesign.co.uk/
*/

require('header.php');
$notice = file_get_contents('notice.html'); // TODO: fetch from DB

$config = settings();
$db = dbConnect();
$i = "0";

if(useraccess($_SESSION['username']) < "4") {
  $owner = getuid($_SESSION['username']);
  $db->where("owner", $owner);
}

$cols = array('created','MaxUser','PortBase','servername','enabled','id','message_notification');
$db->orderBy("PortBase", "DESC");
$servers = $db->get("servers", null, $cols);


if (!isset($servers)) {
  $servers = "<tr><td colspan=\"7\"><i>No servers are created</i></td></tr>";
} else {
  $i = 0;
  foreach ($servers as $server) {

    $serverOutput[$i]['id'] = $server['id'];

    $serverOutput[$i]['servername'] = preg_replace('/_/', ' ', $server['servername']);

    if (!empty($server['message_notification'])) {
      $serverOutput[$i]['message_notification'] = $server['message_notification'];
    }

    $serverOutput[$i]['PortBase'] = $server['PortBase'];

    if (suspendStatus($server['id']) == '1') {
      $serverOutput[$i]['status_html'] = '<a data-toggle="tooltip" data-original-title="Server Offline" href="#"><span class="label label-warning">Suspended</span></a>';
    } else {
      $checkport = webget($config['host_addr'] . ":" . $server['PortBase']);

      if ($checkport == "") {
          $serverOutput[$i]['status_html'] = '<a data-toggle="tooltip" data-original-title="Server Offline" href="' . $config['web_addr'] . '/start/' . $server['PortBase'] . '/' . $server['id'] . '/' . $server['servername'] . '"><span class="label label-danger">Offline</span></a>';
      } else {
         $serverOutput[$i]['status_html'] = '<a data-toggle="tooltip" data-original-title="Server Online" href="' . $config['web_addr'] . '/stop/' . $server['PortBase'] . '/' . $server['id'] . '/' . $server['servername'] . '"><span class="label label-success">Online</span></a>';
      }
    }
    $serverid = $server['id']; 
    $myserver = $db->rawQueryOne("SELECT * FROM servers WHERE id = '$serverid'");
    $showoutput = '1';

    if (strpos($config['sc_serv'], '2.3.5') !== false) {
        $url = "http://".$config['host_addr'] . ":" . $server['PortBase']."/stats?sid=1";
        $nice_url = urlencode($url);
        if ($sc_stats = @simplexml_load_file($nice_url)) {
          if ($sc_stats) {
            $serverOutput[$i]['live_info'] =  '<span class="label label-danger" data-toggle="tooltip" data-original-title="Server Offline">No DJ</span>';
          }

          if ($sc_stats->CONTENT == "") {
            $serverOutput[$i]['live_info'] =  "<span class=\"label label-warning\" data-toggle=\"tooltip\" data-original-title=\"Live Information\">";
            $serverOutput[$i]['live_info'] .=  "No DJ";
            $serverOutput[$i]['live_info'] .=  "</span>";
          } else {
            $serverOutput[$i]['live_info'] =  "<span class=\"label label-success\" data-toggle=\"tooltip\" data-original-title=\"Live Information\">";
            if ($myserver['autodj_active'] == '1') {
              $serverOutput[$i]['live_info'] .=  "AutoDJ";
            } else {
              $serverOutput[$i]['live_info'] .=  "Live DJ";
            }
            $serverOutput[$i]['live_info'] .=  "</span>";
          }
        } else {
          $serverOutput[$i]['live_info'] =  '<span class="label label-danger" data-toggle="tooltip" data-original-title="Server Offline">No DJ</span>';
        }
    } else {
      if ($showoutput == '1') {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, "http://" . $config['host_addr'] . ":" . $server['PortBase'] . "/index.html");
          curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
          $SHOUTcastData = curl_exec($ch);
          curl_close($ch);
          if (!$SHOUTcastData) {
              $serverOutput[$i]['live_info'] =  '<span class="label label-danger" data-toggle="tooltip" data-original-title="Server Offline">No DJ</span>';
          } else {
              preg_match_all('/<font class=default><b>(.*?)<\/b>/si', $SHOUTcastData, $matches);
              $L    = 1;
              $last = count($matches[1]);
              
              if (empty($matches[0][4])) {
                  $serverOutput[$i]['live_info'] =  "<span class=\"label label-warning\" data-toggle=\"tooltip\" data-original-title=\"Live Information\">";
                  $serverOutput[$i]['live_info'] .=  "No DJ";
                  $serverOutput[$i]['live_info'] .=  "</span>";
              } else {
                  $serverOutput[$i]['live_info'] =  "<span class=\"label label-success\" data-toggle=\"tooltip\" data-original-title=\"Live Information\">";
                  if ($myserver['autodj_active'] == '1') {
                    $serverOutput[$i]['live_info'] .=  "AutoDJ";
                  } else {
                    $serverOutput[$i]['live_info'] .=  "Live DJ";
                  }
                  $serverOutput[$i]['live_info'] .=  "</span>";
              }
          }
      }
    }

    unset($myserver);unset($showoutput);unset($date);unset($ch);unset($matches);unset($L);unset($SHOUTcastData);

    $serverOutput[$i]['listners'] = checklistener($server['PortBase'],$server['id']);

    $serverOutput[$i]['action_URL'] = "view/{$server['PortBase']}/{$server['id']}/{$server['servername']}/";

    $i++;
  }
}

$smarty->assign("notice",$notice);
if (!empty($serverOutput)) {
  $smarty->assign("servers",$serverOutput);
}
$smarty->display('home.tpl');

require('footer.php');
?>