<?php

/**
 * Default Actor Authorizer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComActorsDomainAuthorizerDefault extends LibBaseDomainAuthorizerDefault
{
    /**
     * Check to see if viewer can enable or disable a person's account.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeChangeEnabled(AnCommandContext $context)
    {
        return $this->_authorizeAdministration($context);
    }

    /**
    * Check to see if the viewer can edit this actor
    *
    * @param AnCommandContext $context Context parameter
    *
    * @return bool
    */
    protected function _authorizeEdit(AnCommandContext $context)
    {
        return $this->_authorizeAdministration($context);
    }

    /**
     * Check if the actor authorize adminisrating it.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeAdministration(AnCommandContext $context)
    {
        $ret = false;

        if ($this->_entity->authorize('access', $context)) {

            if ($this->_viewer->isAdministrator()) {
                $ret = $this->_viewer->administrator($this->_entity);
            }

            if ($context->strict !== true) {
                $ret = $ret || $this->_viewer->admin();
            }
        }

        return (bool) $ret;
    }

    /**
     * Check if the viewer can set certain privacy value.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeSetPrivacyValue(AnCommandContext $context)
    {
        $value = $context->value;

        if ($this->_entity->authorize('administration')) {
            return true;
        }

        switch ($value) {
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

        return (bool) $ret;
    }

    /**
     * Check if the actor authorize viewing a resource.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeAccess(AnCommandContext $context)
    {
        //if entity is not privatable then it doesn't have access to allow method
        if (!$this->_entity->isPrivatable()) {
            return true;
        }

        if ($this->_viewer->admin()) {
            return true;
        }

        if ($this->_entity->isFollowable() && $this->_entity->blocking($this->_viewer)) {
            return false;
        }

        if ($this->_entity->allows($this->_viewer, 'access')) {
            return true;
        }

        return false;
    }

    /**
     * Authorizes an action on resources owned by the actor.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeAction(AnCommandContext $context)
    {
        //if entity is not privatable then it doesn't have access to allow method
        if (!$this->_entity->isPrivatable()) {
            return true;
        }

        //if viewer is admin then return true on the action
        if ($this->_viewer->admin()) {
            return true;
        }

        $action = $context->action;

        //any action on the actor requires being a follower by default
        $context->append(array(
            'default' => LibBaseDomainBehaviorPrivatable::FOLLOWER,
        ));

        //not access to the entiy
        if (! $this->_entity->authorize('access')) {
            return false;
        }

        $parts = explode(':', $action);

        $component = array_shift($parts);
        //check if it's a social app then if it's enabled

        if ($component) {

            $component = $this->getService('repos:components.component')->find(array('component' => $component));

            if (
                $component &&
                $component->authorize('action', array(
                                                    'actor' => $this->_entity,
                                                    'action' => $parts[1],
                                                    'resource' => $parts[0],
                                                    )) === false
            ) {
                return false;
            }
        }

        return $this->_entity->allows($this->_viewer, $action, $context->default);
    }

    /**
     * If true then owner's name is visiable to the viewer, if not the default name is
     * displayed.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeFollower(AnCommandContext $context)
    {
        //viewer can only follow actor if and only if viewer is leadable and actor is followable
        if ($this->_entity->isFollowable() && !$this->_viewer->isLeadable()) {
            return false;
        }

        if ($this->_viewer->eql($this->_entity)) {
            return false;
        }

        if (is_guest($this->_viewer)) {
            return false;
        }

        if (! $this->_entity->authorize('access', $context)) {

            if ($this->_entity->isLeadable() && $this->_entity->following($this->_viewer)) {
                return true;
            } else {
                return false;
            }
        }

        //if the viewer is blocking the entity, then it can not follow the entity
        if ($this->_viewer->isFollowable() && $this->_viewer->blocking($this->_entity)) {
            return false;
        }

        return true;
    }

    /**
     * If true then the viewer can add new followers to the this actor.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeLead(AnCommandContext $context)
    {
        //obviously guests cannot add new followers
        if (is_guest($this->_viewer)) {
            return false;
        }

        //viewers cannot add new followers to themselves
        if ($this->_viewer->eql($this->_entity)) {
            return false;
        }

        //new followers cannot be added to people
        if (is_person($this->_entity)) {
            return false;
        }

        return $this->_entity->authorize('action', 'leadable:add');
    }

    /**
     * Return if the viewer can request to follow the actor.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeRequester(AnCommandContext $context)
    {
        return $this->_entity->allowFollowRequest;
    }

    /**
     * Checks whether the viewer can unfollow the actor.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeUnfollow(AnCommandContext $context)
    {
        //if the viewer is not following then return false;
        //Riddle : How can you unfollow an actor that you are not following
        if (!$this->_viewer->following($this->_entity)) {
            return false;
        }

        //if entity is adminitrable and the viewer is an admin and there are only one admin. then the viewer can't unfollow
        if (
            $this->_entity->isAdministrable() &&
            $this->_entity->administratorIds->offsetExists($this->_viewer->id)
        ) {
            return ($this->_entity->administratorIds->count() >= 2);
        }

        return true;
    }

    /**
     * Return if the viewer can remove an admin of an actor. It returns true
     * if an actor has at least two actors.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeRemoveAdmin(AnCommandContext $context)
    {
        if ($this->_entity->isAdministrable()) {
            return ($this->_entity->administratorIds->count() >= 2);
        }

        return false;
    }

    /**
     * Check if a node authroize being subscribed too.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeSubscribe($context)
    {
        $entity = $this->_entity;

        if (is_guest($this->_viewer)) {
            return false;
        }

        if (! $entity->isSubscribable()) {
            return false;
        }

        return $this->_viewer->following($entity);
    }

    /**
     * Check if a person can be deleted by the viewer.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeDelete(AnCommandContext $context)
    {
        return $this->_entity->authorize('administration');
    }

    /**
     * If true then viewer can block the entity.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeBlock(AnCommandContext $context)
    {
        if (is_guest($this->_viewer)) {
            return false;
        }

        //viewer can only block actor from following them if and only if actor is leadable (can follow ) and viewer is followable
        if (!$this->_entity->isLeadable()) {
            return false;
        }

        if ($this->_viewer->eql($this->_entity)) {
            return false;
        }

        //you can't block an admin
        if ($this->_entity->admin()) {
            return false;
        }

         //if entity is administrable and the viewer is one of the admins then it can not be blocked
        if (
            $this->_entity->isAdministrable() &&
            $this->_entity->administratorIds->offsetExists($this->_viewer->id)
        ) {
            return false;
        }

        return true;
    }

     /**
      * If true the viewer can remove a follower from an actor.
      *
      * @param AnCommandContext $context Context parameter
      *
      * @return bool
      */
     protected function _authorizeUnlead(AnCommandContext $context)
     {
         return $this->_authorizeAdministration($context);
     }
}
