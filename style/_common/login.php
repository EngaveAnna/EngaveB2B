<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly' );
}

$theme = $AppUI->getTheme ();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $apmconfig['page_title']; ?></title>
<meta http-equiv="Content-Type"
	content="text/html;charset=<?php echo isset($locale_char_set) ? $locale_char_set : 'UTF-8'; ?>" />
<title><?php echo $apmconfig['company_name']; ?> :: <?php echo $AppUI->_('Login form');?></title>
<meta http-equiv="Pragma" content="no-cache" />
<meta name="Version" content="<?php echo $AppUI->getVersion(); ?>" />
<link rel="stylesheet" type="text/css" href="./style/login.css"
	media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="./style/common.css"
	media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css"
	href="./style/<?php echo $uistyle; ?>/main.css" media="all"
	charset="utf-8" />
<style type="text/css" media="all">
@import "./style/<?php echo $uistyle; ?>/main.css";
</style>
<script type="text/javascript" src="./lib/jquery/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" type="text/css"
	href="./style/<?php echo $theme; ?>/bootstrap/css/bootstrap.css"
	media="all" charset="utf-8" />
<script
	src="./style/<?php echo $theme; ?>/bootstrap/js/bootstrap.min.js"
	type="text/javascript"></script>
</head>

<body bgcolor="#f0f0f0" onload="document.loginform.username.focus();">

	<div class="row">
		<div class="text-center" style="margin: 20px auto;">
			<a href="http://www.softmag.eu"><img
				src="./style/<?php echo $uistyle; ?>/images/apm_logo_md.png" alt="" /></a>
		</div>
	</div>
	<div style="width: 100%;" class="row">
		<div
			class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1 col-xs-12">
			<div id="loginbox" style="margin-top: 40px;" class="mainbox">
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title"><?php echo $AppUI->_('Login'); ?></div>
						<div style="float: right; position: relative; top: -10px">
							<a href="#"
								onClick="$('#loginbox').hide(); $('#signupbox').show()"><?php echo $AppUI->_('Forgot Password'); ?></a>
						</div>
					</div>

					<div style="padding-top: 30px" class="panel-body">
					<?php echo $AppUI->getMsg();?>

					<form id="loginform" class="form-horizontal" role="form"
							method="post" action="<?php echo $loginFromPage; ?>"
							name="loginform" accept-charset="utf-8">

							<input type="hidden" name="login" value="<?php echo time(); ?>" />
							<input type="hidden" name="lostpass" value="0" /> <input
								type="hidden" name="redirect" value="<?php echo $redirect; ?>" />


							<div style="margin-bottom: 25px" class="input-group">
								<span class="input-group-addon"><i
									class="glyphicon glyphicon-user"></i></span> <input
									id="username" type="text" class="form-control" name="username"
									value="" placeholder="<?php echo $AppUI->_('Username'); ?>">
							
							</div>

							<div style="margin-bottom: 25px" class="input-group">
								<span class="input-group-addon"><i
									class="glyphicon glyphicon-lock"></i></span> <input
									id="password" type="password" class="form-control"
									name="password"
									placeholder="<?php echo $AppUI->_('Password'); ?>">
							
							</div>



							<div class="input-group">
								<div class="checkbox">
									<label> <input id="login-remember" type="checkbox"
										name="remember" value="1"> <?php echo $AppUI->_('Remember Me'); ?>
                                        </label>
								</div>
							</div>


							<div style="margin-top: 10px" class="form-group">
								<!-- Button -->

								<div class="col-sm-12 controls">

									<input type="submit" id="btn-login" name="login"
										class="btn btn-success"
										value="<?php echo $AppUI->_('login'); ?>">
								
								</div>
							</div>



							<div class="form-group">
								<div class="col-md-12 control">
									<div style="border-top: 1px solid #888; padding-top: 15px;">
                                            <?php echo $AppUI->_('Do not have an account'); ?>
                                            
<!--                                          <a href="#" nClick="$('#loginbox').hide(); $('#signupbox').show()"> -->
                                             <?php // echo $AppUI->_('Signup'); ?> 
<!--                                         </a> -->
										<a href="javascript: void(0);"
											onclick="javascript:window.location=''"><?php echo $AppUI->_('Signup'); ?></a>
									</div>
								</div>
							</div>
						</form>



					</div>
				</div>
			</div>
			<div id="signupbox" style="display: none; margin-top: 50px"
				class="mainbox">
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title"><?php echo $AppUI->_('forgotPassword'); ?></div>
						<div
							style="float: right; font-size: 85%; position: relative; top: -10px">
							<a id="signinlink" href="#"
								onclick="$('#signupbox').hide(); $('#loginbox').show()"><?php echo $AppUI->_('Login'); ?></a>
						</div>
					</div>
					<div class="panel-body">
										<?php echo $AppUI->getMsg();?>
					
						<form id="signupform" method="post"
							action="<?php echo $loginFromPage; ?>" accept-charset="utf-8"
							class="form-horizontal" role="form">
							<input type="hidden" name="lostpass" value="1" /> <input
								type="hidden" name="redirect"
								value="<?php echo isset($redirect) ? $redirect : ''; ?>" />


							<div class="form-group">
								<label for="email" class="col-xs-2 control-label"><?php echo $AppUI->_('Username'); ?></label>
								<div class="col-xs-9">
									<input type="text" class="form-control" name="login">
								
								</div>
							</div>

							<div class="form-group">
								<label for="firstname" class="col-xs-2 control-label"><?php echo $AppUI->_('Email'); ?></label>
								<div class="col-xs-9">
									<input type="password" class="form-control" name="password">
								
								</div>
							</div>

							<div class="form-group">
								<!-- Button -->
								<div class="col-xs-12">
									<input type="submit" id="btn-signup" name="login"
										class="btn btn-success"
										value="<?php echo $AppUI->_('Send password'); ?>">
								
								</div>
							</div>





						</form>
					</div>
				</div>




			</div>
		</div>
	</div>



	<div class="row">

		<div class="text-center">
			<p>
				<span><?php echo $AppUI->_('Version'); ?> 3.6.0</span>
			</p>
		</div>
	</div>





<div class="row footer-cr">
<a href="http://engave.pl/engave-realizuje-projekt-b2b/">
<img class="footer-cr" src="./style/classic/images/logo_ue.png">
</a>
</div>	
	







	<div style="float: left;">
            <?php
												
												$msg = '';
												$msg .= (version_compare ( PHP_VERSION, MIN_PHP_VERSION, '<' )) ? '<br /><span class="warning">WARNING: apmProject is NOT SUPPORT for this PHP Version (' . PHP_VERSION . ')</span>' : '';
												$msg .= function_exists ( 'mysql_pconnect' ) ? '' : '<br /><span class="warning">WARNING: PHP may not be compiled with MySQL support.  This will prevent proper operation of apmProject.  Please check you system setup.</span>';
												echo $msg;
												?>
        </div>
</body>
</html>