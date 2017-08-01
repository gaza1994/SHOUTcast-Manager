<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: New Server
 * @website: http://scottishbordersdesign.co.uk/
*/
require ('include/config.php');
if (useraccess($_SESSION['username']) < "4") {
    echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-ban\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Error!</b> ACCESS DENIED - INCIDENT REPORTED</div>";
    $event = "Attempted to access the restricted new server section.";
    addevent($_SESSION['username'], $event);
} else {
    $config = settings();
    $username = $_SESSION['username'];
    if (isset($_POST['subnew'])) {
        if (portexists($_POST['portbase'])) {
            echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-ban\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Error!</b> A server with port " . $_POST['portbase'] . " exists already. Please <a href=\"/new.php\">try again</a> with a different port.</div>";
            exit;
        } elseif ($_POST['portbase'] < $config['start_portbase']) {
            echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-ban\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Error!</b> Server port number must be " . $config['start_portbase'] . " or higher. Please <a href=\"/new.php\">try again</a> with a different port.</div>";
            exit;
        }
        // New servers are by default "not enabled"
        $enabled = 0;
        // Fetch values from form
        $srvname = $_POST['srvname'];
        $srvname = preg_replace('/\s/', '_', $srvname);
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
        // If form's relayport is not set, assign '0' as values to relay port & host
        if (isset($_POST['isrelay'])) {
            $relayport = $_POST['relayport'];
            $relayhost = $_POST['relayhost'];
        } else {
            $relayport = '0';
            $relayhost = '0';
        }
        $uid = $_POST['uid'];
        $autodump = $_POST['autodump'];
        $autodumptime = $_POST['autodumptime'];
        $publicsrv = $_POST['publicsrv'];
        $allowrelay = $_POST['allowrelay'];
        $allowpubrelay = $_POST['allowpubrelay'];
        $metainterval = $_POST['metainterval'];
        $genre = $_POST['genre'];
        $website = $_POST['website'];
        // Define common insert fields and values
        $insert = array(
            'owner' => $uid,
            'AdminPassword' => $adminpassword,
            'servername' => $srvname,
            'created' => $username,
            'MaxUser' => $maxuser,
            'Password' => $djpassword,
            'PortBase' => $portbase,
            'RealTime' => $realtime,
            'ScreenLog' => $screenlog,
            'ShowLastSongs' => $showlastsongs,
            'W3CEnable' => $w3cenable,
            'SrcIP' => $srcip,
            'DestIP' => $dstip,
            'Yport' => $yport,
            'NameLookups' => $namelookup,
            'RelayPort' => $relayport,
            'RelayServer' => $relayhost,
            'AutoDumpUsers' => $autodump,
            'AutoDumpSourceTime' => $autodumptime,
            'PublicServer' => $publicsrv,
            'AllowRelay' => $allowrelay,
            'AllowPublicRelay' => $allowpubrelay,
            'MetaInterval' => $metainterval,
            'bitrate' => $bitrate,
            'autodj_used_space' => '0',
            'autodj_crossfadeMode' => '1',
            'autodj_crossfadeseconds' => '5000'
        );
        // Extra work if AutoDJ is checked
        if (isset($_POST['autodj'])) {
            // Assign values to extra fields
            $autodj = "1";
            $playlist = '';
            $random = $_POST['random'];
            $autodjspace = $_POST['autodj_max_space'];
            // Define extra fields and values
            $extras = array('autodj' => '0', 'random' => $random, 'autodj_max_space' => $autodjspace, 'genre' => $genre, 'website' => $website);
            // Merge extras into common insert
            $insert = array_merge($insert, $extras);
        }
        // Do the actual insert
        $db = dbConnect();
        $id = $db->insert('servers', $insert);
        // Now create the FTP for the AutoDJ (even if they dont have it enabled, it saves trouble adding it now, than later!)
        mkdir($config['media_path'].$portbase.'/', 0777);
        // If AutoDJ is set, generate SC_SERV config file and playlist
        if (isset($_POST['autodj'])) {
            newPlaylist($portbase);
            buildPlaylist($portbase);
            $header = ";
" . "; $srvname Configuration file
" . "; Auto-generated by SBD
" . ";
" . "PlaylistFile={$config['sbd_path']}/playlists/autodj_$portbase.lst
" . "ServerIP={$config['host_addr']}
" . "ServerPort=$portbase
" . "Password=$djpassword
" . "StreamTitle=$srvname
" . "StreamURL=$website
" . "Genre=$genre
" . "Shuffle=0
" . "; Bitrate/SampleRate/Channels recommended values:
" . "; 8kbps 8000/11025/1
" . "; 16kbps 16000/11025/1
" . "; 24kbps 24000/22050/1
" . "; 32kbps 32000/22050/1
" . "; 64kbps mono 64000/44100/1
" . "; 64kbps stereo 64000/22050/2
" . "; 96kbps stereo 96000/44100/2
" . "; 128kbps stere0 128000/44100/2
" . "Bitrate={$bitrate}000
" . "SampleRate=44100
" . "Channels=2
" . "Quality=5
" . "CrossfadeMode=1
" . "CrossfadeLength=5000
" . "UseID3=0
" . "Public=0
" . "AIM=
" . "ICQ=
" . "IRC=
";
            // Save the configuration file
            $fd = fopen($config['sbd_path'] . "/servers/autodj_" . $portbase . "" . $srvname . ".conf", "w");
            fputs($fd, $header . "

");
            fclose($fd);
        }
        // Create event in event log
        $event = " created a new server with id " . $id . " on port " . $portbase;
        addevent($username, $event);
        // Run MRTG update
        generatemrtg();
        // Set up sc_serv config file for this server
        $header = ";
" . "; $srvname Configuration file
" . "; Auto-generated by SBD
" . ";
" . "AdminPassword=$adminpassword
" . "MaxUser=$maxuser
" . "Password=$djpassword
" . "PortBase=$portbase
" . "RealTime=$realtime
" . "ScreenLog=$screenlog
" . "ShowLastSongs=$showlastsongs
" . "W3CEnable=$w3cenable
" . "SrcIP=$srcip
" . "DestIP=$dstip
" . "Yport=$yport
" . "NameLookups=$namelookup
";
        if ($relayport > 0) {
            $header.= "RelayPort=$relayport
" . "RelayServer=$relayhost
";
        }
        $header.= "AutoDumpUsers=$autodump
" . "AutoDumpSourceTime=$autodumptime
" . "PublicServer=$publicsrv
" . "AllowRelay=$allowrelay
" . "AllowPublicRelay=$allowpubrelay
" . "BanFile=" . $config['sbd_path'] . "/logs/$portbase$srvname.ban
" . "LogFile=" . $config['sbd_path'] . "/logs/$portbase$srvname.log
" . "W3CLog=" . $config['sbd_path'] . "/logs/$portbase$srvname.w3c.log
" . "MetaInterval=$metainterval
";
        // Save the configuration file
        $fd = fopen($config['sbd_path'] . "/servers/" . $portbase . "" . $srvname . ".conf", "w+");
        fputs($fd, $header . "

");
        fclose($fd);
        // If enabled start on creation, start the server
        if ($config['auto_start'] == "on") {
            startstream($id);
        }
        // Spread the good news
        echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Success!</b> New server '$srvname' created!<br><br></b>Click <a href=\"" . $config['web_addr'] . "/home.php\">here</a> to return to the front page.</div>";
        // If enabled start on creation, start the server
        
    } else {
        // We were not called by form submit, so show the form
        $db = dbConnect();
        // Show creation form
        
?>
    <div class="col-md-8">
        <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">New Server</h3>
                </div><!-- /.box-header -->
                <!-- form start -->

<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab_1" aria-expanded="true" data-toggle="tab">New Server Setup</a></li>
    <li class=""><a href="#tab_2" aria-expanded="false" data-toggle="tab">Advanced Setup</a></li>
  </ul>
  <div class="tab-content">
        <form method="post" action="" style="padding: 0 20px;">
            <input type="hidden" name="submitnew" value="1"> 

            <div class="tab-pane active" id="tab_1">  

                <div class="form-group">
                    <label>Server Name</label>
                    <input type="text" class="form-control" name="srvname" placeholder="My Radio Stream">
                </div>   

                <div class="form-group">
                    <label>Port</label>
                    <input type="text" class="form-control" name="portbase" value="<?php echo nextport(); ?>" placeholder="<?php echo nextport(); ?>">
                </div>   


                <div class="form-group">
                    <label>Admin Password</label>
                    <input type="text" class="form-control" name="adminpassword" value="admin_<?php echo rand(0000000,9999999); ?>" placeholder="Admin Password">
                </div> 


                <div class="form-group">
                    <label>Broadcast Password</label>
                    <input type="text" class="form-control" name="djpassword" value="broadcast_<?php echo rand(0000000,9999999); ?>" placeholder="Broadcast Password">
                </div> 


                <div class="form-group">
                    <label>Max Listeners</label>
                    <input type="text" class="form-control" name="maxuser" value="" placeholder="Enter value larger than 0">
                </div> 


                <div class="form-group">
                    <label>Max Bitrate</label>
                    <select class="form-control" name="bitrate">
                        <option value="8">8kbps</option>
                        <option value="16">16kbps</option>
                        <option value="24">24kbps</option>
                        <option value="32">32kbps</option>
                        <option value="64">64kbps</option>
                        <option value="96">96kbps</option>
                        <option value="128" selected>128kbps</option>
                        <option value="198">198kbps</option>
                        <option value="320">320kbps</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Public Server</label>
                    <select class="form-control" name="publicsrv">
                        <option value="default" selected>Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>      

                <div class="form-group">
                    <label>Assign to user</label>
                    <select class="form-control" name="uid">
                        <option value="null">---</option>
                        <?php userlist(); ?>
                    </select>
                </div>        

                <input type="hidden" name="autodj" value="1">
                <div class="form-group">
                    <label>AutoDJ Space (in MB)</label>
                    <input type="text" class="form-control" name="autodj_max_space" value="" placeholder="Enter space in MB, 0 = disabled.">
                </div>  

                <div class="form-group">
                    <label>Randomize Tracks</label>
                    <select class="form-control" name="random">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>              
                  

                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" class="form-control" name="genre" value="Various" placeholder="Enter Server Genre">
                </div>                            
                            
                                
                <div class="form-group">
                    <label>Website</label>
                    <input type="text" class="form-control" name="website" value="" placeholder="http://">
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
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>


                <div class="form-group">
                    <label>Screen Log</label>
                    <select class="form-control" name="screenlog">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>



                <div class="form-group">
                    <label>Song History Amount</label>
                    <input type="text" class="form-control" name="showlastsongs" value="10" placeholder="10">
                </div>                



                <div class="form-group">
                    <label>W3C Enabled</label>
                    <select class="form-control" name="w3cenable">
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                        


                <div class="form-group">
                    <label>Source IP</label>
                    <input type="text" class="form-control" name="srcip" value="ANY" placeholder="ANY">
                </div> 



                <div class="form-group">
                    <label>Destination IP</label>
                    <input type="text" class="form-control" name="dstip" value="ANY" placeholder="ANY">
                </div> 



                <div class="form-group">
                    <label>Yport</label>
                    <input type="text" class="form-control" name="yport" value="80" placeholder="80">
                </div> 


                <div class="form-group">
                    <label>Name Lookups</label>
                    <select class="form-control" name="namelookup">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>        
                                

                <div class="form-group"> 
                    <div class="checkbox">
                        <label>
                            <input name="isrelay" type="checkbox" value="UNCHECKED"> Relay Server?
                        </label>
                    </div>
                </div>


                <div class="form-group">
                    <label>Relay Server</label>
                    <input type="text" class="form-control" name="relayhost" value="127.0.0.1" placeholder="127.0.0.1">
                </div>
                    

                        
                <div class="form-group">
                    <label>Relay Port</label>
                    <input type="text" class="form-control" name="relayport" value="8000" placeholder="8000">
                </div>     
                                
                 
                        
                <div class="form-group">
                    <label>Auto Dump Users</label>
                    <select class="form-control" name="autodump">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>         
                                

                                
                <div class="form-group">
                    <label>Auto Dump Source Time</label>
                    <input type="text" class="form-control" name="autodumptime" value="30" placeholder="30">
                </div>           
                        
                        
                <div class="form-group">
                    <label>Allow Relay</label>
                    <select class="form-control" name="allowrelay">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>   



                <div class="form-group">
                    <label>Allow Public Relay</label>
                    <select class="form-control" name="allowpubrelay">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>                



                <div class="form-group">
                    <label>Meta Interval</label>
                    <input type="text" class="form-control" name="metainterval" value="32768" placeholder="32768">
                </div>
            </div>
                                

            <div class="box-footer">
                <button type="submit" name="subnew" class="btn btn-primary">Create Server</button>
            </div> 
        </form>
  </div><!-- /.tab-content -->
</div><!-- nav-tabs-custom --> 

</div>
    </div>

    <div class="col-md-4">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>
                    <?php echo getTotalServers(); ?>
                </h3>
                <p>
                    Total Servers
                </p>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="<?php echo $config['web_addr']; ?>/home.php" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>

        <div class="small-box bg-blue">
            <div class="inner">
                <h3>
                    Settings
                </h3>
                <p>
                    Edit Settings
                </p>
            </div>
            <div class="icon">
                <i class="ion-gear-b"></i>
            </div>
            <a href="<?php echo $config['web_addr']; ?>/setup.php" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
<?php }
}?>