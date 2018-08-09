<?php
$view = new apm_Output_HTML_ViewHelper ( $AppUI );
?>
    
   <div class="panel panel-default">
	<div class="panel-heading"><?php echo $AppUI->_('Valuation').': '.$object->valuation_name; ?>
    </div>
		<div class="panel-body">

			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Basic information'); ?></td>
							</tr>
						</thead>

						<tbody>
					
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation title'); ?></td>
								<td><?php echo $view->showField('valuation_name', $object->valuation_name); ?></td>
							</tr>
            				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation type'); ?></td>
								<td><?php echo $view->showField('valuation_category', $valuationType[$object->valuation_category]); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation owner'); ?></td>
								<td><?php echo $view->showField('valuation_owner2', $users[$object->valuation_owner]); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation date'); ?></td>
								<td><?php echo $view->showField('valuation_date', $object->valuation_date); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation project'); ?></td>
								<td><?php echo $view->showField('valuation_project', $object->valuation_project); ?></td>
							</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Description'); ?></td>
								<td><?php echo $view->showField('valuation_desc', $object->valuation_desc); ?></td>
							</tr>

						</tbody>
					</table>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<?php if($object->valuation_project) {?>
				<div class="table-responsive">
					<table cellspacing="1" cellpadding="2" border="0" width="100%"
						class="table table-bordered table-striped table-static">
						<thead>
							<tr>
								<td colspan="2"><span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Valuation'); ?></td>
							</tr>
						</thead>

						<tbody>
               				<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation days'); ?></td>
								<td><?php echo number_format($object->valuation_days, 2, ',', '');?></td>
               				</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation real days'); ?></td>
								<td><?php echo number_format($object->valuation_real_days, 2, ',', '');?></td>
               				</tr>																			
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation amount'); ?></td>
								<td><?php echo number_format( $object->valuation_amount, 2, ',', '').' '.$apmconfig['currency_symbol'];?></td>
               				</tr>
							<tr>
								<td class="apm-label"><?php echo $AppUI->_('Valuation real amount'); ?></td>
								<td><?php echo number_format( $object->valuation_real_amount, 2, ',', '').' '.$apmconfig['currency_symbol'];?></td>
               				</tr>               				
						</tbody>
					</table>
				</div>
				<?php } else {?>
				<div class="alert alert-success">
				<span class="fa fa-alert fa-info-circle"></span>
				<a class="close" href="#" data-dismiss="alert">×</a>
				<?php echo $AppUI->_('Automated valuation system, requires that the project to estimate. Create a project and refer to quantify. The project should have the status "The proposed"');?>
				</div>
				<?php } ?>
			</div>
		
		</div>
		<!-- panel-body-->
	</div>
	<!-- panel-default -->



