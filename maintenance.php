<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Admin Maintenance Page
 * @website: http://scottishbordersdesign.co.uk/
 */
session_start();
require('header.php');

if (isset($_POST['action'])) {
	if ($_POST['id'] == 0) {
		switch ($_POST['action']) {
			case 'startall':
				startall();
				break;

			case 'stopall':
				stopall();
				break;

			case 'rebootall':
				restartall();
				break;

			case 'rebuildallautodj':
				$db = dbconnect();
				$allservers = $db->get("servers");
				foreach ($allservers as $key => $server) {
					rebuildautodj($server['id']);
				}
				break;

			case 'rebuildall':
				$db = dbconnect();
				$allservers = $db->get("servers");
				foreach ($allservers as $key => $server) {
					rebuildconf($server['id']);
				}
				break;

			case 'checkallmp3s':
				$files = $db->get("media");
				foreach ($files as $key => $file) {
					$mp3 = $file['files'];
					$db->where('id', $file['id']);
					$remove = $db->delete('media');
					if ($remove) {
						$serverid=getport_by_id($file['port']);
						rebuildautodj($serverid);
					}
				}
				break;
		}
	} else {
		switch ($_POST['action']) {
			case 'start':
				startstream($_POST['id']);
				break;

			case 'stopall':
				stopstream($_POST['id']);
				break;

			case 'rebootall':
				$port = getport_by_id($_POST['id']);
				restart_server($port, $_POST['id']);
				break;

			case 'rebuildautodj':
				$db = dbconnect();
				$db->where("id", $_POST['id']);
				$allservers = $db->get("servers");
				foreach ($allservers as $key => $server) {
					rebuildautodj($server['id']);
				}
				break;

			case 'rebuild':
				$db = dbconnect();
				$db->where("id", $_POST['id']);
				$allservers = $db->get("servers");
				foreach ($allservers as $key => $server) {
					rebuildconf($server['id']);
				}
				break;

			case 'checkmp3s':
				$port = getServerById($_POST['id']);
				$db->where("port", $port);
				$files = $db->get("media");
				foreach ($files as $key => $file) {
					$mp3 = $file['files'];
					$db->where('id', $file['id']);
					$remove = $db->delete('media');
					if ($remove) {
						$serverid=getport_by_id($file['port']);
						rebuildautodj($serverid);
					}
				}
				break;
		}
	}

	exit();
}
?>
	<h1>
		Server Maintenance
		<small>Maintain your SHOUTcast Servers</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="home.php"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
		<li class="active"><i class="glyphicon glyphicon-hdd"></i> Server Maintenance</li>
	</ol>
</section>

<style>
	label{
		display:block;
	}
