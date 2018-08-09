<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly' );
}
// @todo convert to template

$forum = new CForum ();
$canAdd = $forum->canCreate ();

if (! $canAdd) {
	$AppUI->redirect ( ACCESS_DENIED );
}


	$q = new apm_Database_Query ();
	$q->setDelete( 'forum_chat_messages' );
	$q->exec ();

	
	$q = new apm_Database_Query ();
	$q->setDelete( 'forum_chat_chatters' );
	$q->addWhere('name=\''.$_SESSION['chatuser'].'\'');
	$q->exec ();
	unset($_SESSION['chatuser']);
	$AppUI->redirect ( 'm=forums' );