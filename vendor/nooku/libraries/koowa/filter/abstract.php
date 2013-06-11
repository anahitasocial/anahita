<?php
/**
* @version		$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Abstract filter.
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
abstract class KFilterAbstract extends KObject implements KFilterInterface
{
	/**
	 * The filter chain
	 *
	 * @var	object
	 */
	protected $_chain = null;

	/**
	 * If the data to be santized or validated if an object or array,
	 * walk over each individual property or element. Default TRUE.
	 *
	 * @var	boolean
	 */
	protected $_walk = true;

	/**
	 * Constructor
	 *
	 * @param 	object	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

	    $this->_chain = $this->getService('koowa:filter.chain');
		$this->addFilter($this);
	}

    /**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return KFilterInterface
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
       // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

	/**
	 * Command handler
	 *
	 * @param string  The command name
	 * @param object  The command context
	 *
	 * @return object
	 */
	final public function execute($name, KCommandContext $context)
	{
		$function = '_'.$name;
		return $this->$function($context->data);
	}

	/**
	 * Validate a variable or data collection
	 *
	 * @param	mixed	Data to be validated
	 * @return	bool	True when the data is valid
	 */
	final public function validate($data)
	{
		if($this->_walk && (is_array($data) || is_object($data)))
		{
			$arr = (array)$data;

			foreach($arr as $value)
			{
				if($this->validate($value) ===  false) {
					return false;
				}
			}
		}
		else
		{
			$context = $this->_chain->getContext();
			$context->data = $data;

			$result = $this->_chain->run('validate', $context);

			if($result ===  false) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Sanitize a variable or data collection
	 *
	 * @param	mixed	Data to be sanitized
	 * @return	mixed	The sanitized data
	 */
	public final function sanitize($data)
	{
		if($this->_walk && (is_array($data) || is_object($data)))
		{
			$arr = (array)$data;

			foreach($arr as $key => $value)
			{
				if(is_array($data)) {
					$data[$key] = $this->sanitize($value);
				}

				if(is_object($data)) {
					$data->$key = $this->sanitize($value);
				}
			}
		}
		else
		{
			$context = $this->_chain->getContext();
			$context->data = $data;

			$data = $this->_chain->run('sanitize', $context);
		}

		return $data;
	}

	/**
	 * Add a filter based on priority
	 *
	 * @param object 	A KFilter
	 * @param integer	The command priority, usually between 1 (high priority) and 5 (lowest),
     *                  default is 3. If no priority is set, the command priority will be used
     *                  instead.
	 *
	 * @return KFilterAbstract
	 */
	public function addFilter(KFilterInterface $filter, $priority = null)
	{
		$this->_chain->enqueue($filter, $priority);
		return $this;
	}

	/**
	 * Get a handle for this object
	 *
	 * This function returns an unique identifier for the object. This id can be used as
	 * a hash key for storing objects or for identifying an object
	 *
	 * @return string A string that is unique
	 */
	public function getHandle()
	{
		return spl_object_hash( $this );
	}

	/**
	 * Get the priority of the filter
	 *
	 * @return	integer The command priority
	 */
  	public function getPriority()
  	{
  		return KCommand::PRIORITY_NORMAL;
  	}

	/**
	 * Validate a variable
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	abstract protected function _validate($value);

	/**
	 * Sanitize a variable only
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param	scalar	Value to be sanitized
	 * @return	mixed
	 */
	abstract protected function _sanitize($value);
}