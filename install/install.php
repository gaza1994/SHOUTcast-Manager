<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Installer
* @website: http://scottishbordersdesign.co.uk/
*/
require('../include/functions.inc.php');
set_error_handler(function() { /* ignore errors */ });
try { //check if we can connect! - If we cant - continue with installer
  $db = dbconnect();
  $version = $db->getOne("sbd");
  if ($version && !$_POST['step']) {
    header("Location: upgrade.php");
    exit;
  }
} catch (Exception $e) {
  // its just a install - ignore
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html class="bg-black">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>SHOUTcast Client - Installer</title>
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
        .check_good {
          color: #4AA02C;
        }
        .check_miss {
          color: #A0A017;
        }
        .check_bad {
          color: #800517;
        }
        </style>
<link rel="shortcut icon" type="image/x-icon" href="imgs/favicon.ico">
</head>
    <body class="bg-black">
<link rel="shortcut icon" type="image/x-icon" href="../imgs/favicon.ico">


        <div class="form-box" id="login-box">
            <div class="header">Installer</div>
                <div class="body bg-gray">
<!-- HEADER TABLE -->
        
        <strong><big><a href="<?php echo $_SERVER['PHP_SELF']; ?>">SHOUTcast Client - Installation</a></big></strong><br>
        <?php 
        echo '<div class="form-group has-success"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Host</label><input readonly type="text" class="form-control" id="inputSuccess" value="'.php_uname("n").' ('.php_uname("s").') (Required: PHP 5.0 or newer)"></div>';

        ?>
<!-- CONTENT TABLE -->

<?php 
/*
Determine installation step
*/
if(!isset($_POST['step'])) {
/*
Step 1: Determine if prerequisites are met
*/
$failed = 0;    // Reset failed flag
$directions = ""; // Empty string with directions
echo "<strong>STEP 1: CHECKING PREREQUISITES...</strong><br>\n";

// PHP version 
if (version_compare(PHP_VERSION, '5.0.0', '<')) {
 $failed = 1;
 $class = "error";
 $directions = "<p>- Upgrade your PHP installation and restart web server</p>\n";
} else {
 $class = "success";
}
echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking PHP version</label><input readonly type="text" class="form-control" id="inputSuccess" value="'.phpversion().' (Required: PHP 5.0 or newer)"></div>';

// PHP safe mode
$safemode = ini_get('safe_mode');
if ($safemode == 1) {
 $failed = 1;
 $class = "error";
 $tmpstr = "On";
 $directions .= "<p>- Edit ".php_ini_loaded_file()." and set <i>safe_mode = Off</i></p>";
} else {
 $class = "success";
 $tmpstr = "Off";
}
echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking PHP safe mode</label><input readonly type="text" class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: Safe mode off)"></div>';



// PHP MySQLi extension
if (!in_array("mysqli", get_loaded_extensions())) {
 $failed = 1;
 $class = "error";
 $tmpstr = "missing";
 $directions .= "<p>- Install the <i>php-mysqli</i> extension and restart web server</p>";
} else {
 $class = "success";
 $tmpstr = "OK";
}
echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking PHP MySQLi</label><input readonly type="text" class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: PHP MySQLi extension)"></div>';

// PHP cURL extension
if (!in_array("curl", get_loaded_extensions())) {
 $failed = 1;
 $class = "error";
 $tmpstr = "missing";
 $directions .= "<p>- Activate the <i>cURL</i> extension and restart web server</p>";
} else {
 $class = "success";
 $tmpstr = "OK";
}
echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking PHP cURL</label><input readonly type="text" class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: PHP cURL extension)"></div>';



// MySQL version 
if ($safemode == 1) {
 echo "<span class=\"check_bad\">Unable to check with PHP safe mode on</span></p>";
} else {
 $mysql = explode(" ", shell_exec("mysqladmin --version \n"));
 $mysql = rtrim($mysql[5], ",");
 debug($mysql);
 if (version_compare($mysql, '4.1', '<')) {
  $failed = 1;
  $class = "error";
  $directions .= "<p> - Install/upgrade MySQL server to version 4.1 or later</p>\n";
 } else {
  $class = "success";
 }
echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking MySQL version</label><input readonly type="text" class="form-control" id="inputSuccess" value="'.$mysql.' (Required: MySQL 4.1 or newer)"></div>';
}

