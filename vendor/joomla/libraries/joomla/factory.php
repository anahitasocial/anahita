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

		if (! is_object($instance)) {
			$instance = JFactory::_createMailer();
		}

		return $instance;
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
		$mail =& JMail::getInstance();

		// Set default sender
		$mail->setSender(array(
			$mailfrom,
			$fromname
		));

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
