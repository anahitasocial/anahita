<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Service Class.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnServiceClass
{
    /**
     * Register to cache the default classes for an identifier.
     *
     * @var ArrayObject
     */
    protected static $_defaults;

    /**
     * Identifiers.
     *
     * @var array
     */
    protected static $_identifiers = array();

    /**
     * Constructor.
     *
     * Prevent creating instances of this class by making the contructor private
     */
    final private function __construct(KConfig $config)
    {
        //Create the identifier registry 
        self::$_defaults = new ArrayObject();
    }

    /**
     * Clone.
     *
     * Prevent creating clones of this class
     */
    final private function __clone()
    {
    }

    /**
     * Force creation of a singleton.
     * 
     * @param  array  An optional array with configuration options.
     *
     * @return KService
     */
    public static function getInstance($config = array())
    {
        static $instance;

        if ($instance === null) {
            if (!$config instanceof KConfig) {
                $config = new KConfig($config);
            }

            $instance = new self($config);
        }

        return $instance;
    }

    /**
     * Sets a default class.
     *
     * @param string $identifier The identifier
     * @param string $classname  The default class name
     */
    public static function setDefaultClass($identifier, $classname)
    {
        self::$_defaults[(string) $identifier] = $classname;
        unset(self::$_identifiers[(string) $identifier]);
    }

    /**
     * Registers a default.
     *
     * @param array $config Options
     */
    public static function registerDefault($config)
    {
        if (!isset($config['identifier'])) {
            throw new KException('identifier [KServiceIdentifier] options is requied');
        }

        $strIdentifier = (string) $config['identifier'];

        if (isset($config['default'])) {
            $config['default'] = (array) $config['default'];
        } else {
            if (is_object($config['prefix'])) {
                $config['prefix'] = get_class(($config['prefix']));
            }

            if (empty($config['name'])) {
                $path = AnInflector::implode($config['identifier']->path);
                $config['name'] = array($path.ucfirst($config['identifier']->name), $path.'Default');
            }

            unset($config['default']);
        }

        self::$_identifiers[$strIdentifier] = $config;

        if (isset(self::$_defaults[$strIdentifier])) {
            unset(self::$_defaults[$strIdentifier]);
        }
    }

    /**
     * Finds the default class for an identifier or return null.
     *
     * @param KServiceIdentifier $identifier The identifier of the class 
     * 
     * @return string|bool Return the class name or false if not found
     */
    public static function findDefaultClass($identifier)
    {
        $strIdentifier = (string) $identifier;

        if (isset(self::$_defaults[$strIdentifier])) {
            $classname = self::$_defaults[$strIdentifier];

            if ($classname === false || class_exists($classname)) {
                return $classname;
            }
        }

        $classbase = 'Lib'.ucfirst($identifier->package).AnInflector::implode($identifier->path);
        $loader = KService::get('koowa:loader');
        $classname = $classbase.ucfirst($identifier->name);

        if (!class_exists($classname)) {
            $classname = $classbase.'Default';

            if (!class_exists($classname)) {
                $classname = false;
            }
        }

        if ($classname === false) {
            if (isset(self::$_identifiers[$strIdentifier])) {
                $config = self::$_identifiers[$strIdentifier];

                if (isset($config['default'])) {
                    $classes = array_unique($config['default']);
                } else {
                    $classes = get_prefix($config['prefix'], $config['name']);

                    if (isset($config['fallback'])) {
                        $classes[] = $config['fallback'];
                    }
                }
                foreach ($classes as $class) {
                    //make sure to find  path first
                   //then try to load it 
                   if ($loader->findPath($class, $identifier->basepath) &&
                        $loader->loadClass($class, $identifier->basepath)
                           ) {
                       $classname = $class;
                       break;
                   }
                }
            }
        }

        self::setDefaultClass($strIdentifier, $classname);

        return $classname;
    }
}

/**
 * Return an array of class prefix.
 *
 * @param object $object An object
 * @param array  $config An array of configuration
 *
 * @return array
 */
function get_prefix($object, $config = array())
{
    if (!is_array($config) || is_numeric(key($config))) {
        $config = array('append' => (array) $config);
    }

    $config = array_merge(array(
            'break' => 'K',
            'append' => null,
    ), $config);

    $classes = array();
    $class = is_string($object) ? $object : get_class($object);
    $break = $config['break'];
    $append = $config['append'];

    while ($class) {
        if (strpos($class, $break) === 0) {
            break;
        }

        $parts = AnInflector::explode($class);

        if ($parts[0] == 'lib') {
            $classes[] = 'Com'.ucfirst($parts[1]);
            $classes[] = ucfirst($parts[0]).ucfirst($parts[1]);
        } elseif ($parts[0] == 'com') {
            $classes[] = ucfirst($parts[0]).ucfirst($parts[1]);
            $classes[] = 'Lib'.ucfirst($parts[1]);
        } else {
            $classes[] = ucfirst($parts[0]);
        }

        $class = get_parent_class($class);
    };

    $classes = array_unique($classes);

    if ($append) {
        $array = array();
        settype($append, 'array');
        foreach ($classes as $key => $class) {
            foreach ($append as $word) {
                $array[] = $class.$word;
            }
        }
        $classes = $array;
    }

    return $classes;
}

/**
 * Registers a default class for an identifier. This is a private API call within
 * Base classes and it's subject to change.
 *
 * @param array $config Configuration
 */
function register_default($config)
{
    AnServiceClass::registerDefault($config);
}

function unregister_default($identifier)
{
    AnServiceClass::setDefaultClass($identifier, null);
}
