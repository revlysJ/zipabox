
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
 $('#bt_graphEqLogic').off('click').on('click', function () {
  $('#md_modal').dialog({title: "{{Graphique de lien}}"});
  $("#md_modal").load('index.php?v=d&modal=graph.link&filter_type=eqLogic&filter_id='+$('.eqLogicAttr[data-l1key=id]').value()).dialog('open');
});
$('.eqLogicAction[data-action=bt_healthSpecific]').on('click', function () {
  $('#md_modal').dialog({title: "{{Santé Zipabox}}"});
  $('#md_modal').load('index.php?v=d&plugin=zipabox&modal=health').dialog('open');
});
$('.bt_plugin_view_log').on('click',function(){
 if($('#md_modal').is(':visible')){
   $('#md_modal2').dialog({title: "{{Logs de Zipabox}}"});
   $("#md_modal2").load('index.php?v=d&modal=log.display&log='+$(this).attr('data-log')+'&slaveId='+$(this).attr('data-slaveId')).dialog('open');
 }else{
   $('#md_modal').dialog({title: "{{Logs de Zipabox}}"});
   $("#md_modal").load('index.php?v=d&modal=log.display&log='+$(this).attr('data-log')+'&slaveId='+$(this).attr('data-slaveId')).dialog('open');
 }
});
$('.eqLogicAction[data-action=ZI_import]').on('click',function(){
 if($('#md_modal').is(':visible')){
   $('#md_modal2').dialog({title: "{{Import Auto de Modules depuis la Zipabox}}"});
   $("#md_modal2").load('index.php?v=d&modal=log.display&log='+$(this).attr('data-log')+'&slaveId='+$(this).attr('data-slaveId')).dialog('open');
 }else{
   $('#md_modal').dialog({title: "{{Import Auto de Modules depuis la Zipabox}}"});
   $('#md_modal').load('index.php?v=d&plugin=zipabox&modal=import&id=' + $('.eqLogicAttr[data-l1key=id]').value()).dialog('open');
 }
});
 $("#bt_addzipaboxInfo").on('click', function (event) {
    var _cmd = {type: 'info'};
    addCmdToTable(_cmd);
});

 $("#bt_addzipaboxAction").on('click', function (event) {
    var _cmd = {type: 'action'};
    addCmdToTable(_cmd);
});



//$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.zipabox
 */
 function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
	   var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
	   _cmd.configuration = {};
    }
    if (init(_cmd.logicalId) == 'refresh') {
	return;
 }

 if (init(_cmd.type) == 'info') {
	var disabled = (init(_cmd.configuration.zipaboxAction) == '1') ? 'disabled' : '';
	var tr = '<tr class="cmd" style="background-color: rgba(91, 192, 222, 0.1);" data-cmd_id="' + init(_cmd.id) + '" zipaboxAction="' + init(_cmd.configuration.zipaboxAction) + '">';
	tr += '<td>';
	tr += '<span class="cmdAttr" data-l1key="id" style="display:none;" ></span>';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
	//tr += '<td>';
    tr += '<div class="row">';
    tr += '<div class="col-sm-5">';
	tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
    tr += '</div>';
    tr += '<div class="col-sm-7">';
	tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</div>';
    tr += '</div>';
    tr += '<br></td>';

//	tr += '<td><textarea class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="uuid" style="height : 33px;" ' + disabled + ' placeholder="{{uuid attribut}}"></textarea>';
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="uuid" placeholder="{{uuid attribut}}" style="margin-bottom : 5px;width : 70%; display : inline-block;" />';

    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value" placeholder="{{Valeur}}" disabled style="margin-bottom : 5px;width : 50%; display : inline-block;" />';
	tr += '<br><span class="text-warning fa fa-question-circle tooltips" title="{{Http_Request pour règles Zipabox - IP d accés Jeedom depuis Internet}}"> IP_Jeedom:Port</span><span>/plugins/zipabox/core/php/Callback.php?id=' + init(_cmd.id) + '&value=<span class="text-warning">' + init(_cmd.configuration.value) + ' </span> <span  class="text-warning fa fa-question-circle tooltips" title="{{Valeur à envoyer depuis la Zipabox}}"> </span>';
	tr += '</td>';
	tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="unite" style="width : 90px;" placeholder="{{Unité}}"></td>';
	tr += '<td>';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr expertModeVisible" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label></span><br/>';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40%;display : inline-block;"> ';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40%;display : inline-block;">';
	tr += '</td>';
	tr += '<td>';
	if (is_numeric(_cmd.id)) {
	   tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
	   tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
	   $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

if (init(_cmd.type) == 'action') {
    var tr = '<tr class="cmd" style="background-color: rgba(240, 173, 78, 0.1);" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
	tr += '<span class="cmdAttr" data-l1key="id" style="display:none;" ></span>';
    tr += '<div class="row">';
    tr += '<div class="col-sm-6">';
    tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> Icône</a>';
    tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
    tr += '</div>';
    tr += '<div class="col-sm-6">';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom}}">';
    tr += '</div>';
    tr += '</div>';
	
    tr += '<div class="row">';
    tr += '<div class="col-sm-5">';
	tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
    tr += '</div>';
    tr += '<div class="col-sm-7">';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '<input class="cmdAttr" data-l1key="configuration" data-l2key="zipaboxAction" value="1" style="display:none;" >';
    tr += '</div>';
    tr += '</div>';
	
    tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="display : none;margin-top : 5px;margin-right : 10px;" title="{{La valeur de la commande vaut par défaut la commande}}">';
    tr += '<option value="">Aucune</option>';
    tr += '</select>';
    tr += '<br></td>';
	
    // tr += '<td>';
    // tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
    // tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    // tr += '<input class="cmdAttr" data-l1key="configuration" data-l2key="zipaboxAction" value="1" style="display:none;" >';
    // tr += '</td>';
	
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="uuid" placeholder="{{uuid attribut}}" style="margin-bottom : 5px;width : 70%; display : inline-block;" />';

    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value" placeholder="{{Valeur}}" style="margin-bottom : 5px;width : 50%; display : inline-block;" />';

    tr += '</td>';
    tr += '<td></td>';
    tr += '<td>';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="updateCmdId" style="display : none;margin-top : 5px;" title="Commande d\'information à mettre à jour">';
    tr += '<option value="">Aucune</option>';
    tr += '</select>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="updateCmdToValue" placeholder="Valeur de l\'information" style="display : none;margin-top : 5px;">';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '<input class="tooltips cmdAttr form-control input-sm expertModeVisible" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}" style="margin-top : 5px;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40%;display : inline-block;" /> ';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40%;display : inline-block;" />';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
	   tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
	   tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';

    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    var tr = $('#table_cmd tbody tr:last');
    jeedom.eqLogic.builSelectCmd({
	   id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
	   filter: {type: 'info'},
	   error: function (error) {
		  $('#div_alert').showAlert({message: error.message, level: 'danger'});
	   },
	   success: function (result) {
		  tr.find('.cmdAttr[data-l1key=value]').append(result);
		  tr.find('.cmdAttr[data-l1key=configuration][data-l2key=updateCmdId]').append(result);
		  tr.setValues(_cmd, '.cmdAttr');
		  jeedom.cmd.changeType(tr, init(_cmd.subType));
	   }
    });
}
}
