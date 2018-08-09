<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not call this file directly.' );
}
$df = $AppUI->getPref ( 'SHDATEFORMAT' );
$date = apmgetParam ( $_GET, 'date', '' );
$field = apmgetParam ( $_GET, 'field', '' );
$this_day = new apm_Utilities_Date ( $date );
$formatted_date = $this_day->format ( $df );
?>
<script language="javascript" type="text/javascript">
<!--
	window.parent.document.<?php echo $field; ?>.value = '<?php echo $formatted_date; ?>';
//-->
</script>