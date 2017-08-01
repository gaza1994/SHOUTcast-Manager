<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: 2 Step Authentication.
 * @website: http://scottishbordersdesign.co.uk/
 */
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include ('functions.inc.php');
include ('GoogleAuthenticator.php');
$authenticator = new PHPGangsta_GoogleAuthenticator();

switch ($_GET['action']) {
	case 'enable':
		$secret = $authenticator->createSecret();
		$config = settings();
		echo "<center>Scan the following QR code using the <a href='https://support.google.com/accounts/answer/1066447?hl=en' target=_blank>Google Authenticator App <i class=\"fa fa-external-link\"></i></a></center><br />";
		$qrCodeUrl = $authenticator->getQRCodeGoogleUrl($config['site_title'], $secret, $config['web_addr']);
		echo "<center><img src='$qrCodeUrl'></center><br />";
		?>
		<input type="hidden" name="s" id="s" value="<?php echo $secret;?>">
	    <div class="input-group">
	        <span class="input-group-addon">Code from Google</span>
	        <input type="text" name="authcode" id="authcode" class="form-control" placeholder="Google Authenticator Code">
	        <button type="button" onClick="verifyCode();" name="2stephauthVerify" id="2stephauthVerify" class="form-control btn btn-primary">Verify Code</button>
	    </div>

	    <div id="verifyArea">&nbsp;</div>

	    <script>
	    function verifyCode(){
	    	var verCode = $('#authcode').val();
	    	var s 		= $('#s').val();
	    	$('#verifyArea').load("include/2stepauth.php?action=verify-setup&code="+verCode+"&s="+s);
	    }

		$('#authcode').keypress(function(e) {
		    if(e.which == 13) {
		        verifyCode();
		    }
		});
	    </script>
		<?php
		break;

	case 'verify-setup':
		$verifyCode = $_GET['code'];
		$secret 	= $_GET['s'];
		$tolerance = 2; // tolerance set higher due to setup proccess ... this is acceptable.
		$checkResult = $authenticator->verifyCode($secret, $verifyCode, $tolerance);

		if ($checkResult) {
			$db = dbconnect();
			$db->where("username", $_SESSION['username']);
			$add2step = $db->update("members", array(
				"2stepauth" => $secret
			));
			if ($add2step) {
				addevent($_SESSION['username'], " Added 2 step verification to their account.");
				echo "<script>
					$('#2stepauthArea').slideUp();
					$('#editprofile').attr('onsubmit','');
					$('#2stephauth').html('Success, 2 Step authenication has now been enabled.');
					$('#2stephauth').attr('disabled', true);
					</script>";
			} else {
				echo "<script>
				$('#2stepauthArea').slideUp();
				$('#editprofile').attr('onsubmit','');
				$('#2stephauth').html('Something went wrong while authenticating, please click here to try again.');
				</script>";
			}
		} else {
		    echo "<script>
		    $('#2stepauthArea').slideUp();
		    $('#editprofile').attr('onsubmit','');
		    $('#2stephauth').html('Error, this code is invalid, please click here to try again.');
		    </script>";
		}
		break;

	case 'disable':
		$db = dbconnect();
		$db->where("username", $_SESSION['username']);
		$remove2step = $db->update("members", array(
			"2stepauth" => null
		));
		if ($remove2step) {
			addevent($_SESSION['username'], " Removed 2 step verification from their account.");
			echo success("Success", "2 Step authenication has now been disabled.");
			echo "<script>
			$('#editprofile').attr('onsubmit','');
			$('#2stephauth').html('Disabled');
			$('#2stephauth').attr('disabled', true);
			</script>";
		}
		break;
	
	default:
		# code...
		break;
}