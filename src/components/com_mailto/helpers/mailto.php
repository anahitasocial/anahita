<?php
/**
 * @version		$Id: mailto.php 21078 2011-04-04 20:52:23Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * @package		Joomla.Site
 * @subpackage	com_mailto
 */
class MailtoHelper
{
	/**
	 * Adds a URL to the mailto system and returns the hash 
	 *
	 * @param string url
	 * @return URL hash
	 */
	function addLink($url)
	{
		$hash = sha1($url);
		MailtoHelper::cleanHashes();
		$session =& JFactory::getSession();
		$mailto_links = $session->get('com_mailto.links', Array());
		if(!isset($mailto_links[$hash]))
		{
			$mailto_links[$hash] = new stdClass();
		}
		$mailto_links[$hash]->link = $url;
		$mailto_links[$hash]->expiry = time();
		$session->set('com_mailto.links', $mailto_links);
		return $hash;
	}

	/**
	 * Checks if a URL is a Flash file
	 *
	 * @param string
	 * @return URL
	 */
	function validateHash($hash)
	{
		$retval = false;
		$session =& JFactory::getSession();
		MailtoHelper::cleanHashes();
		$mailto_links = $session->get('com_mailto.links', Array());
		if(isset($mailto_links[$hash]))
		{
			$retval = $mailto_links[$hash]->link;
		}
		return $retval;
	}

	/**
	 * Cleans out old hashes
	 *
	 * @since 1.5.23
	 */
	function cleanHashes($lifetime = 1440)
	{
		// flag for if we've cleaned on this cycle
		static $cleaned = false;
		if(!$cleaned)
		{
			$past = time() - $lifetime;
			$session =& JFactory::getSession();
			$mailto_links = $session->get('com_mailto.links', Array());
			foreach($mailto_links as $index=>$link)
			{
				if($link->expiry < $past)
				{
					unset($mailto_links[$index]);
				}
			}
			$cleaned = true;
		}
		
		
	}
}

