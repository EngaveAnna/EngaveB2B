<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo remove database query

// check permissions
$perms = &$AppUI->acl ();
$canEdit = canEdit ( 'system' );
$canRead = canView ( 'system' );
if (! $canRead) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$module = new apm_System_Module ();

$hidden_modules = array (
		'public',
		'install' 
);

$modules = __extract_from_modules_index ( $hidden_modules );
// get the modules actually installed on the file system
$loader = new apm_FileSystem_Loader ();
$modFiles = $loader->readDirs ( 'modules' );

$titleBlock = new apm_Theme_TitleBlock ( 'Modules', 'power-management.png', $m );
$titleBlock->addCrumb ( '?m=system', 'System Admin' );
$titleBlock->show ();

$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );
$listTable = new apm_Output_ListTable ( $AppUI );

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<?php echo $AppUI->_('Select a module to upload'); ?>
    </div>
	<div class="panel-body">
		<tr>
			<td colspan="10" style="text-align: center;">
				<form action="./index.php?m=system&u=modules" method="post"
					enctype="multipart/form-data">
					<input type="hidden" name="dosql" value="do_module_upload" /> <input
						type="file" name="module_upload" size="50" maxlength="1000000"
						class="form-control" />
                <?php if (is_writable(apm_BASE_DIR.'/files')) { ?>
                <input type="submit"
						value="<?php echo $AppUI->_('Upload'); ?>" class="btn btn-info" />
                <?php } else { ?>
                    <span class="error">
                        <?php echo $AppUI->_('Module uploads are not allowed. Please check permissions on the /modules directory.'); ?>
                    </span>
                <?php } ?>
            </form>
			</td>
		</tr>
	</div>
</div>

<?php

echo $listTable->startTable ();

$fieldList = array (
		'mod_name',
		'mod_active',
		'mod_type',
		'mod_version',
		'mod_ui_name',
		'mod_ui_active',
		'mod_ui_order' 
);
// APMoff
/*
 * $fieldList = array (
 * 'mod_name',
 * 'mod_active',
 * 'mod_customize',
 * 'mod_type',
 * 'mod_version',
 * 'mod_ui_name',
 * 'mod_ui_icon',
 * 'mod_ui_active',
 * 'mod_ui_order'
 * );
 * $fieldNames = array (
 * 'Module',
 * 'Status',
 * 'Customize',
 * 'Type',
 * 'Version',
 * 'Menu Text',
 * 'Menu Icon',
 * 'Menu Status',
 * 'Order'
 * );
 * $fieldPriorities = array (
 * 1,
 * 1,
 * 4,
 * 4,
 * 6,
 * 1,
 * 6,
 * 1,
 * 6
 * );
 *
 */

$fieldNames = array (
		'Module',
		'Status',
		'Type',
		'Version',
		'Menu Text',
		'Menu Status',
		'Order' 
);
$fieldPriorities = array (
		1,
		1,
		1,
		1,
		1,
		1,
		1 
);

foreach ( $fieldList as $key => $field ) {
	$fields [] = array (
			'module_config_value' => $field,
			'module_config_text' => $fieldNames [$key],
			'module_config_priority' => $fieldPriorities [$key] 
	);
}

echo $listTable->buildHeader ( $fields, false, $m );

