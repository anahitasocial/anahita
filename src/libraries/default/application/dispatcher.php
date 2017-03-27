<?php

/**
 * Application Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibApplicationDispatcher extends LibBaseDispatcherApplication
{
    /**
     * Application.
     *
     * @var JApplication
     */
    protected $_application;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_application = $config->application;

        if (PHP_SAPI == 'cli') {
            $this->registerCallback('after.load', array($this, 'updateLegacyPluginsParams'));
            $this->registerCallback('after.load', array($this, 'prepclienv'));
        }
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'application' => null
        ));

        parent::_initialize($config);
    }

    /**
     * Parses the route.
     *
     * @param KCommandContext $context Command chain context
     *
     * @return bool
     */
    protected function _actionRoute(KCommandContext $context)
    {
        //route the application
        $url = clone KRequest::url();

        $this->_application->getRouter()->parse($url);

        KRequest::set('get', $url->query);

        // trigger the onAfterRoute events
        dispatch_plugin('system.onAfterRoute');

        $url->query = KRequest::get('get', 'raw');

        //set the request
        $this->getRequest()->append($url->query);

        $component = substr($this->_request->option, 4);

        $this->setComponent($component);
    }

    /**
     * Loads the application.
     */
    protected function _actionLoad($context)
    {
        //already loaded
        if ($this->_application instanceof ComApplication) {
            return;
        }

        //register exception handler
        set_exception_handler(array($this, 'exception'));

        $identifier = clone $this->getIdentifier();
        $identifier->name = 'application';
        $this->getService('koowa:loader')->loadIdentifier($identifier);

        //no need to create session when using CLI (command line interface)
        if (PHP_SAPI == 'cli') {
            $session = false;
        } else {
            $session = true;
        }

        $this->_application = $this->getService('application', array('session' => $session));

        $settings = KService::get('com:settings.setting');

        $this->getService('anahita:language', array(
            'language' => $settings->language
        ));

        $error_reporting = $settings->error_reporting;

        define('ANDEBUG', $settings->debug);

        //taken from nooku application dispatcher
        if ($error_reporting > 0) {
            error_reporting($error_reporting);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }

        $this->getService()->set($identifier, $this->_application);
        $this->getService()->setAlias('application', $identifier);

        //set the default timezone to UTC
        date_default_timezone_set('UTC');

        KRequest::root(str_replace('/'.$this->_application->getName(), '', KRequest::base()));
    }

    /**
     * Prepares the CLI mode.
     *
     * @param KCommandContext $context
     */
    protected function _actionPrepclienv(KCommandContext $context)
    {
        if (!empty($_SERVER['argv']) && count($_SERVER['argv']) > 1) {
            $args = array_slice($_SERVER['argv'], 1);

            if (is_readable(realpath($args[0]))) {
                $file = array_shift($args);
            }

            $args = explode('&-data&', implode($args, '&'));
            $args = array_filter($args, 'trim');

            foreach ($args as $i => $arg) {
                $arg = trim($arg);

                if ($i == 0) {
                    if (strpos($arg, '/') !== false) {
                        $arg = substr_replace($arg, '?', strpos($arg, '&'), 1);
                        $url = KService::get('koowa:http.url', array('url' => $arg));
                        KRequest::url()->path = KRequest::base().$url->path;
                        $_GET = $url->query;
                    } else {
                        KRequest::url()->path = KRequest::base();
                        parse_str($arg, $_GET);
                    }
                } else {
                    parse_str($arg, $_POST);
                }
            }
        }

        $_GET['format'] = 'json';
        KRequest::url()->format = 'json';
        KRequest::url()->setQuery($_GET);

        KService::get('com:plugins.helper')->import('cli');
        dispatch_plugin('cli.onCli');

        //if there's a file then just load the file and exit
        if (!empty($file)) {
            KService::get('koowa:loader')->loadFile($file);
            exit(0);
        }
    }

    /**
    *  if the plugins table still has a legacy params field, rename it to meta
    *  to be consistent with Anahita's convention
    *  @todo remove this method in the future releases
    */
    public function updateLegacyPluginsParams()
    {
        //Renaming a legacy database table field otherwise cli would break
        $db = KService::get('anahita:domain.store.database');
        $pluginColumns = $db->getColumns('plugins');

        if (isset($pluginColumns['params'])) {
           $db->execute('ALTER TABLE `#__plugins` CHANGE `params` `meta` text DEFAULT NULL');
           $db->execute('ALTER TABLE `#__plugins` CHANGE `published` `enabled` tinyint(3) NOT NULL DEFAULT 0');
           $db->execute('DROP INDEX `idx_folder` ON `#__plugins`');
           $db->execute('ALTER TABLE `#__plugins` ADD INDEX `idx_folder` (`enabled`, `folder`)');
           print "Please run the previous command one more time!\n";
           exit(0);
        }
    }
}
