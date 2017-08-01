<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Playlists
 * @website: http://scottishbordersdesign.co.uk/
*/
session_start();
require('include/functions.inc.php');
if(!isset($_SESSION['username'])){
	echo "<center>Please <a href=\"login.php\">LOGIN</a></center>";
} else {

$db = dbConnect();
$config = settings();
$uid = getuid($_SESSION['username']);
$myid = getuid($_SESSION['username']);
$myserver = getmyservers_byid($myid);
$port = $_GET['server'];
/////////////////////////////////////////////////////////////
//
// If we receive a request to create a new playlist
// execute the following:
//
/////////////////////////////////////////////////////////////
if(isset($_POST['new'])){
		$name = $_POST['plname'];
		$db->where("uid", $uid);
		$db->where("name", $name);
		$checkdups = $db->get("playlist");
		if ($db->count >= 1){
			echo 'The playlist name already exists for your user!<br/>';
		} else {
			$db->insert('playlist', array( 'name' => $name, 'uid' => $uid, 'file' => $config['sbd_path']."/playlists/".$name.".lst"));
			echo "Playlist Added!";
		}
}
/////////////////////////////////////////////////////////////
//
// If we receive a request to delete a playlist
// execute the following
//
/////////////////////////////////////////////////////////////
if(isset($_POST['delete'])){
			$pid = $_POST['pid'];
			$db->where('id', $pid);
			$db->where('uid', $uid);
			$db->delete('playlist');
			deletePlaylist($pid,$uid);
			echo "Playlist Deleted";
			}
/////////////////////////////////////////////////////////////
//
// If we receive a request to delete a file from the playlist
// execute the following
//
/////////////////////////////////////////////////////////////
if(isset($_POST['files'])){
			$pid = $_POST['pid'];
			$db->orderBy("uid", "ASC");
			$db->where("id", $uid);
			$checkuid = $db->getOne("playlist");
{
			$files = $_POST['files'];
			foreach($files as $delfile){
					$db->where('fid', $delfile);
					$db->where('pid', $pid);
					$db->delete('playlist_content');
				}
			echo "<br /><div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Updated!</b> Playlist has been updated.</div>";
			$edit = $pid;
			rebuildPlaylist($pid, $port);
}
			}
//////////////////////////////////////////////////////////////
//
// If we have a request to update the files position we should
//
///////////////////////////////////////////////////////////////
if(isset($_POST['position'])) {
  $pid = $_POST['pid'];
  $pos = $_POST['position'];
  $fid = $_POST['fid'];
  $pcid = $_POST['pcid'];
$db->orderBy("uid", "ASC");
$db->where("id", $pid);
$checkuid = $db->getOne("playlist");
{
	reset($pos);
	reset($fid);
	reset($pcid);
	while(($val1 = each($pos)) && ($val2 = each($fid)) && ($val3 = each($pcid))) {
	  $update = array('position' => $val1['value']);
	  $db->where('position', $val1['value']);
	  $cond = array('fid' => $val2['value'], 'pid' => $pid, 'id' => $val3['value']);
	  $db->update('playlist_content', $update);
	  rebuildPlaylist($pid, $port);
	}
  }
}
/////////////////////////////////////////////////////////////
//
// Else display the normal form
//
/////////////////////////////////////////////////////////////
			$cols = array('id','name');
			$db->orderBy('name','ASC');
			$db->where("name", $port);
			$rows = $db->get("playlist", null, $cols);
			foreach($rows as $row) {
				$row = index_array($row);
			 $pid = $row[0];
			}
/////////////////////////////////////////////////////////////
//
// If we receive a request to edit a playlist
// execute the following
//
/////////////////////////////////////////////////////////////
				   $i = "0";
					if (isset($_GET['page'])) {
					 $page  = $_GET['page']; 
					} else {
						$page=0; };
					if(isset($pid)){
				   $pid = $pid;
				   }
					if(isset($pid)){
					$pid = $pid;
					}
					if(isset($_GET['action'])){
					 $pid = $pid;
					 $fid = $_GET['fid'];
					 $direction = $_GET['direction'];
					 $pos = $_GET['pos'];
					 $pcid = $_GET['pcid'];
					if($direction == "up"){
					 $newpos = $pos - 1;
					}else{
					 $newpos = $pos + 1;
					} 
					$db->where('pid', $pid);
					$db->where('fid', $fid);
					$db->where('id', $pcid);
					$db->update('playlist_content', array('position' => $newpos));
					$db->rawQuery('UPDATE playlist_content SET position="'.$pos.'" WHERE pid="'.$pid.'" AND fid!="'.$fid.'" AND id!="'.$pcid.'" AND position="'.$newpos.'"');
							}
						$start_from = ($page) * 50;
						?>
<style>
.positionbox{
	background: none;
    border: none;
    text-align: center;
}

</style>
<div class="box box-primary">
	<form id="updatePlaylistForm" method="post" enctype="multipart/form-data">
		<div class="box-body">
		<p>Tick the boxes to select the tracks you would like to remove.</p>
			<ul class="todo-list ui-sortable">
				<?php 						  
					$query = $db->rawQuery('SELECT playlist_content.fid,media.artist,media.song,playlist_content.position,playlist_content.id from playlist_content,media 	
					WHERE playlist_content.pid="'.$pid.'" AND playlist_content.fid=media.id ORDER BY playlist_content.position,media.song ASC LIMIT '.$start_from.', 50');
					foreach ($query as $key => $row) {
						$row = index_array($row);
					echo "<li>";
					echo "<td valign=\"middle\"><input type=\"hidden\" name=\"fid[]\" value=\"".$row[0]."\">
					<a style=\"padding: 2px;\" class=\"updateArrows\" href=\"playlists.php?action=updatepos&fid=".$row[0]."&server=".$_GET['server']."&direction=up&pos=".$row[3]."&pcid=".$row[4]."\"><i class=\"fa fa-arrow-up fa-1\"></i></a><a style=\"padding: 2px;\" class=\"updateArrows\" href=\"playlists.php?action=updatepos&fid=".$row[0]."&server=".$_GET['server']."&direction=down&pos=".$row[3]."&pcid=".$row[4]."\"><i class=\"fa fa-arrow-down fa-1\"></i></a>
					<input class='positionbox' type=\"text\" size=\"5\" value=\"".$row[3]."\" name=\"position[]\">	
					<input type=\"hidden\" name=\"pcid[]\" value=\"".$row[4]."\"></td>";
					echo "<span style=\"width: 70%;\" class=\"text\">".$row[1]."-".$row[2]."</span>";
					echo "<input data-toggle=\"tooltip\" title=\"Remove From Playlist\" class=\"pull-right\" type=\"checkbox\" name=\"files[]\" value=\"".$row[0]."\">";
					echo "</li>";
					}
				?>	
			</ul>
		</div><!-- /.box-body -->
		<div class="box-footer clearfix no-border">
			<input type="hidden" name="pid" value="<?php echo $pid;?>">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button id="actionButton" type="submit" name="update" class="btn btn-default pull-right">Update Playlist</button>
		</div>
	</form>
</div>

<script>
	var loadingHTML = '<span class="loader-inner ball-pulse"><div></div><div></div><div></div></span>';

	$(function() {
		$(".updateArrows").on("click", function(event) {
			event.preventDefault();
			$('#actionButton').html( loadingHTML );
			$(".modal-body").load( $(this).attr("href"), function(){
				$('#actionButton').html( "Update Playlist" );
			} )
		});

		$("#updatePlaylistForm").on("submit", function(event) {
			event.preventDefault();
			$('#actionButton').html( loadingHTML );

			$.ajax({
				url: "playlists.php?server=<?php echo $port;?>",
				type: "post",
				data: $(this).serialize(),
				dataType: "html",
				success: function(d) {
					$(".modal-body").html(d);
					$('#actionButton').html( "Update Playlist" );
				},
				error: function(d) {
					$(".modal-body").html(d);
					$('#actionButton').html( "Update Playlist" );
				}
			});
		});
	});
</script>
<?php
}
?>