// Optional features checking goes here...
echo "<strong>OPTIONAL FEATURES:</strong><br>\n";

// MRTG presence
if ($safemode == 1) {
  echo "<span class=\"check_bad\">Unable to check with PHP safe mode on</span></p>";
} else {
  if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
    // Try executing mrtg in Windows.
    $mrtg = shell_exec("start /b wperl ".$_SERVER['DOCUMENT_ROOT']."/mrtg/bin/mrtg");
  } else {  
    // Try to execute the mrtg command on Linux. Trap any STDERR from going to STDOUT
    $mrtg = `mrtg 2>&1`;
  }
  // See if we find something familiar from MRTG, fail if not
  if (stristr($mrtg, "Oetiker")) {
    $mrtgver = explode("-", str_replace(" ", "", $mrtg));
    $tmpstr = $mrtgver[2];
    $class = "success";
  } else {
    $class = "warning";
    $tmpstr = "not found (optional)";
    $directions .= "<p>- Install MRTG if you want to use graphical stats</p>\n";
  }

  echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking for MRTG</label><input type="text" readonly class="form-control" id="inputSuccess" value="'.$tmpstr.' (Options MRTG 2 or newer)"></div>';
}

echo "<p>&nbsp</p>";
 if ($failed == 1) {
  echo "<strong>To make your server ready for SHOUTcast Client...</strong>";
  echo $directions;
  echo "<strong>...and then click <big><a href=".$_SERVER['PHP_SELF'].">here</a></big> to reload this page.</strong>"; 
 } else {
  echo "<center><strong>Your server is ready to install SHOUTcast Client!</strong>";
?>
<p>
 <form name="installform" method="post" action="install.php">
  <input type="submit" name="step" value="Step 2 >>">
 </form>
</p>
</center>
<?php  }
}
elseif ($_POST['step'] == "Step 2 >>" || $_POST['step'] == "Reload2") { 

/*
Step 2: Check file permissions
*/
$failed = 0;            // Reset failed flag
$directions = "";       // Empty string with directions
$path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);    // Get pathname of this script
$root_path = str_replace('/install', '', $path_parts['dirname']); // Parent dir is the root path of SHOUTcast Client

echo "<strong>STEP 2: CHECKING FILE PERMISSIONS...</strong><br>\n";

// MRTG working directory
if (is_writeable("../mrtg")) {
 $class = "success";
 $tmpstr = "writable";
} else {
 $failed = 1;
 $class = "error";
 $tmpstr = "failed";
 $directions = "<p>- Set directory <I>".$root_path."/mrtg</I> writable</p>\n";
}

echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking directory MRTG</label><input type="text" readonly class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: read/write access)"></div>';

// PLAYLISTS working directory
if (is_writeable("../playlists")) {
 $class = "success";
 $tmpstr = "writable";
} else {
 $failed = 1;
 $class = "error";
 $tmpstr = "failed";
 $directions .= "<p>- Set directory <I>".$root_path."/playlists</I> writable</p>\n";
}
echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking directory playlists</label><input type="text" readonly class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: read/write access)"></div>';


// SERVERS working directory
if (is_writeable("../servers")) {
 $class = "success";
 $tmpstr = "writable";
} else {
 $failed = 1;
 $class = "error";
 $tmpstr = "failed";
 $directions .= "<p>- Set directory <I>".$root_path."/servers</I> writable</p>\n";
}

echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking directory servers</label><input type="text" readonly class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: read/write access)"></div>';

// AUTODJ working directory
if (is_writeable("../autodj") && is_writeable("../autodj/mp3s")) {
 $class = "success";
 $tmpstr = "writable";
} else {
 $failed = 1;
 $class = "error";
 $tmpstr = "failed";
 $directions .= "<p>- Set directory <I>".$root_path."/autodj</I> and <I>".$root_path."/autodj/mp3s</I> writable</p>\n";
}

echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking directory autodj and autodj/mp3s</label><input type="text" readonly class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: read/write access)"></div>';

// AUTODJ working directory
if (is_writeable("../logs")) {
 $class = "success";
 $tmpstr = "writable";
} else {
 $failed = 1;
 $class = "error";
 $tmpstr = "failed";
 $directions .= "<p>- Set directory <I>".$root_path."/logs</I> writable</p>\n";
}

echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking directory logs</label><input type="text" readonly class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: read/write access)"></div>';

