<?php

/**
 * Abstract Controller. Nothing different from {@link AnControllerAbstract} but override some methods
 * like getBehavior to allow for setting default behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseControllerAbstract extends AnControllerAbstract
{
    /**
     * Controller State.
     *
     * @var KConfigState
     */
    protected $_state;

    /**
     * response object.
     *
     * @var LibBaseControllerResponseAbstract
     */
    protected $_response;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_response = $config->response;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'response' => 'com:base.controller.response',
        ));

        parent::_initialize($config);
    }

    /**
     * Return the controller state.
     *
     * @return KConfigState
     */
    public function getState()
    {
        if (! isset($this->_state)) {
            $this->_state = new LibBaseControllerState();
        }

        return $this->_state;
    }

    /**
	 * Get the request information
	 *
	 * @return KConfig	A KConfig object with request information
	 */
	public function getRequest()
	{
		return $this->_request;
	}

    /**
     * Set the request information.
     *
     * @param array An associative array of request information
     *
     * @return LibBaseControllerAbstract
     */
    public function setRequest(array $request)
    {
        $this->_request = new LibBaseControllerRequest();

        foreach ($request as $key => $value) {
            $this->_request->$key = $value;
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Set the state property of the controller.
     *
     * @param string $key   The property name
     * @param string $value The property value
     */
    public function __set($key, $value)
    {
        $this->getState()->$key = $value;
    }

    /**
     * Get the state value of a property.
     *
     * @param string $key The property name
     */
    public function __get($key)
    {
        return $this->getState()->$key;
    }

    /**
     * Supports a simple form Fluent Interfaces. Allows you to set the request
     * properties by using the request property name as the method name.
     *
     * For example : $controller->view('name')->limit(10)->browse();
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     *
     * @return AnControllerBread
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        //omit anything that starts with underscore
        if (strpos($method, '_') === false) {

            if (count($args) == 1 && !isset($this->_mixed_methods[$method]) && !in_array($method, $this->getActions())) {
                $this->{AnInflector::underscore($method)} = $args[0];
                return $this;
            }

        } elseif (strpos($method, '_action') === 0) {
            //if the missing method is _action[Name] but
            //method exists, then that means the action
            //has been called on the object parent i.e.
            //parent::_action[Name] but since the parent is
            //not implementing the action it falls back to
            //__call.
            //we need to check if a behavior implement this
            //method
            if (method_exists($this, $method)) {
                $action = strtolower(substr($method, 7));

                if (isset($this->_mixed_methods[$action])) {
                    return $this->_mixed_methods[$action]->execute('action.'.$action, isset($args[0]) ? $args[0] : null);
                } else {
                    //we need to throw this
                    //because if it goes to parent::__call it will causes
                    //infinite recursion
                    throw new BadMethodCallException('Call to undefined method :'.$method);
                }
            }
        }

        return parent::__call($method, $args);
    }

    /**
     * Return the response object.
     *
     * @return LibBaseControllerResponseAbstract
     */
    public function getResponse()
    {
        if (! $this->_response instanceof LibBaseControllerResponse) {
            $this->_response = $this->getService($this->_response);

            if (! $this->_response instanceof LibBaseControllerResponse) {
                throw new UnexpectedValueException('Response must be an instanceof LibBaseControllerResponse');
            }
        }

        return $this->_response;
    }

    /**
     * Set the response object.
     *
     * @param mixed $response
     *
     * @return LibBaseControllerResource
     */
    public function setResponse($response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Sets the context response.
     *
     * @return KCommandContext
     */
    public function getCommandContext()
    {
        $context = parent::getCommandContext();
        $context->response = $this->getResponse();
        $context->request = $this->getRequest();

        return $context;
    }

    /**
     * Get a behavior by identifier.
     *
     * @param mixed $behavior Behavior name
     * @param array $config   An array of options to configure the behavior with
     *
     * @see KMixinBehavior::getBehavior()
     *
     * @return AnDomainBehaviorAbstract
     */
    public function getBehavior($behavior, $config = array())
    {
        if (is_string($behavior)) {
            if (strpos($behavior, '.') === false) {

                $identifier = clone $this->getIdentifier();
                $identifier->path = array('controller','behavior');
                $identifier->name = $behavior;

                register_default(array('identifier' => $identifier, 'prefix' => $this));

                $behavior = $identifier;
            }
        }

        return parent::getBehavior($behavior, $config);
    }
}
