<?php 
if(!isset($_SESSION['chatuser']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'){
	die("<script>window.location.reload()</script>");
}
if(isset($_SESSION['chatuser']) && isset($_POST['msg']))
{
	$msg=htmlspecialchars($_POST['msg']);
	if($msg!="")
	{
		$q = new apm_Database_Query ();
		$q->addTable ( 'forum_chat_messages' );
		$q->addInsert( 'name',  $_SESSION['chatuser']);
		$q->addInsert( 'msg',  $msg);
		$q->addInsert( 'posted',  date('Y-m-d H:i:s'));
		$q->exec (); 	
	}
}
?>
