<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

global $AppUI, $tasks, $priorities;
global $m, $a, $date, $min_view, $other_users, $showPinned, $showArcProjs, $showHoldProjs, $showDynTasks, $showLowTasks, $showEmptyDate, $user_id;
$perms = &$AppUI->acl ();
$canDelete = $perms->checkModuleItem ( $m, 'delete' );
?>
<form name="form_buttons" method="post"
	action="index.php?<?php echo 'm=' . $m . '&a=' . $a . '&date=' . $date; ?>"
	accept-charset="utf-8">
	<input type="hidden" name="show_form" value="1" />
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="clear:both;">	
	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('User'); ?></td>
	</tr>
	</thead>
	<tbody>	
	<tr><td class="apm-label"><?php echo $AppUI->_('Select user'); ?></td><td>
                <?php if ($other_users) { $selectedUser = apmgetParam ( $_POST, 'show_user_todo', $AppUI->user_id );
				$users = $perms->getPermittedUsers ( 'tasks' );
				echo arraySelect ( $users, 'show_user_todo', 'class="form-control" onchange="document.form_buttons.submit()"', $selectedUser );	}?>
            </td>
		</tr>
		
</tbody></table></div>
</div>
</form>
<?php
$min_view = true;
include apm_BASE_DIR . '/modules/tasks/viewgantt.php';