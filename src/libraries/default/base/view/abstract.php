<?php

/**
 *  Abstract Class. Provides assign, __set and __get and assign. All the views support assigning
 *  data.
 *
 * @todo refactor this class and LibApplicationRouter
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2016 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class LibBaseViewAbstract extends KObject
{
    /**
     * The view state.
     *
     * @var LibBaseControllerData
     */
    protected $_state;

    /**
     * The assigned data.
     *
     * @var LibBaseControllerData
     */
    protected $_data;

    /**
     * The uniform resource locator.
     *
     * @var object
     */
    protected $_baseurl;

    /**
     * Layout name.
     *
     * @var string
     */
    protected $_layout;

    /**
     * The output of the view.
     *
     * @var string
     */
    public $output = '';

    /**
     * The mimetype.
     *
     * @var string
     */
    public $mimetype = '';

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if (! isset($config)) {
            $config = new KConfig();
        }

        parent::__construct($config);

        //set the base url
        if (! $config->base_url instanceof KHttpUrl) {
            $this->_baseurl = $this->getService('koowa:http.url', array('url' => $config->base_url));
        } else {
            $this->_baseurl = $config->base_url;
        }

        $this->output = $config->output;
        $this->mimetype = $config->mimetype;

        $this->setLayout($config->layout);

        //set the data
        $this->_state = $config->state;
        $this->_data = KConfig::unbox($config->data);
    }

    /**
     * Initializes the config for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'state' => new LibBaseControllerState(),
            'data' => array(),
            'output' => '',
            'mimetype' => '',
            'layout' => 'default',
            'base_url' => '',
        ));

        parent::_initialize($config);
    }

    /**
     * Set a view properties.
     *
     * @param  	string 	The property name.
     * @param 	mixed 	The property value.
     */
    public function __set($property, $value)
    {
        $this->_data[$property] = $value;
    }

    /**
     * Get a view property.
     *
     * @param  	string 	The property name.
     *
     * @return string The property value.
     */
    public function __get($property)
    {
        $result = null;

        if (isset($this->_data[$property])) {
            $result = $this->_data[$property];
        }

        return $result;
    }

    /**
     * Supports a simple form of Fluent Interfaces. Allows you to assign variables to the view
     * by using the variable name as the method name. If the method name is a setter method the
     * setter will be called instead.
     *
     * For example : $view->layout('foo')->title('name')->display().
     * It also supports using name method setLayout($layout) which will be translated to set('layout', $layout)
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     *
     * @return LibBaseViewAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        //If one argument is passed we assume a setter method is being called
        if (!isset($this->_mixed_methods[$method]) && count($args) == 1) {
            $this->set(AnInflector::underscore($method), $args[0]);

            return $this;
        }

        return parent::__call($method, $args);
    }

    /**
     * Set the object properties.
     *
     * @param   string|array|object The name of the property, an associative array or an object
     * @param   mixed               The value of the property
     *
     * @throws KObjectException
     *
     * @return KObject
     */
    public function set($property, $value = null)
    {
        if (is_object($property)) {
            $property = get_object_vars($property);
        }

        if (is_array($property)) {
            foreach ($property as $k => $v) {
                $this->set($k, $v);
            }
        } elseif ('_' != substr($property, 0, 1)) {
            if (method_exists($this, 'set'.ucfirst($property))) {
                $this->{'set'.ucfirst($property)}($value);
            } else {
                $this->$property = $value;
            }
        }

        return $this;
    }

    /**
     * Get the name.
     *
     * @return string The name of the object
     */
    public function getName()
    {
        $total = count($this->getIdentifier()->path);
        return $this->getIdentifier()->path[$total - 1];
    }

    /**
     * Get the format.
     *
     * @return string The format of the view
     */
    public function getFormat()
    {
        return $this->getIdentifier()->name;
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
     * Sets the layout name to use.
     *
     * @param    string  The template name.
     *
     * @return LibBaseViewAbstract
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Return the views output.
     *
     * @return string The output of the view
     */
    public function display()
    {
        return $this->output;
    }

    /**
     * Get the view base url.
     *
     * @return object A KHttpUrl object
     */
    public function getBaseUrl()
    {
        return $this->_baseurl;
    }

    /**
     * Returns the view sata.
     *
     * @return AnControllerState
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * Return the data used in the template.
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get a route based on a full or partial query string.
     *
     * option, view and layout can be ommitted. The following variations
     * will all result in the same route
     *
     * - foo=bar
     * - option=com_mycomp&view=myview&foo=bar
     *
     * In templates, use @route()
     *
     * @todo refactor this method and LibApplicationRouter::getRoute
     *
     * @param	string	The query string used to create the route
     * @param 	bool	If TRUE create a fully qualified route. Default TRUE.
     *
     * @return string The route
     */
    public function getRoute($route = '', $fqr = true)
    {
        if (! is_array($route)) {
            $parts = array();
            parse_str(trim($route), $parts);
            $route = $parts;
        }

        $parts = $route;

        $route = array();

        //Check to see if there is component information in the route if not add it
        if (! isset($parts['option'])) {
            $route['option'] = 'com_'.$this->getIdentifier()->package;
        }

        //Add the view information to the route if it's not set
        if (! isset($parts['view'])) {
            $route['view'] = $this->getName();

            //Add the layout information to the route if it's not set
            if (! isset($parts['layout'])) {
                $route['layout'] = $this->getLayout();

                //@TODO temporary. who are we today what's the default layout
                if ($route['layout'] == 'default') {
                    unset($route['layout']);
                }
            }

            //since the view is missing then get the data from
            //the state
            $data = $this->_state->getData($this->_state->isUnique());

            $route = array_merge($route, $data);

            //Add the format information to the route only if it's not 'html'
            if (!isset($parts['format'])) {
                $route['format'] = $this->getIdentifier()->name;
            }
        }

        $parts = array_merge($route, $parts);
        $parts = http_build_query($parts);
        $route = $this->getService('com:application')->getRouter()->build($parts, $fqr);

        //if ($fqr) {
        //    $route->scheme = $this->getBaseUrl()->scheme;
        //    $route->host = $this->getBaseUrl()->host;
        //}

        return $route;
    }

    /**
     * Execute and return the views output.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->display();
        } catch (Exception $e) {
            $trace = str_replace("\n", '<br />', $e->getTraceAsString());
            trigger_error($e->getMessage().'<br />'.$trace, E_USER_ERROR);
            return '';
        }
    }
}
