/* This file is part of NOALYSS and is under GPL see licence.txt */
function reconcilie(target,dossier_id,p_id,plugin_code,tiers)
{
    var qs="gDossier="+dossier_id+'&plugin_code='+plugin_code+'&act=show&id='+p_id+'&ctl='+target+"&target="+tiers;

    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:error_box,
				      onSuccess:success_box
				  }
				);
    if ( $(target))
    {
	removeDiv(target);
    }
    var sx=calcy(120);
    
    var str_style="top:"+sx+"px;";

    var div={id:target, cssclass:'inner_box',style:str_style,html:loading(),drag:1};

    add_div(div);
}

function save_bank_info(obj)
{
    var query_string=obj.serialize();
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:query_string,
				      onFailure:error_box,
				      onSuccess:success_bank_info
				  });

    return false;
}

function success_bank_info(req,json)
{
    try {
	var answer=req.responseXML;
	var a=answer.getElementsByTagName('extra');
	var name_ctl=a[0].firstChild.nodeValue;
	var ob=name_ctl.evalJSON(true);
	$('st'+ob.id).innerHTML=unescape_xml(ob.msg);
	var div=answer.getElementsByTagName('ctl');
	var div_ctl=div[0].firstChild.nodeValue;
	removeDiv(div_ctl);
    }
    catch(e) {
	alert_box('Erreur success_box_info '+e.message);
    }
}