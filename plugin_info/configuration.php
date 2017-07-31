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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
		<div class="form-group" >
				<label class="col-lg-5 control-label">{{Activer les logs}}</label>
				<div class="col-lg-5">
					<input type="checkbox" class="configKey " data-l1key="ActiveLog" />
				</div>            
		</div>
		<div class="form-group" >
				<label class="col-lg-5 control-label">{{Nombre de Zipabox}}</label>
				<div class="col-lg-5">
					<input type="number" class="configKey " data-l1key="ZiNumber" />
				</div>            
		</div>
  </fieldset>
</form>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-usb"></i> Vos Zipabox
		</h3>
	</div>
	<div class="panel-body">
		<table id="table_zipa" class="table table-bordered table-stripped">
			<thead>
				<tr>
					<th>{{Nom Zipabox}}</th>
					<th>{{Email Identifiant}}</th>
					<th>{{Mot de Passe}}</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach (zipabox::allzipabox() as $i => $name)
			{
				echo '<tr>';
				echo '<td><input type="text" class=" configKey form-control" data-l1key="ZIname_' . $i .'" placeholder="Nom de la zipabox"/></td>';
				echo '<td><input type="email" class=" configKey form-control" data-l1key="ZIusername_' . $i .'" placeholder="email identifiant"/></td>';
				echo '<td><input type="password" class="configKey form-control" data-l1key="ZIpassword_' . $i .'" placeholder="Mot de passe"/></td>';
				echo '</tr>';
			}
			?>
			</tbody>
		</table>
	</div>
</div>