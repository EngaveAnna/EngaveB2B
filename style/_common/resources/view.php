<?php
$view = new apm_Output_HTML_ViewHelper ( $AppUI );

?>
<div class="panel panel-default">
	<div class="panel-heading">
    <?php echo strlen($obj->resource_name) == 0 ? $AppUI->__('New resource') : $obj->resource_name; ?>
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
							<td class="apm-label"><?php echo $AppUI->_('Identifier'); ?></td>
							<td>
            <?php $view->showField('resource_key', $obj->resource_key); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Name'); ?></td>
							<td>
            <?php $view->showField('resource_name', $obj->resource_name); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Type'); ?></td>
							<td>
            <?php $view->showField('resource_type', $AppUI->_($types[$obj->resource_type])); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Percent Allocation'); ?></td>
							<td>
            <?php $view->showField('percent', $obj->resource_max_allocation); ?>
        </td>
						</tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
							<td>
            <?php $view->showField('resource_description', $obj->resource_description); ?>
        </td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- panel-body-->
</div>
<!-- panel-default -->