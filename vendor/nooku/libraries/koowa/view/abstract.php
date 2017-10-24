<?php
/**
 * @version		$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_View
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Abstract View Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_View
 * @uses		KMixinClass
 * @uses 		KTemplate
 */
abstract class KViewAbstract extends KObject
{
	/**
     * Layout name
     *
     * @var string
     */
    protected $_layout;

    /**
     * The uniform resource locator
     *
     * @var object
     */
    protected $_baseurl;

	/**
	 * The output of the view
	 *
	 * @var string
	 */
	public $output = '';

	/**
	 * The mimetype
	 *
	 * @var string
	 */
	public $mimetype = '';

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config = null)
	{
		//If no config is passed create it
		if(!isset($config)) $config = new KConfig();

		parent::__construct($config);

	    //set the base url
        if(!$config->base_url instanceof KHttpUrl) {
            $this->_baseurl = KService::get('koowa:http.url', array('url' => $config->base_url));
        } else {
            $this->_baseurl = $config->base_url;
        }

		$this->output   = $config->output;
		$this->mimetype = $config->mimetype;

        $this->setLayout($config->layout);
	}

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
	    	'output'	=> '',
    		'mimetype'	=> '',
            'layout'    => 'default',
            'base_url'   => '',
	  	));

        parent::_initialize($config);
    }

	/**
	 * Get the name
	 *
	 * @return 	string 	The name of the object
	 */
	public function getName()
	{
		$total = count($this->getIdentifier()->path);
		return $this->getIdentifier()->path[$total - 1];
	}

	/**
	 * Get the format
	 *
	 * @return 	string 	The format of the view
	 */
	public function getFormat()
	{
		return $this->getIdentifier()->name;
	}

	/**
	 * Return the views output
 	 *
	 * @return string 	The output of the view
	 */
	public function display()
	{
		return $this->output;
	}

 	/**
     * Get the layout.
     *
     * @return string The layout name
     */
    public function getLayout()
    {
        return $this->_layout;
    }

   /**
     * Sets the layout name to use
     *
     * @param    string  The template name.
     * @return   KViewAbstract
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
        return $this;
    }

	/**
	 * Get a route based on a full or partial query string
	 *
	 * option, view and layout can be ommitted. The following variations
	 * will all result in the same route
	 *
	 * - foo=bar
	 * - option=com_mycomp&view=myview&foo=bar
	 *
	 * In templates, use @route()
	 *
	 * @param	string	The query string used to create the route
	 * @param 	boolean	If TRUE create a fully qualified route. Default TRUE.
	 * @return 	string 	The route
	 */
	public function getRoute( $route = '', $fqr = true)
	{
		//Parse route
		$parts = array();
		parse_str(trim($route), $parts);

		//Check to see if there is component information in the route if not add it
		if(!isset($parts['option'])) {
			$parts['option'] = 'com_'.$this->getIdentifier()->package;
		}

		//Add the view information to the route if it's not set
		if(!isset($parts['view']))
		{
			$parts['view'] = $this->getName();

		    //Add the layout information to the route if it's not set
	        if(!isset($parts['layout'])) {
			    $parts['layout'] = $this->getLayout();
		    }
		}

		//Add the format information to the route only if it's not 'html'
		if(!isset($parts['format'])) {
			$parts['format'] = $this->getIdentifier()->name;
		}

		 //Add the model state only for routes to the same view
		if($parts['view'] == $this->getName())
		{
		    $state = $this->getModel()->getState()->toArray();
		    $parts = array_merge($state, $parts);
		}

		//Create the route
		$route = KService::get('koowa:http.url', array('url' => route('index.php?'.http_build_query($parts))));

		//Add the host and the schema
		if($fqr)
		{
		    $route->scheme = $this->getBaseUrl()->scheme;
		    $route->host   = $this->getBaseUrl()->host;
		}

		return $route;
	}

	/**
	 * Get the view base url
	 *
	 * @return 	object	A KHttpUrl object
	 */
	public function getBaseUrl()
	{
	    return $this->_baseurl;
	}

	/**
	 * Returns the views output
 	 *
	 * @return 	string
	 */
	public function __toString()
	{
		return $this->display();
	}
}
