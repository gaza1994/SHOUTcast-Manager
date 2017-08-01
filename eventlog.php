<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Event Log Page
* @website: http://scottishbordersdesign.co.uk/
*/
header('Access-Control-Allow-Origin: *');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
    include('header.php');
    $db = dbConnect();
    $config = settings();
    if (isset($_GET['clear'])) {
        switch ($_GET['clear']) {
            case 'admin':
                $clear = $db->rawQuery("TRUNCATE TABLE events");
                break;

            case 'api':
                $clear = $db->rawQuery("TRUNCATE TABLE api_events");
                break;
            
            default:
                # code...
                break;
        }
    }
    ?>
                    <h1>
                        Event Log
                        <small>View users tracks</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="home.php"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active"><i class="glyphicon glyphicon-book"></i> Event Log</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="row">
                        <div class="col-lg-12 col-xs-12">

                                    <div class="box box-solid">
                                <div class="box-header" style=" ">
                                    <h3 class="box-title">Clear Logs</h3>
                                </div>
                                <div class="box-body">
                                    <p>Click the buttons to clear the logs</p>
                                    <p><a class="btn btn-success btn-s" href="<?php echo $config['web_addr'] ?>/events/admin/1/?clear=admin">Clear Admin Log</a> &nbsp;<a class="btn btn-success btn-s" href="<?php echo $config['web_addr'] ?>/events/api/1/?clear=api">Clear API Log</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                  <div class="row">
                    <div class="col-xs-12">

    <?php 
        if(useraccess($_SESSION['username']) < "4"){
            echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Error!</b> ACCESS DENIED</div>";
        }else{
            if ($_GET['log'] == "admin") {
                include('eventviewer.php');
            } elseif($_GET['log'] == "api"){
                include('apieventviewer.php');
            } else {
                echo "Error Fetching Event Logs.";
            }
?>
      </div><!-- /.row -->
    </div>
<!-- place holder -->
<?php
}
require('footer.php');
?>
