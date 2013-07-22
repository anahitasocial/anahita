<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default App Dispatcher
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDispatcherDefault extends LibBaseDispatcherAbstract
{
	/**
	 * Return a dispatcher object
	 * 
	 * @param array $config Configuration
	 * 
	 * @return LibBaseDispatcherAbstract
	 */	
	static public function getInstance($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
	        'component' 	=> substr(KRequest::get('get.option', 'cmd'), 4)
		));
		$identifier = KService::getIdentifier('com:'.$config->component.'.dispatcher');		
		$identifier->application = JFactory::getApplication()->isAdmin() ? 'admin' : 'site';
		register_default(array('identifier'=>$identifier, 'default'=>'ComBaseDispatcher'));
		return KService::get($identifier, KConfig::unbox($config));
	}
}