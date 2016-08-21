<?php
/**
 * @version		$Id: factory.php 19176 2010-10-21 03:06:39Z ian $
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('JPATH_BASE') or die();
/**
 * Joomla Framework Factory class
 *
 * @static
 * @package		Joomla.Framework
 * @since	1.5
 */
class JFactory
{

	/**
	 * Get a configuration object
	 *
	 * Returns a reference to the global {@link JRegistry} object, only creating it
	 * if it doesn't already exist.
	 *
	 * @access public
	 * @param string	The path to the configuration file
	 * @param string	The type of the configuration file
	 * @return object JRegistry
	 */
	static public function &getConfig()
	{
		static $instance;

		if (!is_object($instance)){
			$instance = JFactory::_createConfig();
		}

		return $instance;
	}

	/**
	 * Get a language object
	 *
	 * Returns a reference to the global {@link JLanguage} object, only creating it
	 * if it doesn't already exist.
	 *
	 * @access public
	 * @return object JLanguage
	 */
	static public function &getLanguage()
	{
		static $instance;

		if (!is_object($instance))
		{
			//get the debug configuration setting
			$instance = JFactory::_createLanguage();
			$instance->setDebug(false);
		}

		return $instance;
	}

	/**
	 * Get a cache object
	 *
	 * Returns a reference to the global {@link JCache} object
	 *
	 * @access public
	 * @param string The cache group name
	 * @param string The handler to use
	 * @param string The storage method
	 * @return object JCache
	 */
	static public function &getCache($group = '', $handler = 'callback', $storage = null)
	{
		$handler = ($handler == 'function') ? 'callback' : $handler;

		$conf =& JFactory::getConfig();

		if(!isset($storage)) {
			$storage = $conf->getValue('config.cache_handler', 'file');
		}

		$options = array(
			'defaultgroup' 	=> $group,
			'cachebase' 	=> $conf->getValue('config.cache_path'),
			'lifetime' 		=> $conf->getValue('config.cachetime') * 60,	// minutes to seconds
			'language' 		=> $conf->getValue('config.language'),
			'storage'		=> $storage
		);

		jimport('joomla.cache.cache');

		$cache =& JCache::getInstance( $handler, $options );
		$cache->setCaching($conf->getValue('config.caching'));
		return $cache;
	}

	/**
	 * Get a database object
	 *
	 * Returns a reference to the global {@link JDatabase} object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return object JDatabase
	 */
	static public function &getDBO()
	{
		static $instance;

		if (!is_object($instance))
		{
			//get the debug configuration setting
			$conf =& JFactory::getConfig();
			$debug = $conf->getValue('config.debug');

			$instance = JFactory::_createDBO();
			$instance->debug($debug);
		}

		return $instance;
	}

	/**
	 * Get a mailer object
	 *
	 * Returns a reference to the global {@link JMail} object, only creating it
	 * if it doesn't already exist
	 *
	 * @access public
	 * @return object JMail
	 */
	static public function &getMailer( )
	{
		static $instance;

		if ( ! is_object($instance) ) {
			$instance = JFactory::_createMailer();
		}

		// Create a copy of this object - do not return the original because it may be used several times
		// PHP4 copies objects by value whereas PHP5 copies by reference
		$copy	= (PHP_VERSION < 5) ? $instance : clone($instance);
/*
		if ( JDEBUG || get_config_value('notifications.debug', false) )
		{
		    $emails  = explode(',',get_config_value('notifications.redirect_email'));
		    foreach($emails as $email)
		    {
		        $copy->addBCC($email);
		    }
		}
*/
		return $copy;
	}

	/**
	 * Return a reference to the {@link JURI} object
	 *
	 * @access public
	 * @return object JURI
	 * @since 1.5
	 */
	static public function &getURI($uri = 'SERVER')
	{
		jimport('joomla.environment.uri');

		$instance =& JURI::getInstance($uri);
		return $instance;
	}