// do the modules that are installed on the system
foreach ( $modules as $row ) {
	// clear the file system entry
	if (isset ( $modFiles [$row ['mod_directory']] )) {
		$modFiles [$row ['mod_directory']] = '';
	}
	$query_string = '?m=' . $m . '&u=' . $u . '&a=domodsql&mod_id=' . $row ['mod_id'];
	$s = '';
	
	$s .= $htmlHelper->createCell ( 'na', $row ['mod_name'] );
	$s .= '<td>';
	
	if ($canEdit) {
		
		$s .= '<a href="' . $query_string . '&cmd=toggle&">';
	}
	
	if ($row ['mod_active']) {
		$eye = 'open';
		$css = 'default';
	} else {
		$eye = 'close';
		$css = 'danger';
	}
	$s .= '<label class="status-label btn btn-xs btn-' . $css . '"><span class="glyphicon glyphicon-eye-' . $eye . '"></span></label>';
	
	$s .= ($row ['mod_active'] ? $AppUI->_ ( 'active' ) : $AppUI->_ ( 'disabled' ));
	if ($canEdit) {
		$s .= '</a>';
	}
	if ($row ['mod_type'] != 'core' && $canEdit) {
		$s .= ' | <a href="' . $query_string . '&cmd=remove" onclick="return window.confirm(' . "'" . $AppUI->_ ( 'This will delete all data associated with the module!' ) . "\\n\\n" . $AppUI->_ ( 'Are you sure?' ) . "\\n" . "'" . ');">' . $AppUI->_ ( 'remove' ) . '</a>';
	}
	
	// check for a setup file
	$ok = file_exists ( apm_BASE_DIR . '/modules/' . $row ['mod_directory'] . '/setup.php' );
	if ($ok) {
		include apm_BASE_DIR . '/modules/' . $row ['mod_directory'] . '/setup.php';
		
		// check for upgrades
		// APMoff
		/*
		 * if (version_compare ( $config ['mod_version'], $row ['mod_version'] ) == 1 && $canEdit) {
		 * $s .= ' | <a href="' . $query_string . '&cmd=upgrade" onclick="return window.confirm(' . "'" . $AppUI->_ ( 'Are you sure?' ) . "'" . ');" >' . $AppUI->_ ( 'upgrade' ) . '</a>';
		 * }
		 */
		// check for configuration
		// APMoff
		/*
		 * if (isset ( $config ['mod_config'] ) && $config ['mod_config'] == true && $canEdit) {
		 * $s .= ' | <a href="' . $query_string . '&cmd=configure">' . $AppUI->_ ( 'configure' ) . '</a>';
		 * }
		 */
	}
	$s .= '</td>';
	
	/*
	 * $s .= '<td>';
	 * $views = $module->getCustomizableViews ( $row ['mod_directory'] );
	 * if (count ( $views )) {
	 * // TODO: Should we have a 'reset to default' for each of these?
	 * foreach ( $views as $view ) {
	 * $s .= '<a href="?m=system&u=modules&a=addedit&mod_id=' . $row ['mod_id'] . '&v=' . $view . '">';
	 * $s .= $view;
	 * $s .= '</a><br />';
	 * }
	 * }
	 * $s .= '</td>';
	 */
	$s .= $htmlHelper->createCell ( 'na', $row ['mod_type'] );
	$s .= $htmlHelper->createCell ( 'na', $row ['mod_version'] );
	$s .= $htmlHelper->createCell ( 'na', $row ['mod_ui_name'] );
	// $s .= $htmlHelper->createCell ( 'mod_ui_icon', $row ['mod_ui_icon'] );
	
	$s .= '<td class="data _status">';
	
	if ($canEdit) {
		$s .= '<a href="' . $query_string . '&cmd=toggleMenu">';
	}
	
	if ($row ['mod_ui_active']) {
		$eye = 'open';
		$css = 'default';
	} else {
		$eye = 'close';
		$css = 'danger';
	}
	$s .= '<label class="status-label btn btn-xs btn-' . $css . '"><span class="glyphicon glyphicon-eye-' . $eye . '"></span></label>';
	
	$s .= ($row ['mod_ui_active'] ? $AppUI->_ ( 'visible' ) : $AppUI->_ ( 'hidden' ));
	if ($canEdit) {
		$s .= '</a>';
	}
	$s .= '</td>';
	
	$s .= $htmlHelper->createCell ( '_count', $row ['mod_ui_order'] );
	
	echo '<tr>' . $s . '</tr>';
}

foreach ( $modFiles as $v ) {
	// clear the file system entry
	if ($v == 'admin' || $v == 'calendar') {
		continue;
	}
	if ($v && ! in_array ( $v, $hidden_modules )) {
		$s = '';
		$s .= '<td>' . $AppUI->_ ( $v ) . '</td>';
		$s .= '<td>';
		
		if ($row ['mod_ui_active']) {
			$eye = 'cog';
			$css = 'danger';
		} else {
			$eye = 'close';
			$css = 'cog';
		}
		$s .= '<label class="status-label btn btn-xs btn-' . $css . '"><span class="glyphicon glyphicon-' . $eye . '"></span></label>';
		
		if ($canEdit) {
			$s .= '<a href="?m=' . $m . '&u=modules&a=domodsql&cmd=install&mod_directory=' . $v . '">';
		}
		$s .= $AppUI->_ ( 'install' );
		if ($canEdit) {
			$s .= '</a>';
		}
		$s .= '</td>';
		echo '<tr>' . $s . '</tr>';
	}
}

echo $listTable->endTable ();
?>

