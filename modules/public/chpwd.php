<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

if (! ($user_id = apmgetParam ( $_REQUEST, 'user_id', 0 ))) {
	$user_id = $AppUI->user_id;
}

// check for a non-zero user id
if ($user_id) {
	$old_pwd = db_escape ( trim ( apmgetParam ( $_POST, 'old_pwd', null ) ) );
	$new_pwd1 = db_escape ( trim ( apmgetParam ( $_POST, 'new_pwd1', null ) ) );
	$new_pwd2 = db_escape ( trim ( apmgetParam ( $_POST, 'new_pwd2', null ) ) );
	
	$perms = &$AppUI->acl ();
	$canAdminEdit = canEdit ( 'system' );
	
	// has the change form been posted
	if ($new_pwd1 && $new_pwd2 && $new_pwd1 == $new_pwd2) {
		$user = new CUser ();
		
		if ($canAdminEdit || $user->validatePassword ( $user_id, $old_pwd )) {
			$user->load ( $user_id );
			$user->user_password = $new_pwd1;
			$result = $user->store ();
			
			if ($result) {
				?>
<script language="javascript" type="text/javascript">
                    window.onload = function() {
                        window.close();
		            }
                </script>
<?php
			} else {
				echo '<h1>' . $AppUI->_ ( 'Change User Password' ) . '</h1>';
				echo '<table class="std"><tr><td>' . $AppUI->_ ( 'chgpwUpdated' ) . '</td></tr></table>';
			}
		} else {
			echo '<h1>' . $AppUI->_ ( 'Change User Password' ) . '</h1>';
			echo '<table class="std"><tr><td>' . $AppUI->_ ( 'chgpwWrongPW' ) . '</td></tr></table>';
		}
	} else {
		
		$AppUI->getTheme ()->addFooterJavascriptFile ( 'js/passwordstrength.js' );
		
		?>
<style>
div[class="std titlebar"], form[name="frm_new"], body div:nth-child(2),
	div[class="left"] {
	display: none;
}
</style>
<script language="javascript" type="text/javascript">
		function submitIt() {
			var f = document.frmEdit;
			var msg = '';
		
			if (f.new_pwd1.value.length < <?php echo apmgetConfig('password_min_len'); ?>) {
		        	msg += "<?php echo $AppUI->_('chgpwValidNew', UI_OUTPUT_JS); ?>" + <?php echo apmgetConfig('password_min_len'); ?>;
					f.new_pwd1.focus();
			}
			if (f.new_pwd1.value != f.new_pwd2.value) {
				msg += "\n<?php echo $AppUI->_('chgpwNoMatch', UI_OUTPUT_JS); ?>";
				f.new_pwd2.focus();
			}
			if (msg.length < 1) {
				f.submit();
			} else {
				alert(msg);
			}
		}
		</script>
<h1><?php echo $AppUI->_('Change User Password'); ?></h1>

<form name="frmEdit" method="post" onsubmit="return false"
	accept-charset="utf-8">
	<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
	<table class="std">
				<?php if (!$canAdminEdit) { ?>
					<tr>
			<td class="apm-label"><?php echo $AppUI->_('Current Password'); ?></td>
			<td><input type="password" name="old_pwd" class="form-control" /></td>
		</tr>
				<?php } ?>
				<tr>
			<td class="apm-label"><?php echo $AppUI->_('New Password'); ?></td>
			<td><input type="password" name="new_pwd1" class="form-control"
				onKeyUp="checkPassword(this.value);" /></td>
		</tr>
		<tr>
			<td class="apm-label"><?php echo $AppUI->_('Repeat New Password'); ?></td>
			<td><input type="password" name="new_pwd2" class="form-control" /></td>
		</tr>
		<tr>
			<td class="apm-label"><?php echo $AppUI->_('Password Strength'); ?></td>
			<td>
				<div class="form-control" style="width: 135px;">
					<div id="progressBar"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="apm-label"><input type="button"
				value="<?php echo $AppUI->_('submit'); ?>" onclick="submitIt()"
				class="btn btn-default" /></td>
		</tr>
	</table>
</form>
<?php
	}
} else {
	echo '<h1>' . $AppUI->_ ( 'Change User Password' ) . '</h1>';
	echo '<table class="std"><tr><td>' . $AppUI->_ ( 'chgpwLogin' ) . '</td></tr></table>';
}
