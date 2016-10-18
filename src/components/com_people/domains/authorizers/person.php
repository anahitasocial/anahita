<?php

/**
 * Person Entity Authorizer.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleDomainAuthorizerPerson extends ComActorsDomainAuthorizerDefault
{
    /**
     * Check if the actor authorize adminisrating it.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeAdministration(KCommandContext $context)
    {
        //if viewer is the same as the person
        if ($this->_viewer->eql($this->_entity)) {
            return true;
        }

        //if viewer is super admin
        if ($this->_viewer->superadmin()) {
            return true;
        }

        //if viewer is admin and the person is not a super admin
        if ($this->_viewer->admin() && !$this->_entity->superadmin()) {
            return true;
        }

        return false;
    }

    /**
     * Check to see if viewer has permission to change usertype.
     */
    protected function _authorizeChangeUsertype(KCommandContext $context)
    {
        if ($this->_entity->eql($this->_viewer)) {
            return false;
        }

        if ($this->_viewer->superadmin()) {
            return true;
        }

        if ($this->_viewer->admin() && !$this->_entity->superadmin()) {
            return true;
        }

        return false;
    }

    /**
     * Check to see if viewer can enable or disable a person's account.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeChangeEnabled(KCommandContext $context)
    {
        //non-admins cannot change enabled status of anybody
        if (!$this->_viewer->admin()) {
            return false;
        }

        //people can't change the enabled status of themselves
        if ($this->_viewer->eql($this->_entity)) {
            return false;
        }

        //only super-admins can change the enable status of another super-admin
        if (!$this->_viewer->superadmin() && $this->_entity->superadmin()) {
            return false;
        }

        return true;
    }

    /**
     * Check if a person can be deleted by the viewer.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeDelete(KCommandContext $context)
    {
        //if viewer same as the person whose profile being vieweed and viewer is a super admin don't allow to delete
        if ($this->_viewer->eql($this->_entity) && $this->_entity->superadmin()) {
            return false;
        }

        return parent::_authorizeDelete($context);
    }

    /**
     * Whether the viewer can mention this person or not.
     *
     * @param KCommandContext $context
     *
     * @return bool
     */
    protected function _authorizeMention(KCommandContext $context)
    {
        if (
            $this->_entity->blocking($this->_viewer) ||
            $this->_viewer->blocking($this->_entity)
            ) {
            return false;
        }

        return true;
    }
}
