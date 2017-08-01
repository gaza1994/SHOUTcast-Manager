<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Events Viewer
* @website: http://scottishbordersdesign.co.uk/
*/
header('Access-Control-Allow-Origin: *');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if(useraccess($_SESSION['username']) < "4"){
    echo "ACCESS DENIED - INCIDENT REPORTED";
    $event = " Attempted to access the restricted user management section.";
    addevent($_SESSION['username'], $event);
} else {
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Event Log</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped" id="EVENTLOG">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Timestamp</th>
                    <th>Events</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
<?php 
$i = "0";
    if (isset($_GET['page'])) {
        $page  = $_GET['page']; 
    } else { 
        $page = 1;
    };
    ////////////////////////////////////////////////////////////
    //
    // If user is not a mod or higher only show them their own
    // events.
    //
    ////////////////////////////////////////////////////////////
    if(useraccess($_SESSION['username']) < "4"){
        $db->orderBy('id', 'DESC');
        $db->where('event_user', $_SESSION['username']);
        $db->pageLimit = 50;
        $events = $db->arraybuilder()->paginate("events", $page);
    } else {
        $db->orderBy('id', 'DESC');
        $db->pageLimit = 50;
        $events = $db->arraybuilder()->paginate("events", $page);
    }
foreach ($events as $key => $row){
    $row = index_array($row);
    echo "<tr'>\n";
    ?>
                <td>
                    <?php echo $row[0];?>
                </td>
                <td>
                    <i>
                        <?php echo $row[4];?>
                    </i>
                </td>
                <td>
                    <i>
                        <?php echo $row[2];?>
                    </i>
                </td>
                <td>
                    <?php echo $row[5];?>
                </td>
        </tr>
    <?php     
}
echo "</tbody><tfoot>showing {$page} out of {$db->totalPages}</tfoot></table></div><!-- /.box-body --></div>";
?>
<ul class="pagination pagination-sm no-margin pull-right">
<?php
    for ($d=1; $d<=$db->totalPages; $d++) {
           echo "<li><a href=\"{$config['web_addr']}/events/admin/".$d."/\">".$d."</a></li>";
    };
}
?>
</ul>