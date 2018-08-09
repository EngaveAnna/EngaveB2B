<?php 
if(isset($_SESSION['chatuser'])){
?>
	<div class="panel panel-default" style="height:420px; margin:0px; padding-left:0px;"><div class="panel-heading"><?php echo $AppUI->_('Room');?></div><div class="panel-body">
	<div class='msgs'>
 	<?php include("chat_msgs.php");?>
	</div>
	<div style="margin-top:20px;">
	<form action="./index.php?m=forums&a=chat_chatbox&suppressHeaders=true" id="msg_form" >
	<input class="form-control" name="msg" size="30" type="text"/>
	<button class="btn btn-info"><?php echo $AppUI->_('Send');?></button>
	</form>
	<form action="./index.php?m=forums&a=chat_logout&suppressHeaders=true" id="out_form" method="POST"><input type="submit" class="btn btn-default" value="<?php echo $AppUI->_('Log Out');?>"></form>
	<?php if($canAdd) {	?>
	<form action="./index.php?m=forums&a=chat_clear&suppressHeaders=true" id="clear_form" method="POST"><input type="submit" class="btn btn-default" value="<?php echo $AppUI->_('Clear');?>"></form>
	<?php  } ?>
	</div>
	</div></div>
<?php 
}
?>