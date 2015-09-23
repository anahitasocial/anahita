<?php

/**
 * Abstract Medium Permission.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class ComMediumControllerPermissionAbstract extends LibBaseControllerPermissionDefault
{
    /**
     * Authorize Read.
     *
     * @return bool
     */
    public function canRead()
    {
        $actor = pick($this->actor, get_viewer());

        //if repository is ownable then ask the actor if viewer can publish things
        if (
            in_array($this->getRequest()->get('layout'), array('add', 'edit', 'form', 'composer'))
        ) {
            $result = ($this->getItem()) ? $this->canEdit() : $this->canAdd();

            return $result;
        }

        if (!$this->getItem()) {
            return false;
        }

        //check if an entiy authorize access
        return $this->getItem()->authorize('access');
    }

    /**
     * Authorize if viewer can add.
     *
     * @return bool
     */
    public function canAdd()
    {
        $actor = $this->actor;
        $viewer = get_viewer();

        if ($actor) {
            if ($viewer->blocking($actor)) {
                return false;
            }

            $action = 'com_'.$this->_mixer->getIdentifier()->package.':'.$this->_mixer->getIdentifier()->name.':add';
            $ret = $actor->authorize('action', $action);

            return $ret !== false;
        }

        return false;
    }

    /**
     * Authorize Read.
     *
     * @return bool
     */
    public function canEdit()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }

    /**
     * If an app is not enabled for an actor then don't let the viewer to see it.
     *
     * @param string $action Action name
     *
     * @return bool
     */
    public function canExecute($action)
    {
        if (
            $this->isOwnable() &&
            $this->actor &&
            $this->actor->authorize('access') === false
            ) {
            return false;
        }

        return parent::canExecute($action);
    }
}
