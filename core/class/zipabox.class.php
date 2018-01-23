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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class zipabox extends eqLogic {
	/*	 * *************************Attributs****************************** */



	/*	 * ***********************Methode static*************************** */
	public static function log($log1, $log2)
	{
		if (config::byKey('ActiveLog', 'zipabox', false)) log::add('zipabox', $log1, $log2);
	}

	// Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
	public static function cron30()
	{
		foreach (zipabox::allzipabox() as $i => $name)
		{
			$username = config::byKey('ZIusername_' . $i , 'zipabox', 'none');
			$password = sha1(config::byKey('ZIpassword_' . $i , 'zipabox', 'none'));

			$url = zipabox::GetZipaboxURL(true);
			$json = json_decode(file_get_contents($url . 'Initialize'), true);
			$jsessionid = $json['jsessionid'];
			$nonce = $json['nonce'];

			$token = sha1 ($nonce . $password);
			$parameters = "method=SHA1&username=$username&password=$token";
			$ch = curl_init($url . 'Login');
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=' . $jsessionid);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);

			zipabox::log( 'debug', 'JSESSIONID=' . $jsessionid);
			zipabox::log( 'debug', 'output=' . $output);

			config::save('jsessionid_' . $i, $jsessionid, 'zipabox');
		}
	}

	// releve d'info toutes les minutes
	public static function cron()
	{
		zipabox::log( 'debug', "Début de Mise à jour des commandes INFOS.");
		$n = 0;
		$eqLogics = eqLogic::byType('zipabox');
		foreach ($eqLogics as $eqLogic)
		{
			if ($eqLogic->getIsEnable() == 0)
			{
				zipabox::log( 'debug', 'L\'équipement zipabox ID  ' . $eqLogic->getId() . ' ( ' . $eqLogic->getName() . ' )  est désactivé.');
				continue;
			}
			$zipabox_id = $eqLogic->getConfiguration('zipabox_id');
			foreach ($eqLogic->getCmd('info') as $cmd)
			{
				$uuid = $cmd->getConfiguration('uuid');
				$r = json_decode(zipabox::CallZipabox( $zipabox_id, 'attributes/' . $uuid . '?definition=true&value=true'), true);
				if (!is_array($r))
				{
					zipabox::log( 'debug', "CRON,ATTRIBUTES,DEFINITION-1-La réponse de la Zipabox n'est pas conforme (not an array).");
					continue;
				}
				if (isset($r['value']))
				{
					$cmd->setCollectDate(date('Y-m-d H:i:s'));
					$cmd->event($r['value']['value']);
					$cmd->setConfiguration('value', $r['value']['value']);
					$cmd->save();
					$n++;
				}
			}
		}
		zipabox::log( 'debug', "Fin de Mise à jour des commandes INFOS ( Nombre : " . $n ." ).");
	}

	public static function allzipabox()
	{
		$a = array();
		$ZiNumber = config::byKey('ZiNumber', 'zipabox', 1);
		if ($ZiNumber < 1)
		{
			$ZiNumber = 1;
			config::save('ZiNumber', $ZiNumber, 'zipabox');
		}

		for ($i = 1; $i <= $ZiNumber; $i++)
		{
			$a[$i] = config::byKey('ZIname_' . $i , 'zipabox', 'non défini');
		}
		return $a;
	}


	// Fonction exécutée automatiquement toutes les heures par Jeedom
	public static function cronHourly()
	{
		zipabox::log( 'debug', "Début de vérification de la présence des équipements dans la Zipabox.");

		$uuids = array();
		// on recupère les uuids (endpoints) de toutes les zipabox.
		foreach (zipabox::allzipabox() as $i => $name)
		{
			$r = json_decode(zipabox::CallZipabox($i, 'endpoints'), true);
			if (!is_array($r))
			{
				zipabox::log( 'debug', "ENDPOINTS-cronHourly-La réponse de la Zipabox n'est pas conforme (not an array).");
				continue;
			}
			// on trie uniquement les uuid, puis on les ajoutes aux précédents si plusieurs zipabox
			$uuids = array_merge($uuids, array_column($r, 'uuid'));
		}

		// on cherche dans les équipements ceux qui ont disparus des zipabox, et on les désactives
		$eqLogics = eqLogic::byType('zipabox');
		foreach ($eqLogics as $eqLogic)
		{
			$uuid = $eqLogic->getConfiguration('uuid');
			if (!in_array($uuid , $uuids))
			{
				$eqLogic->setIsEnable(0);
				$eqLogic->save();
				zipabox::log( 'debug', 'L\'équipement zipabox ID ' . $eqLogic->getId() . ' ( ' . $eqLogic->getName() . ' ) est désactivé car non trouvé dans la Zipabox.');
			}
		}

		zipabox::log( 'debug', "Fin de vérification de la présence des équipements dans la Zipabox.");
	}

	/*
	 * Fonction exécutée automatiquement tous les jours par Jeedom
	  public static function cronDayly() {

	  }
	 */



	/*	 * *********************Méthodes d'instance************************* */

	public function preInsert() {

	}

	public function postInsert() {

	}

	public function preSave() {

	}

	public function postSave() {

	}

	public function preUpdate() {

	}

	public function postUpdate() {

	}

	public function preRemove() {

	}

	public function postRemove() {

	}

	public static function CreateEqLogic($name, $zipaboxID, $uuid,  $object_id = null)
	{
		$zipabox = zipabox::byLogicalId($uuid , 'zipabox');
		if (!is_object($zipabox))
		{
			event::add('jeedom::alert', array(
				'level' => 'warning',
				'page' => 'zipabox',
				'message' => __('Nouveau module zipabox detecté.' , __FILE__)
				));
			$zipabox = new zipabox();
			$zipabox->setEqType_name('zipabox');
			$zipabox->setLogicalId($uuid);
			if ($object_id !== null) $zipabox->setObject_id($object_id); // parent
			$zipabox->setIsEnable(1);
			$zipabox->setIsVisible(1);
			$zipabox->setName($name);
			$zipabox->setConfiguration('zipabox_id' , $zipaboxID);
			$zipabox->setConfiguration('uuid' , $uuid);
			$zipabox->save();
			event::add('jeedom::alert', array(
				'level' => 'success',
				'page' => 'zipabox',
				'message' => __('Module zipabox ajouté.' , __FILE__)
				));
			zipabox::log( 'debug', 'EqLogic Added => name : ' . $name . ' uuid : ' . $uuid);
		}
		return $zipabox->getId();
	}

 	public static function CreateCmd($EqLogicID, $vv)
	{
		$uuid = $vv['uuid'];
		$EqLogic = eqLogic::byid($EqLogicID);
		$zipabox_id = $EqLogic->getConfiguration('zipabox_id');

		// Obligatoire pour eviter les "duplicate entry"
		$Nuuid = str_replace('-', '', $uuid);
		// on verifie la longueur du nom
		$NewCmdName = $vv['name'];
		$NewCmdNameLen = strlen($NewCmdName) + 3 + strlen($Nuuid);
		if ( $NewCmdNameLen > 45 ) $NewCmdName = substr($vv['name'] , 0 , 42 - strlen($Nuuid));
		
		$NewCmdNameA = $NewCmdName . '_a_' . $Nuuid;
		$NewCmdNameI = $NewCmdName . '_i_' . $Nuuid;
		$ActionUUID = 'ZI_' . $zipabox_id . '_a_' . $Nuuid;
		$InfoUUID = 'ZI_' . $zipabox_id . '_i_' . $Nuuid;

		//zipabox::log( 'debug', 'cmd 1 Added => ' . json_encode($vv));
		$r = json_decode(zipabox::CallZipabox( $zipabox_id, 'attributes/' . $uuid . '?definition=true&value=true'), true);
		$rr = $r['definition'];
		if (!is_array($rr))
		{
			zipabox::log( 'debug', "ATTRIBUTES,DEFINITION-1-La réponse de la Zipabox n'est pas conforme (not an array).");
			return false;
		}

		if ($rr['writable'])
		{
			// Commande Action
			$cmd = cmd::byLogicalId($ActionUUID);
			if (isset($cmd[0])) $cmd = $cmd[0]; 	// Renvoie un array de tous les objets 'cmd' (ici je n'en veux qu'une donc la '0')

			if (!is_object($cmd))
			{
				$cmd = new zipaboxCmd();
				$cmd->setName($NewCmdNameA);
				if (isset($r['value']))
				{
					$cmd->setConfiguration('value' , $r['value']['value']);
				}
				$cmd->setConfiguration('uuid' , $uuid);
				$cmd->setLogicalId($ActionUUID);
				$cmd->setIsVisible(1);
				$cmd->setType('action');
				$cmd->setSubType('other');
				if (strpos($rr['cluster'], 'Color') !== false)
				{
					if ($rr['attributeType'] == 'NUMBER') $cmd->setSubType('slider');
					if ($rr['attributeType'] == 'STRING') $cmd->setSubType('color');
				}
				$cmd->setEqLogic_id($EqLogicID);
				$cmd->save();

				zipabox::log( 'debug', 'cmd Action Ajoutée => ' . json_encode($vv));
			}
		}
		if ($rr['readable'])
		{
			// Commande Info
			$cmd = cmd::byLogicalId($InfoUUID);
			if (isset($cmd[0])) $cmd = $cmd[0]; 	// Renvoie un array de tous les objets 'cmd' (ici je n'en veux qu'une donc la '0')

			if (!is_object($cmd))
			{
				$cmd = new zipaboxCmd();
				$cmd->setName($NewCmdNameI);
				if (isset($r['value']))
				{
					$cmd->setConfiguration('value' , $r['value']['value']);
					$cmd->event($r['value']['value']);
				}
				$cmd->setConfiguration('uuid' , $uuid);
				$cmd->setLogicalId($InfoUUID);
				$cmd->setIsVisible(1);
				$cmd->setType('info');
				$cmd->setSubType('numeric');
				if ($rr['attributeType'] == 'STRING') $cmd->setSubType('string');
				if ($rr['attributeType'] == 'BOOLEAN') $cmd->setSubType('binary');
				$cmd->setEqLogic_id($EqLogicID);
				zipabox::log( 'debug', 'cmd save => ' . json_encode($vv));
				$cmd->save();

				zipabox::log( 'debug', 'cmd Info Ajoutée => ' . json_encode($vv));
			}
		}
	}

	public static function GetZipaboxURL($init = false)
	{
		$url = 'https://my.zipato.com';
		if ($init) return $url . '/zipato-web/json/';
		else return $url . '/zipato-web/v2/';
	}

	public static function CallZipabox($zipaboxID, $endurl)
	{
		$jsessionid = config::byKey('jsessionid_' . $zipaboxID , 'zipabox', 0);

		$url = zipabox::GetZipaboxURL();

		$ch = curl_init($url . $endurl);
		curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=' . $jsessionid);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);

		zipabox::log( 'debug', 'cmd JSESSIONID=' . $jsessionid);
		zipabox::log( 'debug', 'cmd output=' . $output);
		return $output;
	}

	public static function GetFromZipabox($eqID, $endurl)
	{
		$eqLogic = eqLogic::byid($eqID);
		if (!is_object($eqLogic))
		{
			zipabox::log( 'debug', 'L\'équipement zipabox ID '.$eqID.' n\'existe pas.');
			return false;
		}
		if ($eqLogic->getIsEnable() == 0)
		{
			zipabox::log( 'debug', 'L\'équipement zipabox ID '.$eqID.' est désactivé.');
			return false;
		}

		$ZipaboxID = $eqLogic->getConfiguration('zipabox_id');
		$jsessionid = config::byKey('jsessionid_' . $ZipaboxID , 'zipabox', 0);

		$url = zipabox::GetZipaboxURL();

		$ch = curl_init($url . $endurl);
		curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=' . $jsessionid);
		curl_setopt($ch, CURLOPT_HTTPHEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);

		zipabox::log( 'debug', 'cmd JSESSIONID=' . $jsessionid);
		zipabox::log( 'debug', 'cmd output=' . $output);
		return $output;
	}

	public static function PutToZipabox($eqID, $uuid, $value)
	{
		$eqLogic = eqLogic::byid($eqID);
		if (!is_object($eqLogic))
		{
			zipabox::log( 'debug', 'L\'équipement zipabox ID '.$eqID.' n\'existe pas.');
			return false;
		}
		if ($eqLogic->getIsEnable() == 0)
		{
			zipabox::log( 'debug', 'L\'équipement zipabox ID '.$eqID.' est désactivé.');
			return false;
		}

		$ZipaboxID = $eqLogic->getConfiguration('zipabox_id');
		$jsessionid = config::byKey('jsessionid_' . $ZipaboxID , 'zipabox', 0);

		if (is_array($value)) $value = http_build_query($value);

		$url = zipabox::GetZipaboxURL();

		$ch = curl_init($url . 'attributes/' . $uuid . '/value');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=' . $jsessionid);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: text/plain',
			'Content-Length: ' . strlen($value)
			));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $value);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);

		zipabox::log( 'debug', 'cmd JSESSIONID=' . $jsessionid);
		zipabox::log( 'debug', 'cmd output=' . $output);
	}

	/*
	 * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
	  public function toHtml($_version = 'dashboard') {

	  }
	 */

	/*	 * **********************Getteur Setteur*************************** */
}

