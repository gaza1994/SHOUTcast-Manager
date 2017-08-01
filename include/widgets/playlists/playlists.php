<?php
switch ( $_GET['type'] )
{
	case 'pls':
		header("Content-Type: audio/x-scpls");
		header('Content-Disposition: attachment; filename="listen.pls"');
		break;
	case 'itunes.pls':
		header("Content-Type: audio/x-scpls");
		header('Content-Disposition: attachment; filename="listen.pls"');
		break;
	case 'raw':
		header("Content-Type: audio/x-pn-realaudio");
		header('Content-Disposition: attachment; filename="listen.ram"');
		break;
	case 'asx':
		header("Content-Type: video/x-ms-asf");
		header('Content-Disposition: attachment; filename="listen.asx"');
		break;
}
include('../../functions.inc.php');
$config = settings();
$server = $config['host_addr'];
$port = $_GET['port'];
$type = $_GET['type'];
		switch ( $type )
		{
			case 'pls':
				$structure	=	"[playlist]\nNumberOfEntries=1\nFile1=http://{$server}:{$port}/";
				header("Content-Type: audio/x-scpls");
				header('Content-Disposition: attachment; filename="listen.pls"');
				echo $structure;
				exit();
				break;
			case 'itunes.pls':
				$structure	=	"[playlist]\nNumberOfEntries=1\nFile1=http://{$server}:{$port}/";
				header("Content-Type: audio/x-scpls");
				header('Content-Disposition: attachment; filename="listen.pls"');
				echo $structure;
				exit();
				break;
			case 'raw':
				$structure	=	"http://{$server}:{$port}/listen.pls";
				header("Content-Type: audio/x-pn-realaudio");
				header('Content-Disposition: attachment; filename="listen.ram"');
				echo $structure;
				exit();
				break;
			case 'asx':
				$structure	=	"<asx version=\"3.0\" bannerbar=\"fixed\">\n<Title>{$config['site_title']}</Title>\n<abstract>{$config['site_title']}</abstract>\n<copyright>{$config['web_addr']}</copyright>\n<moreinfo href=\"{$config['web_addr']}\"/>\n<abstract>{$config['site_title']}</abstract>\n<moreinfo target=\"_blank\" href=\"{$config['web_addr']}\"/></banner>\n<Entry>\n<Title>{$config['site_title']}</Title>\n<copyright>{$config['web_addr']}</copyright>\n<ref href=\"http://{$server}:{$port}\"/>\n</Entry>\n</ASX>";
				header("Content-Type: video/x-ms-asf");
				header('Content-Disposition: attachment; filename="listen.asx"');
				echo $structure;
				exit();
				break;
		}
?>