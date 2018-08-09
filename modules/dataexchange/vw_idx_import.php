<?php
if (! defined ( 'apm_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
/* Modified by Thomas Zander Version 1.3 thomas-zander@arcor.de */
/* Modified by Wellison da Rocha Pereira wellisonpereira@gmail.com */

global $AppUI, $canRead, $canEdit, $m;

$action = apmgetParam ( $_POST, 'action', '' );
$filetype = apmgetParam ( $_POST, 'filetype', null );

$myMemLimit = ini_get ( 'memory_limit' );
$myMemLimit = intval ( substr ( $myMemLimit, 0, strlen ( $myMemLimit ) - 1 ) );
$maxFileSize = substr ( ini_get ( 'memory_limit' ), 0, strlen ( ini_get ( 'memory_limit' ) - 1 ) ) * 1024 * 1000;

if ($myMemLimit < 256) {
	ini_set ( 'memory_limit', '256M' );
	ini_set ( 'post_max_size', '256M' );
	ini_set ( 'upload_max_filesize', '256M' );
}

switch ($action) {
	case 'import' :
		if ($_FILES ['upload_file'] ['size'] == 0) {
			unset ( $action );
			echo $AppUI->_ ( 'You must select a file to upload' );
			?>
<form enctype="multipart/form-data" action="index.php?m=dataexchange"
	method="post">
	<input type="file" class="form-control" name="upload_file" size="60" />
	<input type="hidden" name="MAX_FILE_SIZE"
		value="<?php echo $maxFileSize; ?>" /> <input type="hidden"
		name="action" value="import" /> <input type="submit" name="submit"
		class="btn btn-info" value="<?php echo $AppUI->_("Import Data"); ?>" />
</form>
<?php
			break;
		} else {
			$fileext = substr ( $_FILES ['upload_file'] ['name'], - 4 );
			$importer = CImporter::resolveFiletype ( $fileext );
			if (($fileext == '.xml') || ($fileext == '.wbs')) {
				$action = 'preview';
			}
			if (! $importer->loadFile ( $AppUI )) {
				unset ( $action );
				echo "<b>" . $AppUI->_ ( "Failure" ) . "</b> " . $AppUI->_ ( 'taskerror' );
				break;
			}
		}
	case 'preview' :
		?><form name="preForm" action="?m=dataexchange" method="post">
            <?php echo $importer->preview(); ?>
            <input type="hidden" name="action" value="save"> <input
		type="hidden" name="filetype"
		value="<?php echo $importer->fileType;?>"> <input type="submit"
		class="btn btn-info" name="submit"
		value="<?php echo $AppUI->_('Import');?>"
		onClick="validateImport(); return false;"> <input type="submit"
		class="btn btn-default" name="submit"
		value="<?php echo $AppUI->_('cancel');?>"
		onClick="this.form.action.value='cancel'">
</form>
<?php
		break;
	case 'save' :
		$importer = CImporter::resolveFiletype ( $_POST ['filetype'] );
		
		echo $importer->import ( $AppUI );
		if (isset ( $error )) {
			echo $AppUI->_ ( 'Import cancelled. Reason: ' ) . $error;
		} else {
			echo $AppUI->_ ( 'Import success' );
			if ($importer->project_id) {
				echo '<br />';
				echo '<a href="?m=projects&a=view&project_id=' . $importer->project_id . '">';
				echo $AppUI->_ ( 'View the project here' );
				echo '</a>';
			}
		}
		unset ( $action );
		break;
	case 'cancel' :
		echo $AppUI->_ ( 'Import cancelled. Reason: ' ) . $error;
		unset ( $action );
		break;
	default :
		// No specific action set, go back to the form
		echo $AppUI->_ ( 'msinfo' );
		?>
<form enctype="multipart/form-data" action="index.php?m=dataexchange"
	method="post">
	<input type="file" class="form-control" name="upload_file" size="60" />
	<input type="hidden" name="MAX_FILE_SIZE"
		value="<?php echo $maxFileSize; ?>" /> <input type="hidden"
		name="action" value="import" /> <input type="submit" name="submit"
		class="btn btn-info" value="<?php echo $AppUI->_("Import Data"); ?>" />
</form>
<?php
}