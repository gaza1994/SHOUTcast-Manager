<?php
/*
* @author: Scottish Borders Design
* @script: SBD SHOUTcast Manager
* @function: Settings Page
* @website: http://scottishbordersdesign.co.uk/
*/

////////////////////////////////////////////////////////////////////////
require('header.php');

// Process changes if we are called by form
if (isset($_POST['configsub'])) {
  unset($_POST['configsub']);
  updatesettings($_POST);
  docheck(1);
  header("Location: setup.php?group=$_POST[category]&return=3");
  exit;
}
// Use first settings group as default
if (!isset($_REQUEST['group'])) { $_REQUEST['group'] = 1; }

// Build menu of setting groups
$menu = "<div class=\"nav-tabs-custom\"><ul class=\"nav nav-tabs\">";
foreach (confgroups() as $group) {
  if ($group['id'] == $_REQUEST['group']) {
    $activegroup = $group; 
    $menu .= "<li class=\"active\"><a href='#'>".$group['name']."</a></li>";
  } else { 
    $menu .= "<li><a class=\"headbarlink\" href=\"".$_SERVER['PHP_SELF']."?group=".$group['id']."\">".$group['name']."</a></li>"; 
  }
}
$menu .= "</ul>"; 
?>

                    <h1>
                        Administration
                        <small><?php echo $activegroup['title']; ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="home.php"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
                        <li class="active"><i class="glyphicon glyphicon-gear"></i> Administration - <?php echo $activegroup['title']; ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                  <div class="row">
                    <div class="col-xs-12">


            <?php 
            if(useraccess($_SESSION['username']) < "5") {
// do nothing
} else {

if (isset($_GET['return']) && $_GET['return']=='3') {
 ?>
<div class="alert alert-success alert-dismissable">
    <i class="fa fa-check"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <b>Success!</b> Your settings have been saved.
</div>
 <?php
}

  echo $menu;
  } ?>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">

<?php // Store fields from submit
?>
<form action="" method="post" enctype="multipart/form-data">
          <section id="portfolio" class="two">
            <div class="container">
<table border = "0">
<?php 
 if(useraccess($_SESSION['username']) < "5") {
  echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-ban\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>ERROR!</b> ACCESS DENIED - YOU DO NOT HAVE ACCESS TO SERVER SETTINGS</div>";
} else {
/*
    Preprocess dynamic update of multiple choices
*/


$db = dbConnect();
$config = settings();
$dnas = array();

fetchAndUpdateLang();
fetchAndUpdateTheme();

$db->where('setting', 'media_url');
$db->update('config', array('value' => $config['web_addr'].'/autodj/mp3s/'));

$db->where('setting', 'media_path');
$db->update('config', array('value' => $config['sbd_path'].'/autodj/mp3s/'));

// Find available SHOUTcast DNAS binaries
$path = $config['sbd_path']."/shoutcast";
if ($mainhandle = opendir($path)) {
  // Read contents of 'shoutcast/' directory
  while ($dir=readdir($mainhandle)) {
    // Do not read directories '.', '..', '.svn', etc.
    if (is_dir($path.'/'.$dir) && substr($dir, 0, 1) != ".") {
      // Open the directories we found under 'shoutcast/'
      if ($localhandle = opendir($path.'/'.$dir)) {
        // Check the names of contents that are files, add sc_serv
        while ($file=readdir($localhandle)) {
          if (is_file($path.'/'.$dir.'/'.$file) 
        && (strtoupper($file) == "SC_SERV" || strtoupper($file) == "SC_SERV.EXE")) {
      // If it's an sc_serv binary, add it to the list of DNAS'es
      $dnas[$dir] = $path."/".$dir."/".$file;
          }
        }
      }
    }
  }
}

// Find the record id for the 'sc_serv' setting

$db->orderBy("id", "ASC");
$db->where("setting", "sc_serv");
$row = $db->getOne("config");
$row = index_array($row);
$configid = $row[0];

// Replace any existing entries with the DNAS binary paths found
$db->where('configid', $configid);
$db->delete('config_sets');
foreach ($dnas as $key => $value) {
  $db->insert('config_sets', array( 'configid'  => $configid, 
            'value'   => $value,
            'caption'   => $key )
             );
}

// Find available SHOUTcast DNAS binaries
$path = $config['sbd_path']."/shoutcast";
if ($mainhandle = opendir($path)) {
  // Read contents of 'shoutcast/' directory
  while ($dir=readdir($mainhandle)) {
    // Do not read directories '.', '..', '.svn', etc.
    if (is_dir($path.'/'.$dir) && substr($dir, 0, 1) != ".") {
      // Open the directories we found under 'shoutcast/'
      if ($localhandle = opendir($path.'/'.$dir)) {
        // Check the names of contents that are files, add SC_TRANS
        while ($file=readdir($localhandle)) {
          if (is_file($path.'/'.$dir.'/'.$file) 
        && (strtoupper($file) == "SC_TRANS" || strtoupper($file) == "SC_TRANS.EXE")) {
      // If it's an SC_TRANS binary, add it to the list of DNAS'es
      $dnas[$dir] = $path."/".$dir."/".$file;
          }
        }
      }
    }
  }
}

// Find the record id for the 'SC_TRANS' setting
$db = dbConnect();
$db->orderBy("id", "ASC");
$db->where("setting", "SC_TRANS");
$row = $db->getOne("config");
$row = index_array($row);
$configid = $row[0];

// Replace any existing entries with the DNAS binary paths found
$db->where('configid', $configid);
$db->delete('config_sets');
foreach ($dnas as $key => $value) {
  $db->insert('config_sets', array( 'configid'  => $configid, 
            'value'   => $value,
            'caption'   => $key )
             );
}
  

/*
        Proceed to build settings form
*/

// Fetch settings for selected group (if any)
$settings = groupsettings($_REQUEST['group']);
if (isset($settings)) {
  foreach ($settings as $setting) {
    echo "<div class=\"form-group\"><label>".$setting['title']."</label>";
    // Check to see if this is a multiple choice setting
    $options = settingoptions($setting['id']);
    if (isset($options) && !empty($options)) {
      echo "<select class=\"form-control\" name=\"".$setting['setting']."\">";
      foreach ($options as $option) {
        echo "<option value=\"".$option['value']."\"";
        if ($option['value'] == $setting['value']) { 
          echo " selected>"; 
        } else { 
          echo ">"; 
        }
        echo $option['caption']."</option>
";
      }
      echo "</select>
";
    } else { 
      if (strlen($setting['value']) < 8) { 
        $fieldsize = "10"; 
      } else {
        $fieldsize = "50";
      }
      echo "<input type=\"text\" class=\"form-control\" name=\"".$setting['setting']."\" value=\"".$setting['value']."\" placeholder=\"".$setting['value']."\" size=\"".$fieldsize."\"></div>";
    }
  }
  echo "<input type=\"hidden\" name=\"category\" value=\"".$_REQUEST['group']."\">";
  $lastline = "<div class=\"box-footer\"><button type=\"submit\" name=\"configsub\" class=\"btn btn-primary\">Save Changes</button></div>";
} else {
  $lastline = "<div class=\"box-footer\">".$activegroup['title']." category is empty.</div>";
}
}
?>
  <tr>
    <td><br>&nbsp;</td><td>&nbsp;</td>
    <td><?php echo $lastline; ?></td>
  </tr>
</table><br />
            </div>
          </section>
</form>
        </div><!-- /.tab-pane -->
    </div><!-- /.tab-content -->
</div>
<?php require('footer.php');
?>