</style>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-md-6">
				<div class="box box-primary">
					<div class="box-header">
						<h3 class="box-title">Server Maintenance (Individual Server)</h3>
					</div><!-- /.box-header -->

					<div class="box-body" id="singleservercommands">
						<div class="form-group">
							<label>Select Server</label>
							<select class="form-control" name="server" id="server">
								<option value="0">Select Server</option>
								<?php getAllServers(); ?>
							</select>
						</div>  

						<div id="successLoader">
							&nbsp;
						</div>

						<div class="form-group">
							<label>Start</label>
							<p><small>Start the selected server.</small></p>
							<button id="start">Start Server</button>
						</div>

						<div class="form-group">
							<label>Stop</label>
							<p><small>Stop the selected server.</small></p>
							<button id="stop">Stop Server</button>
						</div>

						<div class="form-group">
							<label>Reboot</label>
							<p><small>Reboot the selected server.</small></p>
							<button id="reboot">Reboot Server</button>
						</div>

						<div class="form-group">
							<label>Rebuild</label>
							<p><small>Rebuild the configuration files for the selected server.</small></p>
							<button id="rebuild">Rebuild Server</button>
						</div>

						<div class="form-group">
							<label>Rebuild AutoDJs</label>
							<p><small>Rebuild the AutoDJ configuration files and the AutoDJ playlist files for the selected server.</small></p>
							<button id="rebuildautodj">Rebuild AutoDJ</button>
						</div>

						<div class="form-group">
							<label>Check MP3 Files</label>
							<p><small>Check the mp3s files of the selected server.</small></p>
							<button id="checkmp3s">Check MP3 Files</button>
						</div>
					</div>
				</div>
			</div>



			<div class="col-md-6">
				<div class="box box-primary">
					<div class="box-header">
						<h3 class="box-title">Server Maintenance (All Servers)</h3>
					</div><!-- /.box-header -->

					<div class="box-body" id="allServerCommands">
						<div id="successLoader">
							&nbsp;
						</div>

						<div class="form-group">
							<label>Start All</label>
							<p><small>Start all servers.</small></p>
							<button id="startall">Start All Servers</button>
						</div>

						<div class="form-group">
							<label>Stop All</label>
							<p><small>Stop all servers.</small></p>
							<button id="stopall">Stop All Servers</button>
						</div>

						<div class="form-group">
							<label>Reboot All</label>
							<p><small>Reboot all servers.</small></p>
							<button id="rebootall">Reboot All Servers</button>
						</div>

						<div class="form-group">
							<label>Rebuild All</label>
							<p><small>Rebuild the configuration files for all servers.</small></p>
							<button id="rebuildall">Rebuild All Servers</button>
						</div>

						<div class="form-group">
							<label>Rebuild All AutoDJs</label>
							<p><small>Rebuild the AutoDJ configuration files and the AutoDJ playlist files for all servers.</small></p>
							<button id="rebuildallautodj">Rebuild All AutoDJs</button>
						</div>

						<div class="form-group">
							<label>Check All MP3 Files</label>
							<p><small>Check the mp3s files of all servers.</small></p>
							<button id="checkallmp3s">Check All MP3 Files</button>
						</div>
					</div>
				</div>
			</div>


		</div><!-- /.row -->
	</div>
</section>
<?php
require('footer.php');
?>

<script>
	function sendSuccess(title, content){
		$('#successLoader').html("<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>"+title+"</b> " + content + "</div>");
	}

	function sendCall(action, id=0){
		$('#tableLoader').fadeIn();
		$.post( 
		  "maintenance.php",
		  { 
		  	action: action,
		  	id: id
		   },
		  function(data) {
		     if (data) {
		     	sendSuccess("Success", "Action Completed Successfully.");
		     } else {
		     	alert("Action Failed, please try again.");
		     }
		  }
		);
	}

	$('#startall').on("click", function(){
		sendCall('startall');
	});

	$('#stopall').on("click", function(){
		sendCall('stopall');
	});

	$('#rebootall').on("click", function(){
		sendCall('rebootall');
	});

	$('#rebuildall').on("click", function(){
		sendCall('rebuildall');
	});

	$('#rebuildallautodj').on("click", function(){
		sendCall('rebuildallautodj');
	});

	$('#checkallmp3s').on("click", function(){
		sendCall('checkallmp3s');
	});

	/* Single Commands */
	$('#start').on("click", function(){
		if ($('#server').val() != '0') {
			sendCall('start', $('#server').val() );
		} else {
			alert("Please select a server first.");
		}
	});

	$('#stop').on("click", function(){
		if ($('#server').val() != '0') {
			sendCall('stop', $('#server').val() );
		} else {
			alert("Please select a server first.");
		}

	});

	$('#reboot').on("click", function(){
		if ($('#server').val() != '0') {
			sendCall('reboot', $('#server').val() );
		} else {
			alert("Please select a server first.");
		}
	});

	$('#rebuild').on("click", function(){
		if ($('#server').val() != '0') {
			sendCall('rebuild', $('#server').val() );
		} else {
			alert("Please select a server first.");
		}
	});

	$('#rebuildautodj').on("click", function(){
		if ($('#server').val() != '0') {
			sendCall('rebuildautodj', $('#server').val() );
		} else {
			alert("Please select a server first.");
		}
	});

	$('#checkmp3s').on("click", function(){
		if ($('#server').val() != '0') {
			sendCall('checkmp3s', $('#server').val() );
		} else {
			alert("Please select a server first.");
		}
	});
</script>