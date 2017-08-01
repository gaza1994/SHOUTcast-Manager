<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: API Generation Page
* @website: http://scottishbordersdesign.co.uk/
*/
header('Access-Control-Allow-Origin: *');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_GET['getkey']) || isset($_GET['apistatus']) || isset($_GET['apiLevel']) || isset($_GET['genkey']) || isset($_GET['revokeKey'])) {
  session_start();
  if (isset($_GET['getkey']) && !$_SESSION['username'] || isset($_GET['apistatus']) && !$_SESSION['username'] || isset($_GET['apiLevel']) && !$_SESSION['username'] || isset($_GET['genkey']) && !$_SESSION['username'] || isset($_GET['revokeKey']) && !$_SESSION['username']) {
      die("Error: -99");
  }

  include "include/functions.inc.php";
  $db = dbconnect();
  if (isset($_GET['apistatus']) &&  $_GET['apistatus'] == '1') {
      if ($config['api_enabled'] == '1') {
          die  ('Online');
      } else {
          die ('Offline');
      }
  }
  if (isset($_GET['getkey']) &&  $_GET['getkey'] == '1') {
      $db->where("username", $_SESSION['username']);
      $row = $db->getOne("members");
      if (!$row['api_key'] || $row['api_key'] == '' || $row['api_key'] == 'NULL') {
          $apiKey = "0000-0000-000";
      } else {
          $apiKey = $row['api_key'];
      }
      die($apiKey);
  }
  if (isset($_GET['genkey']) &&  $_GET['genkey'] == '1') {
      $key = genKey();
      $db->where('username', $_SESSION['username']);
      $db->update('members', array(
          'api' => '1',
          'api_key' => $key
      ));
      addevent($_SESSION['username'], ' [API KEY] Generated Key');
  }
  if (isset($_GET['revokeKey']) &&  $_GET['revokeKey'] == '1') {
      $key = genKey();
      $db->where('username', $_SESSION['username']);
      $db->update('members', array(
          'api' => '0',
          'api_key' => ''
      ));
      addevent($_SESSION['username'], ' [API KEY] Revoked Key');
  }
  if (isset($_GET['apiLevel']) && $_GET['apiLevel'] == '1') {
      $db->where("username", $_SESSION['username']);
      $row = $db->getOne("members");
      switch ($row['api']) {
          case '0':
              $level = "None";
              break;
          case '1':
              $level = "Basic";
              break;
          case '2':
              $level = "Reseller";
              break;
          case '3':
              $level = "Admin";
              break;
          default:
              $level = "None";
              break;
      }
      die($level);
  }
  exit;
} else {
  include "header.php";
  $myid = getuid($_SESSION['username']);
  $myservers = getmyserversMulti_byid($myid);
}
?>
                    <h1>
                        SHOUTcast Panel
                        <small>API</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active">API</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">
<div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3 id="apistatustext">
                                        Checking ...
                                    </h3>
                                    <p>
                                        API Status
                                    </p>
                                </div>
                                <div class="icon">
                                    <i id="statuscheckicon" class="ion ion-loading-c"></i>
                                    <!-- status online: ion-android-earth -->
                                    <!-- status offline: ion-close-round -->
                                    <!-- status loading: ion-loading-c -->
                                </div>
                                <a href="#" onClick="checkAPIStatus();return false;" class="small-box-footer">
                                    Check Status <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3 id="yourkey">
                                        Fetching ...
                                    </h3>
                                    <p>
                                        Your Key
                                    </p>
                                </div>
                                <div class="icon">
                                    <i id="keyloader" class="ion ion-key"></i>
                                </div>
                                <a href="#" onClick="genkey();return false;" class="small-box-footer">
                                    Generate Key <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3 id="apilevel">
                                        Fetching ...
                                    </h3>
                                    <p>
                                        Access Level
                                    </p>
                                </div>
                                <div class="icon">
                                    <i id="apiloader" class="ion ion-settings"></i>
                                </div>
                                <a href="#" onClick="revokeKey();return false;" class="small-box-footer">
                                    Revoke <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>
                                        v1.5
                                    </h3>
                                    <p>
                                        API Version
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-code"></i>
                                </div>
                                <a data-toggle="modal" onClick="loadDocs();" data-target="#myModal" href="#myModal" class="small-box-footer">
                                    View Developer Documents <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                    </div>
      </div><!-- /.row -->
    </div>
    
