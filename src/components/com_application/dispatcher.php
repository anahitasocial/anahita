<?php

/**
 * Application Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2016 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComApplicationDispatcher extends LibBaseDispatcherAbstract implements AnServiceInstantiatable
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
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_application = $config->application;

        $this->setComponent($config->component);

        $this->registerCallback('before.route',  array($this, 'load'));
        
        if (PHP_SAPI == 'cli') {
            $this->registerCallback('after.load', array($this, 'prepclienv'));
        }
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
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
     * @param AnConfigInterface  $config    An optional AnConfig object with configuration options
     * @param AnServiceInterface $container A AnServiceInterface object
     *
     * @return AnServiceInstantiatable
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
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
                    AnHttpResponse::INTERNAL_SERVER_ERROR
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
     * @param AnCommandContext $context Command chain context
     *
     * @return bool
     */
    protected function _actionRoute(AnCommandContext $context)
    {
        if (! isset($this->_application)) {
            throw new AnException(
                'Application object is not instantiated!',
                AnHttpResponse::INTERNAL_SERVER_ERROR
            );
        }
        
        //route the application
        $url = clone AnRequest::url();
        $url = $this->_application->getRouter()->parse($url);

        AnRequest::set('get', $url->query);

        $url->query = AnRequest::get('get', 'raw');

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
        $this->getService('anahita:loader')->loadIdentifier($identifier);

        //no need to create session when using CLI (command line interface)
        $session = (PHP_SAPI === 'cli') ? false : true;

        $this->_application = $this->getService('com:application', array('session' => $session));

        $settings = $this->getService('com:settings.config');
        
        $this->getService('anahita:language', array(
            'language' => $settings->language
        ));
        
        if ($settings->cors_enabled) {
            header('Access-Control-Allow-Origin: ' . $settings->client_domain);
            header('Access-Control-Allow-Methods: ' . $settings->cors_methods);
            header('Access-Control-Allow-Headers: ' . $settings->cors_headers);
            
            $cors_credentials = $settings->cors_credentials ? 'true' : 'false';
            header('Access-Control-Allow-Credentials: ' . $cors_credentials);
        }

        if ($settings->error_reporting > 0) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }

        define('ANDEBUG', $settings->debug);

        //set the default timezone to UTC
        date_default_timezone_set('UTC');

        AnRequest::root(str_replace('/'.$this->_application->getName(), '', AnRequest::base()));
    }

    /**
     * Prepares the CLI mode.
     *
     * @param AnCommandContext $context
     */
    protected function _actionPrepclienv(AnCommandContext $context)
    {
        if (! empty($_SERVER['argv']) && count($_SERVER['argv']) > 1) {
            $args = array_slice($_SERVER['argv'], 1);

            if (is_readable(realpath($args[0]))) {
                $file = array_shift($args);
            }

            $args = explode('&-data&', implode('&', $args));
            $args = array_filter($args, 'trim');

            foreach ($args as $i => $arg) {
                $arg = trim($arg);

                if ($i == 0) {
                    if (strpos($arg, '/') !== false) {
                        $arg = substr_replace($arg, '?', strpos($arg, '&'), 1);
                        $url = AnService::get('anahita:http.url', array('url' => $arg));
                        AnRequest::url()->path = AnRequest::base().$url->path;
                        $_GET = $url->query;
                    } else {
                        AnRequest::url()->path = AnRequest::base();
                        parse_str($arg, $_GET);
                    }
                } else {
                    parse_str($arg, $_POST);
                }
            }
        }

        $_GET['format'] = 'json';
        AnRequest::url()->format = 'json';
        AnRequest::url()->setQuery($_GET);

        AnService::get('com:plugins.helper')->import('cli');
        dispatch_plugin('cli.onCli');

        //if there's a file then just load the file and exit
        if (! empty($file)) {
            AnService::get('anahita:loader')->loadFile($file);
            exit(0);
        }
    }

    /**
     * Dispatches the component.
     *
     * @param AnCommandContext $context Command chain context
     *
     * @return void
     */
    protected function _actionDispatch(AnCommandContext $context)
    {
        dispatch_plugin('system.onBeforeDispatch', array( $context ));
        
        $name = 'com_'.$this->getComponent()->getIdentifier()->package;

        define('ANPATH_COMPONENT', ANPATH_BASE.DS.'components'.DS.$name);

        if (! file_exists(ANPATH_COMPONENT)) {
            throw new LibBaseControllerExceptionNotFound(
                'Component '.$name.' not found',
                AnHttpResponse::INTERNAL_SERVER_ERROR
            );
        }

        $app = AnService::get('repos:settings.app')->find(array('package' => $name));

        if (isset($app) && $app->enabled != 1) {
            throw new LibBaseControllerExceptionForbidden(
                'Component '.$name.' is disabled',
                AnHttpResponse::INTERNAL_SERVER_ERROR
            );
        }

        $this->getComponent()->dispatch($context);

        dispatch_plugin('system.onAfterDispatch', array( $context ));
        
        $this->send($context);
    }

    /**
     * Send the response to the client.
     *
     * @param CommandContext $context A command context object
     */
    public function _actionSend(AnCommandContext $context)
    {
        $context->response->send();
        exit(0);
    }

    /**
     * Callback to handle Exception.
     *
     * @param AnCommandContext $context Command chain context
     *                                 caller => AnObject, data => mixed
     *
     * @return void
     */
    protected function _actionException($context)
    {
        $exception = $context->data;

        //if AnException then conver it to AnException
        if ($exception instanceof AnException) {
            $exception = new RuntimeException($exception->getMessage(), $exception->getCode());
        }

        //if cli just print the error and exit
        if (PHP_SAPI === 'cli') {
            print "\n";
            print $exception."\n";
            print debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            exit(0);
        }

        $code = $exception->getCode();

        //check if the error is code is valid
        if ($code < 400 || $code >= 600) {
            $code = AnHttpResponse::INTERNAL_SERVER_ERROR;
        }

        $context->response->status = $code;

        $config = array(
            'response' => $context->response,
            'request' => $context->request,
        );

        $context->request->setFormat('json');

        $this->getService('com:application.controller.exception', $config)
        ->layout('error')
        ->render($exception);

        $this->send($context);
    }
}
