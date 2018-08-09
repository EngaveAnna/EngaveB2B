<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

// check permissions
$perms = $AppUI->acl ();
if (! canEdit ( 'system' )) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$titleBlock = new apm_Theme_TitleBlock ( 'Custom field editor', 'customfields.png', $m );
$titleBlock->addCrumb ( '?m=system', 'system admin' );
$titleBlock->show ();

$manager = new apm_Core_CustomFieldManager ( $AppUI );
$modules = $manager->getModuleList ();

?>
<script language="javascript" type="text/javascript">
function delIt(field_id) {
	if (confirm( '<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS) . ' ' . $AppUI->_('Custom Field', UI_OUTPUT_JS) . '?'; ?>' )) {
        document.frmDelete.field_id.value = field_id;
		document.frmDelete.submit();
	}
}
</script>
<form name="frmDelete" action="./index.php?m=system&u=customfields"
	method="post" accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_customfield_aed" /> <input
		type="hidden" name="del" value="1" /> <input type="hidden"
		name="field_id" value="<?php echo $project_id; ?>" />
</form>
<table class="tbl list">
    <?php foreach ($modules as $module) { ?>
    <tr>
		<td colspan="10">
			<h2>
                <?php echo apmtoolTip($AppUI->_($module['mod_name']), $AppUI->_('Click this icon to Add a new Custom Field to this Module.'), true); ?>
                    <a
					href="?m=system&u=customfields&a=addedit&module=<?php echo $module['mod_id']; ?>">
					<img src="<?php echo apmfindImage('icons/edit_add.png'); ?>" alt=""><?php echo $AppUI->_($module['mod_name']); ?>
                    </a>
                <?php echo apmendTip(); ?>
            </h2>
		</td>
	</tr>
    <?php
					
					$fieldList = array (
							'field_name',
							'field_description',
							'field_htmltype',
							'field_published',
							'field_order' 
					);
					$fieldNames = array (
							'Name',
							'Description',
							'Type',
							'Published',
							'Order' 
					);
					
					$rows = $manager->getStructure ( $module ['mod_name'] );
					
					$htmlHelper = new apm_Output_HTMLHelper ( $AppUI );
					
					$s = '';
					if (count ( $rows )) {
						$s .= '<tr><th width="10"></th>';
						foreach ( $fieldNames as $index => $name ) {
							$s .= '<th>' . $AppUI->_ ( $fieldNames [$index] ) . '</th>';
						}
						$s .= '<th width="10"></th></tr>';
						
						foreach ( $rows as $row ) {
							$s .= '<tr><td class="hilite">';
							$s .= apmtoolTip ( '', $AppUI->_ ( 'Click this icon to Edit this Custom Field.' ), true );
							$s .= '<a href="?m=system&u=customfields&a=addedit&module=' . $module ['mod_id'] . '&field_id=' . $row ['field_id'] . '"><img src="' . apmfindImage ( 'icons/stock_edit-16.png' ) . '" /></a>';
							$s .= apmendTip ();
							$s .= $htmlHelper->createCell ( 'na', $row ['field_name'] );
							$s .= $htmlHelper->createCell ( 'field_description', $row ['field_description'] );
							
							$s .= '<td>' . $AppUI->_ ( $manager->getType ( $row ['field_htmltype'] ) ) . '</td>';
							$s .= '<td>' . ($row ['field_published'] ? $AppUI->_ ( 'Yes' ) : $AppUI->_ ( 'No' )) . '</td>';
							
							$s .= $htmlHelper->createCell ( 'field_order', $row ['field_order'] );
							$s .= '<td>';
							$s .= apmtoolTip ( '', $AppUI->_ ( 'Click this icon to Delete this Custom Field.' ), true );
							$s .= '<a href="javascript:delIt(' . $row ['field_id'] . ');"><img src="' . apmfindImage ( 'icons/stock_delete-16.png' ) . '" /></a>';
							$s .= apmendTip ();
							$s .= '</td></tr>';
						}
						echo $s;
					}
				}
				?>
</table>