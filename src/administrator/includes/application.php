<?php
/**
* @version		$Id: application.php 14401 2010-01-26 14:10:00Z louis $
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

jimport('joomla.application.component.helper');

/**
* Joomla! Application class
*
* Provide many supporting API functions
*
* @package		Joomla
* @final
*/
class JAdministrator extends JApplication
{
	/**
	* Class constructor
	*
	* @access protected
	* @param	array An optional associative array of configuration settings.
	* Recognized key values include 'clientId' (this list is not meant to be comprehensive).
	*/
	function __construct($config = array())
	{
		$config['clientId'] = 1;
		parent::__construct($config);

		//Set the root in the URI based on the application name
		JURI::root(null, str_replace('/'.$this->getName(), '', JURI::base(true)));
	}

	/**
	* Initialise the application.
	*
	* @access public
	* @param array An optional associative array of configuration settings.
	*/
	function initialise($options = array())
	{
		// if a language was specified it has priority
		// otherwise use user or default language settings
		if (empty($options['language']))
		{
			$user = & JFactory::getUser();
			$lang	= $user->getParam( 'admin_language' );

			// Make sure that the user's language exists
			if ( $lang && JLanguage::exists($lang) ) {
				$options['language'] = $lang;
			} else {
				$params = JComponentHelper::getParams('com_languages');
				$client	=& JApplicationHelper::getClientInfo($this->getClientId());
				$options['language'] = $params->get($client->name, 'en-GB');
			}
		}

		// One last check to make sure we have something
		if ( ! JLanguage::exists($options['language']) ) {
			$options['language'] = 'en-GB';
		}

		parent::initialise($options);
	}

	/**
	* Route the application
	*
	* @access public
	*/
	function route()
	{
		$uri = JURI::getInstance();

		if($this->getCfg('force_ssl') >= 1 && strtolower($uri->getScheme()) != 'https') {
			//forward to https
			$uri->setScheme('https');
			$this->redirect($uri->toString());
		}
	}

	/**
	 * Return a reference to the JRouter object.
	 *
	 * @access	public
	 * @return	JRouter.
	 * @since	1.5
	 */
	function &getRouter()
	{
		$router =& parent::getRouter('administrator');
		return $router;
	}

	/**
	* Dispatch the application
	*
	* @access public
	*/
	function dispatch($component)
	{
		$document	=& JFactory::getDocument();
		$user		=& JFactory::getUser();

		switch($document->getType())
		{
			case 'html' :
			{
				$document->setMetaData( 'keywords', $this->getCfg('MetaKeys') );

				if ( $user->get('id') ) {
					$document->addScript( JURI::root(true).'/includes/js/joomla.javascript.js');
				}

				JHTML::_('behavior.mootools');
			} break;

			default : break;
		}

		$document->setTitle( htmlspecialchars_decode($this->getCfg('sitename' )). ' - ' .JText::_( 'Administration' ));
		$document->setDescription( $this->getCfg('MetaDesc') );

		$contents = JComponentHelper::renderComponent($component);
		$document->setBuffer($contents, 'component');
	}

	/**
	* Display the application.
	*
	* @access public
	*/
	function render()
	{
		$component	= JRequest::getCmd('option');
		$template	= $this->getTemplate();
		$file 		= JRequest::getCmd('tmpl', 'index');

		if($component == 'com_login') {
			$file = 'login';
		}

		$params = array(
			'template' 	=> $template,
			'file'		=> $file.'.php',
			'directory'	=> JPATH_THEMES
		);

		$document =& JFactory::getDocument();
		$data = $document->render($this->getCfg('caching'), $params );
		JResponse::setBody($data);
	}

	/**
	* Login authentication function
	*
	* @param	array 	Array( 'username' => string, 'password' => string )
	* @param	array 	Array( 'remember' => boolean )
	* @access public
	* @see JApplication::login
	*/
	function login($credentials, $options = array())
	{
		//The minimum group
		$options['group'] = 'Public Backend';

		 //Make sure users are not autoregistered
		$options['autoregister'] = false;

		//Set the application login entry point
		if(!array_key_exists('entry_url', $options)) {
			$options['entry_url'] = JURI::base().'index.php?option=com_user&task=login';
		}

		$result = parent::login($credentials, $options);

		if(!JError::isError($result))
		{
			$lang = JRequest::getCmd('lang');
			$lang = preg_replace( '/[^A-Z-]/i', '', $lang );
			$this->setUserState( 'application.lang', $lang  );			
		}

		return $result;
	}

	/**
	 * Get the template
	 *
	 * @return string The template name
	 * @since 1.0
	 */
	function getTemplate()
	{
		static $template;

		if (!isset($template))
		{
			// Load the template name from the database
			$db =& JFactory::getDBO();
			$query = 'SELECT template'
				. ' FROM #__templates_menu'
				. ' WHERE client_id = 1'
				. ' AND menuid = 0'
				;
			$db->setQuery( $query );
			$template = $db->loadResult();

			$template = JFilterInput::clean($template, 'cmd');

			if (!file_exists(JPATH_THEMES.DS.$template.DS.'index.php')) {
				$template = 'khepri';
			}
		}

		return $template;
	}

	/**
	* Purge the jos_messages table of old messages
	*
	* static method
	* @deprecated As of version 1.5.4
	* @since 1.5
	*/
	function purgeMessages()
	{
		//doesn't do anything
	}

   /**
	* Deprecated, use JURI::root() instead.
	*
	* @since 1.5
	* @deprecated As of version 1.5
	* @see JURI::root()
	*/
	function getSiteURL()
	{
	   return JURI::root();
	}
}
