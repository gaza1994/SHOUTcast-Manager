<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Main Functions
 * @website: http://scottishbordersdesign.co.uk/
 */
require_once("db_MySQLi.inc.php");
require_once('config.php');

$file  = $_SERVER["SCRIPT_NAME"];
$break = explode("/", $file);
$file  = $break[count($break) - 1];
if ($file == "install.php" || $file == "license.php" || $file == "upgrade.php") {
    // License Check, Install, or Upgrade initialized 
} else {
    require 'Smarty/SmartyBC.class.php';
    $config = settings();
    $smarty = new SmartyBC;
    if ($config['caching'] == 'yes') {
        $smarty->caching = 1;
        $smarty->force_compile = false;
        $smarty->cache_lifetime = 120;
    } else {
        $smarty->caching = false;
        $smarty->force_compile = true;
        $smarty->cache_lifetime = 120;
    }
    if ($config['debugging'] == 'yes') {
        $smarty->debugging = true;
    } else {
        $smarty->debugging = false;
    }
    if ($config['error_reporting'] == 'yes') {
        $smarty->error_reporting = E_ALL & ~E_NOTICE;
    } else {
        $smarty->error_reporting = 0;
    }
    
    $smarty->template_dir = $config['template_dir'].'/'.$config['theme']; 
    $template_dir = $smarty->getTemplateDir();
    $template_dir = '/'.$template_dir[0];
    $smarty->assign("currPage", get_menu_item_active());
    $smarty->assign("title",serviceTitle());
    $smarty->assign("template_dir", $template_dir);

    docheck();
    $_LANG = _LANG();
    $smarty->assign("lang", $_LANG);
}

function sbd_check_license($licensekey, $localkey = "") {
	return true;
}

function docheck($force=0) {
	// this was use as a license check - no longer required.
	return true;
}

function dbconnect() {
    global $dbhost;
    global $dbuser;
    global $dbpass;
    global $dbname;

    $db = new MysqliDb ($dbhost, $dbuser, $dbpass, $dbname);
    return $db;
}

function dbTest($dbhost, $dbuser, $dbpass, $dbname) {
    $db = new MysqliDb ($dbhost, $dbuser, $dbpass, $dbname);
    return $db;
}

function _LANG(){
    $config = settings();
    $langauge = include($config['sbd_path'].'/lang/'.$config['lang'].'.php');
    if ($langauge) {
        return $_LANG;
    } else {
        die("Failed to fetch langauge.");
    }
}

function get_memory_useage() {
    foreach (file("/proc/meminfo") as $ri) {
        $m[strtok($ri, ":")] = strtok("");
    }
    return 100 - round(($m["MemFree"] + $m["Buffers"] + $m["Cached"]) / $m["MemTotal"] * 100);
}

function login_check($username, $password) {
    $db       = dbconnect();
    $password = md5($password);

    $escapedUsername = $db->escape ($username);
    $db->where("username", $escapedUsername);
    $row      = $db->getOne("members");

    if ($username == $row["username"] && $password == $row["password"]) {
        $login_check = true;
    } else {
        $login_check = false;
    }
    
    return $login_check;
}

function google_auth_part_check($username){
    $db       = dbconnect();
    $escapedUsername = $db->escape ($username);
    $db->where("username", $escapedUsername);
    $row      = $db->getOne("members");
    if (!is_null($row['2stepauth'])) {
        return false;
    } else {
        return true;
    }
}

function loginas($id, $username, $isreturn = false) {
    $adminid = getuid($_SESSION["username"]);
    if ($isreturn == true) {
        unset($_SESSION["adminlogin"]);
    } else {
        $_SESSION["adminlogin"] = $_SESSION["username"];
    }
    
    if ($isreturn == true) {
        unset($_SESSION["returnurl"]);
    } else {
        $_SESSION["returnurl"] = "" . "users.php?loginas=yes&id=" . $adminid . "&username=" . $_SESSION["username"];
    }
    
    $timestamp            = "" . strftime("%T") . " " . date("Y-m-d") . "";
    $db                   = dbconnect();
    $insert               = array(
        "event_user" => $_SESSION["username"],
        "event" => $_SESSION["username"] . "logged in as " . $username . " from " . getenv("REMOTE_ADDR"),
        "timestamp" => $timestamp
    );
    $_SESSION["ip"]       = getenv("REMOTE_ADDR");
    $_SESSION["username"] = $username;
    unset($_SESSION["password"]);
    header("Location: home.php");
}

function cpanel_api($type, $user = "", $pass = "", $homedir = "", $quota = "", $domain = "", $deleteDir = "0") {
    $config = settings();
    require("config.php");
    if ($config['cpanel_port'] == "2087" || $config['cpanel_port'] == "2083" || $config['cpanel_port'] == "443") {
        $site = "https://" . $config['cpanel_hostname'] . ":" . $config['cpanel_port'];
    } else {
        $site = "http://" . $config['cpanel_hostname'] . ":" . $config['cpanel_port'];
    }
    
    switch ($type) {
        case "add_ftp":
            $xmlin = "<cpanelaction><module>Ftp</module><func>addftp</func><apiversion>1</apiversion><args>" . $user . "</args><args>" . $pass . "</args><args>/" . $homedir . "</args><args>" . $quota . "</args></cpanelaction>";
            $event = " has created a new FTP Account";
            addevent("SYSTEM", $event);
            break;
        case "del_ftp":
            $xmlin = "<cpanelaction><module>Ftp</module><func>delftp</func><apiversion>1</apiversion><args>" . $user . "</args><args>".$deleteDir."</args></cpanelaction>";
            $event = " has deleted a FTP Account";
            addevent("SYSTEM", $event);            
        break;
        default:
            echo "Type error";
    }
    if ($type == "add_ftp" || $type == "del_ftp" || $type == "add_subdomain" || $type == "del_subdomain") {
        $query = "/xml-api/cpanel?user=" . $config['cpanel_username'] . "&xmlin=" . $xmlin;
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_USERPWD, $config['cpanel_username'] . ":" . $config['cpanel_password']);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_URL, $site . $query);
        $result = curl_exec($curl);
        curl_close($curl);
    }  
    return $result;
}

function removeinstall($dirPath){
}

function getUserIP(){
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function addevent($user, $event) {
    $user_ip = getUserIP();
    $timestamp = "" . strftime("%T") . " " . date("Y-m-d") . "";
    $db        = dbconnect();
    $insert    = array(
        "event_user" => $user,
        "event" => $user . $event,
        "timestamp" => $timestamp,
        "ip" => $user_ip
    );
    $db->insert("events", $insert);
}

function iswin() {
    if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
        return true;
    }   
}

function webget($url) {
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_HEADER, 0);
    curl_setopt($handle, CURLOPT_USERAGENT, "User-Agent: SBD (Mozilla Compatible)");
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($handle, CURLOPT_TIMEOUT, 120);
    ob_start();
    curl_exec($handle);
    curl_close($handle);
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

function restartall() {
    $db      = dbconnect();
    $db->orderBy("PortBase","DESC");
    $cols = array("created","MaxUser","PortBase","servername","enabled","id");
    $servers = $db->get("servers", null, $cols);
    foreach ($servers as $row) {
        $row = index_array($row);
        $srvname = $row[3];
        $srvname = preg_replace("/_/", " ", $srvname);
        $port    = $row[2];
        $sid     = $row[5];
        stopautoDJ($sid);
        usleep(2000);
        stopstream($sid);
        usleep(2000);
        startstream($sid);
        usleep(2000);
        header("Location: home.php");
    }
}

function startall() {
    $db      = dbconnect();
    $db->orderBy("PortBase","DESC");
    $cols = array("created","MaxUser","PortBase","servername","enabled","id");
    $servers = $db->get("servers", null, $cols);
    foreach ($servers as $row) {
        $row = index_array($row);
        $srvname = $row[3];
        $srvname = preg_replace("/_/", " ", $srvname);
        $port    = $row[2];
        $sid     = $row[5];
        startstream($sid);
        usleep(2000);
        header("Location: home.php");
    }
}

