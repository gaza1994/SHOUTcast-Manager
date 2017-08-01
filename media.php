<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Media Manager
* @website: http://scottishbordersdesign.co.uk/
*/
/* DONT CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
/* -- DONT CACHE -- */
include('header.php');
$db = dbConnect();
$config = settings();
$myid = getuid($_SESSION['username']);
$noserver = false;
$myservers = getmyserversMulti_byid($myid);
?>
                    <h1>
                        Media Manager
                        <small>Sort MP3 Files</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active">Media Manager</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">
<?php
if (count($myservers) == 1) {
	$myserver = $myservers[0];
	$port = $myserver['PortBase'];
	$noserver = false;
} else {
	if (isset($_GET['server'])) {
		$port = $_GET['server'];
		$myserver = getserverbyportbase($port);
		$noserver = false;
	}
?>
<div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Select Server to Manage</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form role="form" action="media.php" method="GET">
      <div class="box-body">
        <div class="form-group">
			<label for="server">Manage Server</label>
			<select class="form-control" name="server" id="server" onChange="this.form.submit();">
			<option value="null">Select Server</option>
				<?php 
					foreach ($myservers as $key => $server) {
						if (isset($port) && $port == $server['PortBase']) {
							echo "<option value='{$server['PortBase']}' selected>{$server['servername']} - {$server['PortBase']}</option>";
						} else {
							echo "<option value='{$server['PortBase']}'>{$server['servername']} - {$server['PortBase']}</option>";
						}
					}
				?>
			</select>
<?php
$ownerID = getuserdetails($_SESSION['username']);
if (isset($myserver)) {
	if ($myserver['owner'] != $ownerID['user_id'] && !empty($_GET['server']) ) {
		$noserver = true;
		echo ("<br /><div class=\"alert alert-danger alert-dismissible\">
	                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
	                <h4><i class=\"icon fa fa-ban\"></i> No Server!</h4>
	                Incorrect port for server.
	              </div>");
	}
}
	if ( empty($port) && !isset($_GET['server']) ) {
		$noserver = true;
		echo "<br /><div class=\"alert alert-danger alert-dismissible\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
                <h4><i class=\"icon fa fa-ban\"></i> No Server!</h4>
                No server has been selected.
              </div>";
	}
?>
        </div>
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
<?php
}
	///////////////////////////////////////////////////////////////
        /*
	If the user has submitted files to be added to a playlist process them
	1) Get the max ID on the currect playlist
	2) Insert files above that max position, loop over query until completed
        */
        ////////////////////////////////////////////////////////////////
	if(isset($_POST['mediaupdate']) && !empty($_POST['files'])){
	$pid = $_POST['playlists'];
	$files = $_POST['files'];
	$i = 0;
		foreach($files as $media){
			$p = $db->rawQueryOne('SELECT MAX(position) FROM playlist_content WHERE pid="'.$pid.'"');
			$p = index_array($p);
			if($p[0] == ""){
			$pos = "1";
			}else{
			$pos = $p[0] + 1;
			}
			if ($media == '0') {
				// Skip
			} else {
				$db->insert('playlist_content', array ('fid' => $media, 'pid' => $pid, 'position' => $pos, 'port' => $port));
			}
			$i++;
		}
	// Rebuild the playlist
	rebuildPlaylist($pid, $port);
	echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Updated!</b> Files Added.</div>";
	} else {
		if(isset($_POST['mediaupdate']) && empty($_POST['files'])){
			?>
            <div class="alert alert-danger alert-dismissable">
                <i class="fa fa-ban"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b>Error!</b> You need to select atleast one file to add to the playlist.
            </div>
			<?php
		}
	}
?>
</p>
<?php 	
if(isset($_GET['reload'])){
	reloadmedia($config['media_path'].$myserver['PortBase'].'/', $myserver['PortBase']);
	echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Updated!</b> Media list reloaded.</div>";
	}
        if(isset($_GET['sortfield'])){
        $sortfield = $_GET['sortfield'];
        } else {
        $sortfield = 'Artist';
        }
	if(isset($_GET['page'])){
        $letter = $_GET['page'];
        }else{
        $letter = '%';
        }
        if (isset($myserver['PortBase'])) {
        	$port = $myserver['PortBase'];
        } else {
        	$port = '0000';
        }
	?>
  <!-- Modal -->
  <div class="modal fade" id="playlistModal" tabindex="-1" role="dialog" aria-labelledby="playlistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Manage Playlist for port <?php echo $port;?></h4>
        </div>
        <div class="modal-body" id="playlistcontentmodal">
			<span class="loader-inner ball-pulse"><div></div><div></div><div></div></span>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
    <!-- Modal -->

<!-- File Infos Modal -->
<div class="modal modal-lg fade" id="fileInfosModal" tabindex="-1" role="dialog" aria-labelledby="fileInfosModalLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title" id="fileInfoTitle">MP3 File Info</h4>
    </div>
    <div class="modal-body" id="fileInfoContent">
		<span class="loader-inner ball-pulse"><div></div><div></div><div></div></span>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- File Infos Modal -->
	<?php
	if ($config['cpanel_enabled'] == 'yes') {
	?>
	  <div class="modal fade" id="manageFTPModal" tabindex="-1" role="dialog" aria-labelledby="manageFTPModalLabel" aria-hidden="true">
	    <div class="modal-dialog modal-lg" style="width: 70%;">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title">Manage FTP Accounts</h4>
	        </div>
	        <div class="modal-body" id="manageFTPModalContent">
				<span class="loader-inner ball-pulse"><div></div><div></div><div></div></span>
	        </div>
	      </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	  </div><!-- /.modal -->
  <?php } ?>
<div class="box" style="display:<?php if($noserver){echo 'none';}else{echo 'block';}; ?>;">
    <div class="box-header">
		<div class="pull-right box-tools">                                    
		    <form method="get" enctype="multipart/form-data">
		    	<input type="hidden" name="server" value="<?php echo $port;?>">
		    	<span id="tableLoader" style="display:none"><img src="<?php echo $config['web_addr'].'/'.$config['template_dir'].'/'.$config['theme'].'/imgs/loading.gif';?>" alt="Loading Media List ..."></span>
		    	<button name="reload" type="submit" class="btn btn-primary btn-sm pull-right" data-widget="collapse" value="Reload Media" data-toggle="tooltip" title="Reload Media" style="margin-right: 5px;" data-original-title="Collapse"><i class="fa fa-refresh"> Reload Media</i></button>
		    	<?php if ($config['cpanel_enabled'] == 'yes') { ?>
				<button id="manageftp" onClick="manageftpPopup('<?php echo $port;?>'); return false;" name="manageftp" type="button" class="btn btn-primary btn-sm pull-right" title="Manage FTP Accounts" style="margin-right: 5px;"><i class="fa fa-exchange"> Manage FTP</i></button>
				<?php } ?>
		    	<button type="button" onclick="loadPlaylist('<?php echo $port;?>');return false;"  class="btn btn-primary btn-sm pull-right" title="Manage Playlist" style="margin-right: 10px;"><i class="fa fa-file"> Manage Playlist</i></button>
			</form>
		</div>
        <h3 class="box-title">Media List</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
    	<form method="post" enctype="multipart/form-data">
        <table class="table table-hover">
            <tbody id="fileListTable">
            	<tr>
            	<th>&nbsp;</th>
                <th><strong><a href="media.php?page=<?php echo $letter; ?>&sortfield=Song">Song</a></strong></th>
                <th><strong><a href="media.php?page=<? echo $letter; ?>&sortfield=Artist">Artist</a></strong></th>
                <th>Actions</th>
                <th>
					<?if(isset($_GET['check'])){?>
					<?php if ($config['flashplayer'] == "on") { ?><strong>Play</strong><?php } ?>
					<?}else{?>
		        		Add(<a href="media.php?server=<?php echo $port;?>&page=<?php echo $letter;?>&check=all">Select all</a>)
					<?}?>
                </th>
            </tr>
		<tr>
	<?php 	///////////////////////////////////////////////////////////////
	/*
	Set our default query, and alter the query so that we can sort the files
	as per the users request
	*/
	////////////////////////////////////////////////////////////////
	$i = "0";
	$cols = array('id','files','artist','album','song','genre');
	$db->where("port", $port);
	switch ($sortfield) {
		case 'Artist':
			$db->orderBy("song", 'ASC');
			break;
		case 'Album':
			$db->orderBy("song", 'ASC');
			break;
		case 'Song':
			$db->orderBy("song", 'ASC');
			break;
		default:
			$db->orderBy("song", 'ASC');
			break;
	}
	$query = $db->get("media");
	 if($db->count < 1){
		if ($letter == '%') { echo "<tr><td colspan='6' align='center'>No media files.</td></tr></table>"; }
		else { echo "</table>No files available where Artist begins with `$letter`<br><br>"; }
         } else {
         	foreach ($query as $key => $row) {
         		$row = index_array($row);
			///////////////////////////////////////////////////////////////
		        /*
		        If the flash MP3 player is enabled in our config file display
			the flash player and set config appropriatly.
		        */
		        ////////////////////////////////////////////////////////////////
			if ($config['flashplayer'] == "on") {
			 // Extract mp3 file name from path
			 $mp3path = explode('/',$row[1]);
			 $mp3file = $config['media_url'].$myserver['PortBase'].'/'.rawurlencode($mp3path[sizeof($mp3path) - 1]);
 			 if (file_exists($config['media_path'].$myserver['PortBase'].'/'.$mp3path[sizeof($mp3path) - 1])) {
				$mp3player = "<span id='track_{$row[0]}'><button class='btn btn-success btn-sm' onClick='createPreview({$row[0]}, \"{$mp3file}\");return false;'>Preview File</button></span>";
				$mp3player .= "&nbsp;<span id='track_del_{$row[0]}'><button class='btn btn-danger btn-sm' onClick='deleteFile({$row[0]}, \"".base64_encode($mp3file)."\");return false;'>Delete File</button></span>";
   			 } else {
 			   $mp3player = '<strong>This track does not exist in your media folder.</strong>';
			 }
			 echo "<tr id='file_{$row[0]}'>";
			 echo "<td><button class='btn btn-info btn-sm' onClick='getFileInfo({$row[0]}, {$myserver['PortBase']});return false;' data-toggle=\"tooltip\" title=\"Track Information\"><i class='fa fa-info'></i></button></td>";
			 echo "<td>".$row[2]."</td><td>".$row[3]."</td><td style='width:450px;'>".$mp3player."</td>";
			///////////////////////////////////////////////////////////////
		        /*
		        If the flash player is not enabled do not show it and do not 
			set the extra table column where it would be.
		        */
		        ////////////////////////////////////////////////////////////////
			 }else{
			 $mp3path = explode('/',$row[1]);
			 $mp3file = $config['media_url'].$myserver['PortBase'].'/'.rawurlencode($mp3path[sizeof($mp3path) - 1]);
			 $mp3player = "&nbsp;<span id='track_del_{$row[0]}'><button class='btn btn-danger btn-sm' onClick='deleteFile({$row[0]}, \"".base64_encode($mp3file)."\");return false;'>Delete File</button></span>";
			 echo "<tr id='file_{$row[0]}'>";
			 echo "<td><button class='btn btn-info btn-sm' onClick='getFileInfo({$row[0]}, {$myserver['PortBase']});return false;' data-toggle=\"tooltip\" title=\"Track Information\"><i class='fa fa-info'></i></button></td>";
             echo "<td>".$row[2]."</td><td>".$row[3]."</td><td style='width:450px;'>".$mp3player."</td>";
		}
		///////////////////////////////////////////////////////////////
	        /*
        	Check what our check box settings are and configure the boxes
		to display checked or not checked.
       		*/
	        ////////////////////////////////////////////////////////////////
		if (isset($_GET['check'])){
			?>
<?php
		echo "<td><div class=\"form-group\"><input type=\"checkbox\" name=\"files[]\" value=\"$row[0]\" checked></div></td>";
	 } else {
		echo "<td><div class=\"form-group\"><input type=\"checkbox\" name=\"files[]\" value=\"$row[0]\"></div></td>";
	 }
	}
	echo "</tbody></table>";
	}
	///////////////////////////////////////////////////////////////
        /*
        Drop down list to select what playlist to add the fiels to.
        */
        ////////////////////////////////////////////////////////////////
	?>
	<table border="0" width="100%"><tr><td align="right" style="padding-right: 30px;padding-top: 30px;"><?php 	echo "Add to playlist:&nbsp;";
	$db->orderBy("name", "ASC");
	$cols = array('id','name');
	$db->where("name", $port);
	$rows = $db->get("playlist", null, $cols);
	foreach ($rows as $row) {
		$row = index_array($row);
		echo "<input name=\"playlists\" type=\"hidden\" value=\"".$row[0]."\"><span style=\"width: 15%;display: inline;margin-right: 10px;\">".$row[1]."</span>";
		}
	echo "";
	echo "&nbsp;<input class=\"btn btn-primary\" type=\"submit\" name=\"mediaupdate\" value=\"Add\">";
	echo "</td></tr></table><br><br>";
?>
                                    </tbody>
                                    </table>
                                    </form>
                                </div><!-- /.box-body -->
                            </div> 
      </div><!-- /.row -->
    </div>
</dvv>
<div class="row">
	<div class="col-xs-12">
        <div class="" style="display:<?php if($noserver){echo 'none';}else{echo 'block';}; ?>;">
            <div class="" id="" >

<div class="box box-solid">
    <div class="box-header">
        <h3 class="box-title">Upload MP3</h3>
    </div><!-- /.box-header -->
    <div class="box-body" style="text-align:center;">
<?php 
$usedSpace = autoDJUsedSpaceNew($port);
$usedSpaceGB = round(autoDJUsedSpaceNew($port) / 1024, 2);
$maxSpaceGB = ($myserver['autodj_max_space'] / 1024);
if ( $usedSpace >= $myserver['autodj_max_space']){
	echo alertError("No Space", "You do not have any space left in your SHOUTcast plan.<br />Your plan allows for {$maxSpaceGB}GB, you are currently using {$usedSpaceGB}GB.");
} else {

$myProfile = getUserDetails($_SESSION['username']);
$myuid = $myProfile['user_id'];
if ($myuid == '96') {
	echo "<center><h2><span style='color:#f00;'>Disabled</span></h2><p>As some users cant use this demo responsibly certain parts have now been disabled.  Sorry for any inconvenience.</p></center>";
} else {
?>
<style>
#DropZoneFileUpload { margin-bottom: 3rem; }
.dropzone { border: 2px dashed #0087F7!important; border-radius: 5px!important; background: white!important; }
.dropzone .dz-message { font-weight: 400!important;font-size: 26px;color: #646C7F; }
.dropzone .dz-message .note { font-size: 0.8em!important; font-weight: 200!important; display: block!important; margin-top: 1.4rem!important; }
/* Mimic table appearance */
div.table {
display: table;
}
div.table .file-row {
display: table-row;
}
div.table .file-row > div {
display: table-cell;
vertical-align: top;
border-top: 1px solid #ddd;
padding: 8px;
}
div.table .file-row:nth-child(odd) {
background: #f9f9f9;
}
.modal{
	overflow-y: auto;
}
</style>
<link href="include/plugins/dropzone/dropzone.css" type="text/css" rel="stylesheet" />
<script src="include/plugins/dropzone/dropzone.js"></script>
<div class="alert alert-success alert-dismissable" id="uploadSuccessMessage" style="display:none;">
	<i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<b>Finished!</b> All files have been uploaded.
</div>
<?php
    if (isset($myserver['PortBase'])) {
		$cols = array('id', 'name');
		$db->where("name", $port);
		$db->orderBy("name", "ASC");
		$rows = $db->get("playlist", null, $cols);
		foreach ($rows as $row) {
			$row = index_array($row);
			$theport = $row[1];
		}
    } else {
    	$theport = '0000';
    }
?>
			<form action="include/multi-upload.php?portbase=<?php echo $theport;?>" class="dropzone" id="DropZoneFileUpload">
				<input type="hidden" name="portbase" id="portbase" value="<?php echo $theport;?>">
<div id="actions" class="row">
      <div class="col-lg-7">
      </div>
      <div class="col-lg-5">
        <!-- The global file processing state -->
        <span class="fileupload-process">
          <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="opacity: 0;">
            <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress=""></div>
          </div>
        </span>
      </div>
    </div>
<div class="table table-striped" class="files" id="previews">
  <div id="template" class="file-row">
    <div>
        <p class="name" data-dz-name></p>
        <strong class="error text-danger" data-dz-errormessage></strong>
    </div>
    <div>
        <p class="size" data-dz-size></p>
        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
        </div>
    </div>
    <div>
      <button data-dz-remove class="btn btn-danger delete">
          <i class="glyphicon glyphicon-trash"></i>
          <span>Cancel</span>
      </button>
    </div>
  </div>
</div>
<?php
}
?>
    </div><!-- /.box-body -->

</div> 
            </div>
        </div>
<script>
Dropzone.autoDiscover = false;
var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);
var myDropzone = new Dropzone(document.getElementById("DropZoneFileUpload"), {
  url: "include/multi-upload.php?portbase=<?php echo $theport;?>",
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 800, // MB
  parallelUploads: 3,
  previewTemplate: previewTemplate,
  dictDefaultMessage: 'Drop MP3 files here (or click to select)',
  previewsContainer: "#previews",
  accept: function(file, done) {
    if (file.type == "audio/mp3") {
       done();
    } else { 
    	done("MP3 Files Only."); 
    }
  },
  init: function () {
    this.on("complete", function (file) {
      if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
        completeUpload();
      }
    });
  }
});
// Update the total progress bar
myDropzone.on("totaluploadprogress", function(progress) {
  document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
});
myDropzone.on("sending", function(file) {
  // Show the total progress bar when upload starts
  document.querySelector("#total-progress").style.opacity = "1";
});
myDropzone.on("queuecomplete", function(progress) {
  document.querySelector("#total-progress").style.opacity = "0";
});
myDropzone.on("complete", function(file) {
  $('#tableLoader').fadeIn();
  jQuery('#fileListTable').load("include/media.ajax.php?server=<?php echo $theport;?>", function(){
  	myDropzone.removeFile(file);
  	$('#tableLoader').fadeOut();
  	/* update page height once file has gone */
	$(".wrapper, aside, html, body ").css({
	    "min-height": '0'
	});
  });
});
function completeUpload(){
	$('#uploadSuccessMessage').slideDown().delay(5000).queue(function(){
		$(".wrapper, aside, html, body ").css({
		    "min-height": '0'
		});
	});
}
</script>
	</div>
</div>
</section>
<?php if ($config['cpanel_enabled'] == 'yes') { ?>
<script>
	function manageftpPopup(serverport){
		$("#manageFTPModalContent").html("<span class=\"loader-inner ball-pulse\"><div></div><div></div><div></div></span>");
		$('#manageFTPModal').modal('toggle');
		$("#manageFTPModalContent").load("manageftp.php?server="+serverport);
	}
</script>
<?php } ?>
<script>
	function loadPlaylist(playlistID){
		$(".modal-body").html("<span class=\"loader-inner ball-pulse\"><div></div><div></div><div></div></span>");
		$('#playlistModal').modal('toggle');
		$(".modal-body").load("playlists.php?server="+playlistID);
	}
	function createPreview(id, url){
		$('#track_'+id).fadeOut(500, function(){
			var buildAudio = "<audio controls style='float:left;'><source src='"+url+"' type='audio/mpeg'>Your browser does not support the audio tag.</audio>";
			$('#track_'+id).html("<button class='btn btn-danger btn-sm' onClick='destroyPreview("+id+", \""+url+"\");return false;' style='margin-left: 10px;'>&times;</button>"+buildAudio);
		});
		$('#track_'+id).fadeIn(500);
	}
	function destroyPreview(id, url){
		$('#track_'+id).fadeOut(500, function(){
			$('#track_'+id).html("<button class='btn btn-success btn-sm' onClick='createPreview("+id+", \""+url+"\");return false;'>Preview File</button>");
		});
		$('#track_'+id).fadeIn(500);
	}
	function deleteFile(id, url){
		$('#tableLoader').fadeIn();
		var urlFolder = url;
		$.post( 
		  "include/deleteAutoDJ.php",
		  { trackURL: urlFolder },
		  function(data) {
		     if (data == 1) {
		     	$('#file_'+id).fadeOut('slow');
		     	$('#fileListTable').load("include/media.ajax.php?server=<?php echo $theport;?>");
		     	$('#tableLoader').fadeOut();
		     } else {
		     	alert("Delete Failed, please try again.");
		     }
		  }
		);
	}

	function getFileInfo(trackID, portbase){
		$('#fileInfosModal').modal('toggle');
		$("#fileInfoContent").load("include/mp3Info.php?trackID="+trackID+"&portbase="+portbase);
	}
</script>
<?php
}
?>
<?php include('footer.php');?>