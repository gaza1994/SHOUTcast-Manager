<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Get Live Status for homepage.
 * @website: http://scottishbordersdesign.co.uk/
 */
$myserver = $db->rawQueryOne("SELECT * FROM servers WHERE id = '$serverid'");
$showoutput = '1';
if ($showoutput == '1') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://" . $config['host_addr'] . ":" . $port . "/index.html");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    $SHOUTcastData = curl_exec($ch);
    curl_close($ch);
    if (!$SHOUTcastData) {
        echo '<span class="label label-danger" data-toggle="tooltip" data-original-title="Server Offline">No DJ</span>';
    } else {
        preg_match_all('/<font class=default><b>(.*?)<\/b>/si', $SHOUTcastData, $matches);
        $i    = 1;
        $last = count($matches[1]);
        if (empty($matches[0][4])) {
            echo "<span class=\"label label-warning\" data-toggle=\"tooltip\" data-original-title=\"Live Information\">";
            echo "No DJ";
            echo "</span>";
        } else {
            echo "<span class=\"label label-success\" data-toggle=\"tooltip\" data-original-title=\"Live Information\">";
            if ($myserver['autodj_active'] == '1') {
                echo "AutoDJ";
            } else {
                echo "Live DJ";
            }
            echo "</span>";
        }
    }
}
unset($myserver);unset($showoutput);unset($date);unset($ch);unset($matches);unset($i);unset($SHOUTcastData);