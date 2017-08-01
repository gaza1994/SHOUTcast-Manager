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

include("functions.inc.php");
session_start();

if (empty($_SESSION['username'])) {
	$error = "Sorry - There was an error getting this tracks details.";
} else {
	$error = null;
}
if (isset($_GET['trackID']) && is_numeric($_GET['trackID']) && isset($_GET['portbase'])) {
	$trackID = $_GET['trackID'];
	$portbase = $_GET['portbase'];
	$trackInfo = getMP3Info($trackID, $portbase);
	if ($trackInfo == "ERROR") {
		$error = "Sorry - There was an error getting this tracks details.";
	}
} else {
	$error = "Sorry - There was an error getting this tracks details.";
}
?>
<div class="row">
    <?php 
	if (!empty($error)) {
		echo "<h3>Error</h3>";
		echo "$error";
	} else { ?>
	    <div class="col-md-4">
	    <?php if($trackInfo['picture']){ ?>
	        <img class="img-responsive" width="250" height="250" src="include/mp3Info_cover.php?trackID=<?php echo $trackID;?>&portbase=<?php echo $portbase;?>" alt="">
	    <?php } else { ?>
	    	<img class="img-responsive" width="250" height="250" src="include/getid3/no_cover_art.jpg" alt="">
	    <?php } ?>
	    </div>

	    <div class="col-md-8">
		        <h4>Artist</h4>
		        <p><?php echo $trackInfo['artist']; ?></p>

		        <hr />

		        <h4>Song Name</h4>
		        <p><?php echo $trackInfo['song']; ?></p>

		        <hr />

		        <h4>Album</h4>
		        <p><?php echo $trackInfo['album']; ?></p>

		        <hr />

		        <h4>Genre</h4>
		        <p><?php echo $trackInfo['genre']; ?></p>

		        <hr />

		        <h4>Year</h4>
		        <p><?php echo $trackInfo['year']; ?></p>
	    </div>
	    <script>
	    $('#fileInfoTitle').html("<?php echo $trackInfo['artist']; ?> - <?php echo $trackInfo['song']; ?>");
	    </script>
	<?php } ?>
</div>