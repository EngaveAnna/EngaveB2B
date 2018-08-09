$.getScript('./js/apm-texteditor.js', function()
{
	
	$(document).ready(function() {
	    $("#add_row").on("click", function() {
	        // Dynamic Rows Code
	        
	        // Get max row id and set new id
	        var newid = 0;
	        $.each($("#tab_logic tr"), function() {
	            if (parseInt($(this).data("id")) > newid) {
	                newid = parseInt($(this).data("id"));
	            }
	        });
	        newid++;
	        
	        var tr = $("<tr></tr>", {
	            id: "addr"+newid,
	            "data-id": newid
	        });
	        
	        // loop through each td and create new elements with name of newid
	        $.each($("#tab_logic tbody tr:nth(0) td"), function() {
	            var cur_td = $(this);
	            
	            var children = cur_td.children();
	            
	            // add new td and element if it has a nane
	            if ($(this).data("name") != undefined) {
	                var td = $("<td></td>", {
	                    "data-name": $(cur_td).data("name")
	                });
	                
	                var c = $(cur_td).find($(children[0]).prop('tagName')).clone().val("");
	                c.attr("name", 'invoice_items' +'['+newid+']'+'['+$(cur_td).data("name")+']');
          
	                c.appendTo($(td));
	                td.appendTo($(tr));
	            } else {
	                var td = $("<td></td>", {
	                    'text': $('#tab_logic tr').length
	                }).appendTo($(tr));
	            }
	            
	            $('[id=\'N['+newid+']\']').html(newid);
	        });
	        
	        // add delete button and td
	        /*
	        $("<td></td>").append(
	            $("<button class='btn btn-danger glyphicon glyphicon-remove row-remove'></button>")
	                .click(function() {
	                    $(this).closest("tr").remove();
	                })
	        ).appendTo($(tr));
	        */
	        
	        // add the new row
	        $(tr).appendTo($('#tab_logic'));
	        
	        $(tr).find("td button.row-remove").on("click", function() {
	             $(this).closest("tr").remove();
	        });
	});


	});	
	
	
});