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

    $i = "0";
    if (isset($_GET['page'])) {
        $page  = $_GET['page']; 
    } else { 
        $page = 1;
    };
    $db->pageLimit = 50;
    $events = $db->arraybuilder()->paginate("api_events", $page);
?>

<div class="box">
    <div class="box-header" style=" ">
        <h3 class="box-title">API Event Log</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped" id="APICALLS">
            <thead>
                <tr>
                    <th>User</th>
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
        echo "<td>{$event['user']}</td>";
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
            echo "<li><a href=\"{$config['web_addr']}/events/api/".$d."/\">".$d."</a></li>";
    };
}
?>
</ul>