<?php
/**
 * @version     $Id: abstract.php 1815 2010-03-27 21:42:55Z johan $
 * @package     Koowa_View
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Abstract Template View Class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_View
 * @uses        KMixinClass
 * @uses        KTemplate
 * @uses        KService
 */
abstract class KViewTemplate extends KViewAbstract
{ 
    /**
     * Template identifier (com://APP/COMPONENT.template.NAME)
     *
     * @var string|object
     */
    protected $_template;

    /**
     * Callback for escaping.
     *
     * @var string
     */
    protected $_escape;
    
    /**
     * Auto assign
     *
     * @var boolean
     */
    protected $_auto_assign;
     
    /**
     * The assigned data
     *
     * @var boolean
     */
    protected $_data;
     
    /**
     * The uniform resource locator
     * 
     * @var object
     */
    protected $_mediaurl;

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        //set the media url
        if(!$config->media_url instanceof KHttpUrl) {
            $this->_mediaurl = KService::get('koowa:http.url', array('url' => $config->media_url));
        } else {
            $this->_mediaurl = $config->media_url;
        }
        
        // set the auto assign state
        $this->_auto_assign = $config->auto_assign;
        
        //set the data
        $this->_data = KConfig::unbox($config->data);
          
         // user-defined escaping callback
        $this->setEscape($config->escape);
         
        // set the template object
        $this->_template = $config->template;
             
        //Set the template filters
        if(!empty($config->template_filters)) {
            $this->getTemplate()->addFilter($config->template_filters);
        }
         
        //Add alias filter for media:// namespace
        $this->getTemplate()->getFilter('alias')->append(
            array('media://' => (string) $this->_mediaurl.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        //Clone the identifier
        $identifier = clone $this->getIdentifier();
        
        $config->append(array(
            'data'			   => array(),
            'escape'           => 'htmlspecialchars',
            'template'         => $this->getName(),
            'template_filters' => array('shorttag', 'alias', 'variable', 'script', 'style', 'link', 'template'),
            'auto_assign'      => true,
            'media_url'        => '/media',
        ));
        
        parent::_initialize($config);
    }
    
    /**
     * Set a view properties
     *
     * @param   string  The property name.
     * @param   mixed   The property value.
     */
    public function __set($property, $value)
    {
        $this->_data[$property] = $value;
    }
    
    /**
     * Get a view property
     *
     * @param   string  The property name.
     * @return  string  The property value.
     */
    public function __get($property)
    {
        $result = null;
        if(isset($this->_data[$property])) {
            $result = $this->_data[$property];
        } 
        
        return $result;
    }

    /**
    * Assigns variables to the view script via differing strategies.
    *
    * This method is overloaded; you can assign all the properties of
    * an object, an associative array, or a single value by name.
    *
    * You are not allowed to set variables that begin with an underscore;
    * these are either private properties for KView or private variables
    * within the template script itself.
    *
    * <code>
    * $view = new KViewDefault();
    *
    * // assign directly
    * $view->var1 = 'something';
    * $view->var2 = 'else';
    *
    * // assign by name and value
    * $view->assign('var1', 'something');
    * $view->assign('var2', 'else');
    *
    * // assign by assoc-array
    * $ary = array('var1' => 'something', 'var2' => 'else');
    * $view->assign($obj);
    *
    * // assign by object
    * $obj = new stdClass;
    * $obj->var1 = 'something';
    * $obj->var2 = 'else';
    * $view->assign($obj);
    *
    * </code>
    *
    * @return KViewAbstract
    */
    public function assign()
    {
        // get the arguments; there may be 1 or 2.
        $arg0 = @func_get_arg(0);
        $arg1 = @func_get_arg(1);

        // assign by object or array
        if (is_object($arg0) || is_array($arg0)) {
            $this->set($arg0);
        } 
        
        // assign by string name and mixed value.
        elseif (is_string($arg0) && substr($arg0, 0, 1) != '_' && func_num_args() > 1) {
            $this->set($arg0, $arg1);
        }

        return $this;
    }

    /**
     * Escapes a value for output in a view script.
     *
     * @param  mixed $var The output to escape.
     * @return mixed The escaped value.
     */
    public function escape($var)
    {
        return call_user_func($this->_escape, $var);
    }
    
    /**
     * Return the views output
     *
     * @return string 	The output of the view
     */
    public function display()
    {
        if(empty($this->output))
		{
		    $this->output = $this->getTemplate()
                                 ->loadIdentifier($this->_layout, $this->_data)
                                 ->render();
		}
                        
        return parent::display();
    }
    
     /**
     * Sets the _escape() callback.
     *
     * @param   mixed The callback for _escape() to use.
     * @return  KViewAbstract
     */
    public function setEscape($spec)
    {
        $this->_escape = $spec;
        return $this;
    }
    
	/**
     * Sets the layout name
     *
     * @param    string  The template name.
     * @return   KViewAbstract
     */
    public function setLayout($layout)
    {
        if(is_string($layout) && strpos($layout, '.') === false ) 
		{
            $identifier = clone $this->getIdentifier(); 
            $identifier->name = $layout;
	    }
		else $identifier = $this->getIdentifier($layout);
        
        $this->_layout = $identifier;
        return $this;
    }
    
	/**
     * Get the layout.
     *
     * @return string The layout name
     */
    public function getLayout()
    {
        return $this->_layout->name;
    }
    
    /**
     * Get the identifier for the template with the same name
     *
     * @return  KTemplate
     */
    public function getTemplate()
    {
        if(!$this->_template instanceof KTemplateAbstract)
        { 
            //Make sure we have a template identifier
            if(!($this->_template instanceof KServiceIdentifier)) {
                $this->setTemplate($this->_template);
            }
              
            $options = array(
            	'view' => $this
            );
            
            $this->_template = $this->getService($this->_template, $options);
        }
        
        return $this->_template;
    }
    
    /**
     * Method to set a template object attached to the view
     *
     * @param   mixed   An object that implements KObjectServiceable, an object that 
     *                  implements KServiceIdentifierInterface or valid identifier string
     * @throws  KDatabaseRowsetException    If the identifier is not a table identifier
     * @return  KViewAbstract
     */
    public function setTemplate($template)
    {
        if(!($template instanceof KTemplateAbstract))
        {
            if(is_string($template) && strpos($template, '.') === false ) 
		    {
			    $identifier = clone $this->getIdentifier(); 
                $identifier->path = array('template');
                $identifier->name = $template;
			}
			else $identifier = $this->getIdentifier($template);
            
            if($identifier->path[0] != 'template') {
                throw new KViewException('Identifier: '.$identifier.' is not a template identifier');
            }
        
            $template = $identifier;
        } 
        
        $this->_template = $template;
            
        return $this;
    }
    
	/**
	 * Get the view media url
	 * 
	 * @return 	object	A KHttpUrl object
	 */
	public function getMediaUrl()
	{
	    return $this->_mediaurl;
	}
    
    /**
     * Execute and return the views output
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->display();
    }
    
    /**
     * Supports a simple form of Fluent Interfaces. Allows you to assign variables to the view 
     * by using the variable name as the method name. If the method name is a setter method the 
     * setter will be called instead.
     *
     * For example : $view->layout('foo')->title('name')->display().
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     * @return  KViewAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args) 
    { 
        //If one argument is passed we assume a setter method is being called 
        if(count($args) == 1) 
        { 
            if(method_exists($this, 'set'.ucfirst($method))) { 
                return $this->{'set'.ucfirst($method)}($args[0]); 
            } else { 
                return $this->set($method, $args[0]); 
            } 
        } 
        
        return parent::__call($method, $args); 
    } 
}