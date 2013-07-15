<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Actor Authorizer
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainAuthorizerDefault extends LibBaseDomainAuthorizerDefault
{
	/**
	 * Check if the actor authorize adminisrating it
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */	
	protected function _authorizeAdministration(KCommandContext $context)
	{
	    $ret = false;
	    
	    if ( $this->_entity->authorize('access', $context) )
	    {
            if ( $this->_viewer->isAdministrator() )
            {
                $ret = $this->_viewer->administrator($this->_entity);
            }
	        
	        if ( $context->strict !== true )
	        {
	            $ret = $ret || $this->_viewer->admin();
	        }
	    }
		
		return (bool)$ret;
		
	}
	
    /**
     * Check if the viewer can set certain privacy value
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */     
    protected function _authorizeSetPrivacyValue(KCommandContext $context)
    {
        $value = $context->value;
        
        if ( $this->_entity->authorize('administration') ) {        
            return true;    
        }
        
        switch($value) 
        {
            case LibBaseDomainBehaviorPrivatable::GUEST :
            case LibBaseDomainBehaviorPrivatable::REG :
                $ret = true;
                break;
            case LibBaseDomainBehaviorPrivatable::FOLLOWER :                                
                $ret = $this->_entity->isFollowable() && $this->_entity->leading($this->_viewer);
                break;
            default :
                $ret = $this->_entity->authorize('administration');
        }

        return (bool)$ret;
    }
        
	/**
	 * Check if the actor authorize viewing a resource
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */		
	protected function _authorizeAccess(KCommandContext $context)
	{
        //if entity is not privatable then it doesn't have access to allow method
        if ( !$this->_entity->isPrivatable() )
            return true;
				
	    $ret = true;
        
        if  ( is_person($this->_viewer) && $this->_viewer->admin() ) {
            $ret = true;
        }   
        elseif  ( $this->_entity->isFollowable() && $this->_entity->blocking($this->_viewer) )
            $ret = false;                
        else
            $ret = (bool)$this->_entity->allows($this->_viewer, 'access');
            
        return $ret;
		
	}
	
    /**
     * Authorizes an action on resources owned by the actor
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeAction(KCommandContext $context)
    {
        //if entity is not privatable then it doesn't have access to allow method
        if ( !$this->_entity->isPrivatable() )
            return true;
        
        //if viewer is admin then return true on the action
        if  ( is_person($this->_viewer) && $this->_viewer->admin() ) {
            return true;
        }
                            
        $action = $context->action;
        
        //any action on the actor requires being a follower by default
        $context->append(array(
            'default' => LibBaseDomainBehaviorPrivatable::FOLLOWER
        ));
        
        //not access to the entiy
        if ( $this->_entity->authorize('access') === false )
            return false;
        
        $parts = explode(':', $action);
        $component = array_shift($parts);
        //check if it's a social app then if it's enabled
        if ( $component ) 
        {
        	      
        	$component = $this->getService('repos://site/components.component')
        						->find(array('component'=>$component));

        	if ( $component )
        	{
        	    if ( $component->authorize('action',
        	            array('actor'	 => $this->_entity,
        	                  'action'   => $parts[1],
        	                  'resource' => $parts[0],
        	            )) === false ) {
        	        
        	        return false;
        	    }
        	}
        }
        
        return $this->_entity->allows($this->_viewer, $action, $context->default);
    }
	
	/**
	 * If true then owner's name is visiable to the viewer, if not the default name is 
	 * displayed
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */	
	protected function _authorizeFollower(KCommandContext $context)
	{                
        //viewer can only follow actor if and only if
        //viewer is leadable and actor is followable           
        if ( $this->_entity->isFollowable() && !$this->_viewer->isLeadable() )
            return false;
                    
        if ( $this->_viewer->eql($this->_entity) )
            return false;
            
        if ( is_guest($this->_viewer) )
            return false;
            
	    if ( $this->_entity->authorize('access', $context) === false )
	    {
	        if ( $this->_entity->isLeadable() && $this->_entity->following($this->_viewer) )
	            return true;
	        else
	            return false;
	    }

        //if the viewer is blocking the entity, then it can not follow
        //the entity
		if ( $this->_viewer->isFollowable() && $this->_viewer->blocking($this->_entity) )
			return false;
		
		return true;
	}
     
    /**
     * Return if the viewer can request to follow the actor
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeRequester(KCommandContext $context)
    {
        //viewer can only follow actor if and only if
        //viewer is leadable and actor is followable           
        if ( $this->_entity->isFollowable() && !$this->_viewer->isLeadable() )
            return false;
             
        if ( !$this->_entity->allowFollowRequest )
            return false; 
                        
        if ( $this->_viewer->eql($this->_entity) )
            return false;
            
        if ( is_guest($this->_viewer) )
            return false;

        //cant' send a requet if already following
        if ( $this->_viewer->following($this->_entity) )
            return false;
           
        //can't send a request if the viewer can follow
         if  ($this->_entity->authorize('follower', array('viewer'=>$this->_viewer)))
            return false;
         
        //cant' send a requet if already requested
        if ( $this->_entity->requested($this->_viewer) )
            return false;
            
        //if the viewer is blocking the entity, then it can not follow
        //the entity
        if ( $this->_viewer->isFollowable() && $this->_viewer->blocking($this->_entity) )
            return false;
        
        return true;
    } 
             
    /**
     * Checks whether the viewer can unfollow the actor
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeUnfollow(KCommandContext $context)
    {
        //if the viewer is not following then return false;
        //Riddle : HOW can you unfollow an actor that you are not following
        if ( !$this->_viewer->following($this->_entity) )
            return false;
                    
        //if entity is adminitrable and the viewer is an admin
        //and there are only one admin. 
        //then the viewer can't unfollow
        if ( $this->_entity->isAdministrable() 
                && $this->_entity->administratorIds->offsetExists($this->_viewer->id) ) 
        {            
            return $this->_entity->administratorIds->count() >= 2;
        }
            
        return true;
    }
            
    /**
     * Return if the viewer can remove an admin of an actor. It returns true
     * if an actor has at least two actors 
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeRemoveAdmin(KCommandContext $context)
    {
        if ( $this->_entity->isAdministrable() ) {
            return $this->_entity->administratorIds->count() >= 2;             
        }
        
        return false;
    }
    
    /**
     * Check if a node authroize being subscribed too
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */
    protected function _authorizeSubscribe($context)
    {
        $entity = $this->_entity;
        
        if ( is_guest($this->_viewer) )
            return false;
    
        if ( !$entity->isSubscribable() )
            return false;
        
        return $this->_viewer->following($entity);
    }
    
    /**
     * Check if a person can be deleted by the viewer
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeDelete(KCommandContext $context)
    {        
        return $this->_entity->authorize('administration');    
    }
        
	/**
	 * If true then owner's name is visiable to the viewer, if not the default name is 
	 * displayed
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */	
	protected function _authorizeBlocker(KCommandContext $context)
	{
        //viewer can only block actor from following them if and only if
        //actor is leadable (can follow ) and viewer is followable        
        if ( !$this->_entity->isLeadable() || !$this->_viewer->isFollowable() ) {
            return false;    
        }

        if ( is_guest($this->_viewer) )
            return false;
                           
        if ( $this->_viewer->eql($this->_entity) )
            return false;
         
        //if entity is administrable and the viewer is one of the admins
        //then it can not be blocked 
        if ( $this->_viewer->isAdministrable() 
                && $this->_viewer->administratorIds->offsetExists($this->_entity->id) ) 
        {
            return false;
        }
                 
//         //if the entity is   
//        if ( $this->_entity->following($this->_viewer) )
//            return true;
//            
//	    if ( !$this->_entity->authorize('access', $context) )
//	        return false;
		
		return true;
	 }
}

?>