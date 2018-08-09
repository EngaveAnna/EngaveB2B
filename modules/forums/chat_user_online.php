<?php 
if(isset($_SESSION['chatuser'])){

 $q = new apm_Database_Query ();
 $q->addTable ( 'forum_chat_chatters' );
 $q->addQuery ( '*' );
 $q->addWhere('name='.$_SESSION['chatuser']);
 $q->exec ();
 $row = $q->fetchRow ();
 
 if (!empty($row[0])) {
 	$q = new apm_Database_Query ();
 	$q->addTable ( 'forum_chat_chatters' );
 	$q->addUpdate ( 'seen', date('Y-m-d H:i:s') );
 	$q->addWhere('name='.$_SESSION['chatuser']);
 	$q->exec ();
 }else{
 	$q = new apm_Database_Query ();
 	$q->addTable ( 'forum_chat_chatters' );
 	$q->addInsert( 'name',  $_SESSION['chatuser']);
 	$q->addInsert( 'seen',  date('Y-m-d H:i:s'));
 	$q->exec ();
 }
}
/* Make sure the timezone on Database server and PHP server is same */
 $q = new apm_Database_Query ();
 $q->addTable ( 'forum_chat_chatters' );
 $q->addQuery ( '*' );
 $q->addWhere('name='.$_SESSION['chatuser']);
 $q->exec ();
 $row = $q->fetchRow ();

 if (!empty($row[0])) {
 $curtime=strtotime(date("Y-m-d H:i:s",strtotime('-25 seconds', time())));
 if(strtotime($row['seen']) < $curtime){
 	$q = new apm_Database_Query ();
 	$q->setDelete( 'forum_chat_chatters' );
 	$q->addWhere('name=\''.$row[0]['name'].'\'');
 	$q->exec (); 	
 }
}
?>
