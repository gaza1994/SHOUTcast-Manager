<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Server List
 * @website: http://scottishbordersdesign.co.uk/
*/
$config = settings();
$db = dbConnect();
$i = "0";
$myProfile = getUserDetails($_SESSION['username']);
$myuid = $myProfile['user_id'];
if (useraccess($_SESSION['username']) < "4") {
    $owner = getuid($_SESSION['username']);
    $cond = array('owner' => $owner, 'PortBase' => $_GET['portbase']);
    $db->where("owner", $owner);
    $db->where("PortBase", $_GET['portbase']);
} else {
    $cond = array('PortBase' => $_GET['portbase']);
    $db->where("PortBase", $_GET['portbase']);
}
$db->orderBy("PortBase", "ASC");
$servers = $db->get("servers");
if (!isset($servers)) {
    echo "<tr><td colspan=\"7\"><i>No servers are created</i></td></tr>
";
} else {
    $myserver = $servers[0];
    $cpuUsage = array_sum(sys_getloadavg()) / count(sys_getloadavg());
    if ($cpuUsage < 1) {
        $cpuUsage = 1;
    }
    if (isset($_GET['return'])) {
        $returncode = $_GET['return'];
    } else {
        $returncode = 0;
    }
    switch ($returncode) {
        case '1':
            echo '<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> The server on port ' . $_GET['portbase'] . ' has been started successfuly</div>';
        break;
        case '2':
            echo '<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> The server on port ' . $_GET['portbase'] . ' has been stopped successfuly</div>';
        break;
        case '3':
            echo '<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> The server on port ' . $_GET['portbase'] . ' has been restarted successfuly</div>';
        break;
        default:
            //
        break;
    }
    if ($myserver['autodj'] == '0') {
        $autodj_color = "#f56954";
        $autodj_title = "AutoDJ is Off!";
    }
    if ($myserver['autodj'] == '1' && $myserver['autodj_active']) {
        $autodj_color = "#00a65a";
        $autodj_title = "AutoDJ is On!";
    } else {
        $autodj_color = "#f56954";
        $autodj_title = "AutoDJ is Off!";
    }
?>
<h2 class="page-header">Managing <?php echo $_GET['srvname']; ?> SHOUTcast Server</h2>
<div id="ajaxresponse"></div>
<div id="autodjstatuschange"></div>
<?php if (suspendStatus($myserver['id']) == '1') {
     if (!empty($myserver['message_notification'])) {
?>
<div class="alert alert-warning alert-dismissable">
    <i class="fa fa-exclamation-triangle"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <b>Message!</b> <?php echo $myserver['message_notification'];?>
</div>
<?php
        }
?>
<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <b>Notice!</b> Your server has been suspended, please contact us
</div>
<?php
    } else {
        if (!empty($myserver['message_notification'])) {
?>
<div class="alert alert-warning alert-dismissable">
    <i class="fa fa-exclamation-triangle"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <b>Message!</b> <?php echo $myserver['message_notification'];?>
</div>
<?php
        }
?>
                   <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>
                                        <?php
        $port = $myserver['PortBase'];
        $included = 'yes';
        include ('status/status.php');
?>
                                    </h3>
                                    <p>
                                        Status
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion-information-circled"></i>
                                </div>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>
                                        <?php echo checklistener($myserver['PortBase'], $myserver['id'], 'yes'); ?><sup style="font-size: 20px">%</sup>
                                    </h3>
                                    <p>
                                        Listners Used
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>
                                        <?php echo $myserver['MaxUser']; ?>
                                    </h3>
                                    <p>
                                        Max Listners
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion-headphone"></i>
                                </div>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>
                                        <?php echo getUsedAutoDJ_space($myserver['PortBase']); ?>GB / <?php echo format_2_dp($myserver['autodj_max_space'] / 1024); ?>GB
                                    </h3>
                                    <p>
                                        AutoDJ Storage
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion-android-storage"></i>
                                </div>
                            </div>
                        </div><!-- ./col -->
                    </div><!-- /.row -->
                   <!-- Main row -->
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-lg-6 connectedSortable"> 
                            <div class="box box-info">
                                <div class="box-header">
                                    <i id="shoutcaststatusicon" class="fa fa-music"></i>
                                    <h3 class="box-title">Stream Preview <span id="SHOUTcastStatus"></span></h3>
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button class="btn btn-info btn-sm" data-toggle="tooltip" title="Show Track History" onClick="toggleHistory();return false;"><i class="fa fa-search"></i></button>
                                        <button class="btn btn-info btn-sm" data-toggle="tooltip" title="Refresh" onClick="getRadioStats();return false;"><i class="fa fa-refresh"></i></button>
                                    </div><!-- /. tools -->
                                </div>
                                <div class="box-body" id="streamstatus123">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Radio Stats</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <table class="table table-striped">
                                        <tbody>
                                        <tr>
                                            <td colspan="2" id=""><center><audio controls="controls"><source src="http://<?php echo $config['host_addr']; ?>:<?php echo $myserver['PortBase']; ?>/;livestream.mp3" type="audio/mpeg">Your browser does not support the audio element.</audio></center></td>
                                        </tr>
                                        <tr>
                                            <td>Stream Title</td>
                                            <td id="SHOUTcastTitle" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></td>
                                        </tr>
                                        <tr>
                                            <td>Listners</td>
                                            <td><span id="SHOUTcastListeners" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></span>&nbsp;of&nbsp;<span id="SHOUTcastListenersMax" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></span></td>
                                        </tr>
                                        <tr>
                                            <td>Peak Listners</td>
                                            <td><span id="SHOUTcastListenersPeak" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></span>&nbsp;of&nbsp;<span id="SHOUTcastListenersMax2" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></span></td>
                                        </tr>
                                        <tr>
                                            <td>Current Song</td>
                                            <td id="SHOUTcastSong" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></td>
                                        </tr>
                                        <tr>
                                            <td>Encoder</td>
                                            <td id="SHOUTcastFormat" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></td>
                                        </tr>
                                        <tr>
                                            <td>Current Bitrate</td>
                                            <td id="SHOUTcastBitrate" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></td>
                                        </tr>
                                        <tr>
                                            <td>Genre</td>
                                            <td id="SHOUTcastGenre" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></td>
                                        </tr>
                                        <tr>
                                            <td>Average Listen Time</td>
                                            <td id="SHOUTcastAverageListenTime" class="scStats"><span class="loader-inner ball-pulse"><div></div><div></div><div></div></span></td>
                                        </tr>
                                    </tbody></table>
                                    <div id="tracksLoader" style="/* display:none; */">
                                        <script id="tracksLoaderJS"></script>
                                    </div>
                                </div><!-- /.box-body -->
                            </div>
                                </div>
                            </div>
                            <!-- Box (with bar chart) -->
                            <div class="box box-danger" id="loading-example">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button class="btn btn-danger btn-sm refresh-btn" data-toggle="tooltip" title="Reload"><i class="fa fa-refresh"></i></button>
                                    </div><!-- /. tools -->
                                    <i class="fa fa-cloud"></i>
                                    <h3 class="box-title">Server Stats</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad">
                                                <div class="clearfix">
                                                    <span class="pull-left">Listners</span>
                                                    <small class="pull-right"><?php echo checklistener($myserver['PortBase'], $myserver['id'], 'yes'); ?>%</small>
                                                </div>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-light-blue" style="width: <?php echo checklistener($myserver['PortBase'], $myserver['id'], 'yes'); ?>%;"></div>
                                                </div>
                                                <div class="clearfix">
                                                    <span class="pull-left">AutoDJ Storage</span>
                                                    <small class="pull-right"><?php echo getUsedAutoDJ_space($myserver['PortBase']); ?> GB</small>
                                                </div>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo getUsedAutoDJ_space_percentage($myserver['PortBase'], $myserver['autodj_max_space']); ?>%;"></div>
                                                </div>
                                            </div><!-- /.pad -->
                                        </div><!-- /.col -->
                                    </div><!-- /.row - inside box -->
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->        
                        </section>
                        <!-- Right col -->
                        <section class="col-lg-6 connectedSortable"> 
                            <div class="box box-success">
                                <div class="box-header">
                                    <i class="fa fa-wrench"></i>
                                    <h3 class="box-title"> Server Controls</h3>
                                </div>
                                <div class="box-body">
                                    <p>Manage your current server with this panel.</p>
<?if(useraccess($_SESSION['username']) < "4"){
        echo "<a data-toggle='tooltip' data-original-title='You do not have permission to edit the server' style=\"opacity:0.5;\" class=\"btn btn-app\"><i class=\"fa fa-edit\"></i> Edit</a>";
        }else{
        if($myserver['enabled'] == "0"){
        $status = "0";
        }else{
        $status = "1";
        }
        ?>
        <a data-toggle='tooltip' data-original-title='Edit the Server' href="<?php echo $config['web_addr']; ?>/edit/<?php echo "$myserver[PortBase]/".$myserver['id']."/$status";?>/" class="btn btn-app"><i class="fa fa-edit"></i> Edit</a>
        <?}?>
                <?php         if($myserver['enabled'] == "0"){
            if(useraccess($_SESSION['username']) < "1"){
                echo "<a data-toggle='tooltip' data-original-title='You do not have permission to start the server' style=\"opacity:0.5;\" class=\"btn btn-app\"><i class=\"fa fa-play\"></i> Start Server</a>";
            }else{
                ?>
        <a data-toggle='tooltip' data-original-title='Start the Server' class="btn btn-app" href="<?php echo $config['web_addr']; ?>/start/<?php echo $myserver['PortBase'];?>/<?php echo $myserver['id'];?>/<?php echo $myserver['servername'];?>/"><i class="fa fa-play"></i> Start Server</a>
        <div class="popup">
            <!-- Start server. -->
        </div>
            <?php             }
        }else{
            if(useraccess($_SESSION['username']) < "1"){
                echo "<a data-toggle='tooltip' data-original-title='You do not have permission to stop the server' style=\"opacity:0.5;\" class=\"btn btn-app\"><i class=\"fa fa-stop\"></i> Stop Server</a>";
            }else{
                echo "<a data-toggle='tooltip' data-original-title='Stop the Server' href=\"".$config['web_addr']."/stop/".$myserver['PortBase']."/".$myserver['id']."/".$myserver['servername']."/\" class=\"btn btn-app\"><i class=\"fa fa-stop\"></i> Stop Server</a>";
            }
        }
if(useraccess($_SESSION['username']) < "1"){
                echo "<a data-toggle='tooltip' data-original-title='You do not have permission to restart the server' style=\"opacity:0.5;\" class=\"btn btn-app\"><i class=\"fa fa-refresh\"></i> Restart Server</a>";
            }else{
                echo "<a href=\"".$config['web_addr']."/restart/".$myserver['PortBase']."/".$myserver['id']."/".$myserver['servername']."/\" data-toggle='tooltip' data-original-title='Restart the Server' class=\"btn btn-app\"><i class=\"fa fa-refresh\"></i> Restart Server</a>";
            }
            $lockfile = '.recordLock_'.$myserver['PortBase'];
            if (file_exists($config['sbd_path'].'/include/record/lock/'.$lockfile)) {
                $stylecode = "color:#f00;";
                $recordhtml = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i><b>Error!</b> Recording has been locked (check another user hasnt triggered it)!</div>";
            } else {
                $stylecode = "";
                $recordhtml = "";
            }
            if ($myuid !== '96') {
                if ($myserver['autodj'] == '1' || $myserver['enabled'] == "0") { ?>
                    <a class="btn btn-app" data-toggle='tooltip' data-original-title='Server needs to be on and AutoDJ needs to be Off' style="opacity:0.5;" href="#" onClick="return false;">
                        <i class="fa fa-circle" style="<?php echo $stylecode;?>" id="recordnotification"></i> Record SHOUTcast
                    </a>
                <?php } else { ?>
                    <a class="btn btn-app" data-toggle="modal" href="#recordshoutcast" data-original-title='Record SHOUTcast'>
                        <i class="fa fa-circle" style="<?php echo $stylecode;?>" id="recordnotification"></i> Record SHOUTcast
                    </a>
               <?php } } ?>

<?php if(useraccess($_SESSION['username']) < "4"){/* nothing */}else { ?>
    <a class="btn btn-app" data-toggle='tooltip' data-original-title='Rebuild the configuration files.' href="#" onClick="rebuildConfiguration();return false;">
        <i class="fa fa-file-code-o" id="rebuildConfigFiles"></i> Rebuild Configuration
    </a>
<?php } ?>

<?php if(useraccess($_SESSION['username']) < "4"){
            echo "<a data-toggle='tooltip' data-original-title='You do not have permission to delete this server' style=\"opacity:0.5;\" class=\"btn btn-app\"><i class=\"fa fa-times\"></i> Delete Server</a>";
        }else{?>
        <a href="" onClick="ConfirmDeleteServer();return false;" data-toggle='tooltip' data-original-title='Delete the server (cannot be undone)' class="btn btn-app"><i class="fa fa-times"></i> Delete Server</a>
        <?}?>
                                    <a class="btn btn-app" data-toggle="modal" href="#broadcastPassword" data-original-title='Change Broadcast Password'>
                                        <i class="fa fa-key"></i> Change Broadcast Password
                                    </a>
                                    <a class="btn btn-app" data-toggle="modal" href="#adminPassword" data-original-title='Change Admin Password'>
                                        <i class="fa fa-lock"></i> Change Admin Password
                                    </a>
                                    <a class="btn btn-app" data-toggle="modal" href="#serversettings" data-original-title='Change Server Settings'>
                                        <i class="fa fa-cog"></i> Server Settings
                                    </a>
                                    <?php
                                    if (!$myserver['autodj_max_space'] == 0) { ?>
                                        <a class="btn btn-app" data-toggle="modal" href="#autodjsettings" data-original-title='Change AutoDJ Settings'>
                                            <i class="fa fa-play-circle"></i> AutoDJ Settings
                                        </a>
                                    <?php } ?>
                                    <?php
                                    if (!$myserver['autodj_max_space'] == 0) {
                                    ?>
                                    <a class="btn btn-app" data-toggle='tooltip' onClick="changeautoDJstatus();return false;" data-original-title='<?php echo $autodj_title; ?>'>
                                        <i class="fa fa-headphones" style="color: <?php echo $autodj_color; ?> !important;"></i> Auto DJ
                                    </a>
                                    <?php
                                    }
                                    ?>
                                    <a href="http://<?php echo $config['host_addr']?>:<?php echo $myserver['PortBase']; ?>" target=sbd_<?php echo $myserver['PortBase']?> class="btn btn-app" data-toggle='tooltip' data-original-title='View SHOUTcast Admin'>
                                        <i class="fa fa-external-link-square"></i> SHOUTcast Admin
                                    </a>
                                    <?php if ($config['mrtg'] == "on") { ?>
                                        <a class="btn btn-app" data-toggle="modal" href="#graphicstats" data-original-title='Graphic Stats'>
                                            <i class="fa fa-bar-chart-o"></i> Graphic Stats
                                        </a>
                                    <?php
        } ?>
                                </div><!-- /.box-body -->
                            </div>
                            <div class="box box-warning">
                                <div class="box-header">
                                    <i class="fa fa-link"></i>
                                    <h3 class="box-title"> Connection Details</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
<?php
if ($myuid == '96') {
    echo "<center><h2><span style='color:#f00;'>Disabled</span></h2><p>As some users cant use this demo responsibly certain parts have now been disabled.  Sorry for any inconvenience.</p></center>";
} else {
?>
                                    <table class="table table-condensed">
                                        <tbody><tr>
                                            <th>IP</th>
                                            <th>Port</th>
                                            <th>Password</th>
                                            <th>Admin Password</th>
                                            <th>Connectable?</th>
                                        </tr>
                                        <tr>
                                            <td><textarea style="background: none;border: none;cursor: text;"type="text" class="form-control" readonly><?php echo $config['host_addr'];?></textarea></td>
                                            <td><textarea style="background: none;border: none;cursor: text;"type="text" class="form-control" readonly><?php echo $myserver['PortBase'];?></textarea></td>
                                            <td><textarea style="background: none;border: none;cursor: text;"type="text" class="form-control" readonly><?php echo $myserver['Password'];?></textarea></td>
                                            <td><textarea style="background: none;border: none;cursor: text;"type="text" class="form-control" readonly><?php echo $myserver['AdminPassword'];?></textarea></td>
                                            <td><?php echo checkdj($myserver['PortBase'], $myserver['id']);?></td>
                                        </tr>
                                    </tbody>
                                </table>
<?php 
}
?>
                                </div><!-- /.box-body -->
                            </div>
                        </section>
                    </div>
<!-- Change Broadcast Password Modal -->
  <!-- Modal Broadcast Password -->
  <div class="modal fade" id="broadcastPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Change Broadcast Password</h4>
        </div>
        <div class="modal-body">
            <form action="#" id="changebroadcast" method="post"></form>
            <div class="form-group">
                <label>Broadcast Password</label>
                <input type="text" class="form-control" id="changepasswordcontroldj" name="djpassword" value="" placeholder="Broadcast Password">
            </div>
        </div>
        <div class="modal-footer" id="loading1">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" onClick="changeBroadcastPassword();return false;" class="btn btn-primary">Save changes</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <!-- Modal Admin Password -->
  <div class="modal fade" id="adminPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Change Admin Password</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Admin Password</label>
                <input type="text" class="form-control" id="adminpasswordedit" name="adminpassword" placeholder="Admin Password">
            </div>
        </div>
        <div class="modal-footer" id="loading2">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" onClick="changeAdminPassword();return false;" class="btn btn-primary">Save changes</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <!-- Modal Admin Password -->
  <div class="modal fade" id="recordshoutcast" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Record SHOUTcast</h4>
        </div>
        <div class="modal-body">
            <div class="form-group" id="recordingNow">
                <?php if(empty($recordhtml)){?>
                <label>How Long For (In Minutes)?</label>
                <input type="number" class="form-control" id="recordtime" name="recordtime" value="60" placeholder="Time in Minutes">
                <?php } else {echo $recordhtml;}?>
            </div>
        </div>
        <div class="modal-footer" id="recordload">
                <?php if(empty($recordhtml)){
                    ?>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" onClick="startRecording();return false;" class="btn btn-primary">Start Recording</button>
                <?php } ?>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <script>
  function startRecording(){
    var recordtime = jQuery('#recordtime').val();
    console.log("Record Time: "+recordtime);
    $('#recordingNow').html("<div class='alert alert-success'><i class='fa fa-check'></i><b>Success!</b> Set is now Recording</div><div id='showrecordstats'></div>");
    $('#recordload').slideUp();
    $('#recordnotification').attr("style", 'color: #f00;');
    jQuery.post( "<?php echo $config['web_addr'];?>/include/record/record.php", { port: "<?php echo $myserver['PortBase']; ?>", m: recordtime, dorecord: '1'})
      .done(function( data ) {
        $('#recordingNow').html("<div class='alert alert-success'><i class='fa fa-check'></i><b>Success!</b> SHOUTcast has been recorded, you can find the file in your autoDJ folder.</div><div id='showrecordstats'></div>");
        $('#recordnotification').attr("style", 'color: #666;');
      })
      .fail(function( data ) {
        $('#recordingNow').html("<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i><b>Error!</b> Recording has been locked, check the following:<ul><li>A recording is in progress</li><li>Your time is less that 2 hours (120 minutes)</li></ul></div>");
        $('#recordnotification').attr("style", 'color: #666;');
      })
  }
  </script>
  <!-- Modal Server Settings -->
  <div class="modal fade" id="serversettings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Server Settings</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Broadcast Password</label>
                <input type="text" class="form-control" id="broadcastpw1" name="broadcastpw1" value="<?php echo $myserver['Password']; ?>" placeholder="Broadcast Password">
            </div>
            <div class="form-group">
                <label>Admin Password</label>
                <input type="text" class="form-control" id="adminpw1" name="adminpw1" value="<?php echo $myserver['AdminPassword']; ?>" placeholder="Broadcast Password">
            </div>
            <div class="form-group">
                <label>Song History to Show</label>
                <input type="text" class="form-control" id="songHistory1" name="songHistory1" value="<?php echo $myserver['ShowLastSongs']; ?>" placeholder="10">
            </div>
            <div class="form-group">
                <label>Public Server?</label>
                <select class="form-control" id="publicserver1" name="publicserver1">
                <?php
        if ($myserver['PublicServer'] == 'always') { ?>
                    <option selected value="always">Yes</option>
                    <option value="never">No</option>
                <?php
        } else { ?>
                    <option selected value="never">No</option>
                    <option value="always">Yes</option>
                <?php
        } ?>
                </select>
            </div>
        </div>
        <div class="modal-footer" id="loading3">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" onClick="updateServerDetails();return false;" class="btn btn-primary">Save changes</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <!-- Modal AutoDJ Settings -->
  <div class="modal fade" id="autodjsettings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">AutoDJ Settings</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Stream Title</label>
                <input type="text" class="form-control" id="streamtitle" name="streamtitle"  value="<?php echo $myserver['TitleFormat']; ?>" placeholder="Stream Title">
            </div>
            <div class="form-group">
                <label>Stream Genre</label>
                <input type="text" class="form-control" id="streamgenre" name="streamgenre" value="<?php echo $myserver['genre']; ?>" placeholder="Stream Genre">
            </div>
            <div class="form-group">
                <label>Stream Website</label>
                <input type="text" class="form-control" id="streamwebsite" name="streamwebsite" value="<?php echo $myserver['website']; ?>" placeholder="Stream Website">
            </div>
            <div class="form-group">
                <label>Shuffle Playlist?</label>
                <select class="form-control" id="streamshuffle" name="streamshuffle">
                <?php
        if ($myserver['random'] == '1') { ?>
                    <option selected value="1">Yes</option>
                    <option value="0">No</option>
                <?php
        } else { ?>
                    <option selected value="0">No</option>
                    <option value="1">Yes</option>
                <?php
        } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Public Server?</label>
                <select class="form-control" id="publicserver" name="publicserver">
                <?php
        if ($myserver['PublicServer'] == 'always') { ?>
                    <option selected value="always">Yes</option>
                    <option value="never">No</option>
                <?php
        } else { ?>
                    <option selected value="never">No</option>
                    <option value="always">Yes</option>
                <?php
        } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Crossfade Mode</label>
                <select class="form-control" id="crossfadeMode" name="crossfadeMode">
                    <option <?php echo ($myserver['autodj_crossfadeMode'] == '0') ? "Selected" : "";?> value="0">No Crossfade</option>
                    <option <?php echo ($myserver['autodj_crossfadeMode'] == '1') ? "Selected" : "";?> value="1">100/100 -&gt; 100/0</option>
                    <option <?php echo ($myserver['autodj_crossfadeMode'] == '2') ? "Selected" : "";?> value="2">0/100 -&gt; 100/0</option>
                </select>
            </div>
            <div class="form-group">
                <label>Crossfader Length (in mili seconds 1000 = 1 second)</label>
                <input type="text" class="form-control" id="crossfadeseconds" name="crossfadeseconds" placeholder="5000" value="<?php echo $myserver['autodj_crossfadeseconds']; ?>">
            </div>

            <div class="form-group">
                <label>AutoDJ bitrate</label>
                <select class="form-control" id="autodjBitrate" name="autodjBitrate">
		            <?php
		            	autoDJBitrateSelect($myserver['id']);
		            ?>
                </select>
            </div>            
        </div>
        <div class="modal-footer" id="loading4">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" onClick="updateAutoDJDetails();return false;" class="btn btn-primary">Save changes</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
<?php if ($config['mrtg'] == "on") { ?>
  <!-- Modal Graphical Stats View -->
  <div class="modal fade" id="graphicstats" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:40%;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Graphic Stats</h4>
        </div>
        <div class="modal-body">
            <iframe src="<?php echo $config['web_addr'] . '/mrtg/' . $myserver['PortBase'] . $myserver['id'] . '.html'; ?>" seamless=seamless width="100%" height="60%" frameborder="0"></iframe>
        </div>
        <div class="modal-footer" id="">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <?php
        } ?>
  <?php
        if (!useraccess($_SESSION['username']) < "5") {
            $autodjusername = getusername($myserver['owner']);
        } else {
            $autodjusername = $_SESSION['username'];
        }
?>
  <script>
  function changeBroadcastPassword() {
      $('#loading1').html('<span class="loader-inner ball-pulse"><div></div><div></div><div></div></span>');
      thedjpassword = shoutcastjquery('#changepasswordcontroldj').val();
      jQuery.ajax({
              url: '<?php echo $config['web_addr']; ?>/include/password.broadcast.php',
          method: 'POST',
          data: {
              broadcast: thedjpassword,
              id: "<?php echo $myserver['id'];?>",
              portbase: "<?php echo $myserver['PortBase'];?>",
              allowed: "yes"
          }
      }).done(function (response) {
          $('#ajaxresponse').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Success!</b> Your broadcast password has been changed.</div>');
          $('#broadcastPassword').modal('hide');
          shoutcastjquery('#changepasswordcontroldj').val('')
      }).fail(function () {
          $('#broadcastPassword').modal('hide');
          if (thedjpassword.length < 4) {
              $('#ajaxresponse').html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> Your Broadcast password needs to be more than 4 characters</div>');
              $('#loading1').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" onClick="changeBroadcastPassword();return false;" class="btn btn-primary">Save changes</button>');
              shoutcastjquery('#changepasswordcontroldj').val('')
          } else {
              $('#ajaxresponse').html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> An unknown error has occurd, please refresh the page and try again.</div>');
              $('#loading1').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" onClick="changeBroadcastPassword();return false;" class="btn btn-primary">Save changes</button>');
              shoutcastjquery('#changepasswordcontroldj').val('')
          };
      });
  }
  function changeAdminPassword() {
      $('#loading2').html('<span class="loader-inner ball-pulse"><div></div><div></div><div></div></span>');
      adminpassword = shoutcastjquery('#adminpasswordedit').val();
      jQuery.ajax({
          url: '<?php echo $config['web_addr']; ?>/include/password.admin.php',
          method: 'POST',
          data: {
              broadcast: adminpassword,
              id: "<?php echo $myserver['id'];?>",
              portbase: "<?php echo $myserver['PortBase'];?>",
              allowed: "yes"
          }
      }).done(function (response) {
          $('#ajaxresponse').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Success!</b> Your Admin password has been changed.</div>');
          $('#loading2').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" onClick="changeAdminPassword();return false;" class="btn btn-primary">Save changes</button>');
          $('#adminPassword').modal('hide');
          shoutcastjquery('#adminpasswordedit').val('')
      }).fail(function () {
          $('#adminPassword').modal('hide');
          if (adminpassword.length < 4) {
              $('#ajaxresponse').html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> Your Admin password needs to be more than 4 characters</div>');
              $('#loading2').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" onClick="changeAdminPassword();return false;" class="btn btn-primary">Save changes</button>');
              shoutcastjquery('#adminpasswordedit').val('')
          } else {
              $('#ajaxresponse').html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> An unknown error has occurd, please refresh the page and try again.</div>');
              $('#loading2').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" onClick="changeAdminPassword();return false;" class="btn btn-primary">Save changes</button>');
              shoutcastjquery('#adminpasswordedit').val('')
          };
      });
  }
  function updateServerDetails() {
      $('#loading3').html('<span class="loader-inner ball-pulse"><div></div><div></div><div></div></span>');
      broadcastpw       = shoutcastjquery('#broadcastpw1').val();
      adminpw           = shoutcastjquery('#adminpw1').val();
      songHistory       = shoutcastjquery('#songHistory1').val();
      publicserver      = shoutcastjquery('#publicserver1').val();
      jQuery.ajax({
          url: '<?php echo $config['web_addr']; ?>/include/rebuildconf.php',
          method: 'POST',
          data: {
              broadcastpw: broadcastpw,
              adminpw: adminpw,
              songHistory: songHistory,
              publicserver: publicserver,
              id: "<?php echo $myserver['id'];?>",
              portbase: "<?php echo $myserver['PortBase'];?>",
              allowed: "yes"
          }
      }).done(function (response) {
        console.log(response);
          $('#ajaxresponse').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Success!</b> Server Settings updated successfully, Please restart your server (turn off then back on again).</div>');
          $('#serversettings').modal('hide');
      }).fail(function () {
          $('#serversettings').modal('hide');
              $('#ajaxresponse').html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> An unknown error has occurd, please refresh the page and try again.</div>');
      });
  }
  function updateAutoDJDetails() {
      $('#loading4').html('<span class="loader-inner ball-pulse"><div></div><div></div><div></div></span>');
      streamtitle          = shoutcastjquery('#streamtitle').val();
      streamgenre          = shoutcastjquery('#streamgenre').val();
      streamwebsite        = shoutcastjquery('#streamwebsite').val();
      streamshuffle        = shoutcastjquery('#streamshuffle').val();
      crossfadeMode        = shoutcastjquery('#crossfadeMode').val();
      crossfadeseconds     = shoutcastjquery('#crossfadeseconds').val();
      publicserver         = shoutcastjquery('#publicserver').val();
      autodjBitrate        = shoutcastjquery('#autodjBitrate').val();
      jQuery.ajax({
          url: '<?php echo $config['web_addr']; ?>/include/rebuildautodj.php',
          method: 'POST',
          data: {
              streamtitle: streamtitle,
              streamgenre: streamgenre,
              streamwebsite: streamwebsite,
              streamshuffle: streamshuffle,
              crossfadeMode: crossfadeMode,
              crossfadeseconds: crossfadeseconds,
              publicserver: publicserver,
              id: "<?php echo $myserver['id'];?>",
              portbase: "<?php echo $myserver['PortBase'];?>",
              allowed: "yes",
              autodjBitrate:autodjBitrate
          }
      }).done(function (response) {
        console.log(response);
          $('#ajaxresponse').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Success!</b> AutoDJ Settings updated successfully, Please restart your server and AutoDJ (turn off then back on again).</div>');
          $('#autodjsettings').modal('hide');
		  $('#loading4').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" onClick="updateAutoDJDetails();return false;" class="btn btn-primary">Save changes</button>');
      }).fail(function () {
          $('#autodjsettings').modal('hide');
              $('#ajaxresponse').html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> An unknown error has occurd, please refresh the page and try again.</div>');
		  $('#loading4').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" onClick="updateAutoDJDetails();return false;" class="btn btn-primary">Save changes</button>');
      });
  }
  function changeautoDJstatus(){
    jQuery('#autodjstatuschange').load('<?php echo $config['web_addr']; ?>/include/control.autodj.php?username=<?php echo $autodjusername; ?>&PortBase=<?php echo $myserver['PortBase'];?>');
    setTimeout(function(){location.reload()},500);
  }
  function rebuildConfiguration(){
    jQuery('#ajaxresponse').load('<?php echo $config['web_addr']; ?>/include/rebuildserver.php?x=<?php echo $myserver['id'];?>&p=<?php echo $myserver['PortBase'];?>&y=<?php echo $_GET['srvname'];?>');
  }

  function getRadioStats(){
    <?php if (checkstream($myserver['PortBase'], $myserver['id'])) { ?>

        <?php if (strpos($config['sc_serv'], '2.3.5') !== false) { ?>
            // v2 here
            jQuery.getScript('<?php echo $config['web_addr']; ?>/include/shoutcastv2.js.server.php?PortBase=<?php echo $myserver['PortBase'];?>',function(){
                doGetStats();
            });
        <?php } else { ?>
            jQuery.getScript('<?php echo $config['web_addr']; ?>/include/shoutcast.js.server.php?PortBase=<?php echo $myserver['PortBase'];?>',function(){
                doGetStats();
            });
        <?php } ?>

    <?php } else { ?>
        jQuery('#SHOUTcastStatus').html(" - Offline");
    <?php } ?>
 }
 getRadioStats();
 setInterval(getRadioStats, 30000);
  </script>
  <?if(!useraccess($_SESSION['username']) < "4"){
    ?>
<script>
  function ConfirmDeleteServer()
  {
        if (confirm("Are you sure you want to delete server <?echo $myserver['PortBase'];?>?"))
             location.href='<?php echo $config['web_addr']; ?>/delete/<?echo $myserver['id'];?>/<?echo $myserver['PortBase'];?>/<?php echo $myserver['servername']; ?>/';
        }
</script>
    <?php
    } ?>
                    <?php
}
} ?>