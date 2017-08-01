<html>
	<head>
		<title>{$title}</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="shortcut icon" href="{$template_dir}/imgs/favicon.ico">
        <!-- bootstrap 3.0.2 -->
        <link href="{$template_dir}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="{$template_dir}/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="{$template_dir}/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="{$template_dir}/css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="{$template_dir}/css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- fullCalendar -->
        <link href="{$template_dir}/css/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="{$template_dir}/css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="{$template_dir}/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- CSS Loaders -->
        <link  href="{$template_dir}css/loaders.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="{$template_dir}/css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{$template_dir}/css/skins/_all-skins.min.css">
        <script src="{$web_addr}/include/jquery.js"></script>
        {if $menu_selected  eq 'schedule' || $menu_selected  eq 'dj' || $menu_selected eq 'media'}
            <link href="{$template_dir}/css/timepicker/bootstrap-timepicker.min.css" rel="stylesheet"/>
        {/if}
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    	{literal}
		<script type="text/javascript">
			function go_there() {
			    var where_to = confirm("{/literal}{$lang.deleteconfirm}{literal}");
			    if (where_to == true) {
			        window.location = "{/literal}{$web_addr}{literal}/delete.php";
			    } else {
			        window.location = "{/literal}{$web_addr}{literal}/home.php";
			    }
			}
			function updateClock() {
			    var currentTime = new Date();
			    var currentHours = currentTime.getHours();
			    var currentMinutes = currentTime.getMinutes();
			    var currentSeconds = currentTime.getSeconds();
			    // Pad the minutes and seconds with leading zeros, if required
			    currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
			    currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
			    // Choose either "AM" or "PM" as appropriate
			    var timeOfDay = (currentHours < 12) ? "AM" : "PM";
			    // Convert the hours component to 12-hour format if needed
			    currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
			    // Convert an hours component of "0" to "12"
			    currentHours = (currentHours == 0) ? 12 : currentHours;
			    // Compose the string for display
			    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
			    // Update the time display
			    document.getElementById("clock").firstChild.nodeValue = currentTimeString;
			}
			$(document).ready(function() {
			    jQuery(".serid").hover(function() {
			        $(".popup").fadeIn("fast");
			    }, function() {
			        $(".popup").fadeOut("slow");
			    });
			});
			$(document).ready(function() {
			    jQuery('#rowclick tr')
			        .filter(':has(:checkbox:checked)')
			        .addClass('selected')
			        .end()
			        .click(function(event) {
			            $(this).toggleClass('selected');
			            if (event.target.type !== 'checkbox') {
			                $(this).toggleClass('selected');
			                $(':checkbox', this).trigger('click');
			            }
			        });
			});
			var shoutcastjquery = jQuery.noConflict();
		</script>
    	{/literal}
    </head>
    <body class="hold-transition skin-blue sidebar-mini" onload="updateClock(); setInterval('updateClock()', 1000 );">
    	<header class="main-header">
			<a href="{$web_addr}/home.php" class="logo">
				{$lang.title}
			</a>
			{include file="head-menu.tpl"}
		</header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="{$getgravatar}" class="img-circle" alt="User Image" />
                        </div>
						<div class="pull-left info">
							<p>{$lang.hello}, {$client.fname}</p>
                            <a href="#"><i class="fa fa-circle text-success"></i> {$lang.active}</a>
                        </div>
                    </div>
                    {include file="menu.tpl"}
                </section>
                <!-- /.sidebar -->
            </aside>
            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">