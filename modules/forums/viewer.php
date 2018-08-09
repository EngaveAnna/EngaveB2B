<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

// view posts
$forum_id = ( int ) apmgetParam ( $_GET, 'forum_id', 0 );

$message_id = ( int ) apmgetParam ( $_GET, 'message_id', 0 );
$post_message = ( int ) apmgetParam ( $_GET, 'post_message', 0 );
$f = apmgetParam ( $_POST, 'f', 0 );

// check permissions
$perms = &$AppUI->acl ();
$canAuthor = canAdd ( 'forums' );
$canDelete = canDelete ( 'forums', $forum_id );
$canRead = $perms->checkModuleItem ( 'forums', 'view', $forum_id );
$canEdit = $perms->checkModuleItem ( 'forums', 'edit', $forum_id );
$canAdminEdit = canEdit ( 'system' );

if (! $canRead) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$message = new CForum_Message ();
$message->load ( $message_id );

if (0 == $forum_id) {
	$forum_id = $message->message_forum;
}

$forum = new CForum ();
$forum->load ( $forum_id );

$project = new CProject ();
$project->load ( $forum->forum_project );

if (! $forum) {
	$AppUI->setMsg ( 'Forum' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ( 'm=' . $m );
}

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Forum', 'icon.png', $m );
$titleBlock->addCrumb ( '?m=' . $m, $m . ' list' );
if ($message_id) {
	$titleBlock->addCrumb ( '?m=forums&a=viewer&forum_id=' . $forum_id, 'topics for this forum' );
}
if ($canEdit) {
	$titleBlock->addCrumb ( '?m=forums&a=addedit&forum_id=' . $forum_id, 'edit this forum' );
	
	if ($canDelete) {
		$titleBlock->addCrumbDelete ( 'delete forum', true, $msg );
	}
}

$filters = array (
		'- Filters -' 
);

if (isset ( $a ) && $a == 'viewer') {
	array_push ( $filters, 'My Watched', 'Last 30 days' );
} else {
	array_push ( $filters, 'My Forums', 'My Watched', 'My Projects', 'My Company', 'Inactive Projects' );
}

$titleBlock->addCell ( arraySelect ( $filters, 'f', 'size="1" class="form-control" onchange="document.filterFrm.submit();"', $f, true ), '', '<form action="?m=forums&a=viewer&forum_id=' . $forum_id . '" method="post" name="filterFrm" accept-charset="utf-8">', '</form>' );
$titleBlock->show ();

$view = new apm_Controllers_View ( $AppUI, $forum, 'Forum' );
echo $view->renderDelete ();

include $AppUI->getTheme ()->resolveTemplate ( 'forums/view' );

if ($post_message) {
	include (apm_BASE_DIR . '/modules/forums/post_message.php');
} else {
	if ($message_id) {
		include (apm_BASE_DIR . '/modules/forums/view_messages.php');
	} else {
		include (apm_BASE_DIR . '/modules/forums/view_topics.php');
	}
}