function stopall() {
    $db      = dbconnect();
    $db->orderBy("PortBase","DESC");
    $cols = array("created","MaxUser","PortBase","servername","enabled","id");
    $servers = $db->get("servers", null, $cols);
    foreach ($servers as $row) {
        $row = index_array($row);
        $srvname = $row[3];
        $srvname = preg_replace("/_/", " ", $srvname);
        $port    = $row[2];
        $sid     = $row[5];
        restartAll();
        stopstream($sid);
        usleep(2000);
        header("Location: home.php");
    }
}

function checklistener($port, $id, $displaypercentage = "no", $displaytotal = "no") {
    $config  = settings();
    $dataset = webget($config["host_addr"] . ":" . $port . "/7.html");
    if ($dataset == "") {
        if ($displaypercentage == "yes") {
            echo "0";
        } else {
            return "<div data-toggle='tooltip' data-original-title='Server Offline, No Stats to Show' class=\"progress xs\"><div class=\"progress-bar progress-bar-danger\" style=\"width: 100%\"></div></div>";
        }
        
    } else {
        $entries        = explode(",", $dataset);
        $listener       = $entries[0];
        $status         = $entries[1];
        $listenerpeak   = $entries[2];
        $maxlisteners   = $entries[3];
        $totallisteners = $entries[4];
        $bitrate        = $entries[5];
        $songtitel      = $entries[6];
        $percentage     = $totallisteners / $maxlisteners * 100;
        if ($percentage < 1 && $percentage < 70) {
            $labelcolour = "success";
        }
        
        if ($percentage < 70) {
            $labelcolour = "yellow";
        }
        
        if ($percentage < 99) {
            $labelcolour = "danger";
        }
        
        if ($percentage < 1) {
            $labelcolour = "danger";
        }
        
        if ($displaytotal == "yes") {
            echo $totallisteners;
        } else {
            if ($displaypercentage == "yes") {
                echo format_2_dp($percentage);
            } else {
                return "" . "<div data-toggle='tooltip' data-original-title='Current Listners: " . $totallisteners . " | Max Listners: " . $maxlisteners . "' style='height: 10px;' class=\"progress xs progress active\"><div class=\"progress-bar progress-bar-" . $labelcolour . "\" style=\"width: " . $percentage . "%\"></div></div>";
            }
            
        }
        
    }
}

function checkdj($port, $id) {
    $config  = settings();
    $dataset = webget($config["host_addr"] . ":" . $port . "/7.html");
    if ($dataset == "") {
        echo "<span data-toggle='tooltip' data-original-title='Server Offline' class=\"badge bg-red\">No</span>";
    } else {
        $entries = explode(",", $dataset);
        $status  = $entries[1];
        if ($status == 1) {
            echo "<span data-toggle='tooltip' data-original-title='A DJ is active on this stream' class=\"badge bg-red\">No</span>";
        }
        
        if ($status == "0") {
            echo "<span data-toggle='tooltip' data-original-title='There is currently no one connected to this stream' class=\"badge bg-green\">Yes</span>";
        }
        
    }
}

function checkstream($port, $id) {
    $config  = settings();
    $dataset = webget($config["host_addr"] . ":" . $port . "/7.html");
    if ($dataset == "") {
        return false;
    }
    
    return true;
}

function getagg() {
    $db      = dbconnect();
    $config  = settings();
    $null    = NULL;
    $db->orderBy("PortBase","ASC");
    $cols = array("PortBase","id");
    $servers = $db->get("servers", null, $cols);
    foreach ($servers as $row) {
        $row = index_array($row);
        $port    = $row[0];
        $id      = $row[1];
        $dataset = webget($config["host_addr"] . ":" . $port . "/7.html");
        if ($dataset == "") {
            $db->where("id",$id);
            $db->update("servers", array(
                "listeners" => "0"
            ));
        } else {
            $dataset  = preg_replace("/.*<body>/", "", $dataset);
            $dataset  = preg_replace("/<\\/body>.*/", "", $dataset);
            $entries  = explode(",", trim($dataset));
            $listener = $entries[0];
            $db->where("PortBase", $port);
            $db->update("servers", array(
                "listeners" => $listener
            ));
        }
        
        $row = $db->rawQueryOne("SELECT SUM(listeners) FROM servers");
        return "" . $row["0"] . "\n0\n" . $null . "\n" . $config["host_addr"];
    }
}

function is_menu_active($script) {
    $file  = $_SERVER["SCRIPT_NAME"];
    $break = explode("/", $file);
    $file  = $break[count($break) - 1];
    if ($script == $file) {
        echo "active";
    } else {
        echo "";
    }
}

function get_menu_item_active(){
    $file  = $_SERVER["SCRIPT_NAME"];
    $break = explode("/", $file);
    $file  = $break[count($break) - 1];
    return $file;
}

function gettotalservers() {
    $db      = dbconnect();
    $config  = settings();
    $null    = NULL;
    $servers = $db->get("servers");
    $i       = 0;
    if (empty($servers)) {
        $i = 0;
    } else {
        foreach ($servers as $row) {
            $i++;
        }
    }
    
    return $i;
}

function generatemrtg() {
    $db     = dbconnect();
    $config = settings();
    stopmrtg();
    $db->get("servers");
    if ($config["mrtg"] == "on" && 0 < $db->count) {
        $db = dbconnect();
        deletefile($config["sbd_path"] . "/mrtg/mrtg.cfg");
        $header = "#MRTG Config File\n" . "WorkDir: " . $config["sbd_path"] . "/mrtg\n" . "Interval: " . $config["mrtg_interval"] . "\n" . "RunAsDaemon: yes\n\n";
        $fd     = fopen($config["sbd_path"] . "/mrtg/mrtg.cfg", "w+");
        fputs($fd, $header . "\n\n");
        fclose($fd);
        $db->orderBy("PortBase","ASC");
        $cols = array("PortBase","id");
        $servers = $db->get("servers", null, $cols);
        foreach ($servers as $row) {
            $row = index_array($row);
            $port   = $row[0];
            $id     = $row[1];
            $header = "#MRTG Config for port " . $port . "\n" . "" . "WithPeak[" . $port . $id . "]: ymw\n" . "" . "Target[" . $port . $id . "] : `php " . $config["sbd_path"] . "/mrtg/getlisteners.php " . $port . " " . $id . " " . $config["host_addr"] . "`\n" . "" . "Title[" . $port . $id . "]: Total " . $port . " Listeners\n" . "" . "PageTop[" . $port . $id . "]: <H1> Listeners on port " . $port . " (server #" . $id . ")</H1>\n" . "" . "MaxBytes[" . $port . $id . "]: 100\n" . "" . "XSize[" . $port . $id . "]: 500\n" . "" . "YSize[" . $port . $id . "]: 150\n" . "" . "ShortLegend[" . $port . $id . "]: listeners\n" . "" . "YLegend[" . $port . $id . "]: total listeners\n" . "" . "Legend1[" . $port . $id . "]:\n" . "" . "Legend2[" . $port . $id . "]:\n" . "" . "Legend3[" . $port . $id . "]:\n" . "" . "LegendI[" . $port . $id . "]: tuned in:\n" . "" . "LegendO[" . $port . $id . "]:\n" . "" . "Options[" . $port . $id . "]: gauge,growright,nopercent\n";
            $fd     = fopen($config["sbd_path"] . "/mrtg/mrtg.cfg", "a+");
            fputs($fd, $header . "\n\n");
            fclose($fd);
        }
        $header = "#MRTG Aggregate Config File\n" . "WithPeak[aggregate]: ymw\n" . "Target[aggregate]: `php " . $config["sbd_path"] . "/mrtg/getTotal.php`\n" . "Title[aggregate]: Total Listeners\n" . "PageTop[aggregate]: <H1> Listeners on all ports </H1>\n" . "MaxBytes[aggregate]: 100\n" . "XSize[aggregate]: 500\n" . "YSize[aggregate]: 150\n" . "ShortLegend[aggregate]: listeners\n" . "YLegend[aggregate]: total listeners\n" . "Legend1[aggregate]:\n" . "Legend2[aggregate]:\n" . "Legend3[aggregate]:\n" . "LegendI[aggregate]: tuned in:\n" . "LegendO[aggregate]:\n" . "Options[aggregate]: gauge,growright,nopercent\n";
        $fd     = fopen($config["sbd_path"] . "/mrtg/mrtg.cfg", "a+");
        fputs($fd, $header . "\n\n");
        fclose($fd);
        startmrtg();
    }
}

