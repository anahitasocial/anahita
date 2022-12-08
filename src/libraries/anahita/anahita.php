<?php

define('ANAHITA', 1);

require_once ANPATH_LIBRARIES.'/anahita/functions.php';
require_once ANPATH_LIBRARIES.'/anahita/translator.php';

/**
 * Service Class.
 *
 * @category   Anahita
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class anahita
{
    /**
     * Version.
     *
     * @var string
     */
    protected static $_version = '4.7.3';

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
    private function __clone() {}

    /**
     * Singleton instance.
     *
     * @param  array  An optional array with configuration options.
     *
     * @return Anahita
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
        $this->_path = dirname(__FILE__);
        
        require_once $this->_path.'/loader/loader.php';
        
        $loader = AnLoader::getInstance($config);
        $service = AnService::getInstance($config);
        $service->set('anahita:loader', $loader);

        require_once $this->_path.'/loader/adapter/anahita.php';

        AnLoader::addAdapter(new AnLoaderAdapterAnahita(array('basepath' => $this->_path)));
        AnLoader::addAdapter(new AnLoaderAdapterDefault(array('basepath' => ANPATH_LIBRARIES.'/default')));

        AnServiceClass::getInstance();

        AnServiceIdentifier::addLocator(new AnServiceLocatorAnahita());
        AnServiceIdentifier::addLocator(new AnServiceLocatorRepository());

        // register an empty path for the application
        // a workaround to remove the applicaiton path from an identifier
        AnServiceIdentifier::setApplication('', '');

        // create a central event dispatcher
        AnService::set('anahita:event.dispatcher', AnService::get('anahita:event.dispatcher'));
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
