<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// Include the file first of all, so that the AJAX methods are printed through xajax below
require apm_BASE_DIR . '/includes/ajax_functions.php';

$theme = $AppUI->getTheme ();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta name="Version" content="<?php echo $AppUI->getVersion(); ?>" />
<meta http-equiv="Content-Type"
	content="text/html;charset=<?php echo isset($locale_char_set) ? $locale_char_set : 'UTF-8'; ?>" />
<title><?php echo @apmgetConfig('page_title') . ' | ' . $AppUI->_($m); ?></title>
<link rel="stylesheet" type="text/css" href="./style/common.css"
	media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css"
	href="./style/<?php echo $theme; ?>/bootstrap/css/bootstrap.css"
	media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css"
	href="./style/<?php echo $theme; ?>/main.css" media="all"
	charset="utf-8" />
<script type="text/javascript" src="./lib/jquery/jquery-1.11.1.js"></script>
<script
	src="./style/<?php echo $theme; ?>/bootstrap/js/bootstrap.min.js"
	type="text/javascript"></script>
<script type="text/javascript" src="./js/rwd-table.js"></script>
<link rel="stylesheet" type="text/css" href="./style/rwd-table.min.css">
	<link rel="stylesheet" type="text/css"
		href="./style/<?php echo $theme; ?>/font-awesome/css/font-awesome.css"
		media="all" charset="utf-8" />
	<!-- <link rel="shortcut icon" href="./style/<?php echo $theme; ?>/faviconapm.ico" /> -->
        <?php
								if (isset ( $xajax ) && is_object ( $xajax )) {
									$xajax->printJavascript ( apm_BASE_URL . '/lib/xajax' );
								}
								?>
        <?php $AppUI->getTheme()->loadHeaderJS(); ?>



</head>
<body onload="this.focus();">
<?php $perms = &$AppUI->acl(); ?>
<div class="sysheader">

    <?php if ($AppUI->user_id > 0) { ?>

    <div class="row">
			<div class="col-lg-4 brand-img left">
				<img src="style/<?php echo $theme; ?>/images/logo_mid.png" />
			</div>

			<!-- Add the extra clearfix for only the required viewport -->
			<div class="clearfix visible-xs-block"></div>
			<div class="col-lg-8 right">
				<ul>

					<!-- Add the extra clearfix for only the required viewport -->
					<div class="clearfix visible-xs-block"></div>
					<li class="userlogin">
					<?php $loggedin = ($AppUI->user_id > 0) ? $AppUI->user_display_name : $outsider;
					// echo '<div class="panel panel-default"><div class="panel-heading arrow ar-right"><span class="logged"><p style="float:left;">' . $AppUI->_ ( 'Logged in' ) . ':&nbsp;</p><p style="font-weight: bold; float:left; ">' . $loggedin . '</p></span></div></div>';
					echo '<span class="logged"><p style="float:left; margin:0px;">' . $AppUI->_ ( 'Logged in' ) . ':&nbsp;</p><p style="font-weight: bold; float:left; margin:0px;">' . $loggedin . '</p></span>';
					?>
					</li>
					<!-- Add the extra clearfix for only the required viewport -->
					<div class="clearfix visible-xs-block"></div>
					<li>
<?php
					if (! empty ( $AppUI->user_icon )) {
						echo '<a href="./index.php?m=users&a=view&user_id=' . $AppUI->user_id . '" title="' . $AppUI->_ ( 'My Info' ) . '" class="avatar" type="button" data-toggle="tooltip" data-placement="bottom" role="button">';
						echo '<div></div></a>';
					} else {
						?>
					<a
						href="./index.php?m=users&amp;a=view&amp;user_id=<?php echo $AppUI->user_id; ?>"
						title="<?php echo $AppUI->_('My Info'); ?>"
						class="btn btn-info header-btn" type="button"
						data-toggle="tooltip" role="button"> <span
							class="glyphicon glyphicon-user"></span>
					</a> 
<?php } ?>
					<a href="./index.php?logout=-1" class="btn btn-warning header-btn"
						type="button" role="button" data-toggle="tooltip"
						data-placement="bottom" title="<?php echo $AppUI->_('Logout'); ?>"
						style="margin: 12px 3px;"> <span class="glyphicon glyphicon-off"></span>
					</a>
					</li>
				</ul>

			</div>
		</div>
		<?php } ?>
</div>

	<div class="nav-container col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<form name="frm_new" method="get" action="./index.php"
			accept-charset="utf-8">
			<input type="hidden" name="a" value="addedit" />
            <?php
												// build URI string
												if (isset ( $company_id )) {
													echo '<input type="hidden" name="company_id" value="' . $company_id . '" />';
												}
												if (isset ( $task_id )) {
													echo '<input type="hidden" name="task_parent" value="' . $task_id . '" />';
												}
												if (isset ( $file_id )) {
													echo '<input type="hidden" name="file_id" value="' . $file_id . '" />';
												}
												?>
    
            
		<nav class="navbar navbar-default" role="navigation"> <!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target="#bs-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>

			</div>

			<div class="navbar-inner">
				<div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
                    <?php echo $theme->buildHeaderNavigation('ul', 'li', '', 'nav navbar-nav',''); ?>
                    </div>
			</div>
			</nav>
		</form>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <?php
																				echo $theme->messageHandler ();
																				$AppUI->boxTopRendered = false;
																				if ($m == 'help') {
																				}
//TODO: Basically this entire file is exactly the same as the other two header.php files in core apmProject.. - caseydk 2012-07-01