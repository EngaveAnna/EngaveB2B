
	<input type="hidden" name="task_id"
		value="<?php echo $object->getId(); ?>" /> <input type="hidden"
		name="dosql" value="do_task_aed" /> <input name="hperc_assign"
		type="hidden" value="<?php echo $initPercAsignment; ?>" /> <input
		type="hidden" name="hassign" />

	<div class="table-responsive">
	<table cellspacing="1" cellpadding="2" border="0" width="100%"	class="table table-bordered table-striped table-static">
	<thead><tr><td colspan="2">
	<span class="title-icon glyphicon glyphicon-th-list"></span><?php echo $AppUI->_('Human Resources'); ?></td>
	</tr>
	</thead>
	<tbody>	
	
	
	<tr><td class="apm-label"  style="width:50%;"><?php echo $AppUI->_('Human Resources'); ?><?php echo arraySelect($users, 'resources', 'style="width:100%" size="10" class="form-control" multiple="multiple" ', null); ?></td>
	<td class="apm-label"><?php echo $AppUI->_('Assigned to Task'); ?><?php echo arraySelect($assigned, 'assigned', 'style="width:100%" size="10" class="form-control" multiple="multiple" ', null); ?></td>
	</tr>

				<tr>
					<td colspan="2" align="center">
						<table>
							<tr>
								<td align="right"><input type="button"	class="btn btn-default" style="margin-right:0px; padding:5px 12px;" value="&lt;" onclick="removeUser(document.editFrm)" /></td>
								<td><select name="percentage_assignment" class="form-control" style="margin-bottom:0px;">
                                        <?php for ($i = 5; $i <= 100; $i += 5) { echo '<option ' . (($i == 100) ? 'selected="true"' : '') . ' value="' . $i . '">' . $i . '%</option>'; } ?>
                                </select></td>
                                <td align="left"><input type="button" class="btn btn-default" style="margin-right:0px; padding:5px 12px;" value="&gt;" onclick="addUser(document.editFrm)" /></td>
							</tr>
						</table>
					</td>
				</tr>


			</tbody>
	</table>
	</div>