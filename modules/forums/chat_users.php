<?php 

echo '<div class="panel panel-default" style="height:420px; margin:0px; padding-left:0px;"><div class="panel-heading">'.$AppUI->_('Users').'</div><div class="panel-body">';

$q = new apm_Database_Query ();
$q->addTable ( 'forum_chat_chatters' );
$q->addQuery ( 'name' );
$q->exec ();

$r=null;
while ( $row = $q->fetchRow () ) 
{
		$r.="<div class='user'>{$row['name']}</div>";
}
if(!$r)
$r='<div class="user">'.$AppUI->_('No users').'</div>';	

echo $r.'</div></div>';
?>
