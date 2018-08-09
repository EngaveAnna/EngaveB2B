<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo remove database query

// retrieve any state parameters
if (isset ( $_GET ['orderby'] )) {
	$orderdir = $AppUI->getState ( 'ForumVwOrderDir' ) ? ($AppUI->getState ( 'ForumVwOrderDir' ) == 'asc' ? 'desc' : 'asc') : 'desc';
	$AppUI->setState ( 'ForumVwOrderBy', apmgetParam ( $_GET, 'orderby', null ) );
	$AppUI->setState ( 'ForumVwOrderDir', $orderdir );
}
$orderby = $AppUI->getState ( 'ForumVwOrderBy' ) ? $AppUI->getState ( 'ForumVwOrderBy' ) : 'latest_reply';
$orderdir = $AppUI->getState ( 'ForumVwOrderDir' ) ? $AppUI->getState ( 'ForumVwOrderDir' ) : 'desc';

$items = __extract_from_forums_view_topics ( $AppUI, $forum_id, $f, $orderby, $orderdir );

$crumbs = array ();
$crumbs ['?m=forums'] = 'forums list';

$module = new apm_System_Module ();
$fields = $module->loadSettings ( 'forums', 'view_topics' );

if (0 == count ( $fields )) {
	$fieldList = array (
			'message_name',
			'message_author',
			'replies',
			'latest_reply' 
	);
	$fieldNames = array (
			'Topics',
			'Author',
			'Replies',
			'Last Post' 
	);
	$module->storeSettings ( 'forums', 'view_topics', $fieldList, $fieldNames );
	$fields = array_combine ( $fieldList, $fieldNames );
}
?>
<form name="watcher"
	action="?m=forums&a=viewer&forum_id=<?php echo $forum_id; ?>&f=<?php echo $f; ?>"
	method="post" accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_watch_forum" /> <input
		type="hidden" name="watch" value="topic" />
    <?php
				
				$listHelper = new apm_Output_ListTable ( $AppUI );
				$listHelper->addBefore ( 'watch', 'message_id' );
				// print_r($fields);
				// print_r($items);
				echo $listHelper->startTable ();
				echo $listHelper->buildHeader ( $fields );
				echo $listHelper->buildRows ( $items );
				
				?>
        <tr>
		<td colspan="12">
                 <?php if ($canAuthor) { ?>
                <div class="left-space">
				<input type="button" class="btn btn-info"
					value="<?php echo $AppUI->_('start a new topic'); ?>"
					onclick="javascript:window.location='./index.php?m=forums&a=viewer&forum_id=<?php echo $forum_id; ?>&post_message=1';" />
			</div>
                <?php } ?>
                <div class="left-space">
				<input type="submit" class="btn btn-default"
					value="<?php echo $AppUI->_('update watches'); ?>" />
			</div>


		</td>
	</tr>
	</table>
</form>