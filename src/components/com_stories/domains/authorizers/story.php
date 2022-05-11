<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Story Authorizer.
 *   
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComStoriesDomainAuthorizerStory extends LibBaseDomainAuthorizerDefault
{
    /**
     * Story List.
     * 
     * @var array
     */
    public static $black_list = array('actor_follow','avatar_edit');

    /**
     * Check if a node authroize being updated.
     * 
     * @param AnCommandContext $context Context parameter
     * 
     * @return bool
     */
    protected function _authorizeDelete($context)
    {
        $owneids = $this->_entity->getIds('owner');

        if (count($owneids) > 1) {
            return false;
        } elseif ($this->_viewer->admin()) {
            return true;
        } elseif ($this->_entity->owner->authorize('administration')) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a comment can be added to a story.
     * 
     * @param AnCommandContext $context Context parameter
     * 
     * @return bool
     */
    protected function _authorizeAddComment($context)
    {
        if (isset($this->_entity->object)) {
            if (is_array($this->_entity->object)) {
                return false;
            }

            if (!$this->_entity->object->isAuthorizer()) {
                return false;
            }

            return $this->_entity->object->authorize('add.comment');
        } else {
            return false;
        }
    }

    /**
     * Authoriz vote.
     *
     * @param AnCommandContext $context Context parameter
     * 
     * @return bool
     */
    protected function _authorizeVote($context)
    {
        if (isset($this->_entity->object)) {
            if (is_array($this->_entity->object)) {
                return false;
            }

            if (!$this->_entity->object->isAuthorizer()) {
                return false;
            }

            return $this->_entity->object->authorize('vote');
        } else {
            return false;
        }
    }

    /**
     * Authoriz deleting a comment.
     *
     * @param AnCommandContext $context Context parameter
     * 
     * @return bool
     */
    protected function _authorizeDeleteComment($context)
    {
        $comment = $context->comment;

        //guest can't delete
        if ($this->_viewer->guest()) {
            return false;
        }

        if (isset($this->_entity->object)) {
            if (is_array($this->_entity->object)) {
                return false;
            }

            if (!$this->_entity->object->isAuthorizer()) {
                return false;
            }

            return $this->_entity->object->authorize('delete.comment');
        }

        return false;
    }

    /**
     * Checks if a comment of a  node can be edited.
     * 
     * @param AnCommandContext $context Context parameter
     * 
     * @return bool
     */
    protected function _authorizeEditComment($context)
    {
        return false;
    }
}
