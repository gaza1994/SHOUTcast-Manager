<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Edit Server
* @website: http://scottishbordersdesign.co.uk/
*/

require('header.php');
?>
                    <h1>
                        Edit Server
                        <small>Editing Server <?php echo $_GET['portbase'];?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="home.php"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active"><i class="glyphicon glyphicon-pencil"></i> Editing Server: <?php echo $_GET['portbase'];?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">
<?php include('editserver.php');?>
      </div><!-- /.row -->
    </div>
<?php
require('footer.php');
?>
