<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly' );
}
// @todo convert to template

$forum = new CForum ();
$canRead = $forum->canView ();
$canAdd = $forum->canCreate ();

if (! $canRead) {
	$AppUI->redirect ( ACCESS_DENIED );
}

// retrieve any state parameters
if (isset ( $_GET ['orderby'] )) {
	$orderdir = $AppUI->getState ( 'ForumIdxOrderDir' ) ? ($AppUI->getState ( 'ForumIdxOrderDir' ) == 'asc' ? 'desc' : 'asc') : 'desc';
	$AppUI->setState ( 'ForumIdxOrderBy', apmgetParam ( $_GET, 'orderby', null ) );
	$AppUI->setState ( 'ForumIdxOrderDir', $orderdir );
}
$orderby = $AppUI->getState ( 'ForumIdxOrderBy' ) ? $AppUI->getState ( 'ForumIdxOrderBy' ) : 'forum_name';
$orderdir = $AppUI->getState ( 'ForumIdxOrderDir' ) ? $AppUI->getState ( 'ForumIdxOrderDir' ) : 'asc';

$f = apmgetParam ( $_REQUEST, 'f', 0 );

$items = $forum->getAllowedForums ( $AppUI->user_id, $AppUI->user_company, $f, $orderby, $orderdir );

$filters = array (
		'All Forums' 
);

if (isset ( $a ) && $a == 'viewer') {
	array_push ( $filters, 'My Watched', 'Last 30 days' );
} else {
	array_push ( $filters, 'My Forums', 'My Watched', 'My Projects', 'My Company', 'Inactive Projects' );
}

// setup the title block
$titleBlock = new apm_Theme_TitleBlock ( 'Forums', 'icon.png', $m );
$titleBlock->addFilterCell ( 'Filter', 'f', $filters, $f );

if ($canAdd) {
	$titleBlock->addCrumb ( '?m=forums&a=addedit', 'new forum', '', true );
}

// TODO: this is a little hack to make sure the table header gets generated in the show() method below
global $a;
$a = 'list';
// End of little hack

$titleBlock->show ();

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'forums', 'index_list' );

if (0 == count ( $fields )) {
	$fieldList = array (
			'forum_project',
			'forum_name',
			'forum_description',
			'forum_owner',
			'forum_topics',
			'forum_replies',
			'forum_last_date' 
	);
	$fieldNames = array (
			'Project',
			'Forum Name',
			'Description',
			'Owner',
			'Topics',
			'Replies',
			'Last Post Info' 
	);
	
	$module->storeSettings ( 'forums', 'index_list', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}
?>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left:0px; padding-right:0px;">

			<form name="watcher" action="./index.php?m=forums&f=<?php echo $f; ?>"
	method="post" accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_watch_forum" /> <input
		type="hidden" name="watch" value="forum" />
			
    <?php
	$listTable = new apm_Output_ListTable ( $AppUI );
	$listTable->addBefore ( 'watch', 'forum_id' );
	echo $listTable->startTable ();
	echo $listTable->buildHeader ( $fields, true, 'forums&f=' . $f );
	echo $listTable->buildRows ( $items );
	?>
    <tr>
		<td colspan="<?php echo $listTable->cellCount; ?>"><input type="submit" class="btn btn-default" value="<?php echo $AppUI->_('update watches'); ?>" /></td>
	</tr>
    <?php
				echo $listTable->endTable ();
				?>
</form>

	</div>
	<!-- panel-default -->




 <div class="panel panel-info">
	<div class="panel-heading"><?php echo $AppUI->_('Chat'); ?></div>
		<div class="panel-body">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 chat">
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 users" style="padding-left:0px;">
			     <?php include("chat_users.php");?>
			</div>
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 chatbox">
			     <?php if(isset($_SESSION['chatuser'])){
			      include("chat_chatbox.php");
			     }else{
			      $display_case=true;
			      require_once("chat_login.php");
			     }?>
			</div>



 			</div>
	</div>
	<!-- panel-body-->
</div>
<!-- panel-default -->



