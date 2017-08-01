<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Notices
 * @website: http://scottishbordersdesign.co.uk/
*/

if (isset($_GET['edit'])) {
    include ('header.php');
    if (useraccess($_SESSION['username']) < "5") {
        include ('header.php');
        die('<div class="alert alert-danger alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Error!</b> ACCESS DENIED - YOU DO NOT HAVE ACCESS TO NOTICE EDITOR</div>');
    }
    if (isset($_POST['removestring'])) {
        $buildstring = "";
        file_put_contents('notice.html', $buildstring);
        echo '<div class="alert alert-success alert-dismissable">
						    <i class="fa fa-check"></i>
						    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						    <b>Success!</b> Notice has been deleted. Use this form to enable it again.
						</div>';
    }
    if (isset($_POST['issubmit'])) {
        $titleofstring = $_POST['messagetitle'];
        $contentstring = $_POST['messagecontent'];
        $buildstring = "<div class='alert alert-info alert-dismissable'><i class='fa fa-info'></i><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button><b>{$titleofstring}</b><br /> {$contentstring}</div>";
        file_put_contents('notice.html', $buildstring);
        echo '<div class="alert alert-success alert-dismissable">
						    <i class="fa fa-check"></i>
						    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						    <b>Success!</b> Notice has been updated.
						</div>';
    }
$noticecontent = file_get_contents('notice.html');


?>

		<form action="" method="post">
			<div class="box-body">

				<div class="form-group">
				    <h3>Preview</h3>
				    <?php echo $noticecontent; ?>
				</div>


				<div class="form-group">
				    <label for="messageTitle">Notice Title</label>
				    <input type="text" name="messagetitle" class="form-control" id="messageTitle" placeholder="Enter Title">
				</div>

				<div class="form-group">
				    <label>Notice Content</label>
				    <textarea class="form-control textarea" rows="8" name="messagecontent" placeholder="Your Notice ..."></textarea>
				</div>

				<div class="box-footer">
				    <button type="submit" name="issubmit" class="btn btn-primary">Submit</button>  
				    <button type="submit" name="removestring" class="btn btn-primary">Remove Notice</button>
				</div>
			</div>
		</form>

<script src="<?php echo $config['web_addr']; ?>/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>


		<?php
    include ('footer.php');
    exit();
} else {
    $notices = file_get_contents('notice.html');
    echo ($notices);
}
?>