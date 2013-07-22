<?php 

namespace Console;

require_once 'Console/class.php';
require_once 'Console/Config.php';

require_once __DIR__.'/../src/libraries/koowa/config/interface.php';
require_once __DIR__.'/../src/libraries/koowa/config/config.php';
require_once __DIR__.'/../src/libraries/anahita/functions.php';

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\ArgvInput;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Main Anahita Console Application
 * 
 * Provides anahtia related methods
 * 
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * Installable Anahtia extension package
     * 
     * @var Extension\Pacckages
     */
    protected $_packages;    
    
    /**
     * Provide task callbacks. $applicatio->registerBefore('task', function(){});
     * 
     * @var array
     */
    protected $_callbacks;
    
    /**
     * Constructor
     */
    public function __construct()
    {        
        $this->_callbacks  = array('before'=>array(),'after'=>array()); 
        $this->_packages   = new Extension\Packages();
                                
        $this->_packages->addPackageFromComposerFiles(COMPOSER_ROOT.'/composer.json');

        $this->_packages->addPackageFromComposerFiles(
                Extension\Helper::getComposerFiles(COMPOSER_ROOT.'/packages'));
                
        //@TODO very bad way of doing it.
        //should read composer.lock or installed.json file
        foreach(new \DirectoryIterator(COMPOSER_VENDOR_DIR) as $dir) {
            if ( $dir->isDir() && !$dir->isDot() ) 
            {
                $this->_packages->addPackageFromComposerFiles(
                    Extension\Helper::getComposerFiles($dir->getPathName()));
            }
        }        
        
        $this->_packages->addPackageFromComposerFiles(
                Extension\Helper::getComposerFiles(ANAHITA_ROOT.'/packages'));
        
        parent::__construct();
    }   
    
    /**
     * Loads the Anahita Framework
     */
    public function loadFramework()
    {        
        if ( !defined('JPATH_BASE') )
        {                                    
            $_composerLoader = $GLOBALS['composerLoader'];
            define('JPATH_BASE', WWW_ROOT.'/administrator');
            define('DS', DIRECTORY_SEPARATOR);
            $parts = explode( DS, JPATH_BASE );
            array_pop( $parts );
            
            //Defines
            define( 'JPATH_ROOT',			implode( DS, $parts ) );
            define( '_JEXEC', 1);
            define( 'JPATH_SITE',			JPATH_ROOT );
            define( 'JPATH_CONFIGURATION', 	JPATH_ROOT );
            define( 'JPATH_ADMINISTRATOR', 	JPATH_ROOT.DS.'administrator' );
            define( 'JPATH_XMLRPC', 		JPATH_ROOT.DS.'xmlrpc' );
            define( 'JPATH_LIBRARIES',	 	JPATH_ROOT.DS.'libraries' );
            define( 'JPATH_PLUGINS',		JPATH_ROOT.DS.'plugins'   );
            define( 'JPATH_INSTALLATION',	JPATH_ROOT.DS.'installation' );
            define( 'JPATH_THEMES',			JPATH_BASE.DS.'templates' );
            define( 'JPATH_CACHE',			JPATH_BASE.DS.'cache' );

            require_once( JPATH_LIBRARIES		.DS.'joomla'.DS.'import.php');
            
            // Pre-Load configuration
            require_once( JPATH_CONFIGURATION	.DS.'configuration.php' );
            
            // System configuration
            $CONFIG = new \JConfig();
            
            $config =& \JFactory::getConfig();
            $registry = new \JRegistry('config');
            $registry->loadObject($CONFIG);
            $config = $registry;
            
            error_reporting( E_ALL );
            ini_set( 'display_errors', 1 );            
            
            
            /*
             * Joomla! framework loading
            */
            
            // Joomla! library imports                        
            \jimport( 'joomla.application.menu' );
            \jimport( 'joomla.user.user');
            \jimport( 'joomla.environment.uri' );
            \jimport( 'joomla.html.html' );
            \jimport( 'joomla.html.parameter' );
            \jimport( 'joomla.utilities.utility' );
            \jimport( 'joomla.event.event');
            \jimport( 'joomla.event.dispatcher');
            \jimport( 'joomla.language.language');
            \jimport( 'joomla.utilities.string' );
            
            $_SERVER['HTTP_HOST'] = '';

            require_once( JPATH_LIBRARIES.'/anahita/anahita.php');
            
            //instantiate anahita and nooku
            \Anahita::getInstance(array(
            'cache_prefix'  => uniqid(),
            'cache_enabled' => false
            ));
            
            \KServiceIdentifier::setApplication('site' , JPATH_SITE);
            \KServiceIdentifier::setApplication('admin', JPATH_ADMINISTRATOR);
            
            \KLoader::addAdapter(new \AnLoaderAdapterComponent(array('basepath'=>JPATH_BASE)));
            \KServiceIdentifier::addLocator( \KService::get('anahita:service.locator.component') );

            \KLoader::addAdapter(new \KLoaderAdapterPlugin(array('basepath' => JPATH_ROOT)));
            \KServiceIdentifier::addLocator(\KService::get('koowa:service.locator.plugin'));
            \KService::setAlias('anahita:domain.space',          'com:base.domain.space');
            \KService::set('koowa:database.adapter.mysqli', \KService::get('koowa:database.adapter.mysqli', array('connection'=>\JFactory::getDBO()->_resource, 'table_prefix'=>$CONFIG->dbprefix)));
            \KService::set('anahita:domain.store.database', \KService::get('anahita:domain.store.database', array('adapter'=>\KService::get('koowa:database.adapter.mysqli'))));
            \KService::set('plg:storage.default', new \KObject());
                        
            
            
            global $composerLoader, $console;
            $composerLoader = $_composerLoader;
            $console = $this;
        }
    }    
    
    /**
     * Register a task before callback
     * 
     * @param string $name
     * @param mixed $callback
     * 
     * @return void
     */
    public function registerBefore($name, $callback)
    {
        $this->_callbacks['before'][$name][] = $callback;                    
    }
    
    /**
     * Return if the application is initialized
     * 
     * @return boolean
     */
    public function isInitialized()
    {
        return file_exists(WWW_ROOT.'/configuration.php');
    }
    
    /**
     * Register a task after callback
     *
     * @param string $name
     * @param mixed $callback
     *
     * @return void
     */        
    public function registerAfter($name, $callback)
    {
        $this->_callbacks['name'][$name][] = $callback;
    }   

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Application::doRun()
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $name      = $this->getCommandName($input);
        $result    = true;
        
        if ( isset($this->_callbacks['before'][$name]) ) 
        {
            $callbacks = $this->_callbacks['before'][$name];
            foreach($callbacks as $callback) {
                $result = call_user_func_array($callback, array($input, $output));
                if ( $result === false ) {
                    break;
                }
            }
        }
        
        if ( $result !== false ) 
        {
            parent::doRun($input, $output);
            
            if ( isset($this->_callbacks['after'][$name]) ) {
                $callbacks = $this->_callbacks['after'][$name];
                foreach($callbacks as $callback) {
                    call_user_func_array($callback, array($input, $output));
                }
            }            
        }                 
        
    }
    
    /**
     * Return a set of extension packages
     * 
     * @return Exension/Packages
     */
    public function getExtensionPackages()
    {
         return $this->_packages;   
    }
    
    /**
     * Runs a command.
     * 
     * This method provides a way to run a command during the excution of another command 
     * 
     * @param string $command
     * 
     * @return void
     */
    public function runCommand($command)
    {          
        $this->setAutoExit(false);  
        $argv  = explode(' ','application '.$command);         
        $input = new ArgvInput($argv);
        $this->run($input);
    }       
}

?>