--
-- Table structure for table `api_events`
--

CREATE TABLE IF NOT EXISTS `api_events` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` varchar(2555) NOT NULL,
  `host` varchar(2555) NOT NULL,
  `url` varchar(2555) NOT NULL,
  `action` varchar(2555) NOT NULL,
  `timestamp` varchar(2555) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `groupid` int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
 INSERT INTO `config` VALUES 	(1,'site_title','SHOUTcast Client','Site title',1),
				(2,'web_addr','http://localhost/SHOUTcast','Site URL',1), 
				(3,'adm_email','yourmail@yourdomain.tld','Admin e-mail',1),
				(4,'sbd_path','/','Path to SHOUTcast Control Panel',1), 
				(5,'host_addr','127.0.0.1','Host address',1),
				(6,'start_portbase','8000','Starting portbase',2),
				(7,'nextport_method','low','Next port method',2),
				(8,'auto_start','on','Start on creation',2),
				(9,'sc_trans','/','AutoDJ Platform',3),
				(10,'media_path','/path/to/media','Path to MP3 files',3),	
				(11,'mrtg','off','MRTG statistics',4),
				(12,'mrtg_interval','5','MRTG update interval (minutes)',4),  
				(13, 'sc_serv', 'shoutcast/1.9.8-linux/sc_serv', 'Server for your platform', 2),
				(14, 'flashplayer', 'on', 'Media player in media playlist', 3),
				(15, 'media_url','http://<host address>/<media dir>', 'URL to MP3 files', 3),
        (16, 'license_key', '', 'License Key', 1),
        (17, 'api_enabled', '1', 'Enable API', 5),
        (18, 'lang','english','Site Language',1),
        (19, 'theme','default','Site Theme',1),
        (20, 'debugging', 'no', 'SMARTY Debugging',6),
        (21, 'caching', 'no', 'SMARTY Caching',6),
        (22, 'template_dir', 'templates', 'SMARTY Template Directory',6),
        (23, 'error_reporting', 'no', 'Show SMARTY Errors',6),
        (24, 'ftp_host','localhost', 'Hostname for FTP', 3),
        (25, 'ftp_port','21', 'Port for FTP (normally 21)', 3),
        (26, 'cpanel_hostname','localhost', 'Hostname for cPanel', 7),
        (27, 'cpanel_port','2083', 'Port number for cPanel', 7),
        (28, 'cpanel_username','username', 'Username for cPanel (A user with sufficient permissions)', 7),
        (29, 'cpanel_password','****', 'Password for cPanel User', 7),
        (30, 'cpanel_homedir','/home/username/', 'Home Directory of the current user (include trailing /)', 7),
        (31, 'cpanel_enabled','no', 'Enable cPanel Functionality', 7);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_groups`
--
DROP TABLE IF EXISTS `config_groups`;
CREATE TABLE `config_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Data for table `config_groups`
--

LOCK TABLES `config_groups` WRITE;
/*!40000 ALTER TABLE `config_groups` DISABLE KEYS */;
INSERT INTO `config_groups` VALUES 	(1,'General','General settings'),
					(2,'Server','Stream server settings'),
					(3,'AutoDJ','AutoDJ and media settings'),
					(4,'Graphs','Graphs'),
          (5,'API','API Settings'),
          (6,'Template','Template Options'),
          (7,'cPanel','cPanel Details');
/*!40000 ALTER TABLE `config_groups` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `config_sets`;
CREATE TABLE `config_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `configid` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Data for table `config_sets`
--

LOCK TABLES `config_sets` WRITE;
/*!40000 ALTER TABLE `config_sets` DISABLE KEYS */;
INSERT INTO `config_sets` VALUES 	(1,7,'high','Higher than last port used'),
					(2,7,'low','Lowest vacant port available'),
					(3,8,'on','Enabled'),
					(4,8,'off','Disabled'),
					(5,11,'on','Enabled'),
					(6,11,'off','Disabled'),
					(7,14,'on','Enabled'),
					(8,14,'off','Disabled'),
          (9,17,'1','Yes'),
          (10,17,'0','No'),
          (11,20,'yes','Yes'),
          (12,20,'no','No'),
          (13,21,'yes','Yes'),
          (14,21,'no','No'),
          (15,23,'yes','Yes'),
          (16,23,'no','No'),
          (17,31,'no','No'),
          (18,31,'yes','Yes');
/*!40000 ALTER TABLE `config_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `event_user` varchar(25) DEFAULT NULL,
  `event` text,
  `visible` int(1) DEFAULT '1',
  `timestamp` varchar(20) DEFAULT '00:00:00 0000-00-00',
  `ip` varchar(50),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `ftp`
--

CREATE TABLE IF NOT EXISTS `ftp` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(2555) NOT NULL,
  `password` varchar(255) NOT NULL,
  `PortBase` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `files` varchar(500) DEFAULT NULL,
  `song` varchar(250) NOT NULL DEFAULT '',
  `artist` varchar(250) NOT NULL DEFAULT '',
  `album` varchar(250) NOT NULL DEFAULT '',
  `year` int(25) NOT NULL DEFAULT '0',
  `comment` varchar(250) NOT NULL DEFAULT '',
  `genre` tinyint(3) unsigned DEFAULT '255',
  `port` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `user_id` int(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(65) NOT NULL DEFAULT '',
  `password` varchar(65) NOT NULL DEFAULT '',
  `fname` varchar(25) DEFAULT NULL,
  `lname` varchar(25) DEFAULT NULL,
  `email` varchar(75) DEFAULT NULL,
  `access` int(11) DEFAULT NULL,
  `serviceid` int(255) NOT NULL,
  `api` int(2) NOT NULL DEFAULT '0',
  `api_key` varchar(50) DEFAULT NULL,
  `skin` varchar(50) NOT NULL DEFAULT 'default',
  `2stepauth` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/*!40000 ALTER TABLE `members` DISABLE KEYS */;
