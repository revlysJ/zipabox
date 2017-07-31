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
// TODO : Verifier l'origine de l'appel

zipabox::log( 'debug', 'CALLBACK - Requête reçue : ?' . $_SERVER['QUERY_STRING']);
if (isset($_GET['id']))
{
	$id = trim($_GET['id']);
	if ($id == '' or $id == null or $id == 0) 
	{
		zipabox::log( 'error', 'CALLBACK - id (Commande INFO) fourni vide.');
		return false;
	}

	$cmd = cmd::byId($id);
	if ($cmd === false or $cmd === null)
	{
		zipabox::log( 'error', 'CALLBACK - La commande INFO ' . $id . ' est introuvable.');
		return false;
	}

	if (isset($_GET['value'])) 
	{
		$cmd->setCollectDate(date('Y-m-d H:i:s'));						
		$cmd->event($_GET['value']);		
		$cmd->setConfiguration('value', $_GET['value']);
		$cmd->save();
		zipabox::log( 'debug', "CALLBACK - La commande INFO " . $cmd->getName() . " est mise à jour.");
	}
	else zipabox::log( 'error', "CALLBACK - La commande INFO " . $cmd->getName() . " n'est pas mise à jour.");

}
else zipabox::log( 'error', 'CALLBACK - id (Commande INFO) non défini.');
?>
