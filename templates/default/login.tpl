<html class="bg-black">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>{$title} - {$lang.login}</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
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
	<body class="bg-black">
		<div class="form-box" id="login-box">
		    <div class="header">{$lang.signin}</div>
		    <form action="login.php" name="form1" method="post">
		        <div class="body bg-gray">
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
		        </div>
		        <div class="footer">                                                               
		            <button type="submit" class="btn bg-olive btn-block">{$lang.signmein}</button>  

					<!-- Next Update ...
					<div class="social-auth-links text-center">
						<p>- OR -</p>
						<a href="login/facebook" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
					</div>
					-->

				    <div class="margin text-center">
				        <span><a href="http://scottishbordersdesing.co.uk/">Scottish Borders Design</a> SHOUTcast Client</span>
				    </div>
		        </div>
		    </form>
		</div>
	</body>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	<script src="{$template_dir}/js/bootstrap.min.js" type="text/javascript"></script>

    </body>
</html>