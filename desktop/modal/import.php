<?php

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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>
	<div class="form-group"  style="    ">	
		<label class="col-sm-4 control-label ">Choix de la Zipabox :</label>
		<div class="col-sm-8">
			<a class="btn btn-success pull-right bt_import" id="bt_import" title="Essai d'importation automatique des modules de la Zipabox">* Import</a>
		</div>
	</div>	
	<div class="form-group">
		<label class="col-sm-3 control-label" >{{Zipabox a importer}}</label>
		<div class="col-sm-3">
			<select class="eqLogicAttr form-control" id="zipabox_id">
				<option value="">{{Aucune}}</option>
				<?php
				foreach (zipabox::allzipabox() as $id => $name)
				{
					echo '<option value="' . $id . '">' . $name . '</option>';
				}
				?>
		   </select>
	   </div>
   </div>
   <div  id="import_div" class="form-group">
		<br><br>
		<label class="col-sm-12 control-label ">{{Résultat : }}</label>
		<div  class="col-sm-12" style="border: solid 1px #EEE;border-radius: 2px;" id="import_result">
		</div>
   </div>
<?php
	echo '<script>';
   	echo "
	$('#import_div').hide();
    $('.bt_import').on('click', function () 
	{
	   $.ajax({// fonction permettant de faire de l'ajax
			type: \"POST\", // methode de transmission des données au fichier php
			url: \"plugins/zipabox/core/ajax/zipabox.ajax.php\", // url du fichier php
			data: {
				action: \"bt_import\",
				zipaboxID : $('#zipabox_id').value(),
					},
			dataType: 'json',

		success: function (data) 
		{ // si l'appel a bien fonctionné
			if (data.state != 'ok') 
			{
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#import_result').empty().append(data.result);
			$('#import_div').show();
			//$('#div_alert').showAlert({message: '<i class=\"fa fa-spinner fa-spin fa-fw\"></i> OK!', level: 'success'});
	   }
    });
    });
	";
echo '</script>';
?>