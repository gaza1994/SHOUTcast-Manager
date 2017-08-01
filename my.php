<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: My Details Edit
 * @website: http://scottishbordersdesign.co.uk/
 */
include ('header.php');
include ('include/GoogleAuthenticator.php');

$myProfile = getUserDetails($_SESSION['username']);
if (isset($_POST['posted'])) {
    if (useraccess($_SESSION['username']) < "5") {
        $userlevel = '1';
    } else {
        $userlevel = '5';
    }
    $myuid = $myProfile['user_id'];
    edituser(htmlentities($myuid), htmlentities($_POST['username']), htmlentities($_POST['password']), htmlentities($_POST['firstname']), htmlentities($_POST['lastname']), htmlentities($_POST['email']), htmlentities($userlevel));
    $db = dbConnect();
    $db->where('user_id', $myuid);
    $db->update('members', array('skin' => $_POST['skin']));
}
?>

<form action="" method="POST" id="editprofile">
	<input type="hidden" name="posted" value="<?php echo rand(); ?>">
	<div class="box box-info">
	    <div class="box-header">
	        <h3 class="box-title">Your Profile</h3>
	    </div>
	    <div class="box-body">
	        	<center>
	            	<img data-toggle="tooltip" title="Customer Images are managed by Gravatar" src="<?php echo getgravatar($_SESSION['username']); ?>" class="img-circle" alt="User Image">
	            	<br /><a href="//en.gravatar.com/" target=_blank><i class="fa fa-external-link"></i> Change Customer Image</a>
	                <p>
	                    <?php if (useraccess($_SESSION['username']) < "5") { ?>
	                        Customer <?php
} else { ?>
	                        Administrator <?php
} ?>
	                </p>
	        	</center>
		<br />
	        <div class="input-group">
	            <span class="input-group-addon">Username</span>
	            <input type="text" name="username" class="form-control" value="<?php echo htmlentities($_SESSION['username']); ?>" readonly placeholder="Username">
	        </div>
			<br />
	        <div class="input-group">
	            <span class="input-group-addon">First Name</span>
	            <input type="text" name="firstname" class="form-control" value="<?php echo htmlentities($myProfile['fname']); ?>" placeholder="First Name">
	        </div>
			<br />
	        <div class="input-group">
	            <span class="input-group-addon">Last Name</span>
	            <input type="text" name="lastname" class="form-control" value="<?php echo htmlentities($myProfile['lname']); ?>" placeholder="Last Name">
	        </div>
			<br />
	        <div class="input-group" data-toggle="tooltip" title="Leave empty for no change">
	            <span class="input-group-addon">Password</span>
	            <input type="password" name="password" class="form-control" placeholder="Password">
	        </div>
			<br />
	        <div class="input-group">
	            <span class="input-group-addon">Email Address</span>
	            <input type="text" name="email" class="form-control" value="<?php echo htmlentities($myProfile['email']); ?>" placeholder="Email">
	        </div>
			<br />

			<?php if (is_null($myProfile['2stepauth'])){ ?>
	        <div class="input-group">
	        	<span class="input-group-addon">Google Authenticator</span>
	        	<button type="button" onClick="enable2stepAuth();" name="2stephauth" id="2stephauth" class="form-control">Enable 2 Step Authentication</button>
	        </div>
			<br />
			<div id="2stepauthArea" style="display:none;">
				Loading ...
			</div>
			<?php } else { ?>
	        <div class="input-group">
	        	<span class="input-group-addon">Google Authenticator</span>
	        	<button type="button" onClick="disable2stepAuth();" name="2stephauth" id="2stephauth" class="form-control">Disable 2 Step Authentication</button>
	        </div>
			<br />
			<div id="2stepauthArea" style="display:none;">
				Loading ...
			</div>
			<?php } ?>

			<input type="hidden" name="skin" value="default">
	    </div><!-- /.box-body -->

		<div class="box-footer">
		    <button type="submit" class="btn btn-primary">Save Profile</button>
		</div>
	</div>     
</form>                      
     
<?php
include ('footer.php');
?>

<?php if (is_null($myProfile['2stepauth'])){ ?>
<script>
	function enable2stepAuth(){
		$('#editprofile').attr('onsubmit','return false;');
		$('#2stepauthArea').load("include/2stepauth.php?action=enable&nocache=<?php echo rand();?>", function(){
			$('#2stepauthArea').slideDown();
		});
	}
</script>
<?php } else { ?>
<script>
	function disable2stepAuth(){
		$('#editprofile').attr('onsubmit','return false;');
		$('#2stepauthArea').load("include/2stepauth.php?action=disable&nocache=<?php echo rand();?>", function(){
			$('#2stepauthArea').slideDown();
		});
	}
</script>
<?php } ?>