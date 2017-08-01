<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Media Updater
* @website: http://scottishbordersdesign.co.uk/
*/
include "functions.inc.php";
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();

if (empty($_SESSION['username'])) {
	die("No Auth.");
}

if (!isset($_GET['server'])) {
    die("Invalid Server");
}

$db       = dbConnect();
$config   = settings();
$myid     = getuid($_SESSION['username']);
$port     = $_GET['server'];
$myserver = getserverbyportbase($port);
$noserver = false;

$ownerID = getuserdetails($_SESSION['username']);
if (isset($myserver) || empty($myserver)) {
    if (empty($myserver) || $myserver['owner'] != $ownerID['user_id'] && !empty($_GET['server'])) {
        die("You do not own this server!");
    }
}

if (empty($port) && !isset($_GET['server'])) {
    die("No Server Selected");
}

// Start by doing a reload!
reloadmedia($config['media_path'] . $myserver['PortBase'] . '/', $myserver['PortBase']);

// once done, show the form!
$db->where("port", $myserver['PortBase']);
$query = $db->get("media");
$letter = '';
?>
<tr>
    <th>&nbsp;</th>
    <th><strong><a href="media.php?page=<?php echo $letter; ?>&sortfield=Song">Song</a></strong></th>
    <th><strong><a href="media.php?page=<? echo $letter; ?>&sortfield=Artist">Artist</a></strong></th>
    <th>Actions</th>
	<th>
		<?if(isset($_GET['check'])){?>
		<?php if ($config['flashplayer'] == "on") { ?><strong>Play</strong><?php } ?>
		<?}else{?>
			Add(<a href="media.php?server=<?php echo $port;?>&page=<?php echo $letter;?>&check=all">Select all</a>)
		<?}?>
	</th>
</tr>
<?php
foreach ($query as $key => $row) {
    $row = index_array($row);
    if ($config['flashplayer'] == "on") {
        $mp3path = explode('/', $row[1]);
        $mp3file = $config['media_url'] . $myserver['PortBase'] . '/' . rawurlencode($mp3path[sizeof($mp3path) - 1]);
        if (file_exists($config['media_path'] . $myserver['PortBase'] . '/' . $mp3path[sizeof($mp3path) - 1])) {
            $mp3player = "<span id='track_{$row[0]}'><button class='btn btn-success btn-sm' onClick='createPreview({$row[0]}, \"{$mp3file}\");return false;'>Preview File</button></span>";
            $mp3player .= "&nbsp;<span id='track_{$row[0]}'><button class='btn btn-danger btn-sm' onClick='deleteFile({$row[0]}, \"" . base64_encode($mp3file) . "\");return false;'>Delete File</button></span>";
        } else {
            $mp3player = '<strong>This track does not exist in your media folder.</strong>';
        }
        echo "<tr id='file_{$row[0]}'>";
        echo "<td><button class='btn btn-info btn-sm' onClick='getFileInfo({$row[0]});return false;'><i class='fa fa-info'></i></button></td>";
        echo "<td>".$row[2]."</td><td>".$row[3]."</td><td style='width:450px;'>".$mp3player."</td>";

    } else {
        $mp3path = explode('/',$row[1]);
        $mp3file = $config['media_url'].$myserver['PortBase'].'/'.rawurlencode($mp3path[sizeof($mp3path) - 1]);
        $mp3player = "&nbsp;<span id='track_del_{$row[0]}'><button class='btn btn-danger btn-sm' onClick='deleteFile({$row[0]}, \"".base64_encode($mp3file)."\");return false;'>Delete File</button></span>";
        echo "<tr id='file_{$row[0]}'>";
        echo "<td><button class='btn btn-info btn-sm' onClick='getFileInfo({$row[0]});return false;'><i class='fa fa-info'></i></button></td>";
        echo "<td>".$row[2]."</td><td>".$row[3]."</td><td style='width:450px;'>".$mp3player."</td>";
    }

    echo "<td><div class=\"form-group\"><input type=\"checkbox\" name=\"files[]\" value=\"$row[0]\"></div></td>";
}
?>