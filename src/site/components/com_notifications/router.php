<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Notifications
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Notification Router
 * 
 * @category   Anahita
 * @package    Com_Notifications
 */
class ComNotificationsRouter extends ComBaseRouterDefault
{
	
	/**
	 * (non-PHPdoc)
	 * @see ComBaseRouterAbstract::parse()
	 */
	public function parse(&$segments)
	{
		$query = array();
		$path  = implode('/', $segments);
		if ( $path == 'new' ) {
			array_pop($segments);
			$query = array_merge(parent::parse($segments), array('new'=>true));
		} else {
			return parent::parse($segments);
		}
		return $query;
	}
}