// CONFIG.PHP configuration file
if (is_writeable("../include/config.php")) {
 $class = "success";
 $tmpstr = "writable";
} else {
 $failed = 1;
 $class = "error";
 $tmpstr = "failed";
 $directions .= "<p>- Set file <I>".$root_path."/include/config.php</I> writable</p>\n";
}
echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Checking file config.php</label><input type="text" readonly class="form-control" id="inputSuccess" value="'.$tmpstr.' (Required: read/write access)"></div>';

?>
<form name="installform" method="post" action="install.php">
<?php  echo "<p>&nbsp</p>";
 if ($failed == 1) {
  echo "<strong>To proceed with installation...</strong>";
  echo $directions;
  
  echo "<strong>...and then click below to reload this page.</strong>";
  echo "<center><p><input type=\"submit\" name=\"submit\" value=\"Reload\"></p></center>";
  echo "<input type=\"hidden\" name=\"step\" value=\"Reload2\">\n";
 } else {
  echo "<center><strong>All file and directory permissions OK!</strong>";
  echo "<p><input type=\"submit\" name=\"step\" value=\"Step 3 >>\">\n";
 }
?>
</form><br>
</p>
</center>
<?php } elseif ($_POST['step'] == "Finish!") {

/*
Get config values via form
*/
$createdb = $_POST['createdb'];
$username = $_POST['username'];
$password = $_POST['password'];

  $db = dbConnect();
  
  // Do general settings
  $settings = groupsettings("1");
  foreach ($settings as $setting) {
    $db->where('setting', $setting['setting']);
    $update = array('value' => $_POST[$setting['setting']]);
    $db->update('config', $update);
    echo $db->getLastError();
  } 

  // Update superuser name and password from form
  $update = array('username' => $_POST['username'], 'password' => md5($_POST['password']));
  $db->where('user_id', 1);
  $db->update('members', $update);

  if (iswin()) {
    echo "<p>IMPORTANT! For SHOUTcast Client to run properly under Windows, the registry changes as listed ";
    echo "in the file <strong>pstools.reg</strong> within the <i>wintools</i> directory must be made.</p>";
    $sc_serv = $_POST['sbd_path'].'/shoutcast/1.9.8-Windows/sc_serv.exe';
  } else {
    $sc_serv = $_POST['sbd_path'].'/shoutcast/1.9.8-Linux/sc_serv';
  }

  // A qualified guess of which sc_serv binary to use
  $db->where('setting', 'sc_serv');
  $db->update('config', array('value' => $sc_serv));

  echo "<br><p><div class=\"check_good\">Installation complete, go to the settings menu to setup autoDJ and cron tasks.</div>";
  echo "<br><br><div class=\"check_bad\">IMPORTANT! Delete or rename the install folder!</div>";
  echo "<br><p><div class=\"check_good\"><a href='http://client.scottishbordersdesign.co.uk/knowledgebase/9/SHOUTcast-Client' target=_blank>For information on how to setup WHMCS or setup your first server please see our documents here</a></div>";
  echo "<p>Then click <strong><a href=\"../\">here</a></strong> to login and administrate your SHOUTcast Client installation.</p>";

} elseif ($_POST['step'] == "Step 3 >>"  || substr($_POST['step'], 0, 7) == "Reload3") {

/*
Step 3: MySQL server setup
*/
$failed = 0;            // Reset failed flag
$directions = "";       // Empty string with directions
$path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);      // Get pathname of this script
$root_path = str_replace('/install', '', $path_parts['dirname']); // Parent dir is the root path of SHOUTcast Client

echo "<strong>STEP 3: MYSQL SERVER CONFIGURATION...</strong><br>\n";

// Check to see if we are fresh on step 3 or retrying
if ($_POST['step'] == "Step 3 >>") {
} else if (substr($_POST['step'], 0, 7) == "Reload3") {
  $dbhost = $_POST['databasehost'];
  $dbport = $_POST['databaseport'];
}
?>

<form name="installform" method="post" action="install.php">

  <strong>Database server connection</strong>
   

<div class="form-group">
    <label for="databasehost">Host</label>
    <input type="text" class="form-control" name="databasehost" id="databasehost" value="<?php echo $dbhost; ?>" placeholder="Database Host">
</div>

