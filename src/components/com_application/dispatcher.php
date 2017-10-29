<?php

/**
 * Application Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2016 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComApplicationDispatcher extends LibBaseDispatcherAbstract implements KServiceInstantiatable
{
    /**
     * Application.
     *
     * @var ComApplication
     */
    protected $_application = null;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_application = $config->application;

        $this->setComponent($config->component);

        if (PHP_SAPI == 'cli') {
            $this->registerCallback('after.load', array($this, 'updateLegacyPluginsParams'));
            $this->registerCallback('after.load', array($this, 'prepclienv'));
        }

        $this->registerCallback('before.route',  array($this, 'load'));
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
            'application' => null,
            'component' => $this->getIdentifier()->package,
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (! $container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
            $container->setAlias('application.dispatcher', $config->service_identifier);
        }

        return $container->get($config->service_identifier);
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

    /**
     * Method to get a dispatcher object.
     *
     * @throws \UnexpectedValueException If the controller doesn't implement the ControllerInterface
     *
     * @return LibBaseControllerAbstract
     */
    public function getComponent()
    {
        if (! ($this->_controller instanceof LibBaseControllerAbstract)) {

            $this->_controller = $this->getController();

            if (! $this->_controller instanceof LibBaseControllerAbstract) {
                throw new \UnexpectedValueException(
                    'Dispatcher: '.get_class($this->_controller).' does not implement LibBaseDispatcherAbstract',
                    KHttpResponse::INTERNAL_SERVER_ERROR
                );
            }
        }

        return $this->_controller;
    }

    /**
     * Method to set a dispatcher object.
     *
     * @param mixed $component An object that implements ControllerInterface, ServiceIdentifier object
     *                         or valid identifier string
     *
     * @return ComApplicationDispatcher
     */
    public function setComponent($component, $config = array())
    {
        if (! ($component instanceof LibBaseControllerAbstract)) {

            if (is_string($component) && strpos($component, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->package = $component;
            } else {
                $identifier = $this->getIdentifier($component);
            }

            $component = $identifier;
        }

        $this->setController($component, $config);

        return $this;
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
        $url = $this->_application->getRouter()->parse($url);

        KRequest::set('get', $url->query);

        $url->query = KRequest::get('get', 'raw');

        //set the request
        $this->getRequest()->append($url->query);

        $component = substr($this->_request->option, 4);

        if (empty($component)) {
            $context->request->set('option', 'com_application');
            $component = 'application';
        }

        $this->setComponent($component)->dispatch();

        // trigger the onAfterRoute events
        dispatch_plugin('system.onAfterRoute');
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
        $session = (PHP_SAPI == 'cli') ? false : true;

        $this->_application = $this->getService('com:application', array('session' => $session));

        $settings = $this->getService('com:settings.setting');

        $this->getService('anahita:language', array(
            'language' => $settings->language
        ));

        $error_reporting = $settings->error_reporting;

        if ($error_reporting > 0) {
            error_reporting($error_reporting);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }

        define('ANDEBUG', $settings->debug);

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
        if (! empty($_SERVER['argv']) && count($_SERVER['argv']) > 1) {
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
        if (! empty($file)) {
            KService::get('koowa:loader')->loadFile($file);
            exit(0);
        }
    }

    /**
     * Dispatches the component.
     *
     * @param KCommandContext $context Command chain context
     *
     * @return void
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        $name = 'com_'.$this->getComponent()->getIdentifier()->package;

        define('ANPATH_COMPONENT', ANPATH_BASE.DS.'components'.DS.$name);

        if (! file_exists(ANPATH_COMPONENT)) {
            throw new LibBaseControllerExceptionNotFound(
                'Component '.$name.' not found',
                KHttpResponse::INTERNAL_SERVER_ERROR
            );
        }

        $app = KService::get('repos:settings.app')->find(array('package' => $name));

        if (isset($app) && $app->enabled != 1) {
            throw new LibBaseControllerExceptionForbidden(
                'Component '.$name.' is disabled',
                KHttpResponse::INTERNAL_SERVER_ERROR
            );
        }

        $this->getComponent()->dispatch($context);

        dispatch_plugin('system.onAfterDispatch', array( $context ));

        $location = $context->response->getHeader('Location');
        $isHtml = $context->request->getFormat() == 'html';
        $isAjax = $context->request->isAjax();

        if (! $location && $isHtml && !$isAjax) {
            $this->_render($context);
        }

        $this->send($context);
    }

    /**
    *   Outputs html content
    *   @param KCommandContext $context Command chain context
    *
    *   @return void
    */
    protected function _render(KCommandContext $context)
    {
        dispatch_plugin('system.onBeforeRender', array( $context ));

        $config = array(
            'request' => $context->request,
            'response' => $context->response,
            'theme' => $this->_application->getTemplate(),
        );

        $layout = $this->_request->get('tmpl', 'default');

        $this->getService('com:application.controller.default', $config)
             ->layout($layout)
             ->render();

        dispatch_plugin('system.onAfterRender', array( $context ));
    }

    /**
     * Send the response to the client.
     *
     * @param CommandContext $context A command context object
     */
    public function _actionSend(KCommandContext $context)
    {
        $context->response->send();
        exit(0);
    }

    /**
     * Callback to handle Exception.
     *
     * @param KCommandContext $context Command chain context
     *                                 caller => KObject, data => mixed
     *
     * @return void
     */
    protected function _actionException($context)
    {
        $exception = $context->data;

        //if KException then conver it to KException
        if ($exception instanceof KException) {
            $exception = new RuntimeException($exception->getMessage(), $exception->getCode());
        }

        //if cli just print the error and exit
        if (PHP_SAPI == 'cli') {
            print "\n";
            print $exception."\n";
            print debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            exit(0);
        }

        $code = $exception->getCode();

        //check if the error is code is valid
        if ($code < 400 || $code >= 600) {
            $code = KHttpResponse::INTERNAL_SERVER_ERROR;
        }

        $context->response->status = $code;

        $config = array(
            'response' => $context->response,
            'request' => $context->request,
            'theme' => $this->_application->getTemplate()
        );

        //if ajax or the format is not html
        //then return the exception in json format
        if ($context->request->isAjax() || $context->request->getFormat() != 'html') {
            $context->request->setFormat('json');
        }

        $this->getService('com:application.controller.exception', $config)
        ->layout('error')
        ->render($exception);

        $this->send($context);
    }
}