	/**
	 * Return a reference to the {@link JDate} object
	 *
	 * @access public
	 * @param mixed $time The initial time for the JDate object
	 * @param int $tzOffset The timezone offset.
	 * @return object JDate
	 * @since 1.5
	 */
	static public function &getDate($time = 'now', $tzOffset = 0)
	{
		jimport('joomla.utilities.date');
		static $instances;
		static $classname;
		static $mainLocale;

		if(!isset($instances)) {
			$instances = array();
		}

		$language =& JFactory::getLanguage();
		$locale = $language->getTag();

		if(!isset($classname) || $locale != $mainLocale) {
			//Store the locale for future reference
			$mainLocale = $locale;
			$localePath = JPATH_ROOT . DS . 'language' . DS . $mainLocale . DS . $mainLocale . '.date.php';
			if($mainLocale !== false && file_exists($localePath)) {
				$classname = 'JDate'.str_replace('-', '_', $mainLocale);
				JLoader::register( $classname,  $localePath);
				if(!class_exists($classname)) {
					//Something went wrong.  The file exists, but the class does not, default to JDate
					$classname = 'JDate';
				}
			} else {
				//No file, so default to JDate
				$classname = 'JDate';
			}
		}
		$key = $time . '-' . $tzOffset;

		if(!isset($instances[$classname][$key])) {
			$tmp = new $classname($time, $tzOffset);
			//We need to serialize to break the reference
			$instances[$classname][$key] = serialize($tmp);
			unset($tmp);
		}

		$date = unserialize($instances[$classname][$key]);
		return $date;
	}



	/**
	 * Create a configuration object
	 *
	 * @access private
	 * @param string	The path to the configuration file
	 * @param string	The type of the configuration file
	 * @return object JRegistry
	 * @since 1.5
	 */
	static private function &_createConfig()
	{
		jimport('joomla.registry.registry');

		// Create the registry with a default namespace of config
		$registry = new JRegistry('config');

		// Create the JConfig object
		$config = new JConfig();

		// Load the configuration values into the registry
		$registry->loadObject($config);

		return $registry;
	}

	/**
	 * Create an database object
	 *
	 * @access private
	 * @return object JDatabase
	 * @since 1.5
	 */
	static private function &_createDBO()
	{
		jimport('joomla.database.database');
		jimport( 'joomla.database.table' );

		$conf =& JFactory::getConfig();

		$host 		= $conf->getValue('config.host');
		$user 		= $conf->getValue('config.user');
		$password 	= $conf->getValue('config.password');
		$database	= $conf->getValue('config.db');
		$prefix 	= $conf->getValue('config.dbprefix');
		$driver 	= $conf->getValue('config.dbtype');
		$debug 		= $conf->getValue('config.debug');

		$options	= array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

		$db =& JDatabase::getInstance( $options );

		if ( JError::isError($db) ) {
			header('HTTP/1.1 500 Internal Server Error');
			jexit('Database Error: ' . $db->toString() );
		}

		if ($db->getErrorNum() > 0) {
			JError::raiseError(500 , 'JDatabase::getInstance: Could not connect to database <br />' . 'joomla.library:'.$db->getErrorNum().' - '.$db->getErrorMsg() );
		}

		$db->debug( $debug );
		return $db;
	}

	/**
	 * Create a mailer object
	 *
	 * @access private
	 * @return object JMail
	 * @since 1.5
	 */
	static private function &_createMailer()
	{
		jimport('joomla.mail.mail');

		$conf	=& JFactory::getConfig();

		$sendmail 	= $conf->getValue('config.sendmail');
		$smtpauth 	= $conf->getValue('config.smtpauth');
		$smtpuser 	= $conf->getValue('config.smtpuser');
		$smtppass  	= $conf->getValue('config.smtppass');
		$smtphost 	= $conf->getValue('config.smtphost');
		$smtpsecure	= $conf->getValue('config.smtpsecure');
		$smtpport	= $conf->getValue('config.smtpport');
		$mailfrom 	= $conf->getValue('config.mailfrom');
		$fromname 	= $conf->getValue('config.fromname');
		$mailer 	= $conf->getValue('config.mailer');

		// Create a JMail object
		$mail 		=& JMail::getInstance();

		// Set default sender
		$mail->setSender(array ($mailfrom, $fromname));

		// Default mailer is to use PHP's mail function
		switch ($mailer)
		{
			case 'smtp' :
				$mail->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
				break;
			case 'sendmail' :
				$mail->useSendmail($sendmail);
				break;
			default :
				$mail->IsMail();
				break;
		}

		return $mail;
	}

	/**
	 * Create a language object
	 *
	 * @access private
	 * @return object JLanguage
	 * @since 1.5
	 */
	static private function &_createLanguage()
	{
		jimport('joomla.language.language');

		$settings = new JConfig();
		$lang	=& JLanguage::getInstance($settings->language);

		return $lang;
	}
}
