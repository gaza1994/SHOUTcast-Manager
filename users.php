<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Users Page
 * @website: http://scottishbordersdesign.co.uk/
*/
require ('header.php');
?>
                    <h1>
                        Manage Users
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $config['web_addr']; ?>/home.php"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active"><i class="glyphicon glyphicon-user"></i> Manage Users</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">
<?php 
if(useraccess($_SESSION['username']) < "5") {
    /////////////////////////////////////
    //
    // Login as User - For admins returning
    //
    /////////////////////////////////////
    if(isset($_REQUEST['loginas']) && isset($_SESSION['adminlogin'])) {
      $id = $_REQUEST['id'];
      $username = $_REQUEST['username'];
      loginas($id, $username, true);
    } else {
      echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-ban\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Error!</b> ACCESS DENIED - INCIDENT REPORTED</div>";
      $event = " Attempted to access the restricted user management section.";
      addevent($_SESSION['username'], $event);
    }
} else {
  include('manageusers.php');
}
?>
      </div><!-- /.row -->
    </div>
<?php require('footer.php');
?>