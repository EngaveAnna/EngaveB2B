<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$project_id = ( int ) apmgetParam ( $_REQUEST, 'project_id', 0 );
$report_type = apmgetParam ( $_REQUEST, 'report_type', '' );

$canReport = canView ( 'reports' );
$canRead = canView ( 'projects', $project_id );
if (! $canReport || ! $canRead) {
	$AppUI->redirect ( ACCESS_DENIED );
}

$project_list = array (
		'0' => $AppUI->_ ( 'All', UI_OUTPUT_RAW ) 
);
$projectObj = new CProject ();
$projectList = $projectObj->getAllowedProjects ( $AppUI->user_id, false );
$company = new CCompany ();
$companyList = $company->loadAll ();

foreach ( $projectList as $pr ) {
	if ($pr ['project_id'] == $project_id) {
		$display_project_name = '(' . $companyList [$pr ['project_company']] ['company_name'] . ') ' . $pr ['project_name'];
	}
	$project_list [$pr ['project_id']] = '(' . $companyList [$pr ['project_company']] ['company_name'] . ') ' . $pr ['project_name'];
}

// get the prefered date format
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

$loader = new apm_FileSystem_Loader ();
$reports = $loader->readFiles ( apm_BASE_DIR . '/modules/reports/reports', '\.php$' );

// setup the title block
if (! $suppressHeaders) {
	$titleBlock = new apm_Theme_TitleBlock ( 'Reports', 'icon.png', $m );
	
	/*
	 * $extra = array('from' => 'links', 'where' => 'projects.project_id = link_project');
	 *
	 * $project = new CProject();
	 * $projects = $project->getAllowedRecords($AppUI->user_id, 'projects.project_id,project_name', 'project_name', null, $extra, 'projects');
	 * $projects = arrayMerge(array('0' => $AppUI->_('All', UI_OUTPUT_JS)), $projects);
	 *
	 * $search_string = apmgetParam($_POST, 'search_string', '');
	 * $AppUI->setState($m . '_search_string', $search_string);
	 * $search_string = apmformSafe($search_string, true);
	 *
	 * // setup the title block
	 * $titleBlock = new apm_Theme_TitleBlock('Links', 'icon.png', $m);
	 * $titleBlock->addSearchCell($search_string);
	 * $titleBlock->addFilterCell('Project', 'project_id', $projects, $project_id);
	 *
	 * if ($canCreate) {
	 * $titleBlock->addCrumb('?m=links&a=addedit', 'New link', '', true);
	 * }
	 * $titleBlock->show();
	 */
	
	$titleBlock->addCrumb ( '?m=projects', 'projects list' );
	if ($project_id) {
		 $titleBlock->addCrumb('?m=projects&amp;a=view&amp;project_id=' . $project_id, 'view this project');
	}
	if ($report_type) {
		 $titleBlock->addCrumb('?m=reports&amp;project_id=' . $project_id, 'reports index');
	}
}

$report_type_var = apmgetParam ( $_GET, 'report_type', '' );
if (! empty ( $report_type_var )) {
	$report_type_var = '&amp;report_type=' . $report_type;
}

$frm = '<form name="changeMe" role="form" action="./index.php?m=reports' . $report_type_var . '" method="post" accept-charset="utf-8">
		<div class="form-group">
		<label for="selectchangeMe">' . $AppUI->_ ( 'Project' ) . '</label>
		' . arraySelect ( $project_list, 'project_id', 'size="1" class="form-control" onchange="changeIt();"', $project_id, false ) . '
		</div></form>';

if(!$_GET['suppressHeaders'])
{	
$titleBlock->addCustomCell ( $frm );
//$titleBlock->addCrumb('', $frm,'', FALSE);
$titleBlock->show ();
}

if ($report_type) {
	$report_type = $loader->checkFileName ( $report_type );
	$report_type = str_replace ( ' ', '_', $report_type );
	require apm_BASE_DIR . '/modules/reports/reports/' . $report_type . '.php';
} else {
	
	foreach ( $reports as $key => $v ) {
		$type = str_replace ( '.php', '', $v );
		$link = 'index.php?m=reports&project_id=' . $project_id . '&report_type=' . $type;
		
		/*
		 * TODO: There needs to be a better approach to adding the suppressHeaders
		 * part but I can't come up with anything better at the moment..
		 *
		 * ~ caseydk, 08 May 2011
		 */
		$suppressHeaderReports = array (
				'completed',
				'upcoming',
				'overdue' 
		);
		if (in_array ( $type, $suppressHeaderReports )) {
			$link .= '&suppressHeaders=1';
		}
		
		$items [] = array (
				'report_name' => '<a href="' . $link . '">' . $AppUI->_ ( $type . '_name' ) . '</a>',
				'report_desc' => $AppUI->_ ( $type . '_desc' ) 
		);
		
		/*
		 * $s .= '<tr><td><a href="'.$link.'">'.$AppUI->_($type.'_name') . '</a></td>';
		 * $s .= '<td>' . $AppUI->_($type.'_desc') . '</td></tr>';
		 */
	}
	/*
	 * $s .= '</table>';
	 * echo $s;
	 */
}

$customLookups = '';

if (! $suppressHeaders) {
	?>
<script language="javascript" type="text/javascript">
    function changeIt() {
        var f=document.changeMe;
        f.submit();
    }
    </script>
<?php
}

require (apm_BASE_DIR . '/modules/reports/vw_reports.php');