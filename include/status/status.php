<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Get Status
 * @website: http://scottishbordersdesign.co.uk/
*/
$config = settings();
$checkport = webget($config['host_addr'] . ":" . $port);
if ($checkport == "") {
    if (isset($included)) {
        echo "Offline";
    } else {
        echo '<a data-toggle="tooltip" data-original-title="Server Offline" href="' . $config['web_addr'] . '/start/' . $port . '/' . $row[5] . '/' . $srvname . '"><span class="label label-danger">Offline</span></a>';
    }
} else {
    if (isset($included)) {
        echo "Online";
    } else {
        echo '<a data-toggle="tooltip" data-original-title="Server Online" href="' . $config['web_addr'] . '/stop/' . $port . '/' . $row[5] . '/' . $srvname . '"><span class="label label-success">Online</span></a>';
    }
}