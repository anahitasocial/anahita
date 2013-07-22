<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Actor Bar. Specialized toolbar to provide in-app navigation within a context of an actor
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerToolbarActorbar extends ComBaseControllerToolbarMenubar
{
	/**
	 * Actor
	 * 
	 * @return ComActorsDomainEntityActor
	 */
	protected $_actor;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * 
	 * @return void
	 */	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		//loads the actor langauge
		JFactory::getLanguage()->load('com_actors');
	}
	
	/**
	 * Before Controller _actionRead is executed
	 *
	 * @param KEvent $event
	 *
	 * @return void
	 */
	public function onBeforeControllerGet(KEvent $event)
	{
	    $this->getController()->actorbar = $this;
	    	    
	    //set the actor by default to the data actor or viewer
	    if ( $this->getController()->isOwnable() && $this->getController()->actor ) {
	        $this->setActor( $this->getController()->actor );
	    }
	}
		
	/**
	 * Set an actor for the menubar
	 *
	 * @param 	ComActorsDomainEntityActor $actor
	 * @return	void
	 */
	public function setActor($actor)
	{
		$this->_actor = $actor;
		return $this;
	}
	
	/**
	 * Return the actor
	 *
	 * @return	ComActorsDomainEntityActor
	 */
	public function getActor()
	{
		return $this->_actor;
	}	
}