<?php
  $i = "0";
  if (isset($_GET['page'])) {
    $page  = $_GET['page']; 
  } else { 
    $page = 1;
  };
  $db->pageLimit = 50;
  $db->where("user", $_SESSION['username']);
  $events = $db->arraybuilder()->paginate("api_events", $page);

?>

<div class="box">
    <div class="box-header" style=" ">
        <h3 class="box-title">API Event Log</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Host/IP</th>
                    <th>Type</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>

<?php
foreach ($events as $key => $event){
        echo "<tr>";
        echo "<td>{$event['host']}</td>";
        echo "<td>{$event['url']}</td>";
        echo "<td>{$event['action']}</td>";
        echo "<td>{$event['timestamp']}</td>";
        echo "</tr>"; 
}
echo "</tbody><tfoot>showing {$page} out of {$db->totalPages}</tfoot></table></div><!-- /.box-body --></div>";
?>
<ul class="pagination pagination-sm no-margin pull-right">
<?php
    for ($d=1; $d<=$db->totalPages; $d++) {
            echo "<li><a href=\"{$config['web_addr']}/api.php?page=".$d."/\">".$d."</a></li>";
    };
?>
</ul>

<?php require('footer.php');
?>
<script>
  function checkAPIStatus(){
    $('#statuscheckicon').attr('class', 'ion-loading-c');
    $('#apistatustext').html("Checking ...");
    $.get( "?apistatus=1", function( data ) {
      if (data.indexOf("Offline") > -1) {
        $('#statuscheckicon').attr('class', 'ion-close-round');
      };
      if (data.indexOf("Online") > -1) {
        $('#statuscheckicon').attr('class', 'ion-android-earth');
      };
      $('#apistatustext').html(data);
    });
  } checkAPIStatus();
  function getKey(){
    $('#keyloader').attr('class', 'ion-loading-c');
    $('#yourkey').html("Fetching ...");
    $.get( "?getkey=1", function( data ) {
      $('#yourkey').html(data);
    });
    $('#keyloader').attr('class', 'ion-key');
  } getKey();
  function genkey(){
    $('#keyloader').attr('class', 'ion-loading-c');
    $('#yourkey').html("Generating ...");
    $.get( "?genkey=1", function( data ) {
      getKey();
      apiLevel();
      checkAPIStatus();
    });
    $('#keyloader').attr('class', 'ion-key');
  }
  function apiLevel(){
    $('#apiloader').attr('class', 'ion-loading-c');
    $('#apilevel').html("Fetching ...");
    $.get( "?apiLevel=1", function( data ) {
      $('#apilevel').html(data);
    });
    $('#apiloader').attr('class', 'ion-settings');
  } apiLevel();
  function revokeKey(){
    $('#apiloader').attr('class', 'ion-loading-c');
    $('#apilevel').html("Fetching ...");
    $.get( "?revokeKey=1", function( data ) {
      getKey();
      apiLevel();
      checkAPIStatus();
    });
    $('#apiloader').attr('class', 'ion-settings');
  }
  function loadDocs(){
    jQuery('.yourAPIKey').each(function(){
      jQuery(this).html( jQuery('#yourkey').html() );
    });
  }
  function changePort(){
    if (jQuery('#portNumberSelector').val() == 'null') {
      jQuery('#docsViewer').slideUp();
    } else {
      jQuery('#docsViewer').slideDown();
    }
  }
</script>
  <!-- Modal Graphical Stats View -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:92%;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Developer Documents</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="server">API Server Selection</label>
            <select class="form-control" name="portNumberSelector" id="portNumberSelector" onChange="changePort();">
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
          </div>

          <div id="docsViewer" style="display:none;">
            <?php 
              include("api/docs/index.php");
            ?>
          </div>
        <script>
          $('#portNumberSelector').on('change', function (e) {
              var optionSelected = $("option:selected", this);
              var valueSelected = this.value;
              jQuery('.yourPortNumber').each(function(){
                jQuery(this).html( valueSelected );
              });
          });
        </script>
        </div>
        <div class="modal-footer" id="">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->