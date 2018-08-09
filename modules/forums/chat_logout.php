<?php 
$q = new apm_Database_Query ();
$q->setDelete( 'forum_chat_chatters' );
$q->addWhere('name=\''.$_SESSION['chatuser'].'\'');
$q->exec ();
unset($_SESSION['chatuser']);
$AppUI->redirect ( 'm=forums' );
?>
