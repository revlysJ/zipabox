<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('zipabox');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-lg-2 col-md-3 col-sm-4">
		<div class="bs-sidebar">
			<ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<a class="btn btn-warning eqLogicAction" data-action="gotoPluginConf"><i class="fas fa-plus-circle"></i> {{Ajouter une Zipabox}}</a>
				<a class="btn btn-default eqLogicAction" data-action="add"><i class="fas fa-plus-circle"></i> {{Ajouter un Module}}</a>
				<li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
				<?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" style="' . $opacity .'"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
			?>
		   </ul>
	   </div>
   </div>

   <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
	<legend>{{Mes Zipabox}}</legend>
  <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
  <div class="eqLogicThumbnailContainer">
	  <div class="cursor eqLogicAction" data-action="add" >
		  <center>
			  <i class="fas fa-plus-circle" style="font-size: 38px !important;color:#94ca02;"></i>
		  </center>
		  <span style="color:#94ca02"><center>{{Ajouter}}</center></span>
	  </div>
	  <div class="cursor eqLogicAction" data-action="gotoPluginConf" >
		  <center>
			  <i class="fas fa-wrench" style="font-size: 38px !important;color:#94ca02;"></i>
		  </center>
		  <span style="color:#94ca02"><center>{{Configuration}}</center></span>
	  </div>
	  <div class="cursor eqLogicAction" data-action="ZI_import">
		  <center>
			  <i class="fas fa-sign-in-alt fa-rotate-90" style="font-size: 38px !important;color:#f89406;"></i>
		  </center>
		  <span style="color:#f89406"><center>{{Import Modules}}</center></span>
	  </div>
</div>
<legend><i class="fas fa-sitemap"></i> {{Mes controlleurs (Box)}}</legend>
<div class="eqLogicThumbnailContainer">
  <?php
  foreach (zipabox::allzipabox() as $i => $name)
  {
	$opacity = (true) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<div class="cursor eqLogicAction" data-action="bt_zipabox" data-zipaboxid="' . $i . '" style="' . $opacity . '" >';
	echo "<center>";
	echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
	echo "</center>";
	echo '<span style="color:#94ca02">' . ucfirst($name) . '</span>';
	echo '</div>';
  }
  ?>
  </div>
  <legend><i class="fas fa-table"></i> {{Mes modules (endpoints)}}</legend>
  <input class="form-control" placeholder="{{Rechercher}}" style="margin-bottom:4px;" id="in_searchEqlogic" />
<div class="eqLogicThumbnailContainer">
	<?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="' . $opacity . '" >';
	echo "<center>";
	echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
	echo "</center>";
	echo '<span class="name" style="color:#00979C"><br><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
	echo '</div>';
}
?>
</div>
</div>

<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
		<div style="padding-bottom:40px;">
			<a class="btn btn-success eqLogicAction pull-right" data-action="save"  title="{{Sauver et/ou Générer les commandes automatiquement}}"><i class="fas fa-check-circle"></i> {{Sauver / Générer}}</a>
			<a class="btn btn-danger eqLogicAction pull-right" data-action="remove" title="{{Supprimer l'équipement}}"><i class="fas fa-minus-circle"></i> </a>

			<a class="btn btn-default pull-right" id="bt_graphEqLogic" title="{{Graphique de liens}}"><i class="fas fa-object-group"></i> </a>

			<a class="btn btn-default eqLogicAction pull-right" data-action="configure" title="{{Configuration avancée de l'équipement}}"><i class="fas fa-cogs"></i> </a>
			<a class="btn btn-default eqLogicAction pull-right" data-action="gotoPluginConf"  title="{{Page de Configuration du plugin}}"><i class="fas fa-wrench"></i> </a>
			<!-- <a class="btn btn-info eqLogicAction pull-right" data-action="bt_healthSpecific" title="{{Page de Santé du plugin}}"><i class="fas fa-medkit"></i> </a> -->
			<a class="btn btn-info eqLogicAction pull-right bt_plugin_view_log" data-slaveid="-1" data-log="zipabox" title="{{Logs du plugin}}"><i class="fas fa-file"></i> </a>
			<a href="https://revlysj.github.io/zipabox/fr_FR/index" target="_blank" class="btn btn-success eqLogicAction pull-right"  title="{{Lien vers la Documentation du plugin}}"><i class="fas fa-book"></i> </a>
		</div>

  <ul class="nav nav-tabs" role="tablist">
	<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
	<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
	<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
  </ul>
  <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
	<div role="tabpanel" class="tab-pane active" id="eqlogictab">
	  <br/>
	<form class="form-horizontal">
		<fieldset>
			<div class="form-group">
				<label class="col-sm-3 control-label">{{Nom de l'équipement module}}</label>
				<div class="col-sm-3">
					<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
					<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement zipabox}}"/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" >{{Objet parent}}</label>
				<div class="col-sm-3">
					<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
						<option value="">{{Aucun}}</option>
						<?php
foreach (jeeObject::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
				   </select>
			   </div>
		   </div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-9">
			<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
			<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" >{{Zipabox parente}}</label>
		<div class="col-sm-3">
			<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="zipabox_id">
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
	<div class="form-group">
		<label class="col-sm-3 control-label">{{UUID Module (endpoints)}}</label>
		<div class="col-sm-3">
			<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="uuid" placeholder="{{UUID du module (endpoint)}}" required/>
		</div>
	</div>
	<?php //echo network::getNetworkAccess('dnsjeedom');
	?>
</fieldset>
</form>
</div>
<div role="tabpanel" class="tab-pane" id="commandtab">
	    <a class="btn btn-info btn-sm pull-right" id="bt_addzipaboxInfo" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande info}}</a>
    <a class="btn btn-warning btn-sm  pull-right" id="bt_addzipaboxAction" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande Action}}</a><br/><br/>
<!-- <a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Commandes}}</a><br/><br/> -->
<table id="table_cmd" class="table table-bordered table-condensed">
	<thead>
		<tr>
                <th style="width: 230px;">{{Nom}}</th>
                <th>{{Uuid}}</th>
                <th style="width: 100px;">{{Unité}}</th>
                <th style="width: 200px;">{{Paramètres}}</th>
                <th style="width: 100px;"></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
</div>
</div>

</div>
</div>

<?php include_file('desktop', 'zipabox', 'js', 'zipabox');?>
<?php include_file('core', 'plugin.template', 'js');?>
