<?php
/**
 * @version		$Id: callback.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Mixin
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Callback Command Mixin
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Mixin
 */
class KMixinCallback extends KMixinAbstract implements KCommandInterface
{
 	/**
 	 * Array of callbacks
 	 *
 	 * $var array
 	 */
	protected $_callbacks = array();

	/**
     * Config passed to the callbacks
     *
     * @var array
     */
   	protected $_params = array();

	/**
	 * The command priority
	 *
	 * @var integer
	 */
	protected $_priority;

	/**
	 * Object constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		if(is_null($config->command_chain)) {
			throw new KMixinException('command_chain [KCommandChain] option is required');
		}

		//Set the command priority
		$this->_priority = $config->callback_priority;

		//Enque the command in the mixer's command chain
		$config->command_chain->enqueue($this);
	}

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'command_chain'		=> null,
    		'callback_priority'	=> KCommand::PRIORITY_HIGH
    	));

    	parent::_initialize($config);
    }

	/**
	 * Command handler
	 *
	 * @param string  The command name
	 * @param object  The command context
	 *
	 * @return boolean
	 */
	public function execute( $name, KCommandContext $context)
	{
		$result    = true;

		if(isset($this->_callbacks[$name]))
		{
			$callbacks = $this->_callbacks[$name];
			$params    = $this->_params[$name];

			foreach($callbacks as $key => $callback)
   			{
                $param = $params[$key];

                if(is_array($param) && is_numeric(key($param))) {
   				    $result = call_user_func_array($callback, $params);
                } else {
                    $result = call_user_func($callback,  $context->append($param));
                }

   				//Call the callback
				if ( $result === false) {
        			break;
        		}
   			}
		}

		return $result === false ? false : true;
	}

	/**
 	 * Get the registered callbacks for a command
 	 *
 	 * @param  	string	The method to return the functions for
 	 * @return  array	A list of registered functions
 	 */
	public function getCallbacks($command)
	{
		$result = array();
		$command = strtolower($command);

		if (isset($this->_callbacks[$command]) ) {
       	 	$result = $this->_callbacks[$command];
		}

    	return $result;
	}

	/**
 	 * Registers a callback function
 	 *
 	 * If the callback has already been registered. It will not be re-registered.
 	 *
 	 * If params are passed as a associative array or as a KConfig object they will be merged with the
 	 * context of the command chain and passed along. If they are passed as an indexed array they
 	 * will be passed to the callback directly.
 	 *
 	 * @param  	string|array	The command name to register the callback for or an array of command names
 	 * @param 	callback		The callback function to register
 	 * @param   array|object    An associative array of config parameters or a KConfig object
 	 * @return  KObject	The mixer object
 	 */
	public function registerCallback($commands, $callback, $params = array())
	{
		$commands = (array) $commands;
		$params  = (array) KConfig::unbox($params);

		foreach($commands as $command)
		{
			$command = strtolower($command);

			if (!isset($this->_callbacks[$command]) )
			{
       	 		$this->_callbacks[$command] = array();
       	 		$this->_params[$command]   = array();
			}

			//Don't re-register commands.
			$index = array_search($callback, $this->_callbacks[$command], true);

			if ( $index === false )
			{
		        $this->_callbacks[$command][] = $callback;
    		    $this->_params[$command][]    = $params;
			}
			else
			{
			   $this->_params[$command][$index] = array_merge($this->_params[$command][$index], $params);
			}
		}

		return $this->_mixer;
	}

	/**
 	 * Unregister a callback function
 	 *
 	 * @param  	string|array	The method name to unregister the callback from or an array of method names
 	 * @param 	callback		The callback function to unregister
 	 * @return  KObject The mixer object
 	 */
	public function unregisterCallback($commands, $callback)
	{
		$commands  = (array) $commands;

		foreach($commands as $command)
		{
			$command = strtolower($command);

			if (isset($this->_callbacks[$command]) )
			{
				$key = array_search($callback, $this->_callbacks[$command], true);
       	 		unset($this->_callbacks[$command][$key]);
       	 		unset($this->_params[$command][$key]);
			}
		}

		return $this->_mixer;
	}

	/**
	 * Get the methods that are available for mixin.
	 *
	 * This functions overloads KMixinAbstract::getMixableMethods and excludes the execute()
	 * function from the list of available mixable methods.
	 *
	 * @return array An array of methods
	 */
	public function getMixableMethods(KObject $mixer = null)
	{
        return array_diff(parent::getMixableMethods(), array('execute', 'getPriority'));
	}

	/**
	 * Get the priority of a behavior
	 *
	 * @return	integer The command priority
	 */
  	public function getPriority()
  	{
  		return $this->_priority;
  	}
}