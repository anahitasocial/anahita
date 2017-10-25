<?php

/**
 * Persistable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseControllerBehaviorPersistable extends AnControllerBehaviorAbstract
{
    /**
     * Restores a state for an action.
     *
     * @param string $action
     */
    public function restoreState($action)
    {
        //Built the session identifier based on the action
        $identifier = $this->_mixer->getIdentifier().'.'.$action;
        $state = KRequest::get('session.'.$identifier, 'raw', array());

        //Append the data to the request object
        $this->getState()->append($state);
    }

    /**
     * Restores a state for an action.
     *
     * @param string $action
     */
    public function persistState($action)
    {
        $state = $this->getRequest();

        // Built the session identifier based on the action
        $identifier = $this->_mixer->getIdentifier().'.'.$action;

        //Set the state in the session
        KRequest::set('session.'.$identifier, KConfig::unbox($state));
    }

    /**
     * Load the model state from the request.
     *
     * This functions merges the request information with any model state information
     * that was saved in the session and returns the result.
     *
     * @param 	KCommandContext		The active command context
     */
    protected function _beforeControllerBrowse(KCommandContext $context)
    {
        $this->restoreState($context->action);
    }

    /**
     * Saves the model state in the session.
     *
     * @param 	KCommandContext		The active command context
     */
    protected function _afterControllerBrowse(KCommandContext $context)
    {
        $this->persistState($context->action);
    }
}