class zipaboxCmd extends cmd {
	/*	 * *************************Attributs****************************** */


	/*	 * ***********************Methode static*************************** */


	/*	 * *********************Methode d'instance************************* */

	/*
	 * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
	  public function dontRemoveCmd() {
	  return true;
	  }
	 */

	public function execute($_options = null)
	{
		if ($this->getType() == 'action')
		{
			zipabox::log( 'info','_ - $_options = '. json_encode($_options));
			try
			{
				if ($this->getSubType() == 'other')
				{
					zipabox::PutToZipabox($this->getEqLogic_id(), $this->getConfiguration('uuid'), $this->getConfiguration('value'));
					return false;
				}
				elseif ($this->getSubType() == 'slider')
				{
					if ($_options === null) $_options['slider'] = 77;
					if (!isset($_options['slider'])) $_options['slider'] = 77;
					$value = round($_options['slider']);
					//$this->setConfiguration('value', $value);
					//$this->setConfiguration('minValue', 0);
					//$this->setConfiguration('maxValue', 255);
					//$this->save();
					zipabox::PutToZipabox($this->getEqLogic_id(), $this->getConfiguration('uuid'), $value);
					return false;
				}
				elseif ($this->getSubType() == 'color')
				{
					if ($_options === null) $_options['color'] = '000000';
					if (!isset($_options['color'])) $_options['color'] = '000000';
					$_options['color'] = str_replace('#', '', $_options['color']);
					//$this->setConfiguration('value', $_options['color']);
					//$this->save();
					zipabox::PutToZipabox($this->getEqLogic_id(), $this->getConfiguration('uuid'), $_options['color']);
					return false;
				}
				elseif ($this->getSubType() == 'select')
				{
					if (!isset($_options['select'])) return false;
					zipabox::PutToZipabox($this->getEqLogic_id(), $this->getConfiguration('uuid'), $_options['select']);
					return false;
				}
				elseif ($this->getSubType() == 'message')
				{
					if ($_options === null) return false;
					if (!isset($_options['title'])) $_options['title'] = '';
					if (!isset($_options['message'])) $_options['message'] = '';
					if ($_options['title'] == '') $_options['title'] = 'Zipabox says:';
					if ($_options['message'] == '') $_options['message'] = 'Nothing yet...';
					zipabox::PutToZipabox($this->getEqLogic_id(), $this->getConfiguration('uuid'), $_options['title'] . ' ' . $_options['message']);
					return false;
				}
				else zipabox::log( 'error','CMD execute $this->getSubType() = ' . $this->getSubType() . ' - $_options = '. json_encode($_options));
			}
			catch (Exception $e)
			{
				zipabox::log( 'error', 'CMD execute Exception $e = ' . $e . ' - $_options = ' . json_encode($_options));
				return false;
			}
		}
	}

	/*	 * **********************Getteur Setteur*************************** */
}

?>