function startmrtg() {
    $config = settings();
    if (iswin()) {
        $output = start_proc("wperl " . $_SERVER["DOCUMENT_ROOT"] . "/mrtg/bin/mrtg --logging=eventlog " . $config["sbd_path"] . "/mrtg/mrtg.cfg \n");
    } else {
        $output = shell_exec("nohup env LANG=C /usr/bin/mrtg " . $config["sbd_path"] . "" . "/mrtg/mrtg.cfg > /dev/null & echo \$!");
    }
    
    return $output;
}

function stopmrtg() {
    $config = settings();
    iswin();
    iswin() ? ($pidfile = "mrtg.cfg_l") : ($pidfile = "mrtg.pid");
    if ($mrtgpid = getcontent($config["sbd_path"] . "/mrtg/" . $pidfile)) {
        if (iswin()) {
            proc_kill(trim($mrtgpid));
        } else {
            shell_exec("kill -9 " . trim($mrtgpid));
        }
        
    }
    
    deletefile($config["sbd_path"] . "/mrtg/" . $pidfile);
    return 1;
}

function cleanmrtg($port) {
    $config = settings();
    $path   = $config["sbd_path"] . "/mrtg";
    if ($handle = opendir($path)) {
        while ($file = readdir($handle)) {
            if (is_file($file) && substr($file, 0, strlen($port)) == $port) {
                deletefile($path . "/" . $file);
            }
            
        }
    }
}

function updatecrontab() {
}

function confgroups() {
    $db = dbconnect();
    return $db->get("config_groups");
}

function groupsettings($group) {
    $db = dbconnect();
    $db->where("groupid", $group);
    return $db->get("config");
}

function settingoptions($setting) {
    $db = dbconnect();
    $db->where("configid", $setting);
    return $db->get("config_sets");
}

function cron_check_and_start() {
    $config  = settings();
    $db      = dbconnect();
    $i       = 0;
    $db->orderBy("PortBase","ASC");
    $cols = array("PortBase","id","enabled");
    $servers = $db->get("servers", null, $cols);

    foreach ($servers as $row) {
        if ($row["enabled"] == "1") {
            $checkport = webget($config["host_addr"] . ":" . $row["PortBase"]);
            if ($checkport == "") {
                startstream($row["id"]);
                $i++;
            }
            
        }
        
    }
    echo "" . $i . " server(s) have been started due to server going offline! <br />";
}

function updatesettings($update) {
    $db = dbconnect();
    foreach ($update as $setting => $value) {
        if ($value != strip_tags($value)) {
            $value = htmlentities($value);
        }
        
        $db->where("setting", $setting);
        $db->update("config", array(
            "value" => $value
        ));
    }
    generatemrtg();
}

function settings() {
    $db       = dbconnect();
    $db->orderBy("id","ASC");
    $cols = array("setting","value");
    $settings = $db->get("config", null, $cols);
    foreach ($settings as $id => $values) {
        $return[$values["setting"]] = $values["value"];
    }
    return $return;
}

function adduser($username, $password, $fname, $lname, $email, $level) {
    $db     = dbconnect();
    $insert = array(
        "username" => $username,
        "password" => $password,
        "fname" => $fname,
        "lname" => $lname,
        "email" => $email,
        "access" => $level
    );
    $db->insert("members", $insert);
    if (empty($_SESSION["username"])) {
        $userusername = "WHMCS";
    } else {
        $userusername = $_SESSION["username"];
    }
    
    addevent($userusername, " added " . $username . " as a registered user");
    echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Success!</b> Added " . $username . " successfully!</div>";
}

function deluser($id, $username) {
    $db = dbconnect();
    $db->where("user_id", $id);
    $db->delete("members");
    if (empty($_SESSION["username"])) {
        $userusername = "WHMCS";
    } else {
        $userusername = $_SESSION["username"];
    }
    
    addevent($userusername, " deleted user " . $username);
    echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Success!</b> Deleted " . $username . " successfully!</div>";
}

function edituser($id, $username, $password, $fname, $lname, $email, $level) {
    $db     = dbconnect();
    $update = array(
        "username" => $username,
        "fname" => $fname,
        "lname" => $lname,
        "email" => $email,
        "access" => $level
    );

    if (!empty($password)) {
        $update['password'] = md5($password);
    }

    $db->where("user_id", $id);
    $db->update("members", $update);
    addevent($_SESSION["username"], " updated " . $username);
    echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Success!</b> Your profile has been updated.</div>";
}

function useraccess($username) {
    $db  = dbconnect();

    $db->where("username", $username);
    $row      = $db->getOne("members");

    return $row["access"];
}

function getgravatar($username) {
    $db         = dbconnect();

    $db->where("username", $username);
    $row      = $db->getOne("members");

    $email_hash = md5(strtolower(trim($row["email"])));
    if (isset($_SERVER["HTTPS"])) {
        $gravatar = "//www.gravatar.com/avatar/" . $email_hash;
    } else {
        $gravatar = "http://www.gravatar.com/avatar/" . $email_hash;
    }
    
    return $gravatar;
}

function getuserdetails($username) {
    $db  = dbconnect();

    $db->where("username", $username);
    $row      = $db->getOne("members");

    return $row;
}

function getuid($username) {
    $db  = dbconnect();
    $db->where("username", $username);
    $row      = $db->getOne("members");
    return $row["user_id"];
}

function suspendserver($id) {
    $db = dbconnect();
    $db->where("id", $id);
    $db->update("servers", array(
        "disabled" => "1"
    ));
}

function unsuspendserver($id) {
    $db = dbconnect();
    $db->where("id", $id);
    $db->update("servers", array(
        "disabled" => "0"
    ));
}

function suspendstatus($id) {
    $db     = dbconnect();
    $db->where("id", $id);
    $status      = $db->getOne("servers");
    return $status["disabled"];
}

function getusername($uid) {
    $db  = dbconnect();
    $db->where("user_id", $uid);
    $row      = $db->getOne("members");

    return $row["username"];
}

function getowner_byport($port, $id) {
    $db   = dbconnect();
    $db->where("id", $id);
    $db->where("PortBase", $port);
    $row      = $db->getOne("servers");
    return $row["owner"];
}

function getowner_by_id_full(){
    $db  = dbconnect();
    $db->where("id", $id);
    $row      = $db->getOne("servers");
    return $row;
}

function getowner_by($id) {
    $db  = dbconnect();
    $db->where("id", $id);
    $row      = $db->getOne("servers");
    return $row["owner"];
}

function getmyservers_byid($userid) {
    $db  = dbconnect();
    $db->where("owner", $userid);
    $row      = $db->getOne("servers");
    return $row;
}

function getserverbyportbase($portbase){
    $db  = dbconnect();
    $db->where("PortBase", $portbase);
    $row      = $db->getOne("servers");
    return $row;
}

function getServerById($serverid){
    $db  = dbconnect();
    $db->where("id", $serverid);
    $row = $db->get("servers");
    return $row;
}

function getmyserversMulti_byid($userid) {
    $db  = dbconnect();
    $db->where("owner", $userid);
    $row = $db->get("servers");
    return $row;
}

function getmyservers($username)
{
    $db  = dbconnect();
    $db->where("username", $username);
    $row      = $db->getOne("members");
    $id  = $row["id"];
    $db->where("owner", $id);
    $row      = $db->getOne("servers");
    return $row;
}

function getid_by_srvname($srvname) {
    $db  = dbconnect();
    $db->where("servername", $srvname);
    $row      = $db->getOne("servers");
    return $row["id"];
}

function getport_by_id($id) {
    $db  = dbconnect();
    $db->where("id", $id);
    $row      = $db->getOne("servers");
    return $row["PortBase"];
}

