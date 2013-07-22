<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Story Authorizer
 *   
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesDomainAuthorizerStory extends LibBaseDomainAuthorizerDefault
{
    /**
     * Story List
     * 
     * @var array
     */
    static public $black_list = array('actor_follow','avatar_edit');
    
	/**
	 * Check if a node authroize being updated
	 * 
	 * @param  KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeDelete($context)
	{
		$owneids = $this->_entity->getIds('owner');
				
		if ( count($owneids) > 1 )
			return false;			
		elseif ( $this->_viewer->admin()  )
			return true;
		elseif ( $this->_entity->owner->authorize('administration') )
			return true;
			
		return false;
	}
	
	/**
	 * Checks if a comment can be added to a story
	 * 
	 * @param  KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeAddComment($context)
	{
		if ( isset($this->_entity->object) ) 
		{
			if ( is_array($this->_entity->object) )	
				return false;

			if ( !$this->_entity->object->isAuthorizer() ) return false;
			
			return $this->_entity->object->authorize('add.comment');
		}
		
		if ( in_array($this->_entity->name, self::$black_list) )
			return false;
		
        return parent::_authorizeAddComment($context);
	}	
	
	/**
	 * Authoriz vote 
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeVote($context)
	{
		if ( isset($this->_entity->object) ) {
			if ( is_array($this->_entity->object) )	
				return false;
			
			if ( !$this->_entity->object->isAuthorizer() ) return false;
						
			return $this->_entity->object->authorize('vote');
		}
		
		if ( in_array($this->_entity->name, self::$black_list) )
			return false;
			
		return !$this->_viewer->guest();
	}
	
	/**
	 * Authoriz deleting a comment 
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeDeleteComment($context)
	{
		$comment = $context->comment;
		//guest can't delete
		if ( $this->_viewer->guest() )
			return false;

		//if story has an object forwar authorization to the object	
		if ( !empty($story->object) )
			return $story->object->authorize('delete.comment');
		
		if ( $this->_viewer->admin() || $this->_viewer->eql($comment->author) || $this->_entity->owner->authorize('administration') )
			return true;
						
		return false;
	}
	
	/**
	 * Checks if a comment of a  node can be edited
	 * 
	 * @param  KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeEditComment($context)
	{
		return false;
	}	
}


?>