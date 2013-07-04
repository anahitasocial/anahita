<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Medium Authorizer
 *
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumDomainAuthorizerDefault extends LibBaseDomainAuthorizerDefault
{
    /**
     * Check if a medium authorizes acccess
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */
    protected function _authorizeAccess($context)
    {
        if ( is_person($this->_viewer) && $this->_viewer->admin() )
            return true;
            
        if ( $this->_entity->isPrivatable() ) {
            return $this->_entity->allows($this->_viewer,'access');
        }        
    }
        
	/**
	 * Check if a node authroize being updated
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeAdministration($context)
	{
		//if an ownable object ask the owner
		if ( $this->_entity->isOwnable() )
			return $this->_entity->owner->authorize('administration');
		//if the viewer is a moderator
		if ( $this->_viewer->admin() )
			return true;
		//if the viewer is the author of the object			
		if ( $this->_entity->author->id == $this->_viewer->id )
			return true;
	}
	
	/**
	 * Check if a node authroize being updated
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeEdit($context)
	{
		if ( $this->_viewer->guest() )
			return false;
			
		if ( $this->_viewer->admin() )
			return true;
			
		if ( $this->_viewer->eql($this->_entity->author) )
			return true;

		if ( $this->_entity->isOwnable() )
		{
			//if the viewer is the admin of the medium owner
			if ( $this->_entity->owner->authorize('administration') )
				return true;		
		}
		
		return false;
	}
	
	/**
	 * Check if a node authroize being updated
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeDelete($context)
	{
		return $this->_authorizeAdministration($context);
	}
}

?>