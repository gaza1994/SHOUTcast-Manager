<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Edit Server
* @website: http://scottishbordersdesign.co.uk/
*/

if(useraccess($_SESSION['username']) < "4") {
  echo "ACCESS DENIED - INCIDENT REPORTED";
  $event = " Attempted to access the restricted edit server section.";
  addevent($_SESSION['username'], $event);
} else {
  $username = $_SESSION['username'];
  $config = settings();
  $db = dbConnect();
  
  /****************************
    CALLED BY FORM SUBMIT
  ****************************/
  if(isset($_POST['subedit'])) {
    if (portexists($_POST['portbase']) && ($_POST['oldport'] != $_POST['portbase'])) {
      $backurl = "/edit/".$_POST['oldport']."/".$_POST['id']."/0/";
      echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Success!</b>A server with port ".$_POST['portbase']." exists already - no changes was made. Please <a href=\"".$backurl."\">try again</a> with a different port.</div>";
      exit;
    } elseif ($_POST['portbase'] < $config['start_portbase']) {
      echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Success!</b>Server port number must be ".$config['start_portbase']." or higher. Please <a href=\"".$backurl."\">try again</a> with a different port.</div>";
      exit;
    } // end of if port exists //

    $status = $_POST['status'];
    $oldport = $_POST['oldport'];
    $oldname = $_POST['oldname'];
    $id = $_POST['id'];
    deletefile($config['sbd_path']."/servers/".$oldport.$oldname.".conf");
    $srvname = $_POST['srvname'];
    $portbase = $_POST['portbase'];
    $adminpassword = $_POST['adminpassword'];
    $djpassword = $_POST['djpassword'];
    $maxuser = $_POST['maxuser'];
    $bitrate = $_POST['bitrate'];
    $realtime = $_POST['realtime'];
    $screenlog = $_POST['screenlog'];
    $showlastsongs = $_POST['showlastsongs'];
    $w3cenable = $_POST['w3cenable'];
    $srcip = $_POST['srcip'];
    $dstip = $_POST['dstip'];
    $yport = $_POST['yport'];
    $namelookup = $_POST['namelookup'];
    
    // Set relay to field values or '0'
    if(isset($_POST['isrelay'])) {
      $relayport = $_POST['relayport'];
      $relayhost = $_POST['relayhost'];
    } else {
      $relayport = '0';
      $relayhost = '0';
    } // end if isrelay

    $autodump = $_POST['autodump'];
    $autodumptime = $_POST['autodumptime'];
    $publicsrv = $_POST['publicsrv'];
    $allowrelay = $_POST['allowrelay'];
    $allowpubrelay = $_POST['allowpubrelay'];
    $metainterval = $_POST['metainterval'];
    $genre = $_POST['genre'];
    $website = $_POST['website'];
    $autodj_max_space = $_POST['autodj_max_space'];

    // Check if privilege is high enough
    if(useraccess($_SESSION['username']) >= "4") {	  
      // Start by stopping the stream
      stopstream($id);
      deletefile('ices/'.$id.'.'.$oldport.'.conf');
      
      if(isset($_POST['autodj'])) {
        $autodj = "1";
        $random = $_POST['random'];
        $playlist = $_POST['playlist'];
     
        $db->rawQuery('UPDATE servers SET AdminPassword="'.$adminpassword.'",servername="'.$srvname.'",created="'.$username.'",MaxUser="'.$maxuser.'",
        Password="'.$djpassword.'",PortBase="'.$portbase.'",RealTime="'.$realtime.'",ScreenLog="'.$screenlog.'",ShowLastSongs="'.$showlastsongs.'",
        W3CEnable="'.$w3cenable.'",SrcIP="'.$srcip.'",DestIP="'.$dstip.'",Yport="'.$yport.'",NameLookups="'.$namelookup.'",RelayPort="'.$relayport.'", 
        RelayServer="'.$relayhost.'",AutoDumpUsers="'.$autodump.'", AutoDumpSourceTime="'.$autodumptime.'",PublicServer="'.$publicsrv.'",
        AllowRelay="'.$allowrelay.'",AllowPublicRelay="'.$allowpubrelay.'",MetaInterval="'.$metainterval.'",
        autodj="'.$autodj.'",Playlist="'.$playlist.'",genre="'.$genre.'",website="'.$website.'",bitrate="'.$bitrate.'",autodj_max_space="'.$autodj_max_space.'" WHERE id="'.$id.'"');
        
        genIces0Conf($id, $portbase, $playlist, $random);
        genicespls($id,$random);
        reloadstream($id);
      } else {

$db->rawQuery('UPDATE servers SET AdminPassword="'.$adminpassword.'",servername="'.$srvname.'",created="'.$username.'",MaxUser="'.$maxuser.'",
Password="'.$djpassword.'",PortBase="'.$portbase.'",RealTime="'.$realtime.'",ScreenLog="'.$screenlog.'",ShowLastSongs="'.$showlastsongs.'",W3CEnable="'.$w3cenable.'",
SrcIP="'.$srcip.'",DestIP="'.$dstip.'",Yport="'.$yport.'",NameLookups="'.$namelookup.'",RelayPort="'.$relayport.'",RelayServer="'.$relayhost.'",AutoDumpUsers="'.$autodump.'",
AutoDumpSourceTime="'.$autodumptime.'",PublicServer="'.$publicsrv.'",AllowRelay="'.$allowrelay.'",AllowPublicRelay="'.$allowpubrelay.'",
MetaInterval="'.$metainterval.'",bitrate="'.$bitrate.'" WHERE id="'.$id.'"');
      }

    } else {
      if(getuid($_SESSION['username']) == getowner_byid($id)) {
        if(isset($_POST['autodj'])) {
          $autodj = "1";
	  $playlist = $_POST['playlist'];
	  $random = $_POST['random'];
			
$db->rawQuery('UPDATE servers SET AdminPassword="'.$adminpassword.'" AND servername="'.$srvname.'" AND Password="'.$djpassword.'" AND RealTime="'.$realtime.'"
AND ScreenLog="'.$screenlog.'" AND ShowLastSongs="'.$showlastsongs.'" AND W3CEnable="'.$w3cenable.'" AND SrcIP="'.$srcip.'" AND DestIP="'.$dstip.'" AND
Yport="'.$yport.'" AND NameLookups="'.$namelookup.'" AND RelayPort="'.$relayport.'" AND RelayServer="'.$relayserver.'" AND AutoDumpUsers="'.$autodump.'"
AND AutoDumpSourceTime="'.$autodumptime.'" AND PublicServer="'.$publicsrv.'" AND AllowRelay="'.$allowrelay.'" AND AllowPublicRelay="'.$allowpubrelay.'" AND
MetaInterval="'.$metainterval.'" AND autodj="'.$autodj.'" AND Playlist="'.$playlist.'" AND genre="'.$genre.'" AND website="'.$website.'" AND bitrate="'.$bitrate.'"
 WHERE id="'.$id.'"');
			genIces0Conf($id, $portbase, $playlist, $random);
			genicespls($id,$random);
			reloadstream($this_id);
	  } else {
$db->rawQuery('UPDATE servers SET AdminPassword="'.$adminpassword.'" AND servername="'.$srvname.'" AND Password="'.$djpassword.'" AND RealTime="'.$realtime.'"
AND ScreenLog="'.$screenlog.'" AND ShowLastSongs="'.$showlastsongs.'" AND W3CEnable="'.$w3cenable.'" AND SrcIP="'.$srcip.'" AND DestIP="'.$dstip.'" AND
Yport="'.$yport.'" AND NameLookups="'.$namelookup.'" AND RelayPort="'.$relayport.'" AND RelayServer="'.$relayserver.'" AND AutoDumpUsers="'.$autodump.'"
AND AutoDumpSourceTime="'.$autodumptime.'" AND PublicServer="'.$publicsrv.'" AND AllowRelay="'.$allowrelay.'" AND AllowPublicRelay="'.$allowpubrelay.'" AND
MetaInterval="'.$metainterval.'" AND bitrate="'.$bitrate.'" WHERE id="'.$id.'"');
	 }
      } else {
      echo "DENIED - INCIDENT REPORTED!";
      $event = " Attempted to modify a server they don't own";
      addevent($_SESSION['username'], $event);
    }
  }
  $event = " Edited server on port $portbase";
  addevent($username,$event);





  // If AutoDJ is set, generate SC_SERV config file and playlist
if(isset($_POST['autodj'])) {

    buildPlaylist($portbase);
    $header = ";\n"
            . "; $srvname Configuration file\n"
            . "; Auto-generated by SBD\n"
            . ";\n"
            . "PlaylistFile={$config['sbd_path']}/playlists/autodj_$portbase.lst\n"
            . "ServerIP=188.165.168.203\n"
            . "ServerPort=$portbase\n"
            . "Password=$djpassword\n"
            . "StreamTitle=$srvname\n"
            . "StreamURL=$website\n"
            . "Genre=$genre\n"
            . "Shuffle=0\n"
            . "; Bitrate/SampleRate/Channels recommended values:\n"
            . "; 8kbps 8000/11025/1\n"
            . "; 16kbps 16000/11025/1\n"
            . "; 24kbps 24000/22050/1\n"
            . "; 32kbps 32000/22050/1\n"
            . "; 64kbps mono 64000/44100/1\n"
            . "; 64kbps stereo 64000/22050/2\n"
            . "; 96kbps stereo 96000/44100/2\n"
            . "; 128kbps stere0 128000/44100/2\n"
            . "InputSamplerate=44100\n"
            . "InputChannels=2\n"
            . "Bitrate={$bitrate}000\n"
            . "SampleRate=44100\n"
            . "Channels=2\n"
            . "Quality=5\n"
            . "CrossfadeMode=1\n"
            . "CrossfadeLength=5000\n"
            . "UseID3=0\n"
            . "Public=0\n"
            . "AIM=\n"
            . "ICQ=\n"
            . "IRC=\n";

// Save the configuration file
$fd = fopen($config['sbd_path']."/servers/autodj_".$portbase."".$srvname.".conf", "w");
fputs($fd, $header . "\n\n");
fclose($fd);


}


////////////////////////////////////////////////
//
// We keep the relay and non relay configs seperate
// This way if we have more granularity between the two
//
////////////////////////////////////////////////
  if($relayport == "0") {
 	$header = ";\n"
        . "; $srvname Configuration file\n"
        . "; Auto-generated by SBD\n"
        . ";\n"
	. "AdminPassword=$adminpassword\n"
  . "MaxUser=$maxuser\n"
  . "Password=$djpassword\n"
  . "PortBase=$portbase\n"
  . "RealTime=$realtime\n"
  . "ScreenLog=$screenlog\n"
  . "ShowLastSongs=$showlastsongs\n"
  . "W3CEnable=$w3cenable\n"
  . "SrcIP=$srcip\n"
  . "DestIP=$dstip\n"
  . "Yport=$yport\n"
  . "NameLookups=$namelookup\n"
	. "AutoDumpUsers=$autodump\n"
	. "AutoDumpSourceTime=$autodumptime\n"
	. "PublicServer=$publicsrv\n"
	. "AllowRelay=$allowrelay\n"
	. "AllowPublicRelay=$allowpubrelay\n"
	. "BanFile=".$config['sbd_path']."/logs/$portbase$srvname.ban\n"
	. "LogFile=".$config['sbd_path']."/logs/$portbase$srvname.log\n"
  . "W3CLog=".$config['sbd_path']."/logs/$portbase$srvname.w3c.log\n"
	. "MetaInterval=$metainterval\n";
    $fd = fopen($config['sbd_path']."/servers/$portbase$srvname.conf", "w+");
    fputs($fd, $header . "\n\n");
    fclose($fd);
  } else {
    $header = ";\n"
    . "; $srvname Configuration file\n"
    . "; Auto-generated by SBD\n"
    . ";\n"
	  . "AdminPassword=$adminpassword\n"
    . "MaxUser=$maxuser\n"
    . "Password=$djpassword\n"
    . "PortBase=$portbase\n"
    . "RealTime=$realtime\n"
    . "ScreenLog=$screenlog\n"
    . "ShowLastSongs=$showlastsongs\n"
    . "W3CEnable=$w3cenable\n"
    . "SrcIP=$srcip\n"
    . "DestIP=$dstip\n"
    . "Yport=$yport\n"
    . "NameLookups=$namelookup\n"
	  . "RelayPort=$relayport\n"
    . "RelayServer=$relayhost\n"
    . "AutoDumpUsers=$autodump\n"
    . "AutoDumpSourceTime=$autodumptime\n"
    . "PublicServer=$publicsrv\n"
    . "AllowRelay=$allowrelay\n"
    . "AllowPublicRelay=$allowpubrelay\n"
	  . "BanFile=".$config['sbd_path']."/logs/$portbase$srvname.ban\n"
	  . "LogFile=".$config['sbd_path']."/logs/$portbase$srvname.log\n"
    . "W3CLog=".$config['sbd_path']."/logs/$portbase$srvname.w3c.log\n"
    . "MetaInterval=$metainterval\n";
    $fd = fopen($config['sbd_path']."/servers/$portbase$srvname.conf", "w+");
    fputs($fd, $header . "\n\n");
    fclose($fd);
  } // end if/else relayport = 0


////////////////////////////////////////////////
//
// If we've changed our server name we need to remove old files
//
////////////////////////////////////////////////
if($srvname != $oldname) {
  deletefile("".$config['sbd_path']."/logs/".$oldport."".$oldname.".log");
  deletefile("".$config['sbd_path']."/logs/".$oldport."".$oldname.".w3c.log");
} // end if srvname != oldname


////////////////////////////////////////////////
//
// If we've changed ports then we need to remove all old files
//
////////////////////////////////////////////////
if($oldport != $portbase) {
  deletefile("".$config['sbd_path']."/logs/".$oldport."".$oldname.".log");
  deletefile("".$config['sbd_path']."/logs/".$oldport."".$oldname.".w3c.log");

  // Rename MRTG stats and graphs, regenerate mrtg.cfg
  renamefile("".$config['sbd_path']."/mrtg/".$oldport."".$id.".log", "".$config['sbd_path']."/mrtg/".$portbase."".$id.".log");
  renamefile("".$config['sbd_path']."/mrtg/".$oldport."".$id.".old", "".$config['sbd_path']."/mrtg/".$portbase."".$id.".old");
  renamefile("".$config['sbd_path']."/mrtg/".$oldport."".$id.".html", "".$config['sbd_path']."/mrtg/".$portbase."".$id.".html");
  renamefile("".$config['sbd_path']."/mrtg/".$oldport."".$id."-day.png", "".$config['sbd_path']."/mrtg/".$portbase."".$id."-day.png");
  renamefile("".$config['sbd_path']."/mrtg/".$oldport."".$id."-week.png", "".$config['sbd_path']."/mrtg/".$portbase."".$id."-week.png");
  renamefile("".$config['sbd_path']."/mrtg/".$oldport."".$id."-month.png", "".$config['sbd_path']."/mrtg/".$portbase."".$id."-month.png");
  renamefile("".$config['sbd_path']."/mrtg/".$oldport."".$id."-year.png", "".$config['sbd_path']."/mrtg/".$portbase."".$id."-year.png");
  renamefile("".$config['sbd_path']."/logs/".$oldport."".$srvname.".log", "".$config['sbd_path']."/mrtg/".$portbase."".$id.".log"); 
  renamefile("".$config['sbd_path']."/logs/".$oldport."".$srvname.".w3c.log", "".$config['sbd_path']."/mrtg/".$portbase."".$id.".w3c.log");
  generatemrtg();
} // end if oldport != portbase



////////////////////////////////////////////////
//
//  If our server is running we need to commit changes
// This execution is less than a second on my running machine so end users
// shouldn't even notice ;)
//
////////////////////////////////////////////////
  if($status == "1") {
    startstream($id);
  }
  echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Success!</b><b> Server $srvname edited<br><br></b>Click <a href=\"\">here</a> to return to the front page.</div>";


  } else {


////////////////////////////////////////////////
//
// If we haven't sent a POST then we display our form
//
////////////////////////////////////////////////
$db->where("PortBase", $_REQUEST['portbase']);
$db->where("id", $_REQUEST['id']);
$rows = $db->get('servers');
foreach ($rows as $row) {
// Show creation form
$row = index_array($row);
?>



<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Edit Server</h3>
    </div><!-- /.box-header -->
    <!-- form start -->
<div class="box-body">
  <form method="post" action="">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_1" data-toggle="tab">New Server Setup</a></li>
        <li><a href="#tab_2" data-toggle="tab">Advanced Setup</a></li>
      </ul>
      <div class="tab-content">
            <form method="post" action="" style="padding: 0 20px;">
                <input type="hidden" name="submitnew" value="1"> 

                <div class="tab-pane active" id="tab_1">  

                    <div class="form-group">
                        <label>Server Name</label>
                        <input type="text" class="form-control" name="srvname" placeholder="<?php echo $row[34];?>" value="<?php echo $row[34];?>">
                        <input type="hidden" name="oldname" value="<?php echo $row[34];?>">
                    </div>   

                    <div class="form-group">
                        <label>Port</label>
                        <?php if(useraccess($_SESSION['username']) >= "4"){ ?>
                        <input type="text" class="form-control" name="portbase" value="<?php echo $row[4];?>" placeholder="<?php echo $row[4];?>">
                        <?php }else{ echo "<div class=\"form-control\">".$row[4]."</div>"; } ?>
                    </div>   


                    <div class="form-group">
                        <label>Admin Password</label>
                        <input type="text" class="form-control" name="adminpassword" value="<?php echo $row[16];?>" placeholder="<?php echo $row[16];?>">
                    </div> 


                    <div class="form-group">
                        <label>Broadcast Password</label>
                        <input type="text" class="form-control" name="djpassword" value="<?php echo $row[3];?>" placeholder="<?php echo $row[3];?>">
                    </div> 


                    <div class="form-group">
                        <label>Max Listeners</label>
                        <?php if(useraccess($_SESSION['username']) >= "4"){ ?>
                            <input type="text" class="form-control" name="maxuser" value="<?php echo $row[2];?>" placeholder="<?php echo $row[2];?>">
                        <?php } else { echo "<div class=\"form-control\">".$row[2]."</div>"; } ?>
                    </div> 


                    <div class="form-group">
                        <label>Max Bitrate</label>
                        <?php if(useraccess($_SESSION['username']) >= "4"){ ?>
                        <select class="form-control" name="bitrate">
                            <option value="8"<?php if($row[45] == '8'){echo "selected";}?>>8kbps</option>
                            <option value="16"<?php if($row[45] == '16'){echo "selected";}?>>16kbps</option>
                            <option value="24"<?php if($row[45] == '24'){echo "selected";}?>>24kbps</option>
                            <option value="32"<?php if($row[45] == '32'){echo "selected";}?>>32kbps</option>
                            <option value="64"<?php if($row[45] == '64'){echo "selected";}?>>64kbps</option>
                            <option value="96"<?php if($row[45] == '96'){echo "selected";}?>>96kbps</option>
                            <option value="128"<?php if($row[45] == '128'){echo "selected";}?>>128kbps</option>
                            <option value="198"<?php if($row[45] == '198'){echo "selected";}?>>198kbps</option>
                            <option value="320"<?php if($row[45] == '320'){echo "selected";}?>>320kbps</option>
                        </select>
                        <?php } else { echo "<div class=\"form-control\">".$row[45]."</div>"; } ?>
                    </div>

                    <div class="form-group">
                        <label>Public Server</label>
                        <select class="form-control" name="publicsrv">
                            <option value="default" <?php if($row[21] == 'default'){echo "selected";}?>>Yes</option>
                            <option value="no" <?php if($row[21] == 'no'){echo "selected";}?>>No</option>
                        </select>
                    </div>      

                    <input type="hidden" name="autodj" value="<?php echo $row[40];?>">
                    <div class="form-group">
                        <label>AutoDJ Space (in MB)</label>
                        <input type="text" class="form-control" name="autodj_max_space" value="<?php echo $row[49];?>" placeholder="<?php echo $row[49];?>">
                    </div>  

                    <div class="form-group">
                        <label>Playlist</label>
                        <select class="form-control" name="playlist">
                            <?php $plsq = $db->get('playlist');
                                foreach ((array)$plsq as $pls) {
                                    $pls = index_array($pls);
                                    if($pls[0] == $row[41]){
                                        echo "<option value=\"".$pls[0]."\" SELECTED>".$pls[3]."</option>";
                                    } else {
                                        echo "<option value=\"".$pls[0]."\">".$pls[3]."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Randomize Tracks</label>
                        <select class="form-control" name="random">
                            <option value="0" <?php if($row[42] == '0'){echo "selected";}?>>No</option>
                            <option value="1" <?php if($row[42] == '1'){echo "selected";}?>>Yes</option>
                        </select>
                    </div>              
                      

                    <div class="form-group">
                        <label>Genre</label>
                        <input type="text" class="form-control" name="genre" value="<?php echo $row[43];?>" placeholder="<?php echo $row[43];?>">
                    </div>                            
                                
                                    
                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" class="form-control" name="website" value="<?php echo $row[44];?>" placeholder="<?php echo $row[44];?>">
                    </div> 

                    <div class="form-group">
                        <label>Advanced user?</label>
                        <a href="#tab_2" data-toggle="tab">Click here for Advanced Setup</a>
                    </div> 
                </div>

                <div class="tab-pane" id="tab_2">
                    <div class="form-group">
                        <label>Real Time</label>
                        <select class="form-control" name="realtime">
                            <option value="1" <?php if($row[6] == "1"){ echo "selected";}?>>Yes</option>
                            <option value="0" <?php if($row[6] == "0"){ echo "selected";}?>>No</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label>Screen Log</label>
                        <select class="form-control" name="screenlog">
                            <option value="1" <?php if($row[7] == "1"){ echo "selected";}?>>Yes</option>
                            <option value="0" <?php if($row[7] == "0"){ echo "selected";}?>>No</option>
                        </select>
                    </div>



                    <div class="form-group">
                        <label>Song History Amount</label>
                        <input type="text" class="form-control" name="showlastsongs" value="<?php echo $row[33];?>" placeholder="<?php echo $row[33];?>">
                    </div>                



                    <div class="form-group">
                        <label>W3C Enabled</label>
                        <select class="form-control" name="w3cenable">
                            <option value="yes" <?php if($row[8] == "yes"){ echo "selected"; }?>>Yes</option>
                            <option value="no" <?php if($row[8] == "no"){ echo "selected"; }?>>No</option>
                        </select>
                    </div>
                            


                    <div class="form-group">
                        <label>Source IP</label>
                        <input type="text" class="form-control" name="srcip" value="<?php echo $row[10];?>" placeholder="<?php echo $row[10];?>">
                    </div> 



                    <div class="form-group">
                        <label>Destination IP</label>
                        <input type="text" class="form-control" name="dstip" value="<?php echo $row[11];?>" placeholder="<?php echo $row[11];?>">
                    </div> 



                    <div class="form-group">
                        <label>Yport</label>
                        <input type="text" class="form-control" name="yport" value="<?php echo $row[12];?>" placeholder="<?php echo $row[12];?>">
                    </div> 


                    <div class="form-group">
                        <label>Name Lookups</label>
                        <select class="form-control" name="namelookup">
                            <option value="0" <?php if($row[13] == "0"){ echo "selected"; }?>>No</option>
                            <option value="1" <?php if($row[13] == "1"){ echo "selected"; }?>>Yes</option>
                        </select>
                    </div>        
                                    

                    <div class="form-group"> 
                        <div class="checkbox">
                            <label>
                                <input name="isrelay" type="checkbox" <?php if($row[14] == "1"){ echo "checked";}?>> Relay Server?
                            </label>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Relay Server</label>
                        <input type="text" class="form-control" name="relayhost" value="<?php echo $row[15];?>" placeholder="<?php echo $row[15];?>">
                    </div>
                        

                            
                    <div class="form-group">
                        <label>Relay Port</label>
                        <input type="text" class="form-control" name="relayport" value="<?php echo $row[14];?>" placeholder="<?php echo $row[14];?>">
                    </div>     
                                    
                     
                            
                    <div class="form-group">
                        <label>Auto Dump Users</label>
                        <select class="form-control" name="autodump">
                            <option value="0" <?php if($row[17] == "0"){ echo "selected";}?>>No</option>
                            <option value="1" <?php if($row[17] == "1"){ echo "selected";}?>>Yes</option>
                        </select>
                    </div>         
                                    

                                    
                    <div class="form-group">
                        <label>Auto Dump Source Time</label>
                        <input type="text" class="form-control" name="autodumptime" value="<?php echo $row[18];?>" placeholder="<?php echo $row[18];?>">
                    </div>           
                            
                            
                    <div class="form-group">
                        <label>Allow Relay</label>
                        <select class="form-control" name="allowrelay">
                            <option value="Yes" <?php if($row[22] == "Yes"){ echo "selected";}?>>Yes</option>
                            <option value="No" <?php if($row[22] == "No"){ echo "selected";}?>>No</option>
                        </select>
                    </div>   



                    <div class="form-group">
                        <label>Allow Public Relay</label>
                        <select class="form-control" name="allowpubrelay">
                            <option value="Yes" <?php if($row[23] == "Yes"){ echo "selected";}?>>Yes</option>
                            <option value="No" <?php if($row[23] == "No"){ echo "selected";}?>>No</option>
                        </select>
                    </div>                



                    <div class="form-group">
                        <label>Meta Interval</label>
                        <input type="text" class="form-control" name="metainterval" value="<?php echo $row[24];?>" placeholder="<?php echo $row[24];?>">
                    </div>
                </div>
                                    

                <div class="box-footer">
                    <?php if($_REQUEST['status'] == "1"){ ?>
                        <input type="hidden" name="status" value="1">
                        <button type="submit" name="subedit" class="btn btn-primary">Commit Changes</button>
                    <?php } else { ?>
                        <button type="submit" name="subedit" class="btn btn-primary">Commit Changes</button>
                    <?php } ?>
                    <input type="hidden" name="oldport" value="<?php echo $_REQUEST['portbase'];?>">
                    <input type="hidden" name="id" value="<?php echo $row[0];?>">
                </div>
            </form>
      </div><!-- /.tab-content -->
    </div><!-- nav-tabs-custom --> 
  </form>
</div>

<?php   }
 }
} // end if/else useraccess < 4
?>
