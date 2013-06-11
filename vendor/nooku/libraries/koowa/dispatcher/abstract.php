<?php
/**
 * @version		$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Dispatcher
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract controller dispatcher
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Dispatcher
 * @uses		KMixinClass
 * @uses        KObject
 */
abstract class KDispatcherAbstract extends KControllerAbstract
{
	/**
	 * Controller object or identifier (com://APP/COMPONENT.controller.NAME)
	 *
	 * @var	string|object
	 */
	protected $_controller;

	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		//Set the controller
		$this->_controller = $config->controller;

		if(KRequest::method() != 'GET') {
			$this->registerCallback('after.dispatch' , array($this, 'forward'));
	  	}

	    $this->registerCallback('after.dispatch', array($this, 'render'));
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
        	'controller' => $this->getIdentifier()->package,
    		'request'	 => KRequest::get('get', 'string'),
        ))->append(array (
            'request' 	 => array('format' => KRequest::format() ? KRequest::format() : 'html')
        ));

        parent::_initialize($config);
    }

	/**
	 * Method to get a controller object
	 *
	 * @return	KControllerAbstract
	 */
	public function getController()
	{
		if(!($this->_controller instanceof KControllerAbstract))
		{
		    //Make sure we have a controller identifier
		    if(!($this->_controller instanceof KServiceIdentifier)) {
		        $this->setController($this->_controller);
			}

		    $config = array(
        		'request' 	   => $this->_request,
			    'dispatched'   => true
        	);

			$this->_controller = $this->getService($this->_controller, $config);
		}

		return $this->_controller;
	}

	/**
	 * Method to set a controller object attached to the dispatcher
	 *
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @throws	KDispatcherException	If the identifier is not a controller identifier
	 * @return	KDispatcherAbstract
	 */
	public function setController($controller)
	{
		if(!($controller instanceof KControllerAbstract))
		{
			if(is_string($controller) && strpos($controller, '.') === false )
		    {
		        // Controller names are always singular
			    if(KInflector::isPlural($controller)) {
				    $controller = KInflector::singularize($controller);
			    }

			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('controller');
			    $identifier->name	= $controller;
			}
		    else $identifier = $this->getIdentifier($controller);

			if($identifier->path[0] != 'controller') {
				throw new KDispatcherException('Identifier: '.$identifier.' is not a controller identifier');
			}

			$controller = $identifier;
		}

		$this->_controller = $controller;

		return $this;
	}

	/**
	 * Dispatch the controller
	 *
	 * @param   object		A command context object
	 * @return	mixed
	 */
	protected function _actionDispatch(KCommandContext $context)
	{
	    $action = KRequest::get('post.action', 'cmd', strtolower(KRequest::method()));

	    if(KRequest::method() != KHttpRequest::GET) {
            $context->data = KRequest::get(strtolower(KRequest::method()), 'raw');;
        }

	    $result = $this->getController()->execute($action, $context);

        return $result;
	}

	/**
	 * Forward after a post request
	 *
	 * Either do a redirect or a execute a browse or read action in the controller
	 * depending on the request method and type
	 *
	 * @return mixed
	 */
	protected function _actionForward(KCommandContext $context)
	{
		if (KRequest::type() == 'HTTP')
		{
			if($redirect = $this->getController()->getRedirect())
			{
			    JFactory::getApplication()
					->redirect($redirect['url'], $redirect['message'], $redirect['type']);
			}
		}

		if(KRequest::type() == 'AJAX')
		{
			$context->result = $this->getController()->execute('display', $context);
			return $context->result;
		}
	}

	/**
	 * Push the controller data into the document
	 *
	 * This function divert the standard behavior and will push specific controller data
	 * into the document
	 *
	 * @return	mixed
	 */
	protected function _actionRender(KCommandContext $context)
	{
	    //Headers
	    if($context->headers)
	    {
	        foreach($context->headers as $name => $value) {
	            header($name.' : '.$value);
	        }
	    }

	    //Status
        if($context->status) {
           header(KHttpResponse::getHeader($context->status, KRequest::protocol()));
        }

	    if(is_string($context->result)) {
		     return $context->result;
		}
	}
}