LOCK TABLES `members` WRITE;
INSERT INTO `members` VALUES ('', 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', '', '', '', '5', '', '', '', 'default', NULL);

UNLOCK TABLES;
/*!40000 ALTER TABLE `members` ENABLE KEYS */;


--
-- Table structure for table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
CREATE TABLE `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(200) NOT NULL,
  `uid` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `playlist_content`
--

DROP TABLE IF EXISTS `playlist_content`;
CREATE TABLE `playlist_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `port` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `playlist_cache`
--

DROP TABLE IF EXISTS `playlist_cache`;
CREATE TABLE `playlist_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `current_fid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
 
--
-- Table structure for table `servers`
--

CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `created` varchar(50) DEFAULT NULL,
  `MaxUser` int(10) DEFAULT '32',
  `Password` varchar(25) DEFAULT 'changeme',
  `PortBase` int(5) DEFAULT '8000',
  `LogFile` varchar(50) DEFAULT 'sc_serv.log',
  `RealTime` int(1) DEFAULT '1',
  `ScreenLog` int(1) DEFAULT '1',
  `W3CEnable` varchar(3) DEFAULT 'yes',
  `W3CLog` varchar(25) DEFAULT 'sc_w3c.log',
  `SrcIP` varchar(15) DEFAULT 'ANY',
  `DestIP` varchar(15) DEFAULT 'ANY',
  `Yport` int(5) DEFAULT '80',
  `NameLookups` int(1) DEFAULT NULL,
  `RelayPort` int(5) DEFAULT NULL,
  `RelayServer` varchar(15) DEFAULT NULL,
  `AdminPassword` varchar(25) DEFAULT 'changeme',
  `AutoDumpUsers` int(1) DEFAULT NULL,
  `AutoDumpSourceTime` int(1) DEFAULT '30',
  `TitleFormat` varchar(50) DEFAULT NULL,
  `URLFormat` varchar(50) DEFAULT NULL,
  `PublicServer` varchar(25) DEFAULT 'default',
  `AllowRelay` varchar(3) DEFAULT 'Yes',
  `AllowPublicRelay` varchar(3) DEFAULT 'Yes',
  `MetaInterval` int(5) DEFAULT '32768',
  `ListenerTimer` int(10) DEFAULT NULL,
  `BanFile` varchar(25) DEFAULT NULL,
  `RipFile` varchar(25) DEFAULT NULL,
  `RIPOnly` varchar(3) DEFAULT NULL,
  `Unique` varchar(25) DEFAULT NULL,
  `CpuCount` int(2) DEFAULT NULL,
  `Sleep` varchar(5) DEFAULT NULL,
  `CleanXML` varchar(3) DEFAULT NULL,
  `ShowLastSongs` varchar(10) DEFAULT '10',
  `servername` varchar(50) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT '0',
  `listeners` int(10) DEFAULT NULL,
  `maxlisteners` int(10) DEFAULT NULL,
  `ownder` int(10) DEFAULT NULL,
  `owner` int(10) DEFAULT NULL,
  `autodj` int(11) NOT NULL DEFAULT '0',
  `Playlist` int(11) DEFAULT NULL,
  `random` tinyint(4) NOT NULL DEFAULT '0',
  `genre` varchar(250) NOT NULL DEFAULT '',
  `website` varchar(250) NOT NULL DEFAULT '',
  `bitrate` int(4) DEFAULT NULL,
  `serviceid` int(255) NOT NULL,
  `autodj_active` int(2) NOT NULL DEFAULT '0',
  `autodj_used_space` varchar(50) NOT NULL DEFAULT '0',
  `autodj_max_space` varchar(50) NOT NULL DEFAULT '0',
  `disabled` int(2) NOT NULL DEFAULT '0',
  `message_notification` longtext NOT NULL,
  `autodj_crossfadeMode` varchar(30) NOT NULL,
  `autodj_crossfadeseconds` int(10) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Table structure for table `sbd`
--

DROP TABLE IF EXISTS `sbd`;
CREATE TABLE `sbd` (
  `ver_id` tinyint(1) unsigned NOT NULL,
  `version` varchar(10) NOT NULL,
  PRIMARY KEY (`ver_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Inserting data for table `sbd`
--

LOCK TABLES `sbd` WRITE;
/*!40000 ALTER TABLE `sbd` DISABLE KEYS */;
INSERT INTO `sbd` VALUES(1,'1.0.0');
UNLOCK TABLES;

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
CREATE TABLE `genres` (
  `genreID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `genreTitle` varchar(250) NOT NULL,
  PRIMARY KEY (`genreID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;