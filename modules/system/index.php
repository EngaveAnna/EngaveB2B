<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
$perms = &$AppUI->acl ();
if (! canView ( 'system' )) { // let's see if the user has sys access
	$AppUI->redirect ( ACCESS_DENIED );
}
// TODO: fix the capitalization of 'system admin' vs 'System Admin' throughout this module

$titleBlock = new apm_Theme_TitleBlock ( 'System Administration', 'icon.png', $m );
$titleBlock->show ();

$fields = array (
		0 => array (
				'module_config_value' => 'system_feature_name',
				'module_config_text' => 'Feature',
				'module_config_priority' => 1 
		),
		1 => array (
				'module_config_value' => 'feature_desc',
				'module_config_text' => 'Description',
				'module_config_priority' => 1 
		) 
);

$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );
$listTable = new apm_Output_ListTable ( $AppUI );
echo $listTable->startTable ();
echo $listTable->buildHeader ( $fields, false, $m );
?>


<tr>
	<td><a href="?m=system&a=systemconfig"><?php echo $AppUI->_('System Configuration'); ?></a>
	</td>
	<td>
		<?php echo $AppUI->_('System Status'); ?>
    </td>
</tr>
<tr>
	<td><a href="?m=system&a=addeditpref"><?php echo $AppUI->_('Default User Preferences'); ?></a>
	</td>
	<td>
		<?php echo $AppUI->_('Preferences'); ?>
    </td>
</tr>

<?php
 /*echo '
 <tr>
 <td><a href="?m=system&u=syskeys&a=keys">'.$AppUI->_('System Lookup Keys').'</a></td>
 <td>'.$AppUI->_('Preferences').'</td>
 </tr>
*/ 
/* echo '
 <tr>
 <td><a href="?m=system&u=syskeys">'.$AppUI->_('System Lookup Values').'</a></td>
 <td>'.$AppUI->_('Preferences').'</td>
 </tr>';*/

/*
 echo '
 <tr>
 <td><a href="?m=system&u=customfields">'.$AppUI->_('Custom Field Editor').'</a></td>
 <td>'.$AppUI->_('Preferences').'</td>
 </tr>';*/
?>



<tr>
	<td><a href="?m=system&u=modules"><?php echo $AppUI->_('View Modules'); ?></a>
	</td>
	<td>
		<?php echo $AppUI->_('Modules'); ?>
    </td>
</tr>


<?php
/*
 * echo '<tr>
 * <td><a href="?m=system&a=translate">'.$AppUI->_('Translation Management').'</a>
 * </td>
 * <td>'.$AppUI->_('Language Support').'</td>
 * </tr>';
 */
?>

<tr>
	<td><a href="?m=system&u=roles"><?php echo $AppUI->_('User Roles'); ?></a>
	</td>
	<td>
		<?php echo $AppUI->_('Administration'); ?>
    </td>
</tr>

  <?php /*echo '
  <tr>
  <td>
  <a href="?m=system&a=acls_view">'.$AppUI->_('Users Permissions Information').'</a>
  </td>
  <td>
  '.$AppUI->_('Administration').'
  </td>
  </tr>';*/
	?>
  
  <?php
/*
 * <tr>
 * <td>
 * <a href="?m=system&a=phpinfo&suppressHeaders=1" target="_blank">'.$AppUI->_('PHP Info').'</a>
 * </td>
 * <td>
 * '.$AppUI->_('Administration').'
 * </td>
 * </tr>
 */


echo '
  <tr>
  <td>
  <a href="?m=system&a=billingcode">'.$AppUI->_('Billing Code Table').'</a>
  </td>
  <td>
  '.$AppUI->_('Budgeting Setup').'
  </td>
  </tr>
 
  <tr>
  <td>
  <a href="?m=system&a=budgeting">'.$AppUI->_('Setup Budgets').'</a>
  </td>
  <td>
  '.$AppUI->_('Budgeting Setup').'
  </td>
  </tr>';
 
?>


</div>
</div>
<?php

echo $listTable->endTable ();
?>
