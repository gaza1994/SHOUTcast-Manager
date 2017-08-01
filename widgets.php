<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Widgets
 * @website: http://scottishbordersdesign.co.uk/
*/
require ('header.php');
$config = settings();
$uid = getuid($_SESSION['username']);

$noserver = false;

$myservers = getmyserversMulti_byid($uid);
?>
                    <h1>
                        SHOUTcast Panel
                        <small>Widgets</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $config['web_addr']; ?>/"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active">Widgets</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">


<?php
if (count($myservers) == 1) {
    $myserver = $myservers[0];
    $port = $myserver['PortBase'];
    $noserver = false;
} else {
    if (isset($_GET['server'])) {
        $port = $_GET['server'];
        $myserver = getserverbyportbase($port);
        $noserver = false;
    }
    if (count($myservers) == 0 || empty($myservers)) {
        $noserver = true;
        $myserver = array( );
        $myserver['owner'] = 0;
    }
?>

<div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Select Server to Manage</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form role="form" action="widgets.php" method="GET">
      <div class="box-body">

        <div class="form-group">
            <label for="server">Manage Server</label>
            <select class="form-control" name="server" id="server" onChange="this.form.submit();">
            <option value="null">Select Server</option>
                <?php 
                    foreach ($myservers as $key => $server) {
                        if (isset($port) && $port == $server['PortBase']) {
                            echo "<option value='{$server['PortBase']}' selected>{$server['servername']} - {$server['PortBase']}</option>";
                        } else {
                            echo "<option value='{$server['PortBase']}'>{$server['servername']} - {$server['PortBase']}</option>";
                        }
                    }
                ?>
            </select>

<?php
$ownerID = getuserdetails($_SESSION['username']);
if (isset($myserver)) {
    if ($myserver['owner'] != $ownerID['user_id'] && !empty($_GET['server']) ) {
        $noserver = true;
        echo ("<br /><div class=\"alert alert-danger alert-dismissible\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
                    <h4><i class=\"icon fa fa-ban\"></i> No Server!</h4>
                    Incorrect port for server.
                  </div>");
    }
}

    if ( empty($port) && !isset($_GET['server']) ) {
        $noserver = true;
        echo "<br /><div class=\"alert alert-danger alert-dismissible\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
                <h4><i class=\"icon fa fa-ban\"></i> No Server!</h4>
                No server has been selected.
              </div>";
    }

?>
        </div>
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
<?php
}
?>


<?php if(!$noserver){ ?>
    <div class="nav-tabs-custom" style="display:<?php if($noserver){echo 'none';}else{echo 'block';}; ?>;">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab_1" data-toggle="tab">Stats Widget</a></li>
                                    <li><a href="#tab_2" data-toggle="tab">Media Player Widget</a></li>
                                    <li><a href="#tab_3" data-toggle="tab">Flash Player Widget</a></li>
                                    <li><a href="#tab_4" data-toggle="tab">TuneIn Widget</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_1">
                                        <b>How to use:</b>
                                        <p>Simply copy and paste the following code where you want the stats to show.</p>
                                        <p>You are free to edit this, as long as the id tags stay in-tact.
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" id="statswidget" onClick="selectAllText('statswidget');" placeholder="Refresh the page to show">
Radio Is: &lt;span id="SHOUTcastStatus"&gt; &lt;/span&gt;&lt;br /&gt;
Current DJ: &lt;span id="SHOUTcastTitle"&gt; &lt;/span&gt;&lt;br /&gt;
Current Song: &lt;span id="SHOUTcastSong"&gt; &lt;/span&gt;&lt;br /&gt;
Genre: &lt;span id="SHOUTcastGenre"&gt; &lt;/span&gt;&lt;br /&gt;
Streaming at: &lt;span id="SHOUTcastBitrate"&gt; &lt;/span&gt;&lt;br /&gt;
Listners: &lt;span id="SHOUTcastListeners"&gt; &lt;/span&gt; of &lt;span id="SHOUTcastListenersMax"&gt; &lt;/span&gt;&lt;br /&gt;
Peak Listners: &lt;span id="SHOUTcastListenersPeak"&gt; &lt;/span&gt; of &lt;span id="SHOUTcastListenersMax2"&gt; &lt;/span&gt;&lt;br /&gt;
Average Listen Time: &lt;span id="SHOUTcastAverageListenTime"&gt; &lt;/span&gt;&lt;br /&gt;
Stream Format: &lt;span id="SHOUTcastFormat"&gt; &lt;/span&gt;</textarea>
                                            </div>
                                        <p>Copy and paste the following above the &lt;/body&gt; tag.</p>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" id="statswidget2" onClick="selectAllText('statswidget2');" placeholder="Refresh the page to show">&lt;script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/stats.js"&gt; &lt;/script&gt; &lt;script&gt; doGetStats(); setInterval(doGetStats, 30000); &lt;/script&gt;
                                            
                                                </textarea>
                                            </div>
                                        <div class="box">
                                            <div class="box-header" style=" ">
                                                <div class="pull-right box-tools">                                    
                                                </div>
                                                <h3 class="box-title">Preview</h3>
                                            </div><!-- /.box-header -->
                                                Radio Is: <span id="SHOUTcastStatus"> </span><br />
                                                Current DJ: <span id="SHOUTcastTitle"> </span><br />
                                                Current Song: <span id="SHOUTcastSong"> </span><br />
                                                Genre: <span id="SHOUTcastGenre"> </span><br />
                                                Streaming at: <span id="SHOUTcastBitrate"> </span><br />
                                                Listners: <span id="SHOUTcastListeners"> </span> of <span id="SHOUTcastListenersMax"> </span><br />
                                                Peak Listners: <span id="SHOUTcastListenersPeak"> </span> of <span id="SHOUTcastListenersMax2"> </span><br />
                                                Average Listen Time: <span id="SHOUTcastAverageListenTime"> </span><br />
                                                Stream Format: <span id="SHOUTcastFormat"> </span>
                                                <script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/stats.js"> </script>
                                                <script>doGetStats();</script>
                                                <br />                     
                                            </div><!-- /.box-body -->
                                    </div><!-- /.tab-pane -->
                                    <div class="tab-pane" id="tab_2">
                                        <b>How to use:</b>
                                        <p>Simply copy and paste the following code where you want the media player to show.</p>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" id="playerWidget" onClick="selectAllText('playerWidget');" placeholder="Refresh the page to show">&lt;span id="scplayer"&gt;&lt;script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/player.js"&gt; &lt;/script&gt;&lt;/span&gt;</textarea>
                                            </div>
                                        <p>Want Autoplay? Copy this code instead</p>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" id="playerWidget2" onClick="selectAllText('playerWidget2');" placeholder="Refresh the page to show">&lt;span id="scplayer"&gt;&lt;script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/player.js?autoplay=true"&gt; &lt;/script&gt;&lt;/span&gt;</textarea>
                                            </div>
                                <div class="box">
                                    <div class="box-header" style=" ">
                                        <div class="pull-right box-tools">                                    
                                        </div>
                                        <h3 class="box-title">Preview</h3>
                                    </div><!-- /.box-header -->
                                        &nbsp;<span id="scplayer"><script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/player.js"> </script></span><br />                     
                                    </div><!-- /.box-body -->
                                    </div><!-- /.tab-pane -->
                                    <div class="tab-pane" id="tab_3">
                                        <b>How to use:</b>
                                        <p>Simply copy and paste the following code where you want the media player to show.</p>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" id="flashplayerWidget" onClick="selectAllText('flashplayerWidget');" placeholder="Refresh the page to show">&lt;span id="scflashplayer"&gt;&lt;script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/flashplayer.js"&gt; &lt;/script&gt;&lt;/span&gt;</textarea>
                                            </div>
                                        <p>Want Autoplay? Copy this code instead</p>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" id="flashplayerWidget2" onClick="selectAllText('flashplayerWidget2');" placeholder="Refresh the page to show">&lt;span id="scflashplayer"&gt;&lt;script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/flashplayer.js?autoplay=true"&gt; &lt;/script&gt;&lt;/span&gt;</textarea>
                                            </div>
                                <div class="box">
                                    <div class="box-header" style=" ">
                                        <div class="pull-right box-tools">                                    
                                        </div>
                                        <h3 class="box-title">Preview</h3>
                                    </div><!-- /.box-header -->
                                        &nbsp;<span id="scflashplayer"><script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/flashplayer.js"> </script></span><br />                     
                                    </div><!-- /.box-body -->
                                    </div><!-- /.tab-pane -->
                                    <div class="tab-pane" id="tab_4">
                                        <b>How to use:</b>
                                        <p>Simply copy and paste the following code where you want the tunein links to show.</p>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" id="tuneinwidgetcode" onClick="selectAllText('tuneinwidgetcode');" placeholder="Refresh the page to show">&lt;span id="tuneinwidget"&gt;&lt;script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/tunein.js"&gt; &lt;/script&gt;&lt;/span&gt;</textarea>
                                            </div>
                                <div class="box">
                                    <div class="box-header" style=" ">
                                        <h3 class="box-title">Preview</h3>
                                    </div><!-- /.box-header -->
                                        &nbsp;<span id="tuneinwidget"><script src="<?php echo $config['web_addr']; ?>/widgets/<?php echo $myserver['PortBase']; ?>/tunein.js"> </script></span><br />                     
                                    </div><!-- /.box-body -->
                                    </div><!-- /.tab-pane -->
                                </div><!-- /.tab-content -->
                            </div>
      </div><!-- /.row -->
    </div>
<script>
function selectAllText(formid){
    $("#"+formid).click(function() {
        var $this = $(this);
        $this.select();
        // Work around Chrome's little problem
        $this.mouseup(function() {
            // Prevent further mouseup intervention
            $this.unbind("mouseup");
            return false;
        });
    });
}
</script>
<?php } ?>
<?php require('footer.php');
?>