<?php
$view = new apm_Output_HTML_ViewHelper ( $AppUI );

?>
<div class="panel panel-default">
	<div class="panel-heading">
    <?php echo strlen($object->dept_name) == 0 ? $AppUI->__('New department') : $department->dept_name; ?>
    </div>
	<div class="panel-body">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="table-responsive">
				<table cellspacing="1" cellpadding="2" border="0" width="100%"
					class="table table-bordered table-striped table-static">
					<thead>
						<tr>
							<td colspan="2"><span
								class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Details'); ?></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Name'); ?></td>
							<td>
            <?php $view->showField('dept_name', $department->dept_name); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Company'); ?></td>
							<td>
            <?php $view->showField('dept_company', $department->dept_company); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Owner'); ?></td>
							<td>
            <?php $view->showField('dept_owner', $department->dept_owner); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Type'); ?></td>
							<td>
            <?php $view->showField('dept_type', $types[$department->dept_type]); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Email'); ?></td>
							<td>
            <?php $view->showField('dept_email', $department->dept_email); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Phone'); ?></td>
							<td>
            <?php $view->showField('dept_phone', $department->dept_phone); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Fax'); ?></td>
							<td>
            <?php $view->showField('dept_fax', $department->dept_fax); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Address'); ?></td>
							<td>
            <?php $view->showAddress('dept', $department); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('URL'); ?></td>
							<td>
            <?php $view->showField('dept_url', $department->dept_url); ?>
        </td>
						</tr>

						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
							<td>
            <?php $view->showField('dept_desc', $department->dept_desc); ?>
        </td>
						</tr>
        <?php
        $custom_fields = new apm_Core_CustomFields($m, $a, $department->dept_id, 'view');
        $custom_fields->printHTML();
        ?>


	</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- panel-body-->
</div>
<!-- panel-default -->