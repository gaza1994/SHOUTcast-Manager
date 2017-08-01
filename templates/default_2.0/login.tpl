<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title>{$title} - {$lang.login}</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


				<!-- bootstrap 3.0.2 -->
				<link href="{$template_dir}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
				<!-- font Awesome -->
				<link href="{$template_dir}/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
				<!-- Theme style -->
				<link href="{$template_dir}/css/AdminLTE.css" rel="stylesheet" type="text/css" />
				<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
				<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
				<!--[if lt IE 9]>
					<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
					<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
				<![endif]-->
		<link rel="shortcut icon" type="image/x-icon" href="{$template_dir}/imgs/favicon.ico">
	</head>
	<body class="hold-transition login-page">

		<div class="login-box">
			<div class="login-logo">
				<a href="login.php">{$title}</a>
			</div><!-- /.login-logo -->
			<div class="login-box-body">
				<p class="login-box-msg">{$lang.signin}</p>
				<form action="login.php" name="form1" method="post">
			        {if not isset($2stepcheck)}
			        	<input type="hidden" name="Submit" value="Login" >

			            <div class="input-group">
			                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
			                <input type="text" class="form-control" id="username" name="username" placeholder="{$lang.username}">
			            </div>
			            <div class="input-group">
			                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
			                <input type="password" class="form-control" id="password" name="password" placeholder="{$lang.password}">
			            </div>         
			            <div class="form-group">
			            	{if isset($error)}
			                  <div class="callout callout-danger"><h4></h4><p>{$error}</p></div>
			                {/if}
			            </div>
			        {else}
			        	<input type="hidden" name="2stepusername" value="{$2stepusername}" >
			            <div class="input-group">
			                <span class="input-group-addon">Verification Code</span>
			                <input type="text" autocomplete="off" class="form-control" id="verifycode" name="verifycode" placeholder="Verification Code">
			            </div>
			            <script>
			            	document.addEventListener("DOMContentLoaded", function(event) { 
			            	  $( "#verifycode" ).focus();
			            	});
			            </script>
			        {/if}
					<div class="social-auth-links text-center">
						<button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
					</div>
				</form>
			</div><!-- /.login-box-body -->
		</div><!-- /.login-box -->

	</body>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	<script src="{$template_dir}/js/bootstrap.min.js" type="text/javascript"></script>
		</body>
</html>