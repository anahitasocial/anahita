<?php

/**
 * Abstract Actor Permission.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class ComActorsControllerPermissionAbstract extends LibBaseControllerPermissionDefault
{
    /**
     * Authorize Delete. Only if the viewer if is an admin of the actor.
     *
     * @return bool
     */
    public function canDelete()
    {
        return $this->getItem()->authorize('delete');
    }

    /**
     * Authorize Read.
     *
     * @return bool
     */
    public function canRead()
    {
        if ($this->getRequest()->get('layout') == 'add') {
            return $this->_mixer->canAdd();
        }

        if ($this->getItem() && $this->getItem()->authorize('access')) {
            return true;
        }

        return false;
    }

    /**
     * Authorize Edit.
     *
     * @return bool
     */
    public function canEdit()
    {
        if ($this->getItem() && $this->getItem()->authorize('administration')) {
            return true;
        }

        return false;
    }

    /**
     * Authorize Add.
     *
     * @return bool
     */
    public function canAdd()
    {
        $result = false;

        $component = $this->getService('repos:components.component')
        ->find(array('component' => 'com_'.$this->getIdentifier()->package));

        if ($component) {
            $result = $component->authorize('add');
        }

        return $result;
    }

    /**
     * Authorize following the actor.
     *
     * @return bool
     */
    public function canAddrequest()
    {
        if (!$this->actor) {
            return false;
        }

        if (!$this->getItem()) {
            return false;
        }

        return $this->getItem()->authorize('requester', array('viewer' => $this->actor));
    }

    /**
     * Authorize following the actor.
     *
     * @return bool
     */
    public function canAddfollow()
    {
        if (!$this->actor) {
            return false;
        }

        if (!$this->getItem()) {
            return false;
        }

        return $this->getItem()->authorize('follower', array('viewer' => $this->actor));
    }

    /**
     * Authorize adding a follower to the actor.
     *
     * @return bool
     */
    public function canAddfollower()
    {
        if (!$this->actor) {
            return false;
        }

        if (!$this->getItem()) {
            return false;
        }

        return $this->getItem()->authorize('leadable', array('viewer' => $this->actor));
    }

    /**
     * Authorize unfollowing the actor.
     *
     * @return bool
     */
    public function canDeletefollow()
    {
        if (!$this->actor) {
            return false;
        }

        if (!$this->getItem()) {
            return false;
        }

        return $this->getItem()->authorize('unfollow', array('viewer' => $this->actor));
    }

    /**
     * Authorize blocking the actor.
     *
     * @return bool
     */
    public function canAddblock()
    {
        if (!$this->actor) {
            return false;
        }

        if (!$this->getItem()) {
            return false;
        }

        if ($this->getItem()->isAdministrable() && !$this->canAdminister()) {
            return false;
        }

        return $this->actor->authorize('blocker', array('viewer' => $this->getItem()));
    }

    /**
     * Return if the admin can be removed.
     *
     * @return bool
     */
    public function canRemoveadmin()
    {
        return $this->getItem()->authorize('remove.admin', array('admin' => $this->admin));
    }

    /**
     * Return if the requester can be confirmed.
     *
     * @return bool
     */
    public function canConfirmrequest()
    {
        return !is_null($this->requester);
    }

    /**
     * Return if the requester can be confirmed.
     *
     * @return bool
     */
    public function canIgnorerequest()
    {
        return !is_null($this->requester);
    }

    /**
     * Return if the admin can be removed.
     *
     * @return bool
     */
    public function canAddadmin()
    {
        return !is_null($this->admin);
    }

    /**
     * If the viewer has been blocked by an actor then don't bring up the actor.
     *
     * @param string $action The action
     *
     * @return bool
     */
    public function canExecute($action)
    {
        if ($this->getItem() && $this->getItem()->blocking(get_viewer())) {
            return false;
        }

        //if the action is an admin action then check if the viewer is an admin
        if ($this->isAdministrable()) {
            $methods = $this->getBehavior('administrable')->getMethods();

            if (in_array('_action'.ucfirst($action), $methods) && $this->canAdminister() === false) {
                return false;
            }
        }

        return parent::canExecute($action);
    }

    /**
     * Return if a the viewer can administer.
     *
     * @return bool
     */
    public function canAdminister()
    {
        if (!$this->getItem()) {
            return false;
        }

        return $this->getItem()->authorize('administration');
    }

    /**
     * Only admins can enable.
     *
     * @return bool
     */
    public function canEnable()
    {
        return $this->canAdminister();
    }

    /**
     * Only admins can disable.
     *
     * @return bool
     */
    public function canDisable()
    {
        return $this->canAdminister();
    }
}
