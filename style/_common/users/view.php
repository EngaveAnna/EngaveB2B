<?php
$view = new apm_Output_HTMLHelper ( $AppUI );

?>
<div class="panel panel-default">
	<div class="panel-heading">
    <?php echo $AppUI->__('User').': '; ?> <?php echo strlen($user->user_username) == 0 ? "n/a" : $user->user_username; ?>
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
							<td class="apm-label"><?php echo $AppUI->_('Username'); ?></td>
            <?php echo $view->createCell('user_default', $user->user_username); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Real Name'); ?></td>
            <?php echo $view->createCell('contact_displayname', $user->contact_display_name); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('User Type'); ?></td>
            <?php echo $view->createCell('user_type', $utypes[$user->user_type]); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Company'); ?></td>
            <?php echo $view->createCell('contact_company', $user->contact_company); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Department'); ?></td>
            <?php echo $view->createCell('contact_department', $user->contact_department); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Phone'); ?></td>
            <?php echo $view->createCell('contact_phone', $user->contact_phone); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Email'); ?></td>
            <?php echo $view->createCell('contact_email', $user->contact_email); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Icon'); ?></td>
            <?php echo $view->createCell('contact_icon', $user->user_icon); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Address'); ?></td>
            <?php echo $view->createCell('contact_address',$user->contact_address); ?>
        </tr>
						<tr>
							<td class="apm-label"><?php echo $AppUI->_('Birthday'); ?></td>
            <?php echo $view->createCell('contact_birthday', $user->contact_birthday); ?>
        </tr>
<?php
$fields = $methods ['fields'];
// print_r($methods);
foreach ( $fields as $key => $field ) :
	?>
            <tr>
							<td class="apm-label">
                <?php echo $AppUI->_('Contact by').' "'.$AppUI->_($methodLabels[$field]).'"';?></td>
                <?php echo $view->createCell('contact_name', $methods['values'][$key]); ?>
            </tr>
        <?php endforeach; ?>
            <?php if ($user->feed_token != '') {?>
            	<tr>
							<td class="apm-label"><?php echo $AppUI->_('Calendar Feed'); ?></td>
                <?php
													
$calendarFeed = apm_BASE_URL . '/calendar.php?token=' . $user->feed_token . '&amp;ext=.ics';
													?>
                <td>
								<form name="regenerateToken" action="./index.php?m=users"
									method="post" accept-charset="utf-8">
									<input type="hidden" name="user_id"
										value="<?php echo (int) $user->user_id; ?>" /> <input
										type="hidden" name="dosql" value="do_user_token" /> <input
										type="hidden" name="token"
										value="<?php echo $user->feed_token; ?>" /> <input
										type="submit" class="btn btn-default" name="regenerate token"
										value="<?php echo $AppUI->_('regenerate feed url'); ?>"
										class="btn btn-info btn-xs" />
								</form> <a href="<?php echo $calendarFeed; ?>"><?php echo $AppUI->_('calendar feed'); ?></a>
							</td>
						</tr>
            <?php } ?>
        
        <tr>
							<td class="apm-label"><?php echo $AppUI->_('Signature'); ?></td>
            <?php echo $view->createCell('user_signature', $user->user_signature); ?>
        </tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
