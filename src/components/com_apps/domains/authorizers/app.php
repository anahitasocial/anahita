<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * App Authorizer
 *
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComAppsDomainAuthorizerApp extends LibBaseDomainAuthorizerAbstract 
{
    /**
     * Constats on who can publish if an actor is not present
     */
    const CAN_PUBLISH_ADMIN   = 0;
    const CAN_PUBLISH_SPECIAL = 1;
    const CAN_PUBLISH_ALL     = 2;
    
    /**
     * Authorizes publishing a content for an app. If an actor is passed then
     * the actor must have enabled the app or the app must be global
     *
     * @param KCommandContext $context Context parameter. Options :actor => actor 
     * 
     * @return boolean
     */
    protected function _authorizePublish($context)
    {
        $actor = $context->actor;
        
        if ( $actor ) 
        {            
            if ( $this->_entity->enabled($actor) ||
                 $this->_entity->getAssignment($actor) == ComAppsDomainEntityApp::ACCESS_GLOBAL )
                return true;
            else 
                return false;           
        }
        else {
            
            $can_publish = get_config_value($this->_entity->component,'can_publish', self::CAN_PUBLISH_ALL);
            
            switch($can_publish)
            {
                case self::CAN_PUBLISH_ADMIN :
                    return $this->_viewer->admin();
                case self::CAN_PUBLISH_SPECIAL :
                    return $this->_viewer->userType != 'Registered';                    
                default :
                    return !$this->_viewer->guest();
            }
        }
    }
	
	/**
	 * Return if an app can be installed for an actor or not
	 * 
	 * @param  KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeInstall($context)
	{	      
	    $actor = $context->actor;
        
	    //if an entity is set to always
        if ( $this->_entity->always )
            return false;
            
	    //if app already has been enabled
	    if ( $this->_entity->enabled($actor) ) {
	        return false;
	    }
	    
		$assignment = $this->_entity->getAssignment($actor);
		
		if ( $assignment == ComAppsDomainEntityApp::ACCESS_OPTIONAL ) {
		  return true;  
		}

		return false;
	}
	
	/**
	 * Check if a node authroize being subscribed too
	 * 
	 * @param  KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeUnInstall($context)
	{
	    $actor      = $context->actor;
	    $assignment = $this->_entity->getAssignment($actor);
	    	    
	    if ( $assignment != ComAppsDomainEntityApp::ACCESS_OPTIONAL ) {
	        return false;
	    }
	    
	    //if app already has been enabled
	    if ( $this->_entity->enabled($actor) ) {
	        return true;
	    }
	    
	    return false;		
	}
}