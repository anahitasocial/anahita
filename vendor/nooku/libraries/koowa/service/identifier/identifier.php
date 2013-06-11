<?php
/**
 * @version 	$Id: identifier.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Service
 * @subpackage  Identifier
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Service Identifier
 *
 * Wraps identifiers of the form [application::]type.package.[.path].name
 * in an object, providing public accessors and methods for derived formats.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Service
 * @subpackage  Identifier
 */
class KServiceIdentifier implements KServiceIdentifierInterface
{
    /**
     * An associative array of application paths
     *
     * @var array
     */
    protected static $_applications = array();

    /**
     * Associative array of identifier adapters
     *
     * @var array
     */
    protected static $_locators = array();

    /**
     * The identifier
     *
     * @var string
     */
    protected $_identifier = '';

    /**
     * The application name
     *
     * @var string
     */
    protected $_application = '';

    /**
     * The identifier type [com|plg|mod]
     *
     * @var string
     */
    protected $_type = '';

    /**
     * The identifier package
     *
     * @var string
     */
    protected $_package = '';

    /**
     * The identifier path
     *
     * @var array
     */
    protected $_path = array();

    /**
     * The identifier object name
     *
     * @var string
     */
    protected $_name = '';

    /**
     * The file path
     *
     * @var string
     */
    protected $_filepath = '';

     /**
     * The classname
     *
     * @var string
     */
    protected $_classname = '';

    /**
     * The base path
     *
     * @var string
     */
    protected $_basepath = '';

    /**
     * Constructor
     *
     * @param   string   Identifier string or object in [application::]type.package.[.path].name format
     * @throws  KServiceIdentifierException if the identfier is not valid
     */
    public function __construct($identifier)
    {
        //Check if the identifier is valid
        if(strpos($identifier, ':') === FALSE) {
            throw new KServiceIdentifierException('Malformed identifier : '.$identifier);
        }

        //Get the parts
        if(false === $parts = parse_url($identifier)) {
            throw new KServiceIdentifierException('Malformed identifier : '.$identifier);
        }

        // Set the type
        $this->type = $parts['scheme'];

        //Set the application
        if(isset($parts['host'])) {
            $this->application = $parts['host'];
        }

        // Set the path
        $this->_path = trim($parts['path'], '/');
        $this->_path = explode('.', $this->_path);

        // Set the extension (first part)
        $this->_package = array_shift($this->_path);

        // Set the name (last part)
        if(count($this->_path)) {
            $this->_name = array_pop($this->_path);
        }

        //Cache the identifier to increase performance
        $this->_identifier = $identifier;
    }

	/**
	 * Serialize the identifier
	 *
	 * @return string 	The serialised identifier
	 */
	public function serialize()
	{
        $data = array(
            'application' => $this->_application,
            'type'		  => $this->_type,
            'package'	  => $this->_package,
            'path'		  => $this->_path,
            'name'		  => $this->_name,
            'identifier'  => $this->_identifier,
            'basepath'    => $this->_basepath,
            'filepath'	  => $this->filepath,
            'classname'   => $this->classname,
        );

        return serialize($data);
	}

	/**
	 * Unserialize the identifier
	 *
	 * @return string 	The serialised identifier
	 */
	public function unserialize($data)
	{
	    $data = unserialize($data);

	    foreach($data as $property => $value) {
	        $this->{'_'.$property} = $value;
	    }
	}

	/**
	 * Set an application path
	 *
	 * @param string	The name of the application
	 * @param string	The path of the application
	 * @return void
     */
    public static function setApplication($application, $path)
    {
        self::$_applications[$application] = $path;
    }

	/**
	 * Get an application path
	 *
	 * @param string	The name of the application
	 * @return string	The path of the application
     */
    public static function getApplication($application)
    {
        return isset(self::$_applications[$application]) ? self::$_applications[$application] : null;
    }

	/**
     * Get a list of applications
     *
     * @return array
     */
    public static function getApplications()
    {
        return self::$_applications;
    }

	/**
     * Add a identifier adapter
     *
     * @param object    A KServiceLocator
     * @return void
     */
    public static function addLocator(KServiceLocatorInterface $locator)
    {
        self::$_locators[$locator->getType()] = $locator;
    }

	/**
     * Get the registered adapters
     *
     * @return array
     */
    public static function getLocators()
    {
        return self::$_locators;
    }

    /**
     * Implements the virtual class properties
     *
     * This functions creates a string representation of the identifier.
     *
     * @param   string  The virtual property to set.
     * @param   string  Set the virtual property to this value.
     */
    public function __set($property, $value)
    {
        if(isset($this->{'_'.$property}))
        {
            //Force the path to an array
            if($property == 'path')
            {
                if(is_scalar($value)) {
                     $value = (array) $value;
                }
            }

            //Set the basepath
            if($property == 'application')
            {
               if(!isset(self::$_applications[$value])) {
                    throw new KServiceIdentifierException('Unknow application : '.$value);
               }

               $this->_basepath = self::$_applications[$value];
            }

            //Set the type
            if($property == 'type')
            {
                //Check the type
                if(!isset(self::$_locators[$value]))  {
                    throw new KServiceIdentifierException('Unknow type : '.$value);
                }
            }

            //Set the properties
            $this->{'_'.$property} = $value;

            //Unset the properties
            $this->_identifier = '';
            $this->_classname  = '';
            $this->_filepath   = '';
        }
    }

    /**
     * Implements access to virtual properties by reference so that it appears to be
     * a public property.
     *
     * @param   string  The virtual property to return.
     * @return  array   The value of the virtual property.
     */
    public function &__get($property)
    {
        if(isset($this->{'_'.$property}))
        {
            if($property == 'filepath' && empty($this->_filepath)) {
                $this->_filepath = self::$_locators[$this->_type]->findPath($this);
            }

            if($property == 'classname' && empty($this->_classname)) {
                $this->_classname = self::$_locators[$this->_type]->findClass($this);
            }

            return $this->{'_'.$property};
        }
    }

    /**
     * This function checks if a virtual property is set.
     *
     * @param   string  The virtual property to return.
     * @return  boolean True if it exists otherwise false.
     */
    public function __isset($property)
    {
        return isset($this->{'_'.$property});
    }

    /**
     * Formats the indentifier as a [application::]type.package.[.path].name string
     *
     * @return string
     */
    public function __toString()
    {
        if($this->_identifier == '')
        {
            if(!empty($this->_type)) {
                $this->_identifier .= $this->_type;
            }

            if(!empty($this->_application)) {
                $this->_identifier .= '://'.$this->_application.'/';
            } else {
                $this->_identifier .= ':';
            }

            if(!empty($this->_package)) {
                $this->_identifier .= $this->_package;
            }

            if(count($this->_path)) {
                $this->_identifier .= '.'.implode('.',$this->_path);
            }

            if(!empty($this->_name)) {
                $this->_identifier .= '.'.$this->_name;
            }
        }

        return $this->_identifier;
    }
}