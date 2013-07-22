<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Executable Behavior
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsControllerBehaviorExecutable extends LibBaseControllerBehaviorExecutable
{    		
	/**
	 * Authorize Delete. Only if the viewer if is an admin of the actor
     * 
	 * @return boolean
	 */
	public function canDelete()
	{		
		return $this->getItem()->authorize('delete');
	}
	
	/**
	 * Authorize Read
	 * 
	 * @return boolean
	 */
	public function canRead()
	{	    
	    if ( $this->layout == 'add' )
	        return $this->canAdd();	        
	    	    
	    return true;
	}

	/**
	 * Authorize Edit
     * 
	 * @return boolean
	 */
	public function canEdit()
	{		
		return $this->getItem()->authorize('administration');
	}
		
	/**
	 * Authorize Add
     * 
	 * @return boolean
	 */
	public function canAdd()
	{
	    $app    = $this->getService('repos://site/apps.app')
                    ->fetch(array('component'=>'com_'.$this->getIdentifier()->package));
        $result = false;
        
	    if ( $app ) {
	        $result = $app->authorize('publish');
        }
	        
		return $result;
	}
	
    /**
     * Authorize following the actor
     * 
     * @return boolean
     */
    public function canAddrequester()
    {
        if ( !$this->actor )
            return false;
            
        if ( !$this->getItem() )
            return false;
        
        return $this->getItem()->authorize('requester', array('viewer'=>$this->actor));        
    }
        
	/**
	 * Authorize following the actor
     * 
	 * @return boolean
	 */
	public function canAddfollower()
	{
        if ( !$this->actor )
            return false;
            
	    if ( !$this->getItem() )
            return false;
	    
	    return $this->getItem()->authorize('follower', array('viewer'=>$this->actor));	    
	}
    
    /**
     * Authorize unfollowing the actor
     * 
     * @return boolean
     */
    public function canDeletefollower()
    {
        if ( !$this->actor )
            return false;
            
        if ( !$this->getItem() )
            return false;
        
        return $this->getItem()->authorize('unfollow', array('viewer'=>$this->actor));        
    }

    /**
     * Authorize blocking the actor
     * 
     * @return boolean
     */
    public function canAddblocked()
    {
        if ( !$this->actor )
            return false;
            
        if ( !$this->getItem() )
            return false;
        
        return $this->actor->authorize('blocker', array('viewer'=>$this->getItem()));     
    }
    
    /**
     * Return if the admin can be removed
     * 
     * @return boolean
     */    
    public function canRemoveadmin()
    {
        return $this->getItem()->authorize('remove.admin', array('admin'=>$this->admin));
    }
    
    /**
     * Return if the requester can be confirmed
     * 
     * @return boolean
     */    
    public function canConfirmrequester()
    {        
        return !is_null($this->requester);
    }

    /**
     * Return if the requester can be confirmed
     *   
     * @return boolean
     */    
    public function canIgnorerequester()
    {
        return !is_null($this->requester);
    }    
        
    /**
     * Return if the admin can be removed
     * 
     * @return boolean
     */    
    public function canAddadmin()
    {                
        return !is_null($this->admin);   
    }
        
    /**
     * If the viewer has been blocked by an actor then don't bring up the actor
     * 
     * @param KCommandContext $context The CommandChain Context
     * 
     * @return boolean
     */
    public function canExecute(KcommandContext $context)
    {        
        if ( $this->getItem() && $this->getItem()->blocking(get_viewer()) )
            return false;
        
        $action = '_action'.ucfirst($context->action);
        
        //if the action is an admin action then check if the
        //viewer is an admin
        if ( $this->isAdministrable() ) 
        {
            $methods = $this->getBehavior('administrable')->getMethods();
            
            if ( in_array($action, $methods) ) {              
                if ( $this->canAdministare() === false ) {
                    return false;
                }
            }
        }
             
        return parent::canExecute($context);
    }        
    
    /**
     * Return if a the viewer can administare
     *
     * @return boolean
     */     
    public function canAdministare()
    {
        if ( !$this->getItem() ) {
            return false;
        }
                
        return $this->getItem()->authorize('administration');
    }
}