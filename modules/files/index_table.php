<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
// @todo convert to template

// modified later by Pablo Roca (proca) in 18 August 2003 - added page support
// Files modules: index page re-usable sub-table
global $AppUI, $company_id, $project_id, $task_id;
global $currentTabId, $m, $showProject, $xpg_min, $xpg_pagesize, $page;

$tab = ($m == 'files') ? $currentTabId - 1 : $currentTabId;
$page = apmgetParam ( $_GET, 'page', 1 );
if (! isset ( $project_id )) {
	$project_id = apmgetParam ( $_REQUEST, 'project_id', 0 );
}

$category_id = 0;
if (($company_id || $project_id || $task_id) && ! ($m == 'files')) {
	$category_id = 0;
} else {
	// TODO: the filtering is not working as expected in the flat view
	$category_id = ($tab < 0) ? 0 : $tab + 1;
}

$xpg_pagesize = apmgetConfig ( 'page_size', 50 );
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
                                        
// counts total recs from selection
$fileList = CFile::getFileList ( $AppUI, $company_id, $project_id, $task_id, $tab );
$xpg_totalrecs = count ( $fileList );
$pageNav = buildPaginationNav ( $AppUI, $m, $tab + 1, $xpg_totalrecs, $xpg_pagesize, $page );

?>
<script language="javascript" type="text/javascript">
function expand(id){
  var element = document.getElementById(id);
  element.style.display = (element.style.display == '' || element.style.display == 'none') ? 'block' : 'none';
}
</script>
<?php

$showProject = true;
echo displayFiles ( $AppUI, - 1, $task_id, $project_id, $company_id );
echo $pageNav;

