$.getScript('./js/apm-texteditor.js', function()
{
	$(document).ready( function() {  $("#txtEditor").Editor(); 
	$('#'+textEdBaseArea).html($('#'+textEdSource).attr('value'));
	});
	
});