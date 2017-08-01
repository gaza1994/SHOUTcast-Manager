<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: New User Page
* @website: http://scottishbordersdesign.co.uk/
*/

include('header.php');
?>
    <h1>
        New Server
        <small>Create a New SHOUTcast server</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
        <li class="active"><i class="glyphicon glyphicon-hdd"></i> New Server</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <?php include('newserver.php');?>
      </div><!-- /.row -->
    </div>
<?php require('footer.php');
?>
