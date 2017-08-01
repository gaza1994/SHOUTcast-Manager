<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: FTP Management and Creation
 * @website: http://scottishbordersdesign.co.uk/
*/
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
require('include/functions.inc.php');
if(!isset($_SESSION['username'])){
	echo "<center>Please <a href=\"login.php\">LOGIN</a></center>";
} else {

	$db = dbConnect();
	$config = settings();
	$uid = getuid($_SESSION['username']);
	$myid = getuid($_SESSION['username']);
	$port = $_GET['server'];
	$myserver = getserverbyportbase($port);

	if ($myserver['owner'] != $myid ) {
		die("You do not own this server.");
		exit();
	}

	if (isset($_GET['server']) && isset($_GET['action']) && $_GET['action'] == 'fetchFTP') {
		die(getFTPAccounts($_GET['server']));
		exit;
	}

	if (isset($_GET['server']) && isset($_GET['action']) && $_GET['action'] == 'generateFTP') {
	    $subject = $config['media_path'];
	    $search = $config['cpanel_homedir'];
	    $trim_home = str_replace($search, '', $subject);
	    $passwordForFTP = generatePassword(10,false,'lud');
	    $usernameForFTP = generatePassword(10,false,'ld');

	    $return = cpanel_api('add_ftp', $myserver['servername'].'-'.$usernameForFTP, $passwordForFTP, '/' . $trim_home . '/' . $myserver['PortBase'] . '/', $myserver['autodj_max_space'], '.' . $config['cpanel_hostname']);

		if (strpos($return, 'No Valid Command Given') !== false) {
		    die("Something went wrong, please try again.");
		} else {
		    $insert    = array(
		        "username" => $myserver['servername'].'-'.$usernameForFTP,
		        "password" => $passwordForFTP,
		        "PortBase" => $myserver['PortBase']
		    );
		    $db->insert("ftp", $insert);
		}
	    exit();
	}

	if (isset($_GET['server']) && isset($_GET['action']) && $_GET['action'] == 'deleteFTP') {
		/* Get */
		$db->where("id", $_GET['delID']);
		$db->where("PortBase", $myserver['PortBase']);
		$ftpRow = $db->getOne("ftp");

		/* delete from cPanel */
		cpanel_api( 'del_ftp', $ftpRow['username'], null, null, null, null, 0 );

		/* remove from db */
		$db->where("id", $_GET['delID']);
		$db->where("PortBase", $myserver['PortBase']);
		$deleteFTPID = $db->delete("ftp");
	}
?>
		<div class="box box-primary">
			<div class="box-body">
			    <div class="box-body table-responsive no-padding">
			        <table class="table table-hover">
			            <thead>
				            <tr>
				                <th>Host</th>
				                <th>Username</th>
				                <th>Password</th>
				                <th>Port</th>
				                <th>&nbsp;</th>
				            </tr>
				        </thead>
				        <tbody id="ftprows">
				        	<tr id="noFTPWarning">
				        		<td colspan="5" style="text-align:center;">No FTP Accounts</td>
				        	</tr>
				        </tbody>
				    </table>
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer clearfix no-border">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="FTPactionButton" onClick="createFTP(); return false;" type="button" name="createftpButton" class="btn btn-default pull-right">Create FTP</button>
			</div>
		</div>

		<script>
			function removeFTP(ftpID){
				$('#ftprows').html("<tr><td colspan='5' style='text-align:center;''><span class=\"loader-inner ball-pulse\"><div></div><div></div><div></div></span></td></tr>");
				$('#ftprows').load('manageftp.php?server=<?php echo $port;?>&action=deleteFTP&delID='+ftpID, function(){
					// getFTPAccounts();
				});
			}

			function getFTPAccounts(){
				$('#ftprows').load('manageftp.php?server=<?php echo $port;?>&action=fetchFTP');
			}

			function createFTP(){
				$('#ftprows').html("<tr><td colspan='5' style='text-align:center;''><span class=\"loader-inner ball-pulse\"><div></div><div></div><div></div></span></td></tr>");
				$('#FTPactionButton').attr("disabled", "true");
			
				$('#ftprows').load('manageftp.php?server=<?php echo $port;?>&action=generateFTP', function(){
					$('#FTPactionButton').removeAttr("disabled");
					getFTPAccounts();
				});
			}

			$('#ftprows').load('manageftp.php?server=<?php echo $port;?>&action=fetchFTP');
		</script>
	<?php
} // end of sign in detection

?>