<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Package Controller
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsControllerPackage extends ComBaseControllerService
{		
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->registerCallback(array('before.edit', 'after.add'), array($this, 'setMeta'));

	}
		
	/**
	 * Read a package
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */	
	public function _actionRead($context)
	{
		$this->plugins = JPluginHelper::getPlugin('subscriptions');        
		return parent::_actionRead($context);
	}	
	
	/**
	 * Set the entity gid
	 * 
	 * @param KCommandContext $context
	 * @return boolean
	 */
	public function setMeta(KCommandContext $context)
	{
		$data   	 = $context->data;
		$entity  	 = $this->getItem();
		$plugins	 = KConfig::unbox(pick($data->plugins, array()));
		$entity->setPluginsValues($plugins);
	}	
}