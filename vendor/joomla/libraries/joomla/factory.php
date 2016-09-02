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
		$handler = ($handler === 'function') ? 'callback' : $handler;

		$conf = new JConfig();

		if(!isset($storage)) {
			$storage = $conf->cache_handler ? $conf->cache_handler : 'file';
		}

		$options = array(
			'defaultgroup' 	=> $group,
			'lifetime' => $conf->cachetime * 60,	// minutes to seconds
			'language' => $conf->language,
			'storage' => $storage
		);

		jimport('joomla.cache.cache');

		$cache = JCache::getInstance( $handler, $options );

		$cache->setCaching($conf->caching);

		return $cache;
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

		$language =& $this->getService('anahita:language');
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
	 * Create a mailer object
	 *
	 * @access private
	 * @return object JMail
	 * @since 1.5
	 */
	static private function &_createMailer()
	{
		jimport('joomla.mail.mail');

		$conf = new JConfig();

		$sendmail 	= $conf->sendmail;
		$smtpauth 	= $conf->smtpauth;
		$smtpuser 	= $conf->smtpuser;
		$smtppass  	= $conf->smtppass;
		$smtphost 	= $conf->smtphost;
		$smtpsecure	= $conf->smtpsecure;
		$smtpport	= $conf->smtpport;
		$mailfrom 	= $conf->mailfrom;
		$fromname 	= $conf->fromname;
		$mailer 	= $conf->mailer;

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
}