<div class="form-group">
    <label for="databaseport">Port (normally 3306)</label>
    <input type="text" class="form-control" name="databaseport" id="databaseport" value="<?php echo $dbport; ?>" placeholder="Database Port">
</div>


<?php // Check if host is entered as an IP adress
$ip = false;
if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$dbhost))
{
  $parts=explode(".",$dbhost);
  foreach($parts as $ip_parts)
  {
    if(intval($ip_parts) > 255 || intval($ip_parts) < 0) {
      // Segment not in 0-255 range, most likely a hostname
      $ip = false;
      break;
    }
    $ip = true;
  }
} else {
  $ip = false; // Format does not match that of an ip address
}

// If host is entered as a hostname, try to resolve
if (!$ip) {
  $hostip = gethostbyname($dbhost);
} else {
  $hostip = $dbhost;
}

// If resolving failed, $hostip will just be the same as $dbhost
if ($ip == false && $hostip == $dbhost) {
  $failed = 1;
  $tmpstr = "DNS error";
  $class = "error";
  $directions = "- Use a valid hostname for the MySQL server";
} else {
  $fp = fsockopen($hostip, $dbport, $errno, $errstr, 30); 
  if (!$fp) {
    $failed = 1;
    $tmpstr = $errstr;
    $class = "error";
    $directions = "- Check that the MySQL server daemon is started<br>- Check that database hostname and port info is correct";
  } else {
    fclose($fp);
    $failed = 0;
    $tmpstr = "Connection OK";
    $class = "success";
  }
}
echo '<div class="form-group has-'.$class.'"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Testing Connection</label><input type="text" readonly class="form-control" id="inputSuccess" value="'.$tmpstr.'"></div>';
if ($failed == 1) {
  echo "<p><strong>To proceed with installation...</strong><br>";
  echo $directions;
  echo "<br><strong>...and then click below to retry connecting.</strong></p>";
  echo "<center><p><input type=\"submit\" name=\"submit\" value=\"Retry\"></p></center>";
  echo "<input type=\"hidden\" name=\"step\" value=\"Reload33\">\n";
  echo "</form>";
  exit;
} 

// If this is a retry, use form values instead of those from config.php
if (substr($_POST['step'],0,9) == "Reload333") {
  $dbname = $_POST['database'];
  $dbuser = $_POST['databaseusername'];
  $dbpass = $_POST['databasepassword'];
} else {
  require_once('../include/config.php');
}
?>

 
   
        <strong>Database name and authentication</strong>
   
<div class="form-group">
    <label for="database">Database</label>
    <input type="text" class="form-control" name="database" id="database" value="<?php echo $dbname; ?>" placeholder="Database Name">
</div>

<div class="form-group">
    <label for="databaseusername">Username</label>
    <input type="text" class="form-control" name="databaseusername" id="databaseusername" value="<?php echo $dbuser; ?>" placeholder="Database Username">
</div>


<div class="form-group">
    <label for="databasepassword">Password</label>
    <input type="text" class="form-control" name="databasepassword" id="databasepassword" value="<?php echo $dbpass; ?>" placeholder="Database Password">
</div>

<?php if ($_POST['step'] != 'Step 4 >>'){ ?>
<center><p><input type="submit" name="submit" value="Update Details"></p></center>
<input type="hidden" name="step" value="Reload333">
<?php } ?>


