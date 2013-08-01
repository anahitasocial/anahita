<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
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
 * @package    Com_People
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleDomainAuthorizerPerson extends ComActorsDomainAuthorizerDefault 
{
    /**
     * Check if a person can be deleted by the viewer
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeDelete(KCommandContext $context)
    {
        //if viewer same as the person whose
        //profile being vieweed and viewer is a super admin 
        //don't allow to delete
        if ( $this->_viewer->eql($this->_entity) ) {
            if ( $this->_entity->userType == 'Super Administrator' ) {
                return false;   
            }
        }
        
        return parent::_authorizeDelete($context);
    }    	
}