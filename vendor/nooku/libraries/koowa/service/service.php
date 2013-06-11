<?php
/**
 * @version 	$Id: service.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Service
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Service Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Service
 * @uses 		KServiceIdentifier
 */
class KService implements KServiceInterface
{
	/**
     * The identifier registry
     *
     * @var array
     */
    protected static $_identifiers = null;

    /**
	 * The identifier aliases
	 *
	 * @var	array
	 */
	protected static $_aliases = array();

	/**
	 * The services
	 *
	 * @var	array
	 */
	protected static $_services = null;

	/**
	 * The mixins
	 *
	 * @var	array
	 */
	protected static $_mixins = array();

	/**
	 * The configs
	 *
	 * @var	array
	 */
	protected static $_configs = array();

	/**
	 * Constructor
	 *
	 * Prevent creating instances of this class by making the contructor private
	 */
	final private function __construct(KConfig $config)
	{
	    //Create the identifier registry
        self::$_identifiers = new KServiceIdentifierRegistry();

	    if(isset($config['cache_prefix'])) {
            self::$_identifiers->setCachePrefix($config['cache_prefix']);
        }

	    if(isset($config['cache_enabled'])) {
            self::$_identifiers->enableCache($config['cache_enabled']);
        }

        //Create the service container
	    self::$_services    = new KServiceContainer();

	    //Auto-load the koowa adapter
        KServiceIdentifier::addLocator(new KServiceLocatorKoowa());
	}

	/**
	 * Clone
	 *
	 * Prevent creating clones of this class
	 */
	final private function __clone() { }

	/**
     * Force creation of a singleton
     *
     * @param  array  An optional array with configuration options.
     * @return KService
     */
	public static function getInstance($config = array())
	{
		static $instance;

		if ($instance === NULL)
		{
			if(!$config instanceof KConfig) {
				$config = new KConfig($config);
			}

			$instance = new self($config);
		}

		return $instance;
	}

	/**
	 * Get an instance of a class based on a class identifier only creating it
	 * if it doesn't exist yet.
	 *
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @param	array   An optional associative array of configuration settings.
	 * @return	object  Return object on success, throws exception on failure
	 * @throws	KServiceServiceException
	 */
	public static function get($identifier, array $config = array())
	{
		$objIdentifier = self::getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

		if(!self::$_services->offsetExists($strIdentifier))
		{
		    //Instantiate the identifier
			$instance = self::_instantiate($objIdentifier, $config);

			//Perform the mixin
			self::_mixin($strIdentifier, $instance);
		}
		else $instance = self::$_services->offsetGet($strIdentifier);

		return $instance;
	}

	/**
	 * Insert the object instance using the identifier
	 *
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @param object The object instance to store
	 */
	public static function set($identifier, $object)
	{
		$objIdentifier = self::getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

		self::$_services->offsetSet($strIdentifier, $object);
	}

	/**
	 * Check if the object instance exists based on the identifier
	 *
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public static function has($identifier)
	{
		try
		{
	        $objIdentifier = self::getIdentifier($identifier);
	        $strIdentifier = (string) $objIdentifier;
	        $result = (bool) self::$_services->offsetExists($strIdentifier);

		} catch (KServiceIdentifierException $e) {
		    $result = false;
		}

		return $result;
	}

	/**
     * Add a mixin or an array of mixins for an identifier
     *
     * The mixins are mixed when the indentified object is first instantiated see {@link get}
     * Mixins are also added to objects that already exist in the service container.
     *
     * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
     * @param  string|array   A mixin identifier or a array of mixin identifiers
     * @see KObject::mixin
     */
    public static function addMixin($identifier, $mixins)
    {
        settype($mixins, 'array');

        $objIdentifier = self::getIdentfier($identifier);
        $strIdentifier = (string) $objIdentifier;

        if (!isset(self::$_mixins[$strIdentifier]) ) {
            self::$_mixins[$strIdentifier] = array();
        }

        self::$_mixins[$strIdentifier] = array_unique(array_merge(self::$_mixins[$strIdentifier], $mixins));

        if(self::$_services->offsetExists($strIdentifier))
        {
            $instance = self::$_services->offsetGet($strIdentifier);
            self::_mixin($strIdentifier, $instance);
        }
    }

    /**
     * Get the mixins for an identifier
     *
     * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
     * @return array 	An array of mixins
     */
    public static function getMixins($identifier)
    {
        $objIdentifier = self::getIdentfier($identifier);
        $strIdentifier = (string) $objIdentifier;

        $result = array();
        if(isset(self::$_mixins[$strIdentifier])) {
            $result = self::$_mixins[$strIdentifier];
        }

        return $result;
    }