<?php 
$db = new MysqliDb ($dbhost, $dbuser, $dbpass, $dbname);
// Check database access and privileges
echo "- Testing access...";
if ($db) {
  $failed = 0;
  $class = "good";
  $tmpstr = "OK";
  echo "<span class=\"check_".$class."\"><strong>".$tmpstr."</strong></span><br>";
    
  echo "- Testing CREATE TABLE...";
  // Do a DROP IF EXISTS to clean up earlier attempts, and for now ignore if it works
  $testDrop = $db->rawQueryOne("DROP TABLE IF EXISTS `installtest`");
  if ( !$testDrop) {
    echo $db->getLastError();
  }

  $db->rawQuery("CREATE TABLE `installtest` (`test` varchar(200) NOT NULL,  PRIMARY KEY  (`test`) ) 
          ENGINE=MyISAM DEFAULT CHARSET=latin1");
  if (!$db->getLastError()) {
     echo "<span class=\"check_good\"><strong>OK</strong></span><br>";
  } else {
     $failed = 1;
     echo $db->getLastError();
     echo "<span class=\"check_bad\"><strong>failed</strong></span><br>";
  }

  echo "- Testing INSERT...";
  $db->insert("installtest", array('test' => 'test'));
  if (!$db->getLastError()) {
    echo "<span class=\"check_good\"><strong>OK</strong></span><br>";
  } else {
     $failed = 1;
     echo "<span class=\"check_bad\"><strong>failed</strong></span><br>";
  }


  echo "- Testing UPDATE...";
  $db->update("installtest", array('test' => 'newtest'));
  if (!$db->getLastError()) {
    echo "<span class=\"check_good\"><strong>OK</strong></span><br>";
  } else {
     $failed = 1;
     echo "<span class=\"check_bad\"><strong>failed</strong></span><br>";
  }


   
  echo "- Testing SELECT...";
  $db->get("installtest");
  if (!$db->getLastError()) {
    echo "<span class=\"check_good\"><strong>OK</strong></span><br>";
  } else {
     $failed = 1;
     echo "<span class=\"check_bad\"><strong>failed</strong></span><br>";
  }


  echo "- Testing DELETE...";
  $db->delete("installtest");
  if (!$db->getLastError()) {
    echo "<span class=\"check_good\"><strong>OK</strong></span><br>";
  } else {
     $failed = 1;
     echo "<span class=\"check_bad\"><strong>failed</strong></span><br>";
  }


  echo "- Testing ALTER TABLE...";
  $db->rawQuery("ALTER TABLE installtest ADD COLUMN column2 varchar(200) NOT NULL default 'new column'");
  if (!$db->getLastError()) {
    echo "<span class=\"check_good\"><strong>OK</strong></span><br>";
  } else {
     $failed = 1;
     echo "<span class=\"check_bad\"><strong>failed</strong></span><br>";
  }

  echo "- Testing DROP TABLE...";
  $db->rawQuery("DROP TABLE installtest");
  if (!$db->getLastError()) {
    echo "<span class=\"check_good\"><strong>OK</strong></span><br>";
  } else {
     $failed = 1;
     echo "<span class=\"check_bad\"><strong>failed</strong></span><br>";
  }

} else {
  $failed = 1;
  $tmpstr = mysql_error();
  $class = "error";
} 
  
if ($failed == 1) {
  echo "<p><strong>To proceed with installation...</strong>";
  echo "<br>- Grant user <i>".$dbuser."</i> SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER and DROP access privileges on database <i>".$dbname."</i>";
  echo "<br><strong>...and then click below to recheck.</strong></p>";
  echo "<center><p><input type=\"submit\" name=\"submit\" value=\"Retry\"></p></center>";
  echo "<input type=\"hidden\" name=\"step\" value=\"Reload333\">\n";
  echo "</form>";
  exit;
} 
    $config_file = file_get_contents('../include/config.php');

    // Use perl style regular expressions to replace what is actually in the config file values
    $config_file = preg_replace('/\$dbhost.*=.*".*";/', '$dbhost = "'.$dbhost.'";', $config_file);
    $config_file = preg_replace('/\$dbport.*=.*".*";/', '$dbport = "'.$dbport.'";', $config_file);
    $config_file = preg_replace('/\$dbuser.*=.*".*";/', '$dbuser = "'.$dbuser.'";', $config_file);
    $config_file = preg_replace('/\$dbpass.*=.*".*";/', '$dbpass = "'.$dbpass.'";', $config_file);
    $config_file = preg_replace('/\$dbname.*=.*".*";/', '$dbname = "'.$dbname.'";', $config_file);

    $config = file_put_contents('../include/config.php', $config_file);
?>
<center>

<div class="form-group has-success"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Updating config.php</label><input type="text" readonly class="form-control" id="inputSuccess" value="config.php was updated successfully"></div>
<p><input type="submit" name="step" value="Step 4 >>">
</form><br>
</p>
</center>
<?php } elseif ($_POST['step'] == "Step 4 >>" || $_POST['step'] == "Select") {
/*
Step 4: Database and settings
*/
  echo "<strong>STEP 4: DATABASE SETTINGS...</strong><br>\n";
?>  
        <strong>Config and database tables</strong>
   
<?php // Include config.php with correct db info for table creation
  $db = dbconnect(); 
  // Check for tables in database
  $tables = $db->rawQuery("SHOW TABLES;");
  if (count($tables) < 1) {
    echo "<span class=\"check_good\">Empty</span></p>";

echo '<div class="form-group has-success"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Database Content</label><input type="text" readonly class="form-control" id="inputSuccess" value="Empty"></div>';

    // Create the database tables
    shell_exec('mysql -u'.$dbuser.' -p'.$dbpass.' '.$dbname.' < create.sql');

    echo '<div class="form-group has-success"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Recreating all tables</label><input type="text" readonly class="form-control" id="inputSuccess" value="Success!"></div>';

  // If tables found and we have not chosen already, decide wether to recreate or not
  } elseif ($_POST['step'] != "Select") {
    echo "<span class=\"check_bad\">tables exist. Choose action:</span>";
    ?>
    <form name="installform" method="post" action="install.php">
      
        
          
            <div align="left">
              <select name="createdb">
                <option value="yes">Recreate database tables (all old data will be lost)</option>
                <option value="no" selected>Leave database as it is (no action)</option>
              </select>
            </div>
          
        
        
          
            <input type="submit" name="step" value="Select">
          
        
      
    </form></p>
    <?php     exit;

  // We are called from the form and have made our choice 
  } else {
    $createdb = $_POST['createdb'];

    // We chose to keep database untouched, hope for the best and proceed
    if ($createdb == "no") {
      echo "<span class=\"check_bad\">Keep tables</span></p>";

    // We chose to recreate the tables
    } else {
      echo "<span class=\"check_good\">Recreate tables</span></p>";
     
      // Create the database
      shell_exec('mysql -u'.$dbuser.' -p'.$dbpass.' '.$dbname.' < create.sql');
      echo "<span class=\"check_good\">Success!</span><p>";
      echo '<div class="form-group has-success"><label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Recreating all tables</label><input type="text" readonly class="form-control" id="inputSuccess" value="Success!"></div>';
    }
  }

  // Tables are now either kept from before, or recreated 
?>
<p><hr noshade height="1"></p>
<form name="installform" method="post" action="install.php">
<input type="hidden" name="createdb" value="<?php echo $createdb; ?>">

 
   
  <strong>SHOUTcast Client superuser account</strong>
   
 <div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" name="username" id="username" value="" placeholder="Username">
</div>

 <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" name="password" id="password" value="" placeholder="Password">
</div>
 


<p><hr noshade height="1"></p>

   <strong>General settings</strong>
 

<?php // Create a form with the general settings (category 1)
$db = dbConnect();
$settings = groupsettings('1');
  foreach ($settings as $setting) {
  // Find first time values
  if ($setting['setting'] == "web_addr") {
    $setting['value'] = "http://".str_replace('/install/install.php', '', $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]);
  } elseif ($setting['setting'] == "sbd_path") {
    $path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);
    $setting['value'] = str_replace('/install', '', $path_parts['dirname']);
  } elseif ($setting['setting'] == "host_addr") {
    $setting['value'] = $_SERVER["SERVER_ADDR"];
  }
  // Create &nbsp; to avoid breaks in setting's title
  $setting['title'] = str_replace(' ', '&nbsp;', $setting['title'])
?>
 
     <?php     // Check to see if this is a multiple choice setting
    $options = settingoptions($setting['id']);
    if (!empty($options)) {
      echo "<div class=\"form-group\"><label>".$setting['title']."</label><select class=\"form-control\" name=\"".$setting['setting']."\">\n";
      foreach ($options as $option) {
        echo "<option value=\"".$option['value']."\"";
        if ($option['value'] == $setting['value']) {
          echo " selected>";
        } else {
          echo ">";
        }
        echo $option['caption']."</option>\n";
      }
      echo "</select></div>\n";
    } else {
      if (strlen($setting['value']) < 8) {
        $fieldsize = "10";
      } else {
        $fieldsize = "25";
      }
      echo "<div class=\"form-group\"><label>".$setting['title']."</label><input class=\"form-control\" name=\"".$setting['setting']."\"
            value=\"".$setting['value']."\" size=\"".$fieldsize."\"></div>\n";
    }
?>
 
<?php } ?>


<center><input type="submit" name="step" value="Finish!"></center>
</form>

<?php }
?>
<div align="center">
<p>&nbsp;</p>
</div> 
        <!-- jQuery 2.0.2 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../templates/default/js/bootstrap.min.js" type="text/javascript"></script>

    </body>
</html>
