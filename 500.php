<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Error 500
* @website: http://scottishbordersdesign.co.uk/
*/

require('header.php');
?>
                    <h1>
                        SHOUTcast Panel
                        <small>Error 500</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active">Error 500</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">
                    <div class="error-page">
                        <h2 class="headline text-info"> 500</h2>
                        <div class="error-content">
                            <h3><i class="fa fa-warning text-yellow"></i> Oops! Something went wrong.</h3>
                            <p>
                                We will work on fixing that right away&nbsp;
                                Meanwhile, you may <a href="<?php echo $config['web_addr'];?>">return home</a>.
                            </p>
                        </div><!-- /.error-content -->
                    </div>
      </div><!-- /.row -->
    </div>
<?php require('footer.php');
?>