function restart_server($portbase, $srvname) {
    $db     = dbconnect();
    $config = settings();
    shell_exec("kill `cat " . $config["sbd_path"] . "/servers/" . $portbase . "" . $srvname . ".pid`");
    $db->where("PortBase", $portbase);
    $db->where("servername", $srvname);
    $db->update("servers", array(
        "enabled" => "1"
    ));
    $pid      = shell_exec("nohup " . $config["sc_serv"] . " " . $config["sbd_path"] . "/servers/" . $portbase . "" . $srvname . "" . ".conf > /dev/null & echo \$!");
    $cleanpid = trim($pid);
    exec("" . "echo " . $cleanpid . " > " . $config["sbd_path"] . "/servers/" . $portbase . "" . $srvname . ".pid");

    $db->where("PortBase", $portbase);
    $db->where("servername", $srvname);
    $db->orderBy("autodj","asc");
    $autodj      = $db->getOne("servers");
    if (isset($autodj[0]) && $autodj[0] == "1") {
    }
}

function getpid($portbase, $srvname) {
    $config   = settings();
    $filename = $config["sbd_path"] . "/servers/" . $portbase . $srvname . ".pid";
    if (file_exists($filename)) {
        $handle   = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        return trim($contents);
    }
    
    return false;
}

function getpidautodj($portbase, $srvname) {
    $config   = settings();
    $filename = $config["sbd_path"] . "/servers/autodj_" . $portbase . $srvname . ".pid";
    if (file_exists($filename)) {
        $handle   = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        return trim($contents);
    }
    
    return false;
}

function getcontent($fullpath) {
    if (file_exists($fullpath) && 0 < filesize($fullpath)) {
        $handle   = fopen($fullpath, "r");
        $contents = fread($handle, filesize($fullpath));
        fclose($handle);
        return trim($contents);
    }
    
    return false;
}

function softwarever() {
    $db       = dbconnect();
    $row      = $db->getOne("sbd");
    $filename = "version";
    $handle   = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    return $row["version"] . "-r" . $contents;
}

function getmediapath() {
    $db     = dbconnect();
    $config = settings();
    return $config["media_path"];
}

function getusedautodj_space($port) {
    $f    = "autodj/mp3s/" . $port;
    $io   = popen("/usr/bin/du -sk " . $f, "r");
    $size = fgets($io, 4096);
    $size = substr($size, 0, strpos($size, "\t"));
    pclose($io);
    $size = $size / 1024;
    $size = $size / 1024;
    return format_2_dp($size);
}

function getUsedAutoDJ_space_knob($port){
    $f    = "autodj/mp3s/" . $port;
    $io   = popen("/usr/bin/du -sk " . $f, "r");
    $size = fgets($io, 4096);
    $size = substr($size, 0, strpos($size, "\t"));
    pclose($io);
    $size = $size / 1024;
    return format_2_dp($size);
}

function getusedautodj_space_percentage($port, $maxspace) {
    $f    = "autodj/mp3s/" . $port;
    $io   = popen("/usr/bin/du -sk " . $f, "r");
    $size = fgets($io, 4096);
    $size = substr($size, 0, strpos($size, "\t"));
    pclose($io);
    $size       = $size / 1024;
    $percentage = $size / $maxspace * 100;
    return format_2_dp($percentage);
}

function autoDJUsedSpaceNew($port) {
    $config   = settings();
    $directory = $config['media_path'].$port."/";
    $size = 0;
    try {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }
        $size = ($size / 1024) / 1024;
        return $size; 
    } catch (Exception $e) {
        return 0;
    }
}

function reloadmedia($i, $port) {
    $db        = dbconnect();
    $directory = $i;
    scan_directory_recursively($directory, "mp3");
    $db->orderBy("id","ASC");
    $dbmedia = $db->get("media");
    foreach ((array) $dbmedia as $row) {
        $row = index_array($row);
        if (file_exists($directory . basename($row[1]))) {
            $db->where("id", $row[0]);
            $db->update("media", array(
                "port" => $port,
                "files" => $i . basename($row[1])
            ));
        } else {
            $db->where("port", $port);
            $db->where("id", $row[0]);
            $db->delete("media");

            $db->where("port", $port);
            $db->where("id", $row[0]);
            $db->delete("playlist_content");
        }
        
    }
}

