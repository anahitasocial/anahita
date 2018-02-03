<?php

define('ANAHITA', 1);

/**
 * Include Koowa.
 */
require_once ANPATH_LIBRARIES.'/koowa/koowa.php';
require_once ANPATH_LIBRARIES.'/anahita/functions.php';
require_once ANPATH_LIBRARIES.'/anahita/translator.php';

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
class anahita
{
    /**
     * Version.
     *
     * @var string
     */
    protected static $_version = '4.3.11';

    /**
     * Path to Anahita libraries.
     *
     * @var string
     */
    protected $_path;

    /**
     * Clone.
     *
     * Prevent creating clones of this class
     */
    final private function __clone(){}

    /**
     * Singleton instance.
     *
     * @param  array  An optional array with configuration options.
     *
     * @return Koowa
     */
    final public static function getInstance($config = array())
    {
        static $instance;

        if (is_null($instance)) {
            $instance = new self($config);
        }

        return $instance;
    }

    /**
     * Constructor.
     *
     * Prevent creating instances of this class by making the contructor private
     *
     * @param  array  An optional array with configuration options.
     */
    final private function __construct($config = array())
    {
        //store the path
        $this->_path = dirname(__FILE__);
        $cache_prefix = isset($config['cache_prefix']) ? $config['cache_prefix'] : '';
        $cache_enabled = isset($config['cache_enabled']) ? $config['cache_enabled'] : 0;

        //instantiate koowa
        Koowa::getInstance(array(
            'cache_prefix' => $cache_prefix,
            'cache_enabled' => (bool) $cache_enabled,
        ));

        require_once dirname(__FILE__).'/loader/adapter/anahita.php';

        KLoader::addAdapter(new AnLoaderAdapterAnahita(array('basepath' => dirname(__FILE__))));
        KLoader::addAdapter(new AnLoaderAdapterDefault(array('basepath' => ANPATH_LIBRARIES.'/default')));

        AnServiceClass::getInstance();

        KServiceIdentifier::addLocator(new AnServiceLocatorAnahita());
        KServiceIdentifier::addLocator(new AnServiceLocatorRepository());

        //register an empty path for the application
        //a workaround to remove the applicaiton path from an identifier
        KServiceIdentifier::setApplication('', '');

        //create a central event dispatcher
        KService::set('anahita:event.dispatcher', KService::get('koowa:event.dispatcher'));
    }

    /**
     * Get the version of the Anahita library.
     *
     * @return string
     */
    public static function getVersion()
    {
        return self::$_version;
    }

    /**
     * Get path to Anahita libraries.
     */
    public function getPath()
    {
        return $this->_path;
    }
}
