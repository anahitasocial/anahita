<?php
/**
* @package		An_Controller
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Abstract Controller Toolbar Class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @author      Rastin Mehr <rastin@anahitapolis.com>
 * @package     An_Controller
 * @subpackage 	Toolbar
 * @uses        AnInflector
 */
abstract class AnControllerAbstract extends KObject
{
    /**
     * Array of class methods to call for a given action.
     *
     * @var array
     */
    protected $_action_map = array();

    /**
     * The class actions
     *
     * @var array
     */
    protected $_actions = array();

    /**
     * Has the controller been dispatched
     *
     * @var boolean
     */
    protected $_dispatched;

    /**
	 * The request information
	 *
	 * @var array
	 */
	protected $_request = null;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    public function __construct( KConfig $config = null)
    {
        //If no config is passed create it
        if(! isset($config)) $config = new KConfig();

        parent::__construct($config);

        //Set the dispatched state
        $this->_dispatched = $config->dispatched;

        //Set the mixer in the config
        $config->mixer = $this;

        // Mixin the command interface
        $this->mixin(new KMixinCommand($config));

        // Mixin the behavior interface
        $this->mixin(new KMixinBehavior($config));

        //Set the request
		$this->setRequest((array) KConfig::unbox($config->request));
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'command_chain'     => $this->getService('koowa:command.chain'),
            'dispatch_events'   => true,
            'event_dispatcher'  => $this->getService('koowa:event.dispatcher'),
            'enable_callbacks'  => true,
            'dispatched'		=> false,
            'request'		    => null,
            'behaviors'         => array(),
        ));

        parent::_initialize($config);
    }

	/**
     * Has the controller been dispatched
     *
     * @return  boolean	Returns true if the controller has been dispatched
     */
    public function isDispatched()
    {
        return $this->_dispatched;
    }

    /**
     * Execute an action by triggering a method in the derived class.
     *
     * @param   string      The action to execute
     * @param   object		A command context object
     * @return  mixed|false The value returned by the called method, false in error case.
     * @throws  AnControllerException
     */
    public function execute($action, KCommandContext $context)
    {
        $action = strtolower($action);

        //Update the context
        $context->action = $action;
        $context->caller = $this;

        //Find the mapped action
        if (isset($this->_action_map[$action])) {
           $command = $this->_action_map[$action];
        } else {
           $command = $action;
        }

        //Execute the action
        if ($this->getCommandChain()->run('before.'.$command, $context) !== false) {
            $method = '_action'.ucfirst($command);

            if (! method_exists($this, $method)) {
                if (isset($this->_mixed_methods[$command])) {
                    $context->result = $this->_mixed_methods[$command]->execute('action.'.$command, $context);
                } else {
                    throw new AnControllerException("Can't execute '$command', method: '$method' does not exist");
                }
            }
            else  $context->result = $this->$method($context);

            $this->getCommandChain()->run('after.'.$command, $context);
        }

        return $context->result;
    }

	/**
     * Mixin an object
     *
     * @param   object  An object that implements KMinxInterface
     * @return  KObject
     */
    public function mixin(KMixinInterface $object, $config = array())
    {
        if ($object instanceof AnControllerBehaviorAbstract) {
            foreach ($object->getMethods() as $method) {
                if (substr($method, 0, 7) == '_action') {
                    $this->_actions[] = strtolower(substr($method, 7));
                }
            }

            $this->_actions = array_unique(array_merge($this->_actions, array_keys($this->_action_map)));
        }

        return parent::mixin($object, $config);
    }

    /**
     * Gets the available actions in the controller.
     *
     * @return  array Array[i] of action names.
     */
    public function getActions()
    {
        if(! $this->_actions) {
            $this->_actions = array();

            foreach ($this->getMethods() as $method) {
                if (substr($method, 0, 7) == '_action') {
                    $this->_actions[] = strtolower(substr($method, 7));
                }
            }

            $this->_actions = array_unique(array_merge($this->_actions, array_keys($this->_action_map)));
        }

        return $this->_actions;
    }

    /**
     * Register (map) an action to a method in the class.
     *
     * @param   string  The action.
     * @param   string  The name of the method in the derived class to perform
     *                  for this action.
     * @return  AnControllerAbstract
     */
    public function registerActionAlias( $alias, $action )
    {
        $alias = strtolower($alias);

        if (! in_array($alias, $this->getActions()))  {
            $this->_action_map[$alias] = $action;
        }

        //Force reload of the actions
        $this->_actions = array_unique(array_merge($this->_actions, array_keys($this->_action_map)));

        return $this;
    }

    /**
     * Execute a controller action by it's name.
	 *
	 * Function is also capable of checking is a behavior has been mixed succesfully
	 * using is[Behavior] function. If the behavior exists the function will return
	 * TRUE, otherwise FALSE.
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     * @see execute()
     */
    public function __call($method, $args)
    {
        //Handle action alias method
        if (in_array($method, $this->getActions())) {
            //Get the data
            $data = !empty($args) ? $args[0] : array();

            //Create a context object
            if(! ($data instanceof KCommandContext)) {
                $context = $this->getCommandContext();
                $context->data   = $data;
                $context->result = false;
            }
            else $context = $data;

            //Execute the action
            return $this->execute($method, $context);
        }

        //Check if a behavior is mixed
		$parts = AnInflector::explode($method);

		if ($parts[0] == 'is' && isset($parts[1])) {
            if(! isset($this->_mixed_methods[$method])) {
			    return false;
            }

            return true;
		}

        return parent::__call($method, $args);
    }
}
