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

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

if (!isConnect('admin')) 
{
	throw new Exception('{{401 - Accès non autorisé}}');
}
if (!isset($_GET['zipaboxID'])) throw new Exception("{{ZIPABOX.ID n'est pas conforme.}}");

$r = json_decode(zipabox::CallZipabox($_GET['zipaboxID'], 'box'), true);
if (!is_array($r)) throw new Exception("{{BOX-1-La réponse de la Zipabox n'est pas conforme (not an array).}}");

echo '<table class="table table-bordered">
    <thead>
      <tr class="info">
        <th>{{Champ}}</th>
        <th>{{Valeur}}</th>
      </tr>
    </thead>
    <tbody>';
	
foreach ($r as $k => $v)
{
	if (is_array($v)) continue;
	if ($v === true) $v = 'true';
	if ($v === false) $v = 'false';
	if ($v === null) $v = 'null';
	echo '
	<tr>	
		<td class="col-sm-6">
			' . ucfirst($k) . '
		</td>
		<td class="col-sm-6">
			' . trim($v) . '
		</td>
	</tr>';
}
echo '    
	</tbody>
  </table>';
$r = json_decode(zipabox::CallZipabox($_GET['zipaboxID'], 'contacts/self'), true);
if (!is_array($r)) throw new Exception("{{CONTACTS-1-La réponse de la Zipabox n'est pas conforme (not an array).}}");

echo '<table class="table table-bordered">
    <thead>
      <tr class="info">
        <th>{{Champ}}</th>
        <th>{{Valeur}}</th>
      </tr>
    </thead>
    <tbody>';
	
foreach ($r as $k => $v)
{
	//if (is_array($v)) continue;
	if ($v === true) $v = 'true';
	if ($v === false) $v = 'false';
	if ($v === null) $v = 'null';
	echo '
	<tr>	
		<td class="col-sm-6">
			' . ucfirst($k) . '
		</td>
		<td class="col-sm-6">
			' . trim($v) . '
		</td>
	</tr>';
}
echo '    
	</tbody>
  </table>';
?>