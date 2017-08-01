<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Home
 * @website: http://scottishbordersdesign.co.uk/
*/
require ('header.php');
?>
                    <h1>
                        Viewing Server
                        <small><?php echo $_GET['srvname']; ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="?php echo $config['web_addr'];?>/home.php"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class=""><a href="<?php echo $config['web_addr']; ?>/home.php"><i class="glyphicon glyphicon-hdd"></i> Server List</a></li>
                        <li class="active"><i class="glyphicon glyphicon-hdd"></i> Viewing Server <?php echo $_GET['srvname']; ?></li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">
      <?php include('include/view-server.php');?>
      </div><!-- /.row -->
    </div>
<?php require ('footer.php'); ?>