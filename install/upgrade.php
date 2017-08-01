<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Upgrade Script
* @website: http://scottishbordersdesign.co.uk/
*/
require('../include/functions.inc.php');
$db = dbconnect();
$version = $db->getOne("sbd");
$version = $version['version']; //0.3.4
$version_compare = str_replace(".", "", $version); // make a comparison version
if ($version_compare >= '100') {
  $noRun = true;
  $message = "You are running version ".$version . " which is the latest";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html class="bg-black">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>SHOUTcast Client - Upgrade</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="../templates/default/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="../templates/default/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../templates/default/css/AdminLTE.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <style>
        .check_good, .inputSuccess {
          color: #4AA02C;
        }
        .check_miss {
          color: #A0A017;
        }
        .check_bad, .inputDanger {
          color: #800517;
        }
        </style>
<link rel="shortcut icon" type="image/x-icon" href="imgs/favicon.ico">
</head>
    <body class="bg-black">
<link rel="shortcut icon" type="image/x-icon" href="../imgs/favicon.ico">


        <div class="form-box" id="login-box">
            <div class="header">Upgrade</div>
                <div class="body bg-gray">
<!-- HEADER TABLE -->
<strong><big><a href="<?php echo $_SERVER['PHP_SELF']; ?>">SHOUTcast Client - Upgrade</a></big></strong><br>

<?php 
if (isset($_POST['doUpgrade']) && $_POST['doUpgrade'] == "y") {
  // do zee upgrade
  echo '<div class="form-group has-success">
    <label class="control-label" for="inputSuccess">
      <i class="fa fa-check"></i> 
      Connect to Database
    </label>
    <input readonly type="text" class="form-control" class="inputSuccess" value="OK"></div>';


/* Update Config Groups */
 $data1 = array(
    'name' => 'Template',
    'title' => 'Template Options'
  );
 $insert1 = $db->insert("config_groups", $data1);

 $data2 = array(
    'name' => 'cPanel',
    'title' => 'cPanel Details'
  );
 $insert2 = $db->insert("config_groups", $data2);

 if ($insert1 && $insert2) {
  echo '<div class="form-group has-success">
    <label class="control-label" for="inputSuccess">
      <i class="fa fa-check"></i> 
      Updating Configuration Groups
    </label>
    <input readonly type="text" class="form-control" class="inputSuccess" value="Updated "></div>';
 } else {
  echo '<div class="form-group has-error">
    <label class="control-label" for="inputDanger">
      <i class="fa fa-times"></i> 
      Updating Configuration Groups
    </label>
    <input readonly type="text" class="form-control" class="inputDanger" value="'.$db->getLastError().'"></div>';
 }
 /* END Update Config Groups */

 /* Update Settings */
 $data1 = array(
    'setting' => 'lang',
    'value' => 'english',
    'title' => 'Site Language',
    'groupid' => '1'
  );
 $insert1 = $db->insert("config", $data1);

 $data2 = array(
    'setting' => 'theme',
    'value' => 'default',
    'title' => 'Site Theme',
    'groupid' => '1'
  );
 $insert2 = $db->insert("config", $data2); 

 $insert3 = $db->rawQueryOne("INSERT INTO `config` VALUES (20, 'debugging', 'no', 'SMARTY Debugging',6), (21, 'caching', 'no', 'SMARTY Caching',6), (22, 'template_dir', 'templates', 'SMARTY Template Directory',6), (23,'error_reporting', 'no', 'Show SMARTY Errors',6),(26, 'ftp_host','localhost', 'Hostname for FTP', 3),(27, 'ftp_port','21', 'Port for FTP (normally 21)', 3),(28, 'cpanel_hostname','localhost', 'Hostname for cPanel', 7),(29, 'cpanel_port','2083', 'Port number for cPanel', 7),(30, 'cpanel_username','username', 'Username for cPanel (A user with sufficient permissions)', 7),(31, 'cpanel_password','****', 'Password for cPanel User', 7),(32, 'cpanel_homedir','/home/username/', 'Home Directory of the current user (include trailing /)', 7),(33, 'cpanel_enabled','no', 'Enable cPanel Functionality', 7);");

 $insert4 = $db->rawQueryOne("INSERT INTO `config_sets` VALUES (11,20,'yes','Yes'), (12,20,'no','No'), (13,21,'yes','Yes'), (14,21,'no','No'),          (15,23,'yes','Yes'), (16,23,'no','No'), (17,33,'no','No'), (18,33,'yes','Yes');");

 $insert5 = $db->rawQueryOne("TRUNCATE TABLE  `genres` ;");
 $insert6 = $db->rawQueryOne("ALTER TABLE  `genres` CHANGE  `genreID`  `genreID` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ;");

 $createFTPTable = $db->rawQueryOne("CREATE TABLE IF NOT EXISTS `ftp` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(2555) NOT NULL,
  `password` varchar(255) NOT NULL,
  `PortBase` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;");

 if ($insert1 && $insert2) {
  echo '<div class="form-group has-success">
    <label class="control-label" for="inputSuccess">
      <i class="fa fa-check"></i> 
      Updating Settings
    </label>
    <input readonly type="text" class="form-control" class="inputSuccess" value="Updated "></div>';
 } else {
  echo '<div class="form-group has-error">
    <label class="control-label" for="inputDanger">
      <i class="fa fa-times"></i> 
      Updating Settings
    </label>
    <input readonly type="text" class="form-control" class="inputDanger" value="'.$db->getLastError().'"></div>';
 }
 /* END Update Settings */

 /* Update Events Table */
 $alter = $db->rawQueryOne("ALTER TABLE  `events` ADD  `ip` VARCHAR( 50 ) NOT NULL ;");
 if ($alter) {
  echo '<div class="form-group has-success">
    <label class="control-label" for="inputSuccess">
      <i class="fa fa-check"></i> 
      Update Events
    </label>
    <input readonly type="text" class="form-control" class="inputSuccess" value="Updated "></div>';
 }
 /* END Update Settings */

 /* Update Servers Table */
 $alter = $db->rawQueryOne("ALTER TABLE  `servers` ADD COLUMN  `message_notification` longtext NOT NULL, ADD COLUMN `autodj_crossfadeMode` varchar(30) NOT NULL, ADD COLUMN `autodj_crossfadeseconds` int(10) NOT NULL;");
 if ($alter) {
  echo '<div class="form-group has-success">
    <label class="control-label" for="inputSuccess">
      <i class="fa fa-check"></i> 
      Update Servers
    </label>
    <input readonly type="text" class="form-control" class="inputSuccess" value="Updated "></div>';
 }
 /* END Update Servers */

 /* Update Members Table */
 $alter = $db->rawQueryOne("ALTER TABLE  `members` ADD COLUMN  `2stepauth` varchar(50) DEFAULT NULL;");
 if ($alter) {
  echo '<div class="form-group has-success">
    <label class="control-label" for="inputSuccess">
      <i class="fa fa-check"></i> 
      Update Members
    </label>
    <input readonly type="text" class="form-control" class="inputSuccess" value="Updated "></div>';
 }
 /* END Update Members */

  /* Removing Obsolete Tables */
 $remove1 = $db->rawQueryOne("DROP TABLE IF EXISTS  `requests`;");
 $remove2 = $db->rawQueryOne("DROP TABLE IF EXISTS  `schedule`;");
 $remove3 = $db->rawQueryOne("DROP TABLE IF EXISTS  `sets`;");
 if (true) {
  echo '<div class="form-group has-success">
    <label class="control-label" for="inputSuccess">
      <i class="fa fa-check"></i> 
      Removing Obsolete Tables
    </label>
    <input readonly type="text" class="form-control" class="inputSuccess" value="Updated "></div>';
 } else {
  echo '<div class="form-group has-error">
    <label class="control-label" for="inputDanger">
      <i class="fa fa-times"></i> 
      Removing Obsolete Tables
    </label>
    <input readonly type="text" class="form-control" class="inputDanger" value="'.$db->getLastError().'"></div>';
 }
 /* END Removing Obsolete Tables */


 /* Update Version Number */
 $db->where("ver_id", "1");
 $vNumber = $db->update("sbd", array("version"=>"1.0.0"));
 if ($vNumber) {
  echo '<div class="form-group has-success">
    <label class="control-label" for="inputSuccess">
      <i class="fa fa-check"></i> 
      Update Version Number
    </label>
    <input readonly type="text" class="form-control" class="inputSuccess" value="Updated "></div>';
 } else {
  echo '<div class="form-group has-error">
    <label class="control-label" for="inputDanger">
      <i class="fa fa-times"></i> 
      Update Version Number
    </label>
    <input readonly type="text" class="form-control" class="inputDanger" value="'.$db->getLastError().'"></div>';
 }
 /* END Update Version Number */

 echo "<p><center><strong>Finished!</strong><br />Remember to remove the install directory</center></p>";

 echo '<center><p><input type="submit" name="submit" onClick="finish();return false;" value="Finish &gt;&gt;"></p></center>';
?>
<script>
  function finish(){
    window.location="../";
  }
</script>
<?php
} else {
  if (isset($noRun)) {
    echo $message;
  } else { ?>
    <form action="" method="post">
      <center>
        <p>Welcome to the SHOUTcast manager upgrade.</p>
        <p>We have detected you are running version <strong><?php echo $version;?></strong>.</p>
        <p>This upgrade will put you on version <strong>1.0.0</strong></p>
        <p><strong><u>Remember take a backup of your database before proceeding.</u></strong></p>
        <p><br />Click upgrade below to begin the process.</p>
      </center>
      <input type="hidden" name="doUpgrade" value="y">
      <center><p><input type="submit" name="submit" value="Upgrade &gt;&gt;"></p></center>
    </form>
  <?php }?>
<?php } ?>



<div align="center">
<p>&nbsp;</p>
</div> 
        <!-- jQuery 2.0.2 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../templates/default/js/bootstrap.min.js" type="text/javascript"></script>

    </body>
</html>