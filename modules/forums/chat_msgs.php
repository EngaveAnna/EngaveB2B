<?php 
$q = new apm_Database_Query ();
$q->addTable ( 'forum_chat_messages' );
$q->addQuery ( '*' );
$q->exec ();

while ( $row = $q->fetchRow () ) {
 echo "<div class='msg' title='{$row['posted']}'><span class='name'>{$row['name']}</span> : <span class='msgc'>{$row['msg']}</span></div>";
}
if(!isset($_SESSION['chatuser']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'){
 echo "<script>window.location.reload()</script>";
}
?>
