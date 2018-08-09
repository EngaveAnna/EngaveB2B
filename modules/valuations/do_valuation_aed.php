<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$delete = ( int ) apmgetParam ( $_POST, 'del', 0 );
$object=new CValuation ();
$project_id = ( int ) apmgetParam ( $_POST, 'valuation_project', 0 );

if(!$delete)
{
	
	if(!$_POST['valuation_id'])
	$_POST['valuation_create_date']=date('Y-m-d H:i:s');
		
 	if($_POST['valuation_project'])
 	{
 		$_POST['valuation_date']=date('Y-m-d H:i:s');
 		$_POST['valuation_category']=1;
 		$project = new CProject ();
 		
 		if (! $project->load ( $_POST['valuation_project'])) {
 			$AppUI->redirect ( ACCESS_DENIED );
 		}
 		
 		$projectPriority = apmgetSysVal ( 'ProjectPriority' );
 		$projectPriorityColor = apmgetSysVal ( 'ProjectPriorityColor' );
 		$billingCategory = apmgetSysVal ( 'BudgetCategory' );
 		$pstatus = apmgetSysVal ( 'ProjectStatus' );
 		$ptype = apmgetSysVal ( 'ProjectType' );
 		
		//total budget
 		$totalBudget = 0;
 		foreach ( $billingCategory as $id => $category ) 
 		{
 			$amount = 0;
 			if (isset ( $project->budget [$id] )) {
 				$amount = $project->budget [$id] ['budget_amount'];
 			}
 			$totalBudget += $amount;
		}
 		$_POST['valuation_amount']=$totalBudget;

 		//actual budget
 		$bcode = new CSystem_Bcode ();
 		$results = $bcode->calculateProjectCost ( $project_id );
		$_POST['valuation_real_amount'] = $results ['totalCosts'];

		
		$_POST['valuation_days']=$project->project_scheduled_hours;
		$_POST['valuation_real_days']=$project->project_worked_hours;
 	}
 	else {
 		$_POST['valuation_amount']=$_POST['valuation_real_amount']=$_POST['valuation_days']=$_POST['valuation_real_days']='';
 		$_POST['valuation_category']=0;
 		$_POST['valuation_date']='';
 	}	
	
}



$controller = new apm_Controllers_Base ( $object, $delete, 'Valuations', 'm=valuations', 'm=valuations&a=addedit' );

$AppUI = $controller->process ( $AppUI, $_POST );
$AppUI->redirect ( $controller->resultPath );
