<?php
$view = new apm_Output_HTML_ViewHelper ( $AppUI );

?>
<div class="panel panel-default">
	<div class="panel-heading">
    <?php echo $AppUI->__('Forum').': '; ?> <?php echo strlen($forum->forum_name) == 0 ? "n/a" : $forum->forum_name; ?>
    </div>
	<div class="panel-body">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<p><?php $view->showLabel('Related Project'); ?>
            <?php $view->showField('forum_project', $forum->forum_project); ?>
        </p>
			<p><?php $view->showLabel('Owner'); ?>
            <?php $view->showField('forum_owner', $forum->forum_owner); ?>
        </p>
			<p><?php $view->showLabel('Created On'); ?>
            <?php $view->showField('forum_create_date', $forum->forum_create_date); ?>
        </p>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<p><?php $view->showLabel('Description'); ?>
            <?php $view->showField('forum_description', $forum->forum_description); ?>
        </p>
		</div>
	</div>
</div>