	/**
	 * Returns an identifier object.
	 *
	 * Accepts various types of parameters and returns a valid identifier. Parameters can either be an
	 * object that implements KObjectServiceable, or a KServiceIdentifier object, or valid identifier
	 * string. Function will also check for identifier mappings and return the mapped identifier.
	 *
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @return KServiceIdentifier
	 */
	public static function getIdentifier($identifier)
	{
	    if(!is_string($identifier))
		{
	        if($identifier instanceof KObjectServiceable) {
			    $identifier = $identifier->getIdentifier();
		    }
		}

	    $alias = (string) $identifier;
	    if(array_key_exists($alias, self::$_aliases)) {
	        $identifier = self::$_aliases[$alias];
		}

	    if(!self::$_identifiers->offsetExists((string) $identifier))
        {
		    if(is_string($identifier)) {
		        $identifier = new KServiceIdentifier($identifier);
		    }

		    self::$_identifiers->offsetSet((string) $identifier, $identifier);
        }
        else $identifier = self::$_identifiers->offsetGet((string)$identifier);

		return $identifier;
	}

	/**
	 * Set an alias for an identifier
	 *
	 * @param string  The alias
	 * @param mixed	  An object that implements KObjectServiceable, KServiceIdentifier object
	 * 				  or valid identifier string
	 */
	public static function setAlias($alias, $identifier)
	{
		$identifier = self::getIdentifier($identifier);

		self::$_aliases[$alias] = $identifier;
	}

	/**
	 * Get an alias for an identifier
	 *
	 * @param  string  The alias
	 * @return mixed   The class indentifier or identifier object, or NULL if no alias was found.
	 */
	public static function getAlias($alias)
	{
		return isset(self::$_aliases[$alias])  ? self::$_aliases[$alias] : null;
	}

	/**
     * Get a list of aliasses
     *
     * @return array
     */
    public static function getAliases()
    {
        return self::$_aliases;
    }

	/**
	 * Set the configuration options for an identifier
	 *
	 * @param mixed	  An object that implements KObjectServiceable, KServiceIdentifier object
	 * 				  or valid identifier string
	 * @param array	  An associative array of configuration options
	 */
	public static function setConfig($identifier, array $config)
	{
		$objIdentifier = self::getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

		if(isset(self::$_configs[$strIdentifier])) {
		     self::$_configs[$strIdentifier] =  self::$_configs[$strIdentifier]->append($config);
		} else {
		    self::$_configs[$strIdentifier] = new KConfig($config);
		}
	}

	/**
	 * Get the configuration options for an identifier
	 *
	 * @param mixed	  An object that implements KObjectServiceable, KServiceIdentifier object
	 * 				  or valid identifier string
	 *  @param array  An associative array of configuration options
	 */
	public static function getConfig($identifier)
	{
		$objIdentifier = self::getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

	    return isset(self::$_configs[$strIdentifier])  ? self::$_configs[$strIdentifier]->toArray() : array();
	}

	/**
     * Get the configuration options for all the identifiers
     *
     * @return array  An associative array of configuration options
     */
    public static function getConfigs()
    {
        return self::$_configs;
    }

    /**
     * Perform the actual mixin of all registered mixins with an object
     *
     * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
     * @param   object  A KObject instance to used as the mixer
     * @return void
     */
    protected static function _mixin($identifier, $instance)
    {
        if(isset(self::$_mixins[$identifier]) && $instance instanceof KObject)
        {
            $mixins = self::$_mixins[$identifier];
            foreach($mixins as $mixin)
            {
                $mixin = self::get($mixin, array('mixer'=> $instance));
                $instance->mixin($mixin);
            }
        }
    }

    /**
     * Get an instance of a class based on a class identifier
     *
     * @param   object	A KServiceIdentifier object
     * @param   array   An optional associative array of configuration settings.
     * @return  object  Return object on success, throws exception on failure
     * @throws  KServiceException
     */
    protected static function _instantiate(KServiceIdentifier $identifier, array $config = array())
    {
        $result = null;

        //Load the class manually using the basepath
        if(self::get('koowa:loader')->loadClass($identifier->classname, $identifier->basepath))
        {
            if(array_key_exists('KObjectServiceable', class_implements($identifier->classname)))
            {
                //Create the configuration object
                $config = new KConfig(array_merge(self::getConfig($identifier), $config));

                //Set the service container and identifier
                $config->service_container  = self::getInstance();
                $config->service_identifier = $identifier;

                // If the class has an instantiate method call it
                if(array_key_exists('KServiceInstantiatable', class_implements($identifier->classname))) {
                    $result = call_user_func(array($identifier->classname, 'getInstance'), $config, self::getInstance());
                } else {
                    $result = new $identifier->classname($config);
                }

            }
        }

        //Thrown an error if no object was instantiated
        if(!is_object($result)) {
            throw new KServiceException('Cannot instantiate object from identifier : '.$identifier);
        }

        return $result;
    }
}