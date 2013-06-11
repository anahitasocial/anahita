<?php 
/**
 * @version		$Id$
 * @category	Plg_Connect
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

defined('_JEXEC') or die();

/**
 * Connect Echo plugin
 *
 * @category	Plg_Connect
 */
class PlgConnectEcho extends PlgKoowaDefault
{
	/**
	 * Constructor
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function __construct(& $subject, $config) 
	{	
		parent::__construct($subject, $config);
	}

	/**
	 * Called for getting share adapters
	 * 
	 * @param KEvent $event
	 * 
	 * @return void
	 */
	public function onGetShareAdapters(KEvent $event)
	{          
	      $adapters = $event->adapters;
	      $request  = $event->request;
	      $this->getService('repos://site/connect.session');
	      $sessions = $request->target->sessions;
	      foreach($sessions as $session) 
	      {
	          $identifier = $this->getIdentifier('com://site/connect.sharer.'.$session->get('api'));
	          $sharer     = $this->getService($identifier, 
	                          array('session'=>$session->api));
	          $adapters[] = $sharer;
	      }
	}		
}