function getModalAjaxData(modId, ajaxUrl) {
    if (modId == "") {
        document.getElementById(modId+'_body').innerHTML = "No ID Error";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else if(window.ActiveXObject) {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }else
        {
        	  alert("Your browser does not support XMLHTTP!");
       	}
        xmlhttp.overrideMimeType('text/xml');
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById(modId+'_body').innerHTML=xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET",ajaxUrl,true);
        xmlhttp.send();
    }
}

function modalAjaxProcess(obj, procVar, elemName, elemId, modId, optionsJSON)
{
	if(obj.checked===true)
	updateAjaxVal(obj, procVar, elemName, elemId, modId);
	else
	removeAjaxVal(procVar, elemName, elemId, modId);
}

function updateAjaxVal(obj, procVar, elemName, elemId, modId)
{
	if(obj.type=='radio')
	{
		document.getElementById(modId+'_area').innerHTML='<p id="'+modId+'_'+elemId+'"><button class="btn btn-default" type="button" onclick="removeAjaxVal(\''+procVar+'\', \'\', \''+elemId+'\', \''+modId+'\');"><span class="glyphicon glyphicon-remove"></span></button>&nbsp;'+elemName+'<label class="label label-default label-xs label-micro">ID: '+elemId+'</label></p>';
		document.getElementById(procVar).value=[elemId];
	}
	else
	{
		if(document.getElementById(modId+'_'+elemId)==null)
			document.getElementById(modId+'_area').innerHTML+='<p id="'+modId+'_'+elemId+'"><button class="btn btn-default" type="button" onclick="removeAjaxVal(\''+procVar+'\', \'\', \''+elemId+'\', \''+modId+'\');"><span class="glyphicon glyphicon-remove"></span></button>&nbsp;'+elemName+'<label class="label label-default label-xs label-micro">ID: '+elemId+'</label></p>';

			var arr;
			var str=document.getElementById(procVar).value;
			if(str!='')
			{	
				arr=str.split(',');
				arr.push(elemId);
			}
			else
			{
				arr=[elemId];
			}
		    document.getElementById(procVar).value=arr.toString();

	}
}


function removeAjaxVal(procVar, elemName, elemId, modId)
{
	if(document.getElementById(modId+'_'+elemId))
	{
		var chl = document.getElementById(modId+'_'+elemId)
		chl.parentNode.removeChild(chl);
	}
	
	var str=null;
	str=document.getElementById(procVar).value;

	var arr=str.split(',');

    for(var i = arr.length; i--;) {
        if(arr[i] === elemId) {
            arr.splice(i, 1);
        }
    }

    document.getElementById(procVar).value=arr.toString();
}

function getModalAjaxProcVar(procVar, type)
{
	if(type)
	return document.getElementById(procVar).options[document.getElementById(procVar).selectedIndex].value;
	else
	return document.getElementById(procVar).value;
}

function modalAjaxLoading(alert, modId)
{
	document.getElementById(modId+'_body').innerHTML=
	'<div style="height:30px;"><i class="fa fa-cog fa-spin fa-icon" ></i>'+alert+'</div>';
}


function submitIt() {
	var f = document.editFrm;
	f.submit();
}

//APM modalAJAX custom functions
function modalAjaxTemplate(obj, procVar, elemName, elemId, modId, optionsJSON, editor)
{
	var processing=JSON.parse(optionsJSON);
	if(obj.checked===true)
	{
		if(editor) document.getElementById(textEdBaseArea).innerHTML=processing['template_source'];
		updateAjaxVal(obj, procVar, elemName, elemId, modId);
		if(editor) document.getElementById('template_source').value=processing['template_source'];
	}
	else
	{
		removeAjaxVal(procVar, elemName, elemId, modId);
		if(editor) document.getElementById('template_source').value='';
	}
}