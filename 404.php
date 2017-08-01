<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Error 404
* @website: http://scottishbordersdesign.co.uk/
*/

require('header.php');
?>
                    <h1>
                        SHOUTcast Panel
                        <small>Error 404</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active">Error 404</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">
                    <div class="error-page">
                        <h2 class="headline text-info"> 404</h2>
                        <div class="error-content">
                            <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
                            <p>
                                We could not find the page you were looking for.
                                Meanwhile, you may <a href="<?php echo $config['web_addr'];?>">return home</a>.
                            </p>
                        </div><!-- /.error-content -->
                    </div>
      </div><!-- /.row -->
    </div>
<?php require('footer.php');
?>