<?php
/**
 * @version 	$Id: loader.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Loader
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

require_once dirname(__FILE__).'/adapter/interface.php';
require_once dirname(__FILE__).'/adapter/abstract.php';
require_once dirname(__FILE__).'/adapter/koowa.php';
require_once dirname(__FILE__).'/registry.php';

/**
 * KLoader class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 */
class KLoader
{
    /**
     * The file container
     *
     * @var array
     */
    protected $_registry = null;

    /**
     * Adapter list
     *
     * @var array
     */
    protected static $_adapters = array();

    /**
     * Prefix map
     *
     * @var array
     */
    protected static $_prefix_map = array();

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the contructor private
     */
    final private function __construct($config = array())
    {
        //Create the class registry
        $this->_registry = new KLoaderRegistry();

        if(isset($config['cache_prefix'])) {
            $this->_registry->setCachePrefix($config['cache_prefix']);
        }

        if(isset($config['cache_enabled'])) {
            $this->_registry->enableCache($config['cache_enabled']);
        }

        //Add the koowa class loader
        $this->addAdapter(new KLoaderAdapterKoowa(
            array('basepath' => dirname(dirname(__FILE__)))
        ));

        //Auto register the loader
        $this->register();
    }

    /**
     * Clone
     *
     * Prevent creating clones of this class
     */
    final private function __clone() { }

    /**
     * Singleton instance
     *
     * @param  array  An optional array with configuration options.
     * @return KLoader
     */
    public static function getInstance($config = array())
    {
        static $instance;

        if ($instance === NULL) {
            $instance = new self($config);
        }

        return $instance;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));

        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }
    }


    /**
     * Get the class registry object
     *
     * @return object KLoaderRegistry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

 	/**
     * Add a loader adapter
     *
     * @param object    A KLoaderAdapter
     * @return void
     */
    public static function addAdapter(KLoaderAdapterInterface $adapter)
    {
        self::$_adapters[$adapter->getType()]     = $adapter;
        self::$_prefix_map[$adapter->getPrefix()] = $adapter->getType();
    }

	/**
     * Get the registered adapters
     *
     * @return array
     */
    public static function getAdapters()
    {
        return self::$_adapters;
    }

    /**
     * Load a class based on a class name
     *
     * @param string    The class name
     * @param string    The basepath
     * @return boolean  Returns TRUE on success throws exception on failure
     */
    public function loadClass($class, $basepath = null)
    {
        $result = false;

        //Extra filter added to circomvent issues with Zend Optimiser and strange classname.
        if((ctype_upper(substr($class, 0, 1)) || (strpos($class, '.') !== false)))
        {
            //Pre-empt further searching for the named class or interface.
            //Do not use autoload, because this method is registered with
            //spl_autoload already.
            if (!class_exists($class, false) && !interface_exists($class, false))
            {
                //Get the path
                $path = self::findPath( $class, $basepath );

                if ($path !== false) {
                    $result = self::loadFile($path);
                }
            }
            else $result = true;
        }

        return $result;
    }

	/**
     * Load a class based on an identifier
     *
     * @param string|object The identifier or identifier object
     * @return boolean      Returns TRUE on success throws exception on failure
     */
    public function loadIdentifier($identifier)
    {
        $result = false;

        $identifier = KService::getIdentifier($identifier);

        //Get the path
        $path = $identifier->filepath;

        if ($path !== false) {
            $result = self::loadFile($path);
        }

        return $result;
    }

    /**
     * Load a class based on a path
     *
     * @param string	The file path
     * @return boolean  Returns TRUE on success throws exception on failure
     */
    public function loadFile($path)
    {
        $result = false;

        /*
         * Don't re-include files and stat the file if it exists
         * realpath is needed to resolve symbolic links
         */
        if (!in_array(realpath($path), get_included_files()) && file_exists($path))
        {
            $mask = E_ALL ^ E_WARNING;
            if (defined('E_DEPRECATED')) {
                $mask = $mask ^ E_DEPRECATED;
            }

            $old = error_reporting($mask);
            $included = include $path;
            error_reporting($old);

            if ($included) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get the path based on a class name
     *
     * @param string	The class name
     * @param string    The basepath
     * @return string   Returns canonicalized absolute pathname
     */
    public function findPath($class, $basepath = null)
    {
        static $base;

        //Switch the base
        $base = $basepath ? $basepath : $base;

        if(!$this->_registry->offsetExists($base.'-'.(string) $class))
        {
            $result = false;

            $word  = preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $class);
            $parts = explode(' ', $word);

            if(isset(self::$_prefix_map[$parts[0]]))
            {
                $result = self::$_adapters[self::$_prefix_map[$parts[0]]]->findPath( $class, $basepath);

                if ($result !== false)
                {
                   //Get the canonicalized absolute pathname
                   $path = realpath($result);
                   $result = $path !== false ? $path : $result;
                }

                $this->_registry->offsetSet($base.'-'.(string) $class, $result);
            }

        } else $result = $this->_registry->offsetGet($base.'-'.(string)$class);

        return $result;
    }
}