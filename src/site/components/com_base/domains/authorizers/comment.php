<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Comment Authorizer
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDomainAuthorizerComment extends LibBaseDomainAuthorizerDefault
{
	/**
	 * Checks if a comment of a  node can be deleted
	 * 
	 * @param  KCommandContext $context
	 * @return boolean
	 */
	protected function _authorizeDelete($context)
	{
		$ret = false;
		
		$comment = $this->_entity;
		
		//guest can't delete
		if ( $this->_viewer->guest() )
			return false;
						
		if ( $this->_viewer->admin() || $this->_viewer->eql($comment->author) )
			return true;
									
		//check if the parent is ownable and the parent owner authorizes administrator	
		if ( $this->_entity->parent->isOwnable() && $this->_entity->parent->owner->authorize('administration') )
			return true;

		return false;
	}

	/**
	 * Checks if a comment of a  node can be edited
	 * 
	 * @param  KCommandContext $context
	 * @return boolean
	 */
	protected function _authorizeEdit($context)
	{
		return $this->_authorizeDelete($context);
	}	
}