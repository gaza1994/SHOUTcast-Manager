<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Record functions
 * @website: http://scottishbordersdesign.co.uk/
 */
header('Access-Control-Allow-Origin: *');
include('../functions.inc.php');
$config = settings();
$lockfile = $config['sbd_path'].'/include/record/lock/.recordLock_'.$_REQUEST['port'];
$doeslockexist = file_exists($lockfile);

if (isset($_REQUEST['dorecord']) && $_REQUEST['dorecord'] == '1' && !$doeslockexist && ($_REQUEST['m']) < 121) {
  $event = " Started recording a live set.";
  addevent($_REQUEST['port'], $event);

  $handle = fopen($lockfile, 'w') or die('Cannot open file: lock/'.$lockfile);

  error_reporting(0);
  set_time_limit(0);
  date_default_timezone_set('Europe/London');

  require 'lib/ripper.php';

  $minutes = ($_REQUEST['m']*60);
  $port  = $_REQUEST['port'];

  # You must always specify the protocol (http://)
  $url = "http://198.204.252.107:$port";

  $ripper = new SHOUTcastRipper\Ripper(array(
    'path'               => "../../autodj/mp3s/$port",
    'split_tracks'       => false,
    'max_track_duration' => $minutes
  ));

  echo "1";
  $ripper->start($url);
  unlink($lockfile);
  $event = " Recording Finished.";
  addevent($_REQUEST['port'], $event);
} else {
  header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}

?>