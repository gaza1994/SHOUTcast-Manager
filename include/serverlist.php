<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Server List
 * @website: http://scottishbordersdesign.co.uk/
*/
$config = settings();
?>
<div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Servers</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-hover text-center">
                                        <tbody><tr>
                                            <th>Server Name</th>
                                            <th>Port</th>
                                            <th>Status</th>
                                            <th>Live</th>
                                            <th>Listeners</th>
                                            <th>Action</th>
                                        </tr>
<?php $i = "0";
if(useraccess($_SESSION['username']) < "4") {
  $owner = getuid($_SESSION['username']);
  $db->where("owner", $owner);
} else {
  $cond = "";
}
$db = dbConnect();
$db->orderBy("PortBase", "DESC");
$cols = array('created','MaxUser','PortBase','servername','enabled','id','message_notification');
$servers = $db->get("servers", null, $cols);

if (!isset($servers)) {
  echo "<tr><td colspan=\"7\"><i>No servers are created</i></td></tr>
";
} else {
  foreach ($servers as $row) {
    $row = index_array($row);
    echo "<tr>
";
?>
    <td>
                <?php     $srvname = $row[3];
    $srvname = preg_replace('/_/', ' ', $srvname);
    echo "<span id=\"$row[5]\">$srvname ";
    if (!empty($row[6])) {
      echo "<i style='color:#d9534f;' class='fa fa-exclamation-triangle' data-toggle=\"tooltip\" data-original-title=\"You have a message notification on this server, click 'View Server'\"></i>";
    }
    echo "</span>";
    ?>
                </td>
    <td>
                  <a href="http://<?php echo $config['host_addr'].":".$row[2] ?>" target=<?php echo "sbd_".$row[2].">".$row[2];?></a>
                </td>
    <td>
                <?php     $port = $row[2];
  if (suspendStatus($row[5]) == '1') {
    ?> <a data-toggle="tooltip" data-original-title="Server Offline" href="#"><span class="label label-warning">Suspended</span></a> <?php
} else {
    include ('status/status.php');
}
?>
                </td>
<td><?php $serverid = $row[5]; include('status/getlive.php');?></td>
    <td align=>
    <?php     checklistener($port,$row[5]);
    ?>
    </td>
    <td>
    <a href="view/<?php echo $port; ?>/<?php echo $row[5]; ?>/<?php echo $srvname; ?>/"><i class="fa fa-arrow-circle-right"> View Server</i></a>
                </td> 
  </tr>
<?php   }
}
?>
</tbody></table>
</div><!-- /.box-body -->
</div>