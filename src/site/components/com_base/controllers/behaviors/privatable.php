<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Privatable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorPrivatable extends KControllerBehaviorAbstract
{	
	/**
	 * Set a privacy for a privatable entity 
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	public function _actionSetPrivacy($context)
	{
		$data 	= $context->data;
		$names	= KConfig::unbox($data->privacy_name);
		settype($names, 'array');
		foreach($names as $name) {
			$this->getItem()->setPermission($name, $data->$name);	
		}
	}	
}