function scan_directory_recursively($directory, $filter) {
    require_once('getid3/getid3.php');
    $db = dbconnect();
    if (substr($directory, 0 - 1) == "/") {
        $directory = substr($directory, 0, 0 - 1);
    }
    
    if (!file_exists($directory) || !is_dir($directory)) {
        return FALSE;
    }
    
    if (is_readable($directory)) {
        $directory_list = opendir($directory);
        while (FALSE !== ($file = readdir($directory_list))) {
            if ($file != "." && $file != "..") {
                $path = $directory . "/" . $file;
                if (is_readable($path)) {
                    $subdirectories = explode("/", $path);
                    if (is_dir($path)) {
                        $directory_tree[] = array(
                            "path" => $path,
                            "name" => end($subdirectories),
                            "kind" => "directory",
                            "content" => scan_directory_recursively($path, $filter)
                        );
                    } else {
                        if (is_file($path)) {
                            $ext_end        = end($subdirectories);
                            $ext_explod     = explode(".", $ext_end);
                            $extension      = end($ext_explod);
                            if ($filter === FALSE || $filter == $extension) {
                                $directory_tree[] = array(
                                    "path" => $path,
                                    "name" => end($subdirectories),
                                    "extension" => $extension,
                                    "size" => filesize($path),
                                    "kind" => "file"
                                );
                                if (strpos($path, "'") !== false) {
                                    $temp_name_path = str_replace("'", "", $path);
                                    rename($path, $temp_name_path);
                                    $path = $temp_name_path;
                                }

                                $db->where("files", $path);
                                $db->get("media");
                                
                                if (0 < $db->count) {
                                } else {
                                    $mp3    = "" . $path;
                                    $artist = " ";
                                    $song   = " ";
                                    if ($song == " ") {
                                        $tempsong = preg_split("[/]", $mp3);
                                        $song     = $tempsong[sizeof($tempsong) - 1];
                                    }
                                    
                                    $album = " ";
                                    $year  = 0;
                                    $genre = 255;
                                    if (!is_numeric($genre)) {

                                        $db->where("genreTitle", $genre);
                                        $db->get("genres");

                                        if (0 < $db->count) {
                                            $genre = $g[0];
                                        } else {
                                            $genre = 255;
                                        }
                                        
                                    } else {
                                        if ($genre < 0 || 255 < $genre) {
                                            $genre = 255;
                                        }
                                        
                                    }
                                    
                                    $path    = str_replace("'", "", $path);
                                    $getID3 = new getID3;
                                    $mp3ID3 = $mp3ID3ORG = $getID3->analyze($path);
                                    if (isset($mp3ID3['tags'])) {
                                       $mp3ID3 = $mp3ID3['tags'];
                                    } else {
                                        $mp3ID3 = null;
                                    }
                                    

                                    if (empty($mp3ID3['id3v2']['title'])) {
                                        if (empty($mp3ID3['id3v1']['title'])) {
                                            $song = $mp3ID3ORG['filename'];
                                        } else {
                                            $song = $mp3ID3['id3v1']['title'];
                                            $song = $song[0]; 
                                        }
                                    } else {
                                        $song = $mp3ID3['id3v2']['title'];
                                        $song = $song[0];
                                    }

                                    if (empty($mp3ID3['id3v2']['artist'])) {
                                        if (empty($mp3ID3['id3v1']['artist'])) {
                                            $artist = "N/A";
                                        } else {
                                            $artist = $mp3ID3['id3v1']['artist'];
                                            $artist = $artist[0];                                           
                                        }
                                    } else {
                                        $artist = $mp3ID3['id3v2']['artist'];
                                        $artist = $artist[0];
                                    }

                                    if (empty($mp3ID3['id3v2']['album'])) {
                                        if (empty($mp3ID3['id3v1']['album'])) {
                                            $album = "N/A";
                                        } else {
                                            $album = $mp3ID3['id3v1']['album'];
                                            $album = $album[0];                                          
                                        }
                                    } else {
                                        $album = $mp3ID3['id3v2']['album'];
                                        $album = $album[0];
                                    }

                                    if (empty($mp3ID3['id3v2']['year'])) {
                                        if (empty($mp3ID3['id3v1']['year'])) {
                                            $year = "0";
                                        } else {
                                            $year = $mp3ID3['id3v1']['year'];
                                            $year = $year[0];
                                        }
                                    } else {
                                        $year = $mp3ID3['id3v2']['year'];
                                        $year = $year[0];
                                    }

                                    if (empty($mp3ID3['id3v2']['genre'])) {
                                        if (empty($mp3ID3['id3v1']['genre'])) {
                                            $genre = "None";
                                        } else {
                                            $genre = $mp3ID3['id3v1']['genre'];
                                            $genre = $genre[0];
                                        }
                                    } else {
                                        $genre = $mp3ID3['id3v2']['genre'];
                                        $genre = $genre[0];
                                    }

                                    $db->where("genreTitle", $genre);
                                    $isGenre = $db->getOne("genres");
                                    if (empty($isGenre['genreID'])) {
                                        $insert = array('genreTitle' => $genre);
                                        $genre = $db->insert("genres", $insert);
                                        $lastInsertId = "id";
                                    } else {
                                        $genre = $isGenre['genreID'];
                                    }

                                    $comment = " ";

                                    $insert  = array(
                                        "files" => addslashes($path),
                                        "song" => addslashes($song),
                                        "artist" => addslashes($artist),
                                        "album" => addslashes($album),
                                        "year" => addslashes($year),
                                        "comment" => addslashes($comment),
                                        "genre" => addslashes($genre)
                                    );
                                    $db->insert("media", $insert);
                                }
                                
                            }
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }
        closedir($directory_list);
    }
}

function getMP3Info($trackID, $portbase, $image=false){
    require_once('getid3/getid3.php');
    $db = dbconnect();

    $db->where("id", $trackID);
    $db->where("port", $portbase);
    $track = $db->getOne("media");
    if (empty($track)) {
        return "ERROR";
    }
    $path = $track['files'];

    $getID3 = new getID3;
    $mp3ID3 = $mp3ID3ORG = $getID3->analyze($path);

    if ($image) {
        $picture = @$mp3ID3['comments']['picture'][0];
        return $picture;
    }

    $song = $track['song'];
    $artist = $track['artist'];
    $album = $track['album'];
    $year = $track['year'];
    $db->where("genreID", $track['genre']); $genre = $db->getOne("genres");
    $comment = "";

    if (empty($mp3ID3ORG['comments']['picture'][0]['data'])) {
        $ispicture = false;
    } else {
        $ispicture = true;
    }
    $fileInfo = array(
        "song"      => $song,
        "artist"    => $artist,
        "album"     => $album,
        "year"      => $year,
        "genre"     => $genre['genreTitle'],
        "comment"   => $comment,
        "picture"   => $ispicture
    );
    return $fileInfo;
}

function rebuildplaylist($i, $port) {
    require("config.php");
    $db = dbconnect();
    $config      = settings();
    $db->orderBy("position", "ASC");
    $db->where("pid", $i);
    $playlist_content = $db->get('playlist_content');

    $fidsarray[] = array();
    $tracklist   = array();
    foreach ($playlist_content as $key => $fids) {
        $db->where("port", $port);
        $db->where("id", $fids['fid']);
        $media_files = $db->get("media");
        foreach ($media_files as $key => $media) {
            $tracklist[] = $media["files"];
        }
    }
    if ($h = fopen($config["sbd_path"] . "/playlists/autodj_" . $port . ".lst", "w")) {
        fputcsv($h, $tracklist, "\n");
        fclose($h);
    }
    
    $str = file_get_contents($config["sbd_path"] . "/playlists/autodj_" . $port . ".lst");
    $str = str_replace("\"", "", $str);
    file_put_contents($config["sbd_path"] . "/playlists/autodj_" . $port . ".lst", $str);
}

function deleteplaylist($pid, $uid) {
    $db   = dbconnect();

    $db->where("uid", $uid);
    $db->where("id", $pid);
    $cols = array('uid', 'name');

    $row      = $db->getOne("playlist", null, $cols);
    unlink("" . "playlists/" . $row["0"] . $row["1"] . ".pls");
}

function rebuildconf($id) {
    $conf          = settings();
    $db            = dbconnect();
    $db->where("id", $id);
    $stream      = $db->getOne("servers");
    $stream = index_array($stream);

    $adminpassword = $stream[16];
    $maxuser       = $stream[2];
    $realtime      = $stream[6];
    $screenlog     = $stream[7];
    $showlastsongs = $stream[33];
    $w3cenable     = $stream[8];
    $srcip         = $stream[10];
    $dstip         = $stream[11];
    $yport         = $stream[12];
    $namelookup    = $stream[13];
    $autodump      = $stream[17];
    $autodumptime  = $stream[18];
    $allowrelay    = $stream[22];
    $allowpubrelay = $stream[23];
    $metainterval  = $stream[24];
    $portbase      = $stream[4];
    $djpassword    = $stream[3];
    $srvname       = $stream[34];
    $website       = $stream[44];
    $genre         = $stream[43];
    $shuffle       = $stream[42];
    $bitrate       = $stream[45];
    $crossfadeM    = $stream[52];
    $crossfadeL    = $stream[53];
    $publicsrv     = $stream[21];
    $header        = ";\n" . "" . "; " . $srvname . " Configuration file\n" . "; Auto-generated by SHOUTcast Client\n" . ";\n" . "" . "AdminPassword=" . $adminpassword . "\n" . "" . "MaxUser=" . $maxuser . "\n" . "" . "Password=" . $djpassword . "\n" . "" . "PortBase=" . $portbase . "\n" . "" . "RealTime=" . $realtime . "\n" . "" . "ScreenLog=" . $screenlog . "\n" . "" . "ShowLastSongs=" . $showlastsongs . "\n" . "" . "W3CEnable=" . $w3cenable . "\n" . "" . "SrcIP=" . $srcip . "\n" . "" . "DestIP=" . $dstip . "\n" . "" . "Yport=" . $yport . "\n" . "" . "NameLookups=" . $namelookup . "\n";
    $header .= "" . "AutoDumpUsers=" . $autodump . "\n" . "" . "AutoDumpSourceTime=" . $autodumptime . "\n" . "" . "PublicServer=" . $publicsrv . "\n" . "" . "AllowRelay=" . $allowrelay . "\n" . "" . "AllowPublicRelay=" . $allowpubrelay . "\n" . "BanFile=" . $conf["sbd_path"] . "" . "/logs/" . $portbase . $srvname . ".ban\n" . "LogFile=" . $conf["sbd_path"] . "" . "/logs/" . $portbase . $srvname . ".log\n" . "W3CLog=" . $conf["sbd_path"] . "" . "/logs/" . $portbase . $srvname . ".w3c.log\n" . "" . "MetaInterval=" . $metainterval . "\n";
    $fd = fopen($conf["sbd_path"] . "/servers/" . $portbase . "" . $srvname . ".conf", "w+");
    fputs($fd, $header . "\n\n");
    fclose($fd);
}

function rebuildautodj($id) {
    $config      = settings();
    $db          = dbconnect();
    $db->where("id", $id);
    $stream      = $db->getOne("servers");
    $stream = index_array($stream);

    $portbase    = $stream[4];
    $djpassword  = $stream[3];
    $srvname     = $stream[34];
    $website     = $stream[44];
    $genre       = $stream[43];
    $shuffle     = $stream[42];
    $bitrate     = $stream[45];
    $streamtitle = $stream[19];
    $crossfadeM    = $stream[52];
    $crossfadeL    = $stream[53];
    $public      = $stream[21];
    buildplaylist($portbase);
    $header = ";\n" . "" . "; " . $srvname . " Configuration file\n" . "; Auto-generated by SHOUTcast Client\n" . ";\n" . "" . "PlaylistFile=" . $config["sbd_path"] . "/playlists/autodj_" . $portbase . ".lst\n" . "" . "ServerIP=" . $config["host_addr"] . "\n" . "" . "ServerPort=" . $portbase . "\n" . "" . "Password=" . $djpassword . "\n" . "" . "StreamTitle=" . $streamtitle . "\n" . "" . "StreamURL=" . $website . "\n" . "" . "Genre=" . $genre . "\n" . "" . "Shuffle=" . $shuffle . "\n" . "; Bitrate/SampleRate/Channels recommended values:\n" . "; 8kbps 8000/11025/1\n" . "; 16kbps 16000/11025/1\n" . "; 24kbps 24000/22050/1\n" . "; 32kbps 32000/22050/1\n" . "; 64kbps mono 64000/44100/1\n" . "; 64kbps stereo 64000/22050/2\n" . "; 96kbps stereo 96000/44100/2\n" . "; 128kbps stere0 128000/44100/2\n" . "" . "Bitrate=" . $bitrate . "000\n" . "SampleRate=44100\n" . "Channels=2\n" . "Quality=5\n" . "" . "CrossfadeMode=" . $crossfadeM . "\n" . "" . "CrossfadeLength=" . $crossfadeL . "\n" . "UseID3=0\n" . "" . "Public=" . $public . "\n" . "AIM=\n" . "ICQ=\n" . "IRC=\n";
    $fd     = fopen($config["sbd_path"] . "/servers/autodj_" . $portbase . "" . $srvname . ".conf", "w");
    fputs($fd, $header . "\n\n");
    fclose($fd);
}

function rebuildautodj_with_bitrate($id, $autoDJBitrate) {
    $config      = settings();
    $db          = dbconnect();
    $db->where("id", $id);
    $stream      = $db->getOne("servers");
    $stream = index_array($stream);

    $portbase    = $stream[4];
    $djpassword  = $stream[3];
    $srvname     = $stream[34];
    $website     = $stream[44];
    $genre       = $stream[43];
    $shuffle     = $stream[42];
    $bitrate     = $autoDJBitrate;
    $streamtitle = $stream[19];
    $crossfadeM    = $stream[52];
    $crossfadeL    = $stream[53];
    $public      = $stream[21];
    buildplaylist($portbase);
    $header = ";\n" . "" . "; " . $srvname . " Configuration file\n" . "; Auto-generated by SHOUTcast Client\n" . ";\n" . "" . "PlaylistFile=" . $config["sbd_path"] . "/playlists/autodj_" . $portbase . ".lst\n" . "" . "ServerIP=" . $config["host_addr"] . "\n" . "" . "ServerPort=" . $portbase . "\n" . "" . "Password=" . $djpassword . "\n" . "" . "StreamTitle=" . $streamtitle . "\n" . "" . "StreamURL=" . $website . "\n" . "" . "Genre=" . $genre . "\n" . "" . "Shuffle=" . $shuffle . "\n" . "; Bitrate/SampleRate/Channels recommended values:\n" . "; 8kbps 8000/11025/1\n" . "; 16kbps 16000/11025/1\n" . "; 24kbps 24000/22050/1\n" . "; 32kbps 32000/22050/1\n" . "; 64kbps mono 64000/44100/1\n" . "; 64kbps stereo 64000/22050/2\n" . "; 96kbps stereo 96000/44100/2\n" . "; 128kbps stere0 128000/44100/2\n" . "" . "Bitrate=" . $bitrate . "000\n" . "SampleRate=44100\n" . "Channels=2\n" . "Quality=5\n" . "" . "CrossfadeMode=" . $crossfadeM . "\n" . "" . "CrossfadeLength=" . $crossfadeL . "\n" . "UseID3=0\n" . "" . "Public=" . $public . "\n" . "AIM=\n" . "ICQ=\n" . "IRC=\n";
    $fd     = fopen($config["sbd_path"] . "/servers/autodj_" . $portbase . "" . $srvname . ".conf", "w");
    fputs($fd, $header . "\n\n");
    fclose($fd);
}

function cron_stop_server($port, $id, $srvname, $bitrate, $maxrate) {
    $db = dbconnect();
    $config = settings();
    $rootdir = $config['sbd_path'];
    $notify = $config['adm_email'];
    $db->where('PortBase', $port);
    $db->where('id', $id);
    $data = array('enabled' => 0 );
    $db->update("servers", $data);
    shell_exec("kill `cat $rootdir/servers/$port$srvname.pid`");
    unlink("$rootdir/servers/$port$srvname.pid");
    $message = "Server $srvname on port $port stopped due to excessive bitrate of $bitrate.  The configured bitrate for this server is $maxrate";
    // In case any of our lines are larger than 70 characters, we should use wordwrap()
    $message = wordwrap($message, 70);
    // Send
    mail('' . $notify . '', 'Server ' . $srvname . ' on ' . $port . ' exceeded bitrate setting', $message);

    // Send copy to owner of the server
    $owner = getowner_by_id_full($id);
    $ownerEmail = $owner['email'];
    mail('' . $ownerEmail . '', 'Server ' . $srvname . ' on ' . $port . ' exceeded bitrate setting', $message);
}

function startstream($id) {
    $evenautodj = "";
    $db         = dbconnect();
    $cols = array("PortBase", "servername", "autodj");
    $db->where("id", $id);
    $stream      = $db->getOne("servers", null, $cols);

    $port       = $stream["PortBase"];
    $srvname    = $stream["servername"];
    $config     = settings();
    $sc_conf    = $config["sbd_path"] . "/servers/" . $port . "" . $srvname . ".conf";
    if (file_exists($sc_conf)) {
        if (iswin()) {
            $cmdstr   = $config["sc_serv"];
            $cfg_path = str_replace("/", "\\", $sc_conf);
            $cmdstr .= " " . $cfg_path;
            debug($cmdstr);
            $pid = start_proc($cmdstr);
        } else {
            $cmdstr = "nohup " . $config["sc_serv"] . " " . $config["sbd_path"] . "/servers/" . $port . "" . $srvname . ".conf";
            $cmdstr .= "" . " > /dev/null & echo \$!";
            $pid = shell_exec($cmdstr);
        }
        
        $cleanpid = trim($pid);
        $fd       = fopen($config["sbd_path"] . "/servers/" . $port . "" . $srvname . ".pid", "w+");
        fputs($fd, $cleanpid);
        fclose($fd);
        $db->where("PortBase", $port);
        $db->where("id", $id);
        $db->update("servers", array(
            "enabled" => "1"
        ));
        if ($stream["autodj"] == "1") {
            $ices     = $config["sc_trans"];
            $pid      = shell_exec("" . $ices . " " . $config["sbd_path"] . "/servers/autodj_" . $port . $srvname . "" . ".conf > /dev/null & echo \$!");
            $cleanpid = trim($pid);
            system("" . "echo " . $cleanpid . " > " . $config["sbd_path"] . "/servers/autodj_" . $port . $srvname . ".pid");
            $evenautodj = "With AutoDJ ";
        }
        
        $username = $_SESSION["username"];
        $event    = " Started server " . $evenautodj . "on port " . $port;
        addevent($username, $event);
    } else {
        echo "Server configuration file is missing: " . $sc_conf . "<br>";
        exit();
    }
}

function stopstream($id) {
    $db      = dbconnect();
    $cols = array("PortBase", "servername", "autodj");
    $db->where("id", $id);
    $stream      = $db->getOne("servers", null, $cols);
    $port    = $stream["PortBase"];
    $srvname = $stream["servername"];
    $config  = settings();
    $db->where("PortBase", $port);
    $db->where("id", $id);
    $db->update("servers", array(
        "enabled" => "0"
    ));
    if ($pid = trim(getpid($port, $srvname))) {
        if (iswin()) {
            proc_kill($pid);
        } else {
            shell_exec("kill -9 " . $pid);
        }
        
        deletefile("" . $config["sbd_path"] . "/servers/" . $port . "" . $srvname . ".pid");
        if ($stream["autodj"] == "1") {
            $autopid = getpidautodj($port, $srvname);
            if (file_exists($config["sbd_path"] . "/servers/autodj_" . $port . $srvname . ".pid")) {
                shell_exec("kill -9 " . $autopid);
                deletefile($config["sbd_path"] . "/servers/autodj_" . $port . $srvname . ".pid");
            }
            
        }
        
        $username = $_SESSION["username"];
        $event    = " Stopped server on port " . $port;
        addevent($username, $event);
    }
}

function stopautodj($id) {
    $db      = dbconnect();
    $cols = array("PortBase", "servername", "autodj");
    $db->where("id", $id);
    $stream      = $db->getOne("servers", null, $cols);
    $port    = $stream["PortBase"];
    $srvname = $stream["servername"];
    $config  = settings();
    if ($pid = trim(getpid($port, $srvname))) {
        $autopid = getpidautodj($port, $srvname);
        if (file_exists($config["sbd_path"] . "/servers/autodj_" . $port . $srvname . ".pid")) {
            shell_exec("kill -9 " . $autopid);
            deletefile($config["sbd_path"] . "/servers/autodj_" . $port . $srvname . ".pid");
        }

        $db->where("PortBase", $port);
        $db->where("id", $id);
        $db->update("servers", array(
            "autodj_active" => "0",
            "autodj" => "0"
        ));
        
    }
    
    $username = $_SESSION["username"];
    $event    = " Stopped AutoDJ on port " . $port;
    addevent($username, $event);
}

function reloadstream($id) {
    $db     = dbconnect();
    $config = settings();
    $cols = array("autodj", "Portbase");
    $db->where("id", $id);
    $adj      = $db->getOne("servers", null, $cols);
    $adj = index_array($adj);
    if (file_exists("" . $config["sbd_path"] . "/servers/" . $id . "." . $adj[1] . ".pid")) {
        stopstream($id);
        startstream($id);
    }
}

function genices0conf($id, $port, $pls, $random) {
}

function getserverid() {
    $db  = dbconnect();
    $row = $db->rawQueryOne("SELECT MAX(id) FROM ranch");
    return $row[0];
}

function geticesloc() {
    $config = settings();
    return $config["sc_trans"];
}

function servicetitle() {
    $config = settings();
    return $config["site_title"];
}

function userlist() {
    $db    = dbconnect();
    $db->orderBy("user_id","ASC");
    $cols = array("user_id","username");
    $users = $db->get("members", null, $cols);
    foreach ($users as $user) {
        $user = index_array($user);
        echo "<option value=\"" . $user[0] . "\">" . $user[1] . "</option>";
    }
}

function msgbox($message, $return = "home.php") {
    header("" . "Location: " . $return);
    exit();
}

function format_2_dp($input) {
    echo round($input, 2);
}

function playlist() {
    $db = dbconnect();
    $db->orderBy("name","ASC");
    ($rows = $db->get("playlist")) ? ($rows = $rows) : ($rows[] = "");
    foreach ($rows as $row) {
        $row = index_array($row);
        echo "<option value=\"" . $row[0] . "\">" . $row[3] . "</option>";
    }
}

function newplaylist($port, $uid = "") {
    require("config.php");
    $db     = dbconnect();
    $config = settings();
    if (empty($uid)) {
        $uid = getuid($_SESSION["username"]);
    } else {
        $uid = $uid;
    }
    
    $db->insert("playlist", array(
        "name" => $port,
        "uid" => $uid,
        "file" => $config["sbd_path"] . "/playlists/" . $port . ".lst"
    ));
}

function buildplaylist($port) {
    $db = dbconnect();
    $config    = settings();
    $db->where("port", $port);
    $media_files = $db->get("media");
    $tracklist = array();
    foreach ($media_files as $key => $media) {
        $tracklist[] = $media["files"];
    }
    if ($h = fopen($config["sbd_path"] . "/playlists/autodj_" . $port . ".lst", "w")) {
        fputcsv($h, $tracklist, "\n");
        fclose($h);
    }
    
    $str = file_get_contents($config["sbd_path"] . "/playlists/autodj_" . $port . ".lst");
    $str = str_replace("\"", "", $str);
    file_put_contents($config["sbd_path"] . "/playlists/autodj_" . $port . ".lst", $str);
}

function genicespls($sid, $rand) {
}

function delicespls($sid) {
}

function nextport() {
    $db      = dbconnect();
    $config  = settings();
    $newport = $config["start_portbase"];
    $row     = $db->rawQueryOne("SELECT MAX(PortBase) AS LastPort FROM servers");
    if ($row["LastPort"] < $newport) {
        return $newport;
    }
    
    if (strtolower($config["nextport_method"]) == "high") {
        return $row["LastPort"] + 2;
    }
    $db->orderBy("PortBase","ASC");
    $cols = array("PortBase");
    $servers = $db->get("servers", null, $cols);

    $lastport = $config["start_portbase"] - 2;
    foreach ($servers as $row) {
        $row = index_array($row);
        if ($lastport + 2 < $row[0]) {
            $newport = $lastport + 2;
            break;
        }
        
        $lastport = $row[0];
        $newport  = $lastport + 2;
    }
    return $newport;
}

function portexists($checkport) {
    $db = dbconnect();
    $db->where("PortBase", $checkport);
    $db->get("servers");
    return $db->count;
}

function deletefile($path) {
    if (file_exists($path)) {
        unlink($path);
        return true;
    }
    
    return false;
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        return $dirPath;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

function renamefile($oldpath, $newpath) {
    if (file_exists($oldpath)) {
        rename($oldpath, $newpath);
    }
}

function check_install() {
    if (file_exists("install/install.php")) {
        $fe = "1";
    } else {
        $fe = "0";
    }
    
    return $fe;
}

function get_settings() {
    $db = dbconnect();
    $result = $db->get("config");
    return $result;
}

function start_proc($comm) {
    $config         = settings();
    $dn             = dirname(__FILE__);
    $descriptorspec = array(
        array(
            "pipe",
            "r"
        ),
        array(
            "pipe",
            "w"
        ),
        array(
            "pipe",
            "w"
        )
    );
    $cmd            = "start /b " . $config["sbd_path"] . "/wintools/psexec.exe -d";
    $fpr            = proc_open($cmd . " " . $comm, $descriptorspec, $pipes, $dn);
    fclose($pipes[0]);
    fclose($pipes[1]);
    $stderr = "";
    while (!feof($pipes[2])) {
        $stderr .= fgets($pipes[2], 128);
    }
    fclose($pipes[2]);
    proc_close($fpr);
    $pid = FALSE;
    if (preg_match("/process ID ([\\d]{1,10})\\./im", $stderr, $matches)) {
        $pid = $matches[1];
    } else {
        $pid = FALSE;
    }
    
    return $pid;
}

function proc_isalive($pid) {
    $config         = settings();
    $alive          = FALSE;
    $dn             = dirname(__FILE__);
    $descriptorspec = array(
        array(
            "pipe",
            "r"
        ),
        array(
            "pipe",
            "w"
        ),
        array(
            "pipe",
            "w"
        )
    );
    $cmd            = "start /b " . $config["sbd_path"] . "/wintools/pslist.exe";
    $fpr            = proc_open($cmd . " " . $pid, $descriptorspec, $pipes, $dn);
    fclose($pipes[0]);
    $stdout = "";
    while (!feof($pipes[1])) {
        $stdout .= fgets($pipes[1], 128);
    }
    fclose($pipes[1]);
    $stderr = "";
    while (!feof($pipes[2])) {
        $stderr .= fgets($pipes[2], 128);
    }
    fclose($pipes[2]);
    proc_close($fpr);
    if (strpos($stdout, "not found") === FALSE) {
        $alive = TRUE;
    }
    
    return $alive;
}

function proc_kill($pid) {
    $config         = settings();
    $succ           = FALSE;
    $dn             = dirname(__FILE__);
    $descriptorspec = array(
        array(
            "pipe",
            "r"
        ),
        array(
            "pipe",
            "w"
        ),
        array(
            "pipe",
            "w"
        )
    );
    $fpr            = proc_open("start /b " . $config["sbd_path"] . "/wintools/pskill.exe " . $pid, $descriptorspec, $pipes, $dn);
    fclose($pipes[0]);
    $stdout = "";
    while (!feof($pipes[1])) {
        $stdout .= fgets($pipes[1], 128);
    }
    fclose($pipes[1]);
    $stderr = "";
    while (!feof($pipes[2])) {
        $stderr .= fgets($pipes[2], 128);
    }
    fclose($pipes[2]);
    proc_close($fpr);
    if (strpos($stdout, "killed") !== FALSE) {
        $succ = TRUE;
    }
    
    return $succ;
}

function debug($msg) {
    error_log("[SHOUTcast Manager] " . $msg);
}

function prettyArray($arrayInput){
    echo "<pre>";
    print_r($arrayInput);
    echo "</pre>";
}

function index_array($array){
    $array = array_values( $array );
    for ( $i = 0, $n = count( $array ); $i < $n; $i++ ) {
        $element = $array[$i];
        if ( is_array( $element ) ) {
            $array[$i] = $this->index_array( $element );
        }
    }
    return $array;
}

function checkSSL(){
    $config = settings();
    $theURL = parse_url($config['web_addr']);
    if (!isset($theURL['scheme']) || $theURL['scheme'] == 'http') {
        // We dont need to do anything, but we do need to catch this
    } else {
        if ( empty($_SERVER['HTTPS']) ) { // if https isnt the current thing we're using, make it!
            // we are using SSL!
            header("Location: ".$theURL['scheme'].'://'.$theURL['host'].$theURL['path']);
        }
    }
}

function fetchAndUpdateLang(){
    $db = dbconnect();
    $db->where("setting", "lang");
    $langID = $db->getOne("config");
    $langID = $langID['id'];
    $lang_dirs = glob('lang/*.{php}', GLOB_BRACE);
    $lang_dirs = str_replace('lang/', '', $lang_dirs);
    $lang_dirs = str_replace('.php', '', $lang_dirs);


    $db->where("configid", $langID);
    $deleteLang = $db->delete("config_sets");

    foreach ($lang_dirs as $key => $lang) {
        $data = array(
            'configid' => $langID,
            'value' => $lang,
            'caption' => $lang
         );
        $db->insert("config_sets", $data);
    }
}

function fetchAndUpdateTheme(){
    $db = dbconnect();
    $db->where("setting", "theme");
    $themeID = $db->getOne("config");
    $themeID = $themeID['id'];
    $themes = array_filter(glob('templates/*'), 'is_dir');
    $themes = str_replace('templates/', '', $themes);
    $db->where("configid", $themeID);
    $deleteLang = $db->delete("config_sets");

    foreach ($themes as $key => $theme) {
        $data = array(
            'configid' => $themeID,
            'value' => $theme,
            'caption' => $theme
         );
        $db->insert("config_sets", $data);
    }
}

function genKey($length = 11) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    $randomString = implode("-", str_split($randomString, 4));
    return $randomString;
}

function getFTPAccounts($serverport){
    $db  = dbconnect();
    $config = settings();
    $db->where("PortBase", $serverport);
    $list = $db->get("ftp");

    if (!empty($list)) {
        foreach ($list as $key => $value) {
            echo '<tr>';
            echo '    <td>'.$config['ftp_host'].'</td>';
            echo '    <td>'.$value['username'].'@'.$config['ftp_host'].'</td>';
            echo '    <td>'.$value['password'].'</td>';
            echo '    <td>'.$config['ftp_port'].'</td>';
            echo '    <td>';
            echo '        <a href="#" onclick="removeFTP(\''.$value['id'].'\');return false;"><span class="badge bg-red">Delete</span></a>';
            echo '    </td>';
            echo '</tr>';
        }
    } else {
        echo "<tr id='noFTPWarning'><td colspan='5' style='text-align:center;'>No FTP Accounts</td></tr>";
    }
}

function generatePassword($length = 9, $add_dashes = false, $available_sets = 'luds') {
    $sets = array();
    if(strpos($available_sets, 'l') !== false)
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    if(strpos($available_sets, 'u') !== false)
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    if(strpos($available_sets, 'd') !== false)
        $sets[] = '23456789';
    if(strpos($available_sets, 's') !== false)
        $sets[] = '!@#$%&*?';
    $all = '';
    $password = '';
    foreach($sets as $set)
    {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
    }
    $all = str_split($all);
    for($i = 0; $i < $length - count($sets); $i++)
        $password .= $all[array_rand($all)];
    $password = str_shuffle($password);
    if(!$add_dashes)
        return $password;
    $dash_len = floor(sqrt($length));
    $dash_str = '';
    while(strlen($password) > $dash_len)
    {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
    }
    $dash_str .= $password;
    return $dash_str;
}

function apiChangePassowrd($userid, $newPassword){
    $db = dbconnect();
    $db->where("user_id", $userid);
    $user = $db->getOne("members");

    if ($user) {
        $db->where("user_id", $userid);
        $db->update("members", array(
            "password" => md5($newPassword)
        ));
    }
}

function apiUpgradeDowngrade($userid, $serverid, $newPackageArray){
    $db = dbconnect();

    if ($userid && $serverid && $newPackageArray) {
        $_A = $newPackageArray;
        $db->where("id", $serverid);
        $db->update("servers", array(
            "bitrate"           => $_A['bitrate'],
            "MaxUser"           => $_A['maxuser'],
            "autodj_max_space"  => ($_A['autodj_space'] * 1024)
        ));
    }

    rebuildconf($serverid);
    rebuildautodj($serverid);

    return true;
}

function doAutoDJCheck($portNumber, $server){
    $db = dbconnect();
    $config = settings();

    $directory = $config['media_path'] . $portNumber . '/';
    $files = glob($directory . '*.mp3');
    $filecount = count( $files );
    return $filecount;
}

function checkAutoDJFolder($portNumber, $server){
    $db = dbconnect();
    $config = settings();
    $files = doAutoDJCheck($portNumber, $server);

    if ($server['autodj'] == '1' && $server['autodj_active'] == '1' && $files < 2) {
        stopautodj($server['id']);
        rebuildautodj($server['id']);
        echo "AutoDJ Stopped for server {$server['id']}<br />";
        return 'error';
    }

    if ($files < 2) {
        echo "{$server['id']} hes less than 2 files.<br />";
        return 'error';
    }

    return "success";
}

function getAllServers(){
    $db = dbconnect();
    $config = settings();

    $allservers = $db->get("servers");
    foreach ($allservers as $key => $server) {
        echo "<option value='{$server['id']}'>{$server['servername']} - {$server['PortBase']}</option>";
    }
}

function success($title, $content){
    echo "<div class=\"alert alert-success alert-dismissable\"><i class=\"fa fa-check\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>{$title}</b> " . $content . "</div>";
}

function alertError($title, $content){
    echo "<div class=\"alert alert-danger alert-dismissable\"><i class=\"fa fa-ban\"></i><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>{$title}</b> " . $content . "</div>";
}

function autoDJBitrateSelect($serverID){
    $db = dbconnect();
    $config = settings();
    $db->where("id", $serverID);
    $server = $db->getOne("servers");
    $maxBitrate = $server['bitrate'];
    echo "
        <option value='8' ". (($maxBitrate=='8')? 'selected':'') . (($maxBitrate) < 8? 'disabled':'') .">8kbps ".(($maxBitrate) < 8? ' - Not available for your server.':'')."</option>
        <option value='16' ". (($maxBitrate=='16')? 'selected':'') . (($maxBitrate) < 16? 'disabled':'') .">16kbps ".(($maxBitrate) < 16? ' - Not available for your server.':'')."</option>
        <option value='24' ". (($maxBitrate=='24')? 'selected':'') . (($maxBitrate) < 24? 'disabled':'') .">24kbps ".(($maxBitrate) < 24? ' - Not available for your server.':'')."</option>
        <option value='32' ". (($maxBitrate=='32')? 'selected':'') . (($maxBitrate) < 32? 'disabled':'') .">32kbps ".(($maxBitrate) < 32? ' - Not available for your server.':'')."</option>
        <option value='64' ". (($maxBitrate=='64')? 'selected':'') . (($maxBitrate) < 64? 'disabled':'') .">64kbps ".(($maxBitrate) < 64? ' - Not available for your server.':'')."</option>
        <option value='96' ". (($maxBitrate=='96')? 'selected':'') . (($maxBitrate) < 96? 'disabled':'') .">96kbps ".(($maxBitrate) < 96? ' - Not available for your server.':'')."</option>
        <option value='128' ". (($maxBitrate=='128')? 'selected':'') . (($maxBitrate) < 128? 'disabled':'') .">128kbps ".(($maxBitrate) < 128? ' - Not available for your server.':'')."</option>
        <option value='198' ". (($maxBitrate=='198')? 'selected':'') . (($maxBitrate) < 198? 'disabled':'') .">198kbps ".(($maxBitrate) < 198? ' - Not available for your server.':'')."</option>
        <option value='320' ". (($maxBitrate=='320')? 'selected':'') . (($maxBitrate) < 320? 'disabled':'') .">320kbps ".(($maxBitrate) < 320? ' - Not available for your server.':'')."</option>
    ";
}
