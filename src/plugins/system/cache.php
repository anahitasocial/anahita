<?php
/**
* @version		$Id: cache.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! Page Cache Plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class  plgSystemCache extends JPlugin
{

	var $_cache = null;

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemCache(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//Set the language in the class
		$config =& JFactory::getConfig();
		$options = array(
			'cachebase' 	=> JPATH_BASE.DS.'cache',
			'defaultgroup' 	=> 'page',
			'lifetime' 		=> $this->params->get('cachetime', 15) * 60,
			'browsercache'	=> $this->params->get('browsercache', false),
			'caching'		=> false,
			'language'		=> $config->getValue('config.language', 'en-GB')
		);

		jimport('joomla.cache.cache');
		$this->_cache =& JCache::getInstance( 'page', $options );
	}

	/**
	* Converting the site URL to fit to the HTTP request
	*
	*/
	function onAfterInitialise()
	{
		global $mainframe, $_PROFILER;
		$user = &JFactory::getUser();

		if($mainframe->isAdmin() || JDEBUG) {
			return;
		}

		if (!$user->get('aid') && $_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->_cache->setCaching(true);
		}

		$data  = $this->_cache->get();

		if($data !== false)
		{
			// the following code searches for a token in the cached page and replaces it with the
			// proper token.
			$token	= JUtility::getToken();
			$search = '#<input type="hidden" name="[0-9a-f]{32}" value="1" />#';
			$replacement = '<input type="hidden" name="'.$token.'" value="1" />';
			$data = preg_replace( $search, $replacement, $data );

			JResponse::setBody($data);

			echo JResponse::toString($mainframe->getCfg('gzip'));

			if(JDEBUG)
			{
				$_PROFILER->mark('afterCache');
				echo implode( '', $_PROFILER->getBuffer());
			}

			$mainframe->close();
		}
	}

	function onAfterRender()
	{
		global $mainframe;

		if($mainframe->isAdmin() || JDEBUG) {
			return;
		}

		$user =& JFactory::getUser();
		if(!$user->get('aid')) {
			//We need to check again here, because auto-login plugins have not been fired before the first aid check
			$this->_cache->store();
		}
	}
}
