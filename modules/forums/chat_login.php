<?php 
if(!isset($_SESSION['chatuser']) && !isset($display_case)){
	$name=htmlspecialchars($AppUI->user_first_name.' '.$AppUI->user_last_name);
	$q = new apm_Database_Query ();
	$q->addTable ( 'forum_chat_chatters' );
	$q->addQuery ( 'name' );
	$q->addWhere ( 'name=\''.$name.'\'');
	$q->exec();
	$row=$q->fetchRow();
	if (empty($row[0])) 
	{
		$q = new apm_Database_Query ();
		$q->addTable ( 'forum_chat_chatters' );
		$q->addInsert( 'name',  $name);
		$q->addInsert( 'seen',  date('Y-m-d H:i:s'));
		$q->exec ();
		$_SESSION['chatuser']=$name;
	}
	else
	{
			$name=$name.' (copy)';
			$q = new apm_Database_Query ();
			$q->addTable ( 'forum_chat_chatters' );
			$q->addInsert( 'name',  $name);
			$q->addInsert( 'seen',  date('Y-m-d H:i:s'));
			$q->exec ();
			$_SESSION['chatuser']=$name;
	}	
	$AppUI->redirect ( 'm=forums' );
}elseif(isset($display_case))
{

?>

<div class="alert alert-success">
<span class="fa fa-alert fa-info-circle"></span>
<a class="close" href="#" data-dismiss="alert">×</a>
<?php echo $AppUI->_('To log on to a chat, press "Start Chat". Name assigned to your login will provide a user name will also be visible to other users chat').'.';?>
</div>

<form action="./index.php?m=forums&a=chat_login&suppressHeaders=true" method="POST">
<input class="btn btn-info" type="submit" value="<?php echo $AppUI->_('Submit & Start Chatting'); ?>">
</form>
<?php 

}
?>
