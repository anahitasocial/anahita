<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Medium
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
 * @package    Com_Medium
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumControllerBehaviorExecutable extends LibBaseControllerBehaviorExecutable
{
	/**
	 * Authorize Browse
	 * 
	 * @return boolean
	 */
	public function canBrowse()
	{		    		
		$viewer = get_viewer();
        
		if ( $this->isOwnable() && $this->actor ) 
		{
			//a viewer can't see  ownable items coming from another actor's leaders
			if ( $this->filter == 'leaders' ) {
				if ( $viewer->id != $this->actor->id )
					return false;
			}
		}
		return true;
	}
	
	/**
	 * Authorize Read
	 * 
	 * @return boolean
	 */
	public function canRead()
	{
		$actor		= pick($this->actor, get_viewer());
        
		$action 	= 'com_'.$this->_mixer->getIdentifier()->package.':'.$this->_mixer->getIdentifier()->name.':add';
        
        //if repository is ownable then ask the actor if viewer can publish things
		if ( $this->getRepository()->isOwnable() && in_array($this->layout, array('add', 'edit', 'form','composer')))
			return $actor->authorize('action', $action);
				
        if ( !$this->getItem() )
            return false;
        
        //check if an entiy authorize access       
        return $this->getItem()->authorize('access');
	}
	
	/**
	 * Authorize if viewer can add
	 *
	 * @return boolean
	 */
	public function canAdd()
	{
        $actor = $this->actor;
        
	    if ( $actor )
	    {
	        $action  = 'com_'.$this->_mixer->getIdentifier()->package.':'.$this->_mixer->getIdentifier()->name.':add';
	        return $actor->authorize('action',$action);
	    }
	    
	    return false;	    
	}
	
	/**
	 * Authorize Read
	 * 
	 * @return boolean
	 */
	public function canEdit()
	{
        if ( $this->getItem() ) {
            return $this->getItem()->authorize('edit');
        }   
        		
		return false;
	}
	
	/**
	 * If an app is not enabled for an actor then don't let the viewer to see it
	 * 
     * @param KCommandContext $context The CommandChain Context
     *
     * @return boolean
	 */
	public function canExecute(KCommandContext $context)
	{       
	    $viewer = get_viewer();
        
	    if ( KRequest::method() != 'GET' && $viewer->guest() ) {
            return false;
	    }
        
		//check if viewer has access to actor
		if ( $this->isOwnable() && $this->actor )  {
            if ( $this->actor->authorize('access') === false ) 
                return false;			
		}
        
        return parent::canExecute($context);
	}
}