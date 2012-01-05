/**
 *javascript
 */

/**
 * Modifier un copropriétaire et les lots qu'il a
 */
function mod_coprop(dossier,coprop_id,plugin_code,ac)
{
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&coprop_id="+coprop_id+'&ac='+ac+"&act=modcopro";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					$('listcoprolot').hide();
					$('ajoutcopro').hide();
					$('divcopropmod').innerHTML=response.responseText;
					//response.responseText.evalScripts();
				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}
function remove_lot(plugin_code,ac,dossier,lot_id)
{
	if (! confirm("Vous voulez enlever ce lot ?")) { return;}
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&lot_id="+lot_id+'&ac='+ac+"&act=removelot";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					$("row"+lot_id).style.color="red";
					$("row"+lot_id).style.textDecoration="line-through";
                                        $("col"+lot_id).style.textDecoration="none";
					$("col"+lot_id).innerHTML="Effacé";

					//response.responseText.evalScripts();
				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}
/**
 * Ajout un lien entre copropriétaire et lot
 */
function add_coprop()
{
	try
	{
		$('listcoprolot').hide();
		$('ajoutcopro').show();
	}
	catch(e)
	{
		alert(e.message);
	}
}
function copro_show_list()
{
	try
	{
		$('listcoprolot').show();
		$('ajoutcopro').hide();
	}
	catch(e)
	{
		alert(e.message);
	}

}
/**
 * Ajout clef + tantième lot associés
 */
function add_key(dossier,plugin_code,ac)
{
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+'&ac='+ac+"&act=addkey";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					$('keydetail_div').innerHTML=response.responseText;
					response.responseText.evalScripts();
				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}
/**
 * Modifie clef + tantième lot associés
 */
function mod_key(dossier,plugin_code,ac,key_id)
{
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&key_id="+key_id+'&ac='+ac+"&act=modkey";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					$('keydetail_div').innerHTML=response.responseText;
					response.responseText.evalScripts();

				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}

function remove_key(plugin_code,ac,dossier,key_id)
{
	if (! confirm("Vous voulez effacer cette clef ?")) { return;}
	waiting_box();
	try
	{
		var queryString="plugin_code="+plugin_code+"&gDossier="+dossier+"&key_id="+key_id+'&ac='+ac+"&act=removekey";
		var action=new Ajax.Request ( 'ajax.php',
		{
			method:'get',
			parameters:queryString,
			onFailure:null,
			onSuccess:function (response)
			{
				try
				{
					remove_waiting_box();
					$("row"+key_id).style.color="red";
					$("row"+key_id).style.textDecoration="line-through";
                                        $("col"+key_id).style.textDecoration="none";
					$("col"+key_id).innerHTML="Effacé";

					//response.responseText.evalScripts();
				}
				catch(e)
				{
					alert("Réponse Ajax ="+e.message);
				}
			}
		}
		);
	}
	catch(e)
	{
		alert(e.message);
	}
}
function compute_key()
{
	try
	{
		str="";
		var array=$("fkey").getInputs('text');
		var tot=0;
		for (i=0;i<array.length;i++)
			{
				if ( array[i].name.search(/part/) > -1)
				{
					if (! isNaN(array[i].value)) {
						tot+=parseFloat(array[i].value);
					}
				}
			}
		$("span_tantieme").innerHTML=Math.round(tot);
		if ( ! isNaN($('cr_tantieme').value)) {
			var difference=parseFloat($('cr_tantieme').value)-tot;
			if ( difference != 0 )	{
					$('span_diff').style.color="red";
				} else {
					$('span_diff').style.color="green";
				}
			$('span_diff').innerHTML=difference;
		}

	}
	catch(e)
	{
		alert(e.message);
	}
}