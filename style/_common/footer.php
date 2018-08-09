<?php
global $a, $AppUI;

$tab = ( int ) apmgetParam ( $_GET, 'tab', 0 );


echo $AppUI->getTheme ()->loadFooterJS ();
echo $AppUI->getMsg ();
?>
</div>
</div>

<div class="footer-cr"><a href="http://engave.pl/engave-realizuje-projekt-b2b/"><img class="footer-cr" src="<?php echo apm_BASE_URL .'/style/'.$AppUI->getPref ( 'UISTYLE' ).'/images/logo_ue.png';?>"></a></div>
	
<!-- <div style="text-align: right; margin: 5px 20px 30px 10px;">&copy;	Engave sp. z o.o.</div> -->
</div>


<script type="text/javascript">
jQuery(function(){
    $('[data-toggle="tooltip"]').tooltip({
        placement : 'bottom'
    });
    $('[data-toggle="dropdown"]').tooltip({
        placement : 'bottom'
    });
});

$('[data-toggle="tabajax"]').click(function(e) {
    var $this = $(this),
        loadurl = $this.attr('href'),
        targ = $this.attr('data-target');

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });
    
    $this.tab('show');
    return false;
});

$(document).ready(function(){
 
	  $('.danger').popover({ 
	    html : true,
	    content: function() {
	      return $('#popover_content_wrapper').html();
	    }
	  });
	});
</script>

