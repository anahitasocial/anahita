<?php
/**
 * @version		$Id: executable.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Controller Executable Behavior Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Controller
 * @subpackage	Behavior
 */
class KControllerBehaviorExecutable extends KControllerBehaviorAbstract
{
	/**
	 * The read-only state of the behavior
	 *
	 * @var boolean
	 */
	protected $_readonly;

	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct( KConfig $config)
	{
		parent::__construct($config);

		$this->_readonly = (bool) $config->readonly;
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
            'priority'   => KCommand::PRIORITY_HIGH,
            'readonly'   => false,
            'auto_mixin' => true
        ));

        parent::_initialize($config);
    }

	/**
     * Command handler
     *
     * Only handles before.action commands to check ACL rules.
     *
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Can return both true or false.
     * @throws  KControllerException
     */
    public function execute( $name, KCommandContext $context)
    {
        $parts = explode('.', $name);

        if($parts[0] == 'before')
        {
            $action = $parts[1];

            //Check if the action exists
            if(!in_array($action, $context->caller->getActions()))
            {
                $context->setError(new KControllerException(
            		'Action '.ucfirst($action).' Not Implemented', KHttpResponse::NOT_IMPLEMENTED
                ));

                $context->header = array('Allow' =>  $context->caller->execute('options', $context));
                return false;
            }

            //Check if the action can be executed
            $method = 'can'.ucfirst($action);

            if(method_exists($this, $method))
            {
		        if($this->$method() === false)
		        {
		            if($context->action != 'options')
		            {
		                $context->setError(new KControllerException(
		        			'Action '.ucfirst($action).' Not Allowed', KHttpResponse::METHOD_NOT_ALLOWED
		                ));

		                $context->header = array('Allow' =>  $context->caller->execute('options', $context));
		            }

		            return false;
		        }
            }
        }

        return true;
    }

 	/**
     * Get an object handle
     *
     * Force the object to be enqueue in the command chain.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        return KMixinAbstract::getHandle();
    }

    /**
     * Set the readonly state of the behavior
     *
     * @param boolean
     * @return KControllerBehaviorExecutable
     */
    public function setReadOnly($readonly)
    {
         $this->_readonly = (bool) $readonly;
         return $this;
    }

    /**
     * Get the readonly state of the behavior
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->_readonly;
    }

	/**
     * Generic authorize handler for controller browse actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canBrowse()
    {
        return true;
    }

	/**
     * Generic authorize handler for controller read actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canRead()
    {
        return true;
    }

	/**
     * Generic authorize handler for controller edit actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canEdit()
    {
        return !$this->_readonly;
    }

 	/**
     * Generic authorize handler for controller add actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canAdd()
    {
        return !$this->_readonly;
    }

 	/**
     * Generic authorize handler for controller delete actions
     *
     * @return  boolean     Can return both true or false.
     */
    public function canDelete()
    {
         return !$this->_readonly;
    }
}