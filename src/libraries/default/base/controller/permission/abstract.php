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
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.Anahita.io
 */

/**
 * Executable Controller Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
abstract class LibBaseControllerPermissionAbstract extends AnControllerBehaviorAbstract
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
             'priority' => AnCommand::PRIORITY_LOW,
             'auto_mixin' => true,
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler.
     *
     * @param string          $name    The command name
     * @param AnCommandContext $context The command context
     *
     * @return bool Can return both true or false.
     */
    public function execute($name, AnCommandContext $context)
    {
        $parts = explode('.', $name);

        if ($parts[0] == 'before') {
            $action = $parts[1];

            //Check if the action exists
            if (! in_array($action, $context->caller->getActions())) {
                throw new LibBaseControllerExceptionNotImplemented(
                        'Action '.ucfirst($action).' Not Implemented');
            }

            if ($this->_mixer->canExecute($action) === false) {
                if ($this->viewer && !$this->viewer->guest()) {
                    throw  new LibBaseControllerExceptionForbidden('Action '.ucfirst($action).' Not Allowed');
                } else {
                    throw  new LibBaseControllerExceptionUnauthorized('Action '.ucfirst($action).' Not Allowed');
                }
            }
        }

        return true;
    }

    /**
     * Return the object handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return AnMixinAbstract::getHandle();
    }

    /**
     * Authorizes an action. This method can be overriden by the mixer. This method
     * will call can[Action Name].
     *
     * @param AnCommandContext $context The CommandChain Context
     *
     * @return bool
     */
    public function canExecute($action)
    {
        $ret = true;

        //Check if the action can be executed
        $method = 'can'.ucfirst($action);

        if (in_array($method, $this->_mixer->getMethods())) {
            $ret = $this->_mixer->$method();
        } else {
            //if method doesn't exist then check if the action
            //exists
            $actions = $this->getActions();
            $actions = array_flip($actions);
            $ret = isset($actions[$action]);
        }

        return $ret;
    }
}
