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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    
    ajax::init();

 	if (init('action') == 'bt_import') 
    {
		if (init('zipaboxID') != '')
		{
			$r = json_decode(zipabox::CallZipabox(init('zipaboxID'), 'endpoints'), true);
			if (!is_array($r)) ajax::error("ENDPOINTS-1-La réponse de la Zipabox n'est pas conforme (not an array).");
			// Demande des pieces (rooms)
			$rr = json_decode(zipabox::CallZipabox(init('zipaboxID'), 'rooms/'), true);
			zipabox::log( 'debug', 'ROOMS => ' . json_encode($rr));
			if (!is_array($rr)) ajax::error("ROOMS-1-La réponse de la Zipabox n'est pas conforme (not an array).");
			$rooms = array_column($rr, 'name', 'id');
			$t = '';
			// Création des équipements
			foreach($r as $v)
			{
				if (isset($v['name']) && isset($v['uuid']))
				{
					$room_name = '';
					$object_id = null;
					if (isset($v['room'])) 
					{
						$room_name = strtolower($rooms[$v['room']]);
						zipabox::log( 'debug', '$room_name [ ' . $v['room'] . ' ] => ' . $room_name);

						foreach (object::all() as $object) 
						{
							if (strtolower($object->getName()) ==  $room_name)
							{
								$object_id = $object->getId();
								break;
							}
						}
					}
					
					$t .= $v['name'] . ' = ' . $v['uuid'] . '<br>';
					$EqLogicID = zipabox::CreateEqLogic($v['name'], init('zipaboxID'), $v['uuid'], $object_id);
					
					// Création des commandes
					$rr = json_decode(zipabox::CallZipabox(init('zipaboxID'), 'endpoints/' .$v['uuid'] . '?attributes=true'), true);
					zipabox::log( 'debug', '$rr 1 => ' . json_encode($rr));
					zipabox::log( 'debug', '$rr[attributes] 2 => ' . json_encode($rr['attributes']));
					
					$rrr = $rr['attributes'];
					//$rr = json_decode(zipabox::CallZipabox(init('zipaboxID'), 'attributes/' .$v['uuid'] . '?definition=true&value=true'), true);
					//zipabox::log( 'debug', 'CallZipabox => attributes/' .$v['uuid'] . '?definition=true&value=true');
					if (!is_array($rrr)) 
					{
						zipabox::log( 'debug', "ENDPOINTS,ATTRIBUTES-2-La réponse de la Zipabox n'est pas conforme (not an array).");
						continue;
					}
					$i = 0;
					foreach($rrr as $vv)
					{						
						zipabox::log( 'debug', 'BCLE CMD i ' . $i .' => ' . json_encode($vv));
						if (isset($vv['name']) && isset($vv['uuid']))
						{
							$i++;
							$t .= ' > ' . $vv['name'] . ' = ' . $vv['uuid'] . '<br>';
							zipabox::CreateCmd($EqLogicID, $vv);
						}
						else  ajax::error("CMD - Les tags 'name' et/ou 'uuid' sont manquants. " . json_encode($rr));
					}
					// Si pas de commandes trouvées, on désactive l'équipement
					if ($i == 0)
					{
						$EqLogic = eqLogic::byid($EqLogicID);
						$EqLogic->setIsEnable(0);
						$EqLogic->save();
						zipabox::log( 'debug', $EqLogic->getName() . " - Pas de commandes trouvées, on désactive l'équipement.");
					}
				}
				else  ajax::error("EQLOGIC - Les tags 'name' et/ou 'uuid' sont manquants.");
			}
			ajax::success($t);
		}
		else ajax::error('Aucune Zipabox de choisie.');
	}   
	
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action') . ' zipaboxID = ' . init('zipaboxID'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>