<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Delete Server
* @website: http://scottishbordersdesign.co.uk/
*/

require('header.php'); 
?>
                    <h1>
                        Deleting Server
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="home.php"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active"><i class="glyphicon glyphicon-ban"></i> Deleting Server</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">
      <?php include('deleteserver.php');?>
      </div><!-- /.row -->
    </div>
<?php 
require('footer